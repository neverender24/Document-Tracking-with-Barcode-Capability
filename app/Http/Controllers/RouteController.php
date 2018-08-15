<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Route;
use DB;

class RouteController extends Controller
{
    public function __construct()
	{
		$this->middleware("auth");
	}

    public function getRoutes(Request $request)
	{
		$barcode = $request->barcode;

		$parent = Route::with(['subDocument', 'office', 'receivedBy', 'releasedBy'])
		->whereHas('subDocument', function($query) use(&$barcode){
			$query->where('documents.document_code', $barcode)
				  ->orWhere('documents.document_id', $barcode);
		})
		->sorted('asc');

		
		$child = Route::with(['document', 'office', 'receivedBy', 'releasedBy'])
			->barcode($barcode)
			->sorted('asc')
			->union($parent)
			->get();
		
		return $child;
	}

	public function populateRoutes(Request $request)
	{
		return Route::leftjoin('documents','documents.document_code','=','routes.barcode')
		->leftjoin('offices','offices.id','=','release_to')
		->where('barcode', $request->document_id)
		->orderBy('routes.created_at', 'asc')
		->select('release_to as office_id', 'routes.id')
		->get();
	}

	public function getReceive(Request $request)
	{
		return Route::leftjoin('documents','documents.document_code','=','routes.barcode')
		->leftjoin('offices','offices.id','=','release_to')
		->leftjoin('users as received_by','received_by.id','=','routes.receive_by')
		->leftjoin('users as released_by','released_by.id','=','routes.released_by')
		->where('routes.receive_at','!=','IS NULL')
		->where('routes.receive_by', \Auth::user()->id)
		->whereRaw('Date(receive_at) = CURDATE()')
		->select(
			'office_prefix',
			'receive_at',
			'release_at',
			'barcode',
			'document_title',
			'released_by.name as released_by',
			'received_by.name as received_by'
		)
		->orderBy('routes.created_at', 'desc')
		->get();
	}

	public function storeReceive(Request $request)
	{
		$barcode = $request->barcode;
			
		$edit = Route::where('barcode', $barcode)->whereNull('receive_at')->where('release_to','=', \Auth::user()->office_id)->update([
			'receive_by' => \Auth::user()->id,
			'office_id' => \Auth::user()->office_id,
			'receive_at' => \Carbon\Carbon::now()->toDateTimeString(),
		]);

		return $edit;
	}

	public function release(Request $request)
	{
		$request['user_id'] = \Auth::user()->id;
		$request['office_id'] = \Auth::user()->office_id;
		$request['receive_at'] = \Carbon\Carbon::now()->toDateTimeString();

		//loop through each routes defined in vue
		foreach($request->process as $id => $data)
		{
			$office = $data['office_id'];
			$remarks = $request->remarks;

			//loop through subdocuments define in vue
			foreach($request->subDocuments as $id => $sub)
			{
				if( is_null($sub["code"]) ){
					continue;
				}

				/*
				** Get duplicate route: if there is no route found with following parameters then add route
				** Param: barcode, receive_at, release_to
				*/
				$edit = Route::where('barcode', $sub['code'])->whereNull('receive_at')->where('release_to','=',$office)->first();
				if(is_null($edit))
				{
					$create = new \App\Route;
					$create->release_at = \Carbon\Carbon::now()->toDateTimeString();
					$create->barcode = $sub['code'];
					$create->user_id = \Auth::user()->id;
					$create->released_by = \Auth::user()->id;
					$create->office_id = \Auth::user()->office_id;
					$create->release_to = $office;
					$create->remarks = $remarks;
					$create->save();
				}
			}
		}
		
		return 'success';
	}

	public function store(Request $request)
	{
		$request['user_id'] = \Auth::user()->id;
		$request['office_id'] = \Auth::user()->office_id;
		
		$create = Route::create($request->all());

		return $create;
	}

	public function update(Request $request, $id)
    {
        $edit = Route::findOrFail($id);
        $edit->update($request->all());

        return $edit;
    }
}
