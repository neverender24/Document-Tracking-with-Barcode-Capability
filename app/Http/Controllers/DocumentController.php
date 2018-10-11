<?php

namespace App\Http\Controllers;

use App\Document;
use App\Route;
use Illuminate\Http\Request;
use App\Http\Requests\DocumentRequest;
use JasperPHP\JasperPHP as JasperPHP;

class DocumentController extends Controller
{
	public function __construct()
	{
		$this->middleware("auth");
	}
	
	public function index()
	{
		return view('admin.index');
	}

	public function printForm(Request $request)
	{
        // $ext = "pdf";
        // $text = '"' .  $request->barcode . '"';
        // $output = public_path() . '/routing_slip_form';
        // $jasper = new JasperPHP;
        // // Compile a JRXML to Jasper
        // $jasper->compile(public_path() . '/routing_slip_form.jrxml')->execute();

        // // Process a Jasper file to PDF and RTF (you can use directly the .jrxml)
        // $jasper->process(
        //     public_path() . '/routing_slip_form.jasper',
        //     public_path() . '/routing_slip_form',
        //     array("pdf"),
        //     array("barcode" => $text),
        //     \Config::get('database.connections.hrmd') //DB connection array
        // )->execute();

	}

	public function open_pdf()
    {
        return view('admin.open_pdf');
    }

    public function getDocuments(Request $request)
	{
		$columns = ['document_code','document_code', 'document_title',];

		$length = $request->length;
		$column = $request->column;
		$dir = $request->dir;
		$searchValue = $request->search;

		$index = Document::with('routes')
			->leftjoin('document_types','documents.document_type_id', '=', 'document_types.id')
			->where('documents.user_id', auth()->user()->id)
			->select(
				'document_code', 
				'document_title', 
				'document_type_prefix', 
				'documents.created_at', 
				'documents.id', 
				'documents.document_type_id', 
				'documents.document_id'
			)
			->orderBy($columns[$column], $dir);

		if ($searchValue) {
			$index->where(function($query) use($searchValue){
				$query->orWhere('document_code','LIKE','%'.$searchValue.'%');
			});
		}

		$index = $index->paginate($length);

    	return ['data'=>$index, 'draw'=> $request->draw];
	}

	public function returnedMyDocuments(Request $request)
	{
		$columns = ['document_code','document_code', 'document_title',];

		$length = $request->length;
		$column = $request->column;
		$dir = $request->dir;
		$searchValue = $request->search;

		$index = Document::with('routes')
			->leftjoin('routes','routes.barcode','=','documents.document_code')
			->leftjoin('document_types','documents.document_type_id', '=', 'document_types.id')
			->where('documents.user_id', auth()->user()->id)
			->where('routes.remarks','LIKE', '%return%')
			->select(
				'document_code', 
				'document_title', 
				'document_type_prefix', 
				'documents.created_at', 
				'documents.id', 
				'documents.document_type_id', 
				'documents.document_id'
			)
			->orderBy('documents.created_at', 'desc');

    	if ($searchValue) {
			$index->where(function($query) use($searchValue){
				$query->orWhere('document_code','LIKE','%'.$searchValue.'%');
			});
		}

		$index = $index->paginate($length);

    	return ['data'=>$index, 'draw'=> $request->draw];
	}

	public function returnedAllDocuments(Request $request)
	{
		$columns = ['document_code','document_code', 'document_title',];

		$length = $request->length;
		$column = $request->column;
		$dir = $request->dir;
		$searchValue = $request->search;

		$index = Document::with('routes')
			->leftjoin('document_types','documents.document_type_id', '=', 'document_types.id')
			->leftjoin('routes','routes.barcode', '=', 'documents.document_code')
			->where(function($query){
				$query->orWhere('routes.release_to', auth()->user()->office_id);
				$query->orWhere('documents.office_id', auth()->user()->office_id);
			})
			->where('routes.remarks','LIKE', '%return%')
			->select('document_code', 'document_title', 'document_type_prefix', 'documents.created_at', 'documents.id', 'documents.document_type_id', 'documents.document_id')
			->orderBy('documents.created_at', 'desc')
			->distinct();

    	if ($searchValue) {
			$index->where(function($query) use($searchValue){
				$query->orWhere('document_code','LIKE','%'.$searchValue.'%');
			});
		}

		$index = $index->paginate($length);

    	return ['data'=>$index, 'draw'=> $request->draw];
	}

	public function allDocuments(Request $request)
	{
		$columns = ['document_code','document_code', 'document_title',];

		$length = $request->length;
		$column = $request->column;
		$dir = $request->dir;
		$searchValue = $request->search;

		$index = Document::with('routes')
			->leftjoin('document_types','documents.document_type_id', '=', 'document_types.id')
			->leftjoin('routes','routes.barcode', '=', 'documents.document_code')
			->where(function($query) use($searchValue){
				$query->orWhere('routes.release_to', auth()->user()->office_id);
				$query->orWhere('documents.office_id', auth()->user()->office_id);
			})
			->select('document_code', 'document_title', 'document_type_prefix', 'documents.created_at', 'documents.id', 'documents.document_type_id', 'documents.document_id')
			->orderBy($columns[$column], $dir)
			->distinct();

		if ($searchValue) {
			$index->where(function($query) use($searchValue){
				$query->orWhere('document_code','LIKE','%'.$searchValue.'%');
			});
		}

		$index = $index->paginate($length);

    	return ['data'=>$index, 'draw'=> $request->draw];
	}

	public function edit(Request $request)
	{
		return Document::where("id", $request->id)->first();
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
		$save = Document::create($doc[0]);
		
		foreach($doc[1] as $id => $data)
		{
			$code = $data['code'];
			$index = Document::where('document_code', $code)->update(['document_id' =>  $save->document_code ]);	
		}

		foreach($doc[2] as $id => $data)
		{
			$office = $data['office_id'];
			$create = new \App\Route;
			$create->release_at = now()->toDateTimeString();
			$create->released_by = auth()->user()->id;
			$create->barcode = $save->document_code;
			$create->office_id = auth()->user()->office_id;
			$create->release_to = $office;
			$create->save();
		}

		return $save;
	}

	public function update(Request $request, $id)
    {
		// definition
		// $doc[0] = document details
		// $doc[1] = subDocuments
		// $doc[2] = removed
		// $doc[3] = removeRoute
		// $doc[4] = process

		$document = $request->document;
		$subDocument = $request->subDocuments;
		$routes = $request->routes;

		//dd($request->all());

        $edit = Document::findOrFail($id);
		$edit->update($document);
		
		$subdocs =  Document::where('document_id', $document['document_code'])->get();
		$isInside = false;

		// add document
		foreach($subDocument as $id => $data){
			$code = $data['document_code'];
			$index = Document::where('document_code', $code)->update(['document_id' => $document['document_code'] ]);
		}

		// Route
		foreach($routes as $id => $data)
		{
			$office = $data['office_id'];
			/*
			** check if route exist: false add new route
			** condition: barcode, receive_at, release_to
			*/
			$excluded = Route::where('barcode', $document['document_code'])->whereNull('receive_at')->where('release_to', $office)->latest()->first();
			if(is_null($excluded)){
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
		//dd($request->all());
		$index = Document::where('document_code', $request->code)->first();

    	return $index;
	}

	public function getSubDocuments(Request $request)
	{

		$index = Document::whereNotNull('document_id')->where('document_id', $request->document_id)->get();

    	return $index;
	}

	public function releasedDocuments(Request $request)
	{

		$length = $request->length;
		$column = $request->column;
		$dir = $request->dir;
		$searchValue = $request->search;
		
		$data = Route::with(['document', 'office', 'receivedBy', 'releasedBy'])
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

		$data = Route::with(['document', 'office', 'receivedBy', 'releasedBy'])
			->notNull()
			->orWhere('barcode',"LIKE","%".$searchValue."%")
			->receivedBy(auth()->user()->id)
			->paginate($length);
		
		return ['data'=>$data, 'draw'=> $request->draw];
	}

	public function unactedDocuments()
	{
		return Route::leftjoin('documents','documents.document_code','=','routes.barcode')
		->leftjoin('offices','offices.id','=','release_to')
		->leftjoin('users as received_by','received_by.id','=','routes.receive_by')
		->leftjoin('users as released_by','released_by.id','=','routes.released_by')
		->whereNull('routes.release_at')
		->where('routes.released_by', \Auth::user()->id)
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

	public function deleteDocument(Request $request)
	{
		$delete = Document::where('document_code', $request->id)->delete();

		return $delete;
	}
}
