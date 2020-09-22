<?php

namespace App\Http\Controllers;

use App\Document;
use App\Route;
use Illuminate\Http\Request;

class DocumentController extends Controller
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

    /**
     * get all documents of the logged-in user only.
     * dili niya makita ang ang uban maski kauban silag office.
     *
     * @param Request $request
     * @return void
     */
    public function getDocuments(Request $request)
    {
        $index = $this->model->with(['routes', 'documentType'])
            ->where('user_id', auth()->user()->id);

        $index = $this->searchFilter($index, $request->search, $request->length);

        return ['data' => $index, 'draw' => $request->draw];
    }

    /**
     * get all returned documents of the logged-in user only.
     * dili makita ang uban documents maski kauban clag office
     *
     * @param Request $request
     * @return void
     */
    public function returnedMyDocuments(Request $request)
    {
        $index = $this->model->with(['routes', 'documentType'])
            ->where('user_id', auth()->user()->id)
            ->whereHas('routes', function ($q) {
                $q->where('remarks', 'like', '%return%');
            });

        $index = $this->searchFilter($index, $request->search, $request->length);

        return ['data' => $index, 'draw' => $request->draw];
    }

    /**
     * get all returned documents na ni-agi sa office niya
     *
     * @param Request $request
     * @return void
     */
    public function returnedAllDocuments(Request $request)
    {
        $user = auth()->user()->office_id;

        $index = $this->model->with(['routes', 'documentType'])
        ->where(function($q) use($user){
            $q->where('office_id', $user)
            ->whereHas('routes', function ($query) {
                $query->where('remarks', 'like', '%return%');
            })
            ->orWhereHas('routes', function ($query) use ($user) {
                $query->where('remarks', 'like', '%return%')
                ->where(function($q) use ($user){
                    $q->where('office_id', $user)
                    ->orWhere('release_to', $user);
                });
            });
        });

        $index = $this->searchFilter($index, $request->search, $request->length);

        return ['data' => $index, 'draw' => $request->draw];
    }

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
            ->where(function($q) use($user){
                $q->where('office_id', $user)
                ->orWhereHas('routes', function ($query) use ($user) {
                    $query->where('office_id', $user)
                        ->orWhere('release_to', $user);
                });
            });

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
            });
        }

        $index = $index->orderBy('created_at', 'desc')->paginate($length);

        return $index;
    }

    /**
     * edit document
     *
     * @param Request $request
     * @return void
     */
    public function edit(Request $request)
    {
        return $this->model->where("id", $request->id)->first();
    }

    /**
     * saving document
     *
     * @param Request $request
     * @return void
     */
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

    /**
     * updating the document
     *
     * @param Request $request
     * @param [int] $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        // definition
        $document = $request->document;
        $subDocument = $request->subDocuments;
        $routes = $request->routes;


        $edit = $this->model->findOrFail($id);
        $edit->update($document);

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
            $excluded = Route::where('barcode', $document['document_code'])
                ->whereNull('receive_at')
                ->where('release_to', $office)
                ->latest()
                ->first();

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

    /**
     * getting the subdocument
     *
     * @param Request $request
     * @return void
     */
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

    /**
     * get all sub documents
     *
     * @param Request $request
     * @return void
     */
    public function getSubDocuments(Request $request)
    {
        return $this->model->whereNotNull('document_id')->where('document_id', $request->document_id)->get();
    }

    /**
     * delete document
     *
     * @param Request $request
     * @return void
     */
    public function deleteDocument(Request $request)
    {
        $barcode = $request->id;

        Route::where('barcode', $barcode)->limit(1)->delete();
        $this->model->where('document_code', $barcode)->limit(1)->delete();

        return $barcode;
    }

    /**
     * randomly generate barcodes
     *
     * @return void
     */
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

    /**
     * open print pdf
     *
     * @return void
     */
    public function open_pdf()
    {
        return view('admin.open_pdf');
    }

    public function file_document(Request $request)
    {
        $route = $this->model->where('id', $request->document_id)->first();
        $route->update([
            "file_tag" => $request->file_tag
        ]);

        return $route;
    }
}
