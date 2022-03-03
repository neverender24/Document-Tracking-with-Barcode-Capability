<?php

namespace App\Http\Controllers;

use App\Document;
use App\Route;
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

        $parent = $this->model->with(['document', 'subDocument', 'office', 'receivedBy', 'releasedBy'])
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
        $dateReceived = $request->date_received;

        $index = $this->model->with(['document', 'office', 'receivedBy', 'releasedBy'])
            ->notNull()
            ->receivedBy(auth()->user()->id)
            ->where(function ($query) use ($dateReceived, $searchValue) {

                if ($dateReceived || $searchValue) {
                    if ($dateReceived) {
                        $query->whereDate('receive_at', $dateReceived);
                    }

                    if ($searchValue) {
                        $query->whereHas('document', function ($query) use ($searchValue) {
                            $query->where('document_title', 'LIKE', '%' . $searchValue . '%');
                        });
                    }
                } else {
                    $query->whereRaw('Date(receive_at) = CURDATE()');
                }

            });

        $index = $index->orderBy('routes.id', 'desc')->paginate($length);

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
            ->whereHas('routes', function ($q) use ($dateFrom) {
                if ($dateFrom) {
                    $q->whereDate('receive_at', $dateFrom);
                } else {
                    $q->whereRaw('DATE(receive_at) = CURDATE()');
                }
            })
            ->where(function ($q) use ($searchValue) {
                return $q->where('file_tag', '<>', 1)
                    ->orWhereNull('file_tag');
            })
            ->where(function ($q) use ($searchValue) {
                return $q->orWhere('document_code', "LIKE", "%" . $searchValue . "%")
                    ->orWhere('document_title', "LIKE", "%" . $searchValue . "%");
            })

            ->where('user_id', auth()->user()->id)
            ->paginate($length);

        return ['data' => $data, 'draw' => $request->draw];
    }

    public function get_work_summary(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $data = [];

        if ($from != $to) {
            $received = $this->model
                ->where('receive_by', auth()->user()->id)
                ->whereDate('receive_at', '>=', $from)
                ->whereDate('receive_at', '<=', $to)
                ->count();

            $released = $this->model
                ->where('released_by', auth()->user()->id)
                ->whereDate('release_at', '>=', $from)
                ->whereDate('release_at', '<=', $to)
                ->count();

            $returned = $this->model
                ->where('released_by', auth()->user()->id)
                ->where('remarks', 'like', "%return%")
                ->whereDate('release_at', '>=', $from)
                ->whereDate('release_at', '<=', $to)
                ->count();
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

    public function getTransactionDetails(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $data = [];

        if ($from != $to) {
            $received = $this->model
                ->leftJoin('documents', 'routes.barcode', '=', 'documents.document_code')
                ->leftJoin('document_types', 'documents.document_type_id', '=', 'document_types.id')
                ->where('receive_by', auth()->user()->id)
                ->whereDate('receive_at', '>=', $from)
                ->whereDate('receive_at', '<=', $to)
                ->selectRaw('*, count(*) as tots')
                ->groupBy('document_type_id')
                ->get();

            $approved_po = $this->model
                ->leftJoin('documents', 'routes.barcode', '=', 'documents.document_code')
                ->leftJoin('document_types', 'documents.document_type_id', '=', 'document_types.id')
                ->where('approved_po', 1)
                ->where('receive_by', auth()->user()->id)
                ->whereDate('receive_at', '>=', $from)
                ->whereDate('receive_at', '<=', $to)
                ->count();

            $released = $this->model
                ->leftJoin('documents', 'routes.barcode', '=', 'documents.document_code')
                ->leftJoin('document_types', 'documents.document_type_id', '=', 'document_types.id')
                ->where('released_by', auth()->user()->id)
                ->whereDate('release_at', '>=', $from)
                ->whereDate('release_at', '<=', $to)
                ->selectRaw('*, count(*) as tots')
                ->groupBy('document_type_id')
                ->get();

            $returned = $this->model
                ->where('released_by', auth()->user()->id)
                ->where('remarks', 'like', "%return%")
                ->whereDate('release_at', '>=', $from)
                ->whereDate('release_at', '<=', $to)
                ->count();
        } else {
            $received = $this->model
                ->leftJoin('documents', 'routes.barcode', '=', 'documents.document_code')
                ->leftJoin('document_types', 'documents.document_type_id', '=', 'document_types.id')
                ->where('receive_by', auth()->user()->id)
                ->whereDate('receive_at', $from)
                ->selectRaw('*, count(*) as tots')
                ->groupBy('document_type_id')
                ->get();

            $approved_po = $this->model
                ->leftJoin('documents', 'routes.barcode', '=', 'documents.document_code')
                ->leftJoin('document_types', 'documents.document_type_id', '=', 'document_types.id')
                ->whereDate('receive_at', $from)
                ->where('approved_po', 1)
                ->where('receive_by', auth()->user()->id)
                ->count();

            $released = $this->model
                ->leftJoin('documents', 'routes.barcode', '=', 'documents.document_code')
                ->leftJoin('document_types', 'documents.document_type_id', '=', 'document_types.id')
                ->where('released_by', auth()->user()->id)
                ->whereDate('release_at', $from)
                ->selectRaw('*, count(*) as tots')
                ->groupBy('document_type_id')
                ->get();

            $returned = $this->model
                ->where('released_by', auth()->user()->id)
                ->where('remarks', 'like', "%return%")
                ->whereDate('release_at', $from)
                ->count();
        }

        foreach ($received as $key => $value) {
            if ($value['document_type_id'] == 30) {
                $value['approve'] = $approved_po;
            }
        }

        $data["received"] = $received;
        $data["released"] = $released;
        // $data["returned"] = $returned;

        return response()->json($data);

    }

    public function getTimeSummary(Request $request)
    {

        $from = $request->range['from'];
        $to = $request->range['to'];
        $documentType = $request->documentType;
        $scope = $request->scope;
        $barcode = $request->barcode;

        $data = $this->model
            ->leftJoin('documents', 'routes.barcode', '=', 'documents.document_code')
            ->leftJoin('document_types', 'documents.document_type_id', '=', 'document_types.id');

        if ($scope == 1) {
            $data = $data->where(function ($query) {
                $query->where('documents.office_id', auth()->user()->office_id);
            });
        } else {
            $data = $data->where(function ($query) {
                $query->where('routes.office_id', auth()->user()->office_id);
            });
        }

        // ->where('receive_by', auth()->user()->id)

        if ($documentType) {
            $data = $data->where('document_type_id', $documentType);
        }

        if ($barcode) {
            $data = $data->where('routes.barcode','like', "%".$barcode);
        }

        $data = $data->whereDate('receive_at', '>=', $from)
            ->whereDate('receive_at', '<=', $to)
            ->whereNotNull('receive_at')
            ->orderBy('barcode')
            ->orderBy('routes.id')
            ->limit(1000)
            ->get();

        $totalSeconds = 0;
        $perBarcode = [];
        $totalDays = 0;
        $totalDaysOnly = 0;

        $totalTravelDays = 0;
        $countSimilarRows = 0;

        foreach ($data as $key => $val) {
            $currentBarcode = $data[$key]['barcode'];
            
            $currentRow = Carbon::parse($data[$key]['receive_at']);
            $received = Carbon::parse($data[$key]['receive_at']);
            $released = Carbon::parse($data[$key]['release_at']);

            if (isset($data[$key + 1])) { //kung naa pay next record

                $nextBarcode = $data[$key + 1]['barcode'];

                if (isset($data[$key - 1])) {
                    $prevBarcode = $data[$key - 1]['barcode'];

                    if ($currentBarcode == $nextBarcode && $currentBarcode == $prevBarcode) {
                        $countSimilarRows++;
                    }
                }

                if ($currentBarcode == $nextBarcode) {

                    $totalTravelDays += $received->diffInSeconds($released);

                    $nextRow = Carbon::parse($data[$key + 1]['release_at']);

                    //witout weekends
                    $totalDays = $nextRow->diffInDaysFiltered(function (Carbon $date) {
                        return !$date->isWeekend();
                    }, $currentRow);

                    //withweekends
                    $totalDaysOnly += $nextRow->diffInDays($currentRow);

                    //seconds with weekends
                    $totalSeconds += $nextRow->diffInSeconds($currentRow);

                } else {
                    $totalTravelDays += $received->diffInSeconds($released);

                    $totalDays = $totalDays * 86400; //86400 total seconds in a day
                    $totalDaysOnly = $totalDaysOnly * 86400;

                    $diff = $totalSeconds - $totalDaysOnly; //get the decimals

                    if ($totalDays == 86400) {
                        $totalSeconds = $diff;
                    } else {
                        $totalSeconds = $totalDays + $diff;
                    }

                    $toText = $this->secondsToTime($totalSeconds);
                    if ($totalSeconds == 0 && $scope == 1) {
                        $toText = "<div class='badge badge-sm badge-warning'>Not yet received</div>";
                    }
                    $toText2 = $this->secondsToTime($totalTravelDays);

                    array_push($perBarcode, [

                        "barcode" => $currentBarcode,
                        "title" => $data[$key]['document_title'],
                        "officeTime" => $toText,
                        "travelTime" => $toText2,

                    ]);

                    $totalSeconds = 0;
                    $totalDays = 0;
                    $totalDaysOnly = 0;
                    $totalTravelDays = 0;
                }

            } else {
                $totalTravelDays += $received->diffInSeconds($released);

                $totalDays = $totalDays * 86400;
                $totalDaysOnly = $totalDaysOnly * 86400;

                $diff = $totalSeconds - $totalDaysOnly; //get the decimals

                if ($totalDays == 86400) {
                    $totalSeconds = $diff;
                } else {
                    $totalSeconds = $totalDays + $diff;
                }

                $toText = $this->secondsToTime($totalSeconds);
                if ($totalSeconds == 0 && $scope == 1) {
                    $toText = "<div class='badge badge-sm badge-warning'>Not yet received</div>";
                }

                $toText2 = $this->secondsToTime($totalTravelDays);

                array_push($perBarcode, [

                    "barcode" => $currentBarcode,
                    "title" => $data[$key]['document_title'],
                    "officeTime" => $toText,
                    "travelTime" => $toText2,

                ]);
            }

            $countSimilarRows = 0;

        }

        return $perBarcode;
    }

    public function secondsToTime($seconds)
    {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");

        return $dtF->diff($dtT)->format('%ad, %hh, %im, %ss');
    }

    public function get_all_routes()
    {
        return DB::select('SELECT documents.document_title, barcode,routes.receive_at, routes.release_at
            from routes
            LEFT JOIN documents on routes.barcode = documents.document_code
            WHERE routes.release_to = 8 and DATE(routes.receive_at) between "2019-01-18" AND "2021-03-18"
            group by barcode
            order by  barcode, routes.id');
    }

}
