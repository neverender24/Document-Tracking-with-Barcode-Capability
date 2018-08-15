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

    public function getDocuments()
	{

		$index = Document::with('routes')
			->leftjoin('document_types','documents.document_type_id', '=', 'document_types.id')
			->where('documents.user_id', \Auth::user()->id)
			->select(
				'document_code', 
				'document_title', 
				'document_type_prefix', 
				'documents.created_at', 
				'documents.id', 
				'documents.document_type_id', 
				'documents.document_id'
			)
			->orderBy('documents.created_at', 'desc')->get();
    	return $index;
	}

	public function allDocuments()
	{
		$index = Document::with('routes')
			->leftjoin('document_types','documents.document_type_id', '=', 'document_types.id')
			->leftjoin('routes','routes.barcode', '=', 'documents.document_code')
		//	->where('user_id', \Auth::user()->id)
			->where(function($query){
				$query->where('routes.office_id', \Auth::user()->office_id);
				$query->orWhere('routes.release_to', \Auth::user()->office_id);
				$query->orWhere('documents.office_id', \Auth::user()->office_id);
			})
			->select('document_code', 'document_title', 'document_type_prefix', 'documents.created_at', 'documents.id', 'documents.document_type_id', 'documents.document_id')
			->orderBy('documents.created_at', 'desc')
			->distinct()
			->get();

    	return $index;
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
		$doc[0]['user_id'] = \Auth::user()->id;
		$doc[0]['office_id'] = \Auth::user()->office_id;

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
			$create->release_at = \Carbon\Carbon::now()->toDateTimeString();
			$create->released_by = \Auth::user()->id;
			$create->barcode = $save->document_code;
			$create->user_id = \Auth::user()->id;
			$create->office_id = \Auth::user()->office_id;
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

		$doc = $request->all();
        $edit = Document::findOrFail($id);
		$edit->update($doc[0]);
		
		$subdocs =  Document::where('document_id', $doc[0]['document_code'])->get();
		$isInside = false;

		// add document
		foreach($doc[1] as $id => $data){
			$code = $data['document_code'];
			$index = Document::where('document_code', $code)->update(['document_id' => $doc[0]['document_code'] ]);
		}

		//remove document
		if(!empty($request[2])){
			foreach($request[2] as $id => $data){
				$code = $data['document_code'];
				$index = Document::where('document_code', $code)->update(['document_id' =>  NULL ]);
			}
		}

		//remove route
		if(!empty($request[3])){
			foreach($request[3] as $id => $data){
				$code = $data['id'];
				$index = Route::where('id', $code)->delete();
			}
		}

		// Route
		foreach($request[4] as $id => $data)
		{
			$office = $data['office_id'];

			/*
			** check if route exist: false add new route
			** condition: barcode, receive_at, release_to
			*/
			$excluded = Route::where('barcode', $doc[0]['document_code'])->whereNull('receive_at')->where('release_to', $office)->first();
			if(is_null($excluded)){
				$create = new \App\Route;
				$create->release_at = \Carbon\Carbon::now()->toDateTimeString();
				$create->barcode = $doc[0]['document_code'];
				$create->user_id = \Auth::user()->id;
				$create->office_id = \Auth::user()->office_id;
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

	public function releasedDocuments()
	{
		return Route::with(['document', 'office', 'receivedBy', 'releasedBy'])
			//->notNull()
			->releasedBy(auth()->user()->id)
			->sorted('desc')
			->get();
	}

	public function receivedDocuments()
	{
		return Route::with(['document', 'office', 'receivedBy', 'releasedBy'])
			->notNull()
			->receivedBy(auth()->user()->id)
			->sorted('desc')
			->get();
	}

	public function unactedDocuments()
	{
		return Route::leftjoin('documents','documents.document_code','=','routes.barcode')
		->leftjoin('offices','offices.id','=','release_to')
		->leftjoin('users as received_by','received_by.id','=','routes.receive_by')
		->leftjoin('users as released_by','released_by.id','=','routes.released_by')
		->whereNull('routes.release_at')
		->where('routes.user_id', \Auth::user()->id)
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
