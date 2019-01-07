<?php

namespace App\Http\Controllers;

use DB;
use App\Route;
use App\Document;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function __construct(
		Route $route
	)
	{
		$this->model = $route;
		$this->middleware("auth");
	}

    public function getRoutes(Request $request)
	{
		$barcode = $request->barcode;

		$parent = $this->model->with(['subDocument', 'office', 'receivedBy', 'releasedBy'])
		->whereHas('subDocument', function($query) use(&$barcode){
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
		$columns = ['barcode','receive_at', 'released_by',];

		$length = $request->length;
		$column = $request->column;
		$dir = $request->dir;
		$searchValue = $request->search;

		$index = $this->model->with(['document', 'office', 'receivedBy', 'releasedBy'])
		->notNull()
		->receivedBy(auth()->user()->id)
		->whereRaw('Date(receive_at) = CURDATE()')
		->orderBy('routes.id', 'desc');

		if ($searchValue) {
			$index->where(function($query) use($searchValue){
				$query->orWhere('document_code','LIKE','%'.$searchValue.'%');
			});
		}

		$index = $index->paginate($length);

    	return ['data'=>$index, 'draw'=> $request->draw];

	}

	public function storeReceive(Request $request)
	{
		$barcode = $request->barcode;

		return $this->model
		->where('barcode', $barcode)
		->whereNull('receive_at')
		->where('release_to','=', auth()->user()->office_id)
		->update([
			'receive_by' => auth()->user()->id,
			'receive_at' => now()->toDateTimeString(),
		]);
	}

	public function release(Request $request)
	{
		$errors = [];
		//loop through each routes defined in vue
		foreach($request->process as $id => $data)
		{
			$office = $data['office_id'];
			$remarks = $request->remarks;

			//loop through subdocuments define in vue
			foreach($request->subDocuments as $id => $sub)
			{
				$code = $sub["code"];

				if ( is_null($code) ) {
					continue;
				}

				$document = Document::where('document_code', $code)->get();

				if ($document->count() == 0) {
					array_push($errors,"Document ".$code." Not Found. Make Sure you received it. Error 1");
					continue;
				}
				
				$lastRecord = $this->model->where('barcode', $code)->orderBy('id', 'desc')->first();

				if ($lastRecord->release_to != auth()->user()->office_id) {
					array_push($errors, "Document ".$code." Not Found. Make Sure you received it. Error 2");
					continue;
				}

				// $document = $this->model->where(function($query) use(&$code, &$office){
				// 	$query->where('barcode', $code)
				// 		->where('release_to','=',auth()->user()->office_id);
				// 		//->where('released_by','=',auth()->user()->id);
				// })
				// ->get();

				// if ($document->count() == 0) {
				// 	continue;
				// }

				/*
				** Get duplicate route: if there is no route found with following parameters then add route
				** Param: barcode, receive_at, release_to
				*/
				$edit = $this->model->where(function($query) use(&$code, &$office){
					$query->where('barcode', $code)
						->whereNull('receive_at')
						->where('release_to','=',auth()->user()->office_id);
				})
				->first();
				// dd($edit);
				if(is_null($edit))
				{
					$create = new \App\Route;
					$create->release_at = now()->toDateTimeString();
					$create->barcode = $sub['code'];
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

		$delete = $this->model->whereNull('receive_at')->where('id',$request->id)->delete();

		return $delete;
		
	}

	public function releasedDocuments(Request $request)
	{

		$length = $request->length;
		$column = $request->column;
		$dir = $request->dir;
		$searchValue = $request->search;
		
		$data = $this->model->with(['document', 'office', 'receivedBy', 'releasedBy'])
			->orWhere('barcode',"LIKE","%".$searchValue."%")
			->releasedBy(auth()->user()->id)
			->paginate($length);
		
		return ['data'=>$data, 'draw'=> $request->draw];
	}

	public function receivedDocuments(Request $request)
	{
		$length = $request->length;
		$column = $request->column;
		$dir = $request->dir;
		$searchValue = $request->search;

		$data = $this->model->with(['document', 'office', 'receivedBy', 'releasedBy'])
			->notNull()
			->orWhere('barcode',"LIKE","%".$searchValue."%")
			->receivedBy(auth()->user()->id)
			->paginate($length);
		
		return ['data'=>$data, 'draw'=> $request->draw];
	}

	public function unactedDocuments()
	{
		return $this->model->leftjoin('documents','documents.document_code','=','routes.barcode')
		->leftjoin('offices','offices.id','=','release_to')
		->leftjoin('users as received_by','received_by.id','=','routes.receive_by')
		->leftjoin('users as released_by','released_by.id','=','routes.released_by')
		->whereNull('routes.release_at')
		->where('routes.released_by', auth()->user()->id)
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
}
