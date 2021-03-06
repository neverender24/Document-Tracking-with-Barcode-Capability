<?php

namespace App\Http\Controllers;

use App\Document;
use App\Route;
use Illuminate\Http\Request;

class DocumentControllerOld extends Controller
{
    public function __construct(Document $document)
    {
        $this->model = $document;
        $this->middleware("auth");
    }

    public function index()
    {
        return view('admin.index');
    }

    public function open_pdf()
    {
        return view('admin.open_pdf');
    }

    public function getDocuments(Request $request)
    {
        $index = $this->model->with(['routes', 'documentType'])
            ->where('user_id', auth()->user()->id);

        $index = $this->searchFilter($index, $request->search, $request->length);

        return ['data' => $index, 'draw' => $request->draw];
    }

    public function returnedMyDocuments(Request $request)
    {
        $index = $this->model->with(['routes', 'documentType'])
            ->whereHas('routes', function ($q) {
                $q->where('remarks', 'like', '%return%');
            })
            ->where('user_id', auth()->user()->id);

        $index = $this->searchFilter($index, $request->search, $request->length);

        return ['data' => $index, 'draw' => $request->draw];
    }

    public function returnedAllDocuments(Request $request)
    {
        $user = auth()->user()->office_id;

        $index = Document::with('routes')
            ->leftjoin('document_types', 'documents.document_type_id', '=', 'document_types.id')
            ->leftjoin('routes', 'routes.barcode', '=', 'documents.document_code')
            ->where(function ($query) use($user){
                $query->where('routes.office_id', $user);
                $query->orWhere('routes.release_to', $user);
                $query->orWhere('documents.office_id', $user);
            })
            ->where('routes.remarks', 'LIKE', '%return%')
            ->select('document_code', 'document_title', 'document_type_prefix', 'documents.created_at', 'documents.id', 'documents.document_type_id', 'documents.document_id')
            ->orderBy('documents.created_at', 'desc')
            ->distinct();

        $index = $this->searchFilter($index, $request->search, $request->length);

        return ['data' => $index, 'draw' => $request->draw];
    }

    // public function allDocuments(Request $request)
    // {
    //     $user = auth()->user()->office_id;

    //     $index = $this->model->with(['routes', 'documentType'])
    //         ->leftjoin('document_types', 'documents.document_type_id', '=', 'document_types.id')
    //         ->leftjoin('routes', 'routes.barcode', '=', 'documents.document_code')
    //         ->where(function ($query) use ($user){
    //             $query->where('routes.office_id', $user);
    //             $query->orWhere('routes.release_to', $user);
    //             $query->orWhere('documents.office_id', $user);
    //         })
    //         ->select('document_code', 'document_title', 'document_type_prefix', 'documents.created_at', 'documents.id', 'documents.document_type_id', 'documents.document_id')
    //         ->distinct();

    //     $index = $this->searchFilter($index, $request->search, $request->length, $user);

    //     return ['data' => $index, 'draw' => $request->draw];
    // }

    /**
     * Display tanan documents na ni-agi sa office sa user
     *
     * @param Request $request
     * @return void
     */
    public function allDocuments(Request $request)
    {
        $user = auth()->user()->office_id;
        
        $index = $this->model->with(['routes', 'documentType'])
            ->where('office_id', $user);

        $index = $this->searchFilter($index, $request->search, $request->length);

        return ['data' => $index, 'draw' => $request->draw];
    }
    
    /**
     * Search ang documents, common sa tanan query
     *
     * @param [collection] $index
     * @param [string] $searchValue
     * @param [int] $length
     * @return void
     */
    public function searchFilter($index, $searchValue, $length)
    {
        if ($searchValue) {
            $index->where(function ($query) use ($searchValue) {
                $query->orWhere('document_code', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('document_id', 'LIKE', '%' . $searchValue . '%')
                    ->orWhere('document_title', 'LIKE', '%' . $searchValue . '%');
            }) ;
        }

        $index = $index->orderBy('documents.created_at', 'desc')->paginate($length);

        return $index;
    }

    public function edit(Request $request)
    {
        return $this->model->where("id", $request->id)->first();
    }

    public function store(Request $request)
    {
        $doc = $request->all();

        // $doc[0] = document details
        // $doc[1] = sub documents
        // $doc[2] = office route
        $doc[0]['user_id'] = auth()->user()->id;
        $doc[0]['office_id'] = auth()->user()->office_id;

        //create document first
        $save = $this->model->create($doc[0]);

        foreach ($doc[1] as $id => $data) {
            $code = $data['code'];
            $index = $this->model->where('document_code', $code)
                ->update(['document_id' => $save->document_code]);
        }

        foreach ($doc[2] as $id => $data) {
            $office = $data['office_id'];
            $create = new \App\Route;
            $create->release_at = now()->toDateTimeString();
            $create->released_by = auth()->user()->id;
            $create->barcode = $save->document_code;
            $create->office_id = auth()->user()->office_id;
            $create->release_to = $office;
            $create->remarks = "Start";
            $create->save();
        }

        return $save;
    }

    public function update(Request $request, $id)
    {
        // definition
        $document = $request->document;
        $subDocument = $request->subDocuments;
        $routes = $request->routes;

        //dd($request->all());

        $edit = $this->model->findOrFail($id);
        $edit->update($document);

        $subdocs = $this->model->where('document_id', $document['document_code'])->get();
        $isInside = false;

        // add document
        foreach ($subDocument as $id => $data) {
            $code = $data['document_code'];
            $index = $this->model->where('document_code', $code)->update(['document_id' => $document['document_code']]);
        }

        // Route
        foreach ($routes as $id => $data) {
            $office = $data['office_id'];
            /*
             ** check if route exist: false add new route
             ** condition: barcode, receive_at, release_to
             */
            $excluded = Route::where('barcode', $document['document_code'])->whereNull('receive_at')->where('release_to', $office)->latest()->first();
            if (is_null($excluded)) {
                $create = new \App\Route;
                $create->release_at = now()->toDateTimeString();
                $create->barcode = $document['document_code'];
                $create->released_by = auth()->user()->id;
                $create->office_id = auth()->user()->office_id;
                $create->release_to = $office;
                $create->save();
            }
        }

        return $edit;
    }

    public function getSubDocument(Request $request)
    {
        return $this->model
            ->with(['routes'])
            ->whereHas('routes', function ($q) {
                $q->latest();
            })
            ->where('document_code', $request->code)
            ->first();
    }

    public function getSubDocuments(Request $request)
    {
        return $this->model->whereNotNull('document_id')->where('document_id', $request->document_id)->get();
    }

    public function deleteDocument(Request $request)
    {
        return $this->model->where('document_code', $request->id)->delete();

    }

    public function generateBarcodes()
    {

        $total = request()->get('total');
        $barcodes = [];

        for ($index = 0; $index < $total; $index++) {
            $random = str_random(8);

            $duplicate = $this->model->where('document_code', $random)->first();
            if (!$duplicate) {
                array_push($barcodes, $random);
            }

        }

        return $barcodes;
    }
}
