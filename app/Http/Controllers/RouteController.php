<?php

namespace App\Http\Controllers;

use App\Route;
use App\Document;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    public function __construct(
        Route $route,
        Document $document
    ) {
        $this->model = $route;
        $this->document = $document;
        $this->middleware("auth", ["except" => ["getRoutes", "fastTrack", "track"]]);
    }

    public function getRoutes(Request $request)
    {
        $barcode = $request->barcode;

        $parent = $this->model->with(['document','subDocument', 'office', 'receivedBy', 'releasedBy'])
            ->whereHas('subDocument', function ($query) use ($barcode) {
                $query->where('document_code', $barcode);
            })
            // ->whereHas('document', function ($query) {
            //     $query->where('document_type_id',"<>", 39);
            // })
            ->sorted('asc'); 
        
        $child = $this->model->with(['document', 'office', 'receivedBy', 'releasedBy'])
            // ->whereHas('document', function ($query) {
            //     $query->where('document_type_id',"<>", 39);
            // })
            ->barcode($barcode)
            ->sorted('asc');

        return $child->union($parent)->get();

    }

    public function track($barcode)
    {
        return view('admin.fast_track', compact('barcode'));
    }

    public function fastTrack(Request $request)
    {

        $barcode = $request->barcode;

        $parent = $this->model->with(['subDocument', 'office', 'receivedBy', 'releasedBy'])
            ->whereHas('subDocument', function ($query) use (&$barcode) {
                $query->where('documents.document_code', $barcode)
                    ->orWhere('documents.document_id', $barcode);
            })
            ->sorted('asc');

        $child = $this->model->with(['document', 'office', 'receivedBy', 'releasedBy'])
            ->barcode($barcode)
            ->sorted('asc')
            ->union($parent)
            ->get();

        return $child;
    }

    public function populateRoutes(Request $request)
    {
        return $this->model->where('barcode', $request->barcode)
            ->orderBy('routes.created_at', 'asc')
            ->select('release_to as office_id', 'routes.id')
            ->get();
    }

    public function getReceive(Request $request)
    {
        $length = $request->length;
        $searchValue = $request->search;

        $index = $this->model->with(['document', 'office', 'receivedBy', 'releasedBy'])
            ->notNull()
            ->receivedBy(auth()->user()->id)
            ->whereRaw('Date(receive_at) = CURDATE()')
            ->orderBy('routes.id', 'desc');

        if ($searchValue) {
            $index->where(function ($query) use ($searchValue) {
                $query->orWhere('document_code', 'LIKE', '%' . $searchValue . '%');
            });
        }

        $index = $index->paginate($length);

        return ['data' => $index, 'draw' => $request->draw];

    }

    public function storeReceive(Request $request)
    {
        $barcode = $request->barcode;

        return $this->model
            ->where('barcode', $barcode)
            ->whereNull('receive_at')
            ->where('release_to', '=', auth()->user()->office_id)
            ->update([
                'receive_by' => auth()->user()->id,
                'receive_at' => now()->toDateTimeString(),
            ]);
    }

    public function release(Request $request)
    {
        $errors = [];
        //loop through each routes defined in vue
        foreach ($request->process as $id => $data) {
            $office = $data['office_id'];
            $remarks = $request->remarks;

            //loop through subdocuments define in vue
            foreach ($request->subDocuments as $id => $sub) {
                $code = $sub["code"];

                if (is_null($code)) {
                    continue;
                }

                // add error kung ang document kay returned unya wala remarks
                // dapat naa remarks is returned ang document.
                $return = $this->model->where('barcode', $code)->where('release_to', $office)->first();
                if ($return && ($remarks == "Good")) {
                    array_push($errors, " Document " . $code . " Returned documents should have remarks.");
                    continue;
                }

                $document = Document::where('document_code', $code)->get();

                // check if document exist
                if ($document->count() == 0) {
                    array_push($errors, " Document " . $code . " Not Found. Error 1");
                    continue;
                }

                //kuhaon ang last record para ma sure jud na latest record ang gina validate
                $lastRecord = $this->model->where('barcode', $code)->orderBy('id', 'desc')->first();

                //check if document is received
                if ($lastRecord->receive_by == null) {
                    array_push($errors, " Document " . $code . "Document not yet received. Error 4");
                    continue;
                }

                //check if ang ma releasean tama na office
                if ($lastRecord->release_to != auth()->user()->office_id) {
                    array_push($errors, " Document " . $code . " This document is not routed to your office. You are not allowed to release it. Error 2");
                    continue;
                }

                /*
                 ** Get duplicate route: if there is no route found with following parameters then add route
                 ** Param: barcode, receive_at, release_to
                 */
                $edit = $this->model->where(function ($query) use (&$code) {
                    $query->where('barcode', $code)
                        ->whereNull('receive_at')
                        ->where('release_to', '=', auth()->user()->office_id);
                })->first();

                if (is_null($edit)) {
                    $create = new \App\Route;
                    $create->release_at = now()->toDateTimeString();
                    $create->barcode = strtolower($sub['code']);
                    $create->released_by = auth()->user()->id;
                    $create->office_id = auth()->user()->office_id;
                    $create->release_to = $office;
                    $create->remarks = $remarks;
                    $create->save();
                }
            }
        }

        return $errors;
    }

    public function store(Request $request)
    {
        $request['released_by'] = auth()->user()->id;
        $request['office_id'] = auth()->user()->office_id;

        $create = $this->model->create($request->all());

        return $create;
    }

    public function update(Request $request, $id)
    {
        $edit = $this->model->findOrFail($id);
        $edit->update($request->all());

        return $edit;
    }

    public function deleteRoute(Request $request)
    {

        $delete = $this->model->whereNull('receive_at')->where('id', $request->id)->delete();

        return $delete;

    }

    public function releasedDocuments(Request $request)
    {

        $length = $request->length;
        $searchValue = $request->search;

        $data = $this->model->with(['document', 'office', 'receivedBy', 'releasedBy'])
            ->orWhere('barcode', "LIKE", "%" . $searchValue . "%")
            ->releasedBy(auth()->user()->id)
            ->paginate($length);

        return ['data' => $data, 'draw' => $request->draw];
    }

    public function receivedDocuments(Request $request)
    {
        $length = $request->length;
        $searchValue = $request->search;

        $data = $this->model->with(['document', 'office', 'receivedBy', 'releasedBy'])
            ->notNull()
            ->orWhere('barcode', "LIKE", "%" . $searchValue . "%")
            ->receivedBy(auth()->user()->id)
            ->paginate($length);

        return ['data' => $data, 'draw' => $request->draw];
    }

    public function unacted_documents(Request $request)
    {
        $length = $request->length;
        $searchValue = $request->search;
        $dateFrom = $request->date_from;

        $data = $this->document
            ->with(['documentType'])
            ->whereHas('routes', function($q) use($dateFrom){
                if ($dateFrom) {
                    $q->whereDate('receive_at', $dateFrom);
                } else {
                    $q->whereRaw('DATE(receive_at) = CURDATE()');  
                }
            })
            ->where(function($q) use($searchValue){
                return $q->where('file_tag','<>', 1)
                ->orWhereNull('file_tag');
            })
            ->where(function($q) use($searchValue){
                return $q->orWhere('document_code', "LIKE", "%" . $searchValue . "%")
                    ->orWhere('document_title', "LIKE", "%" . $searchValue . "%");
            })
            
            ->where('user_id', auth()->user()->id)
            ->paginate($length);

        return ['data' => $data, 'draw' => $request->draw];
    }

    public function get_work_summary(Request $request) {
        $from = $request->from;
        $to = $request->to;
        $data = [];

        if ($from != $to) {
            $received = $this->model->where('receive_by', auth()->user()->id)->whereDate('receive_at', '>=', $from)->whereDate('receive_at', '<=', $to)->count();
            $released = $this->model->where('released_by', auth()->user()->id)->whereDate('release_at', '>=', $from)->whereDate('release_at', '<=', $to)->count();
            $returned = $this->model->where('released_by', auth()->user()->id)->where('remarks','like', "%return%")->whereDate('release_at', '>=', $from)->whereDate('release_at', '<=', $to)->count();            
        } else {
            $received = $this->model->where('receive_by', auth()->user()->id)->whereDate('receive_at', $from)->count();
            $released = $this->model->where('released_by', auth()->user()->id)->whereDate('release_at', $from)->count();
            $returned = $this->model->where('released_by', auth()->user()->id)->where('remarks', 'like', "%return%")->whereDate('release_at', $from)->count();
        }

        $data["received"] = $received;
        $data["released"] = $released;
        $data["returned"] = $returned;

        return response()->json($data);

    }


}
