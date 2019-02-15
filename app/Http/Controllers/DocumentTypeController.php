<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DocumentType;

class DocumentTypeController extends Controller
{
    public function __construct()
	{
		$this->middleware("auth");
	}

    public function getDocumentTypes()
	{
		return DocumentType::orderBy('document_type', 'asc')->get();
	}

	public function store(Request $request)
	{	

		$create = DocumentType::create($request->all());

		return $create;
	}

	public function update(Request $request, $id)
    {
        $edit = DocumentType::findOrFail($id);
        $edit->update($request->all());

        return $edit;
	}
	
	public function getStep(Request $request)
	{
		return DocumentType::where('id',$request->document_type_id)->select('process')->get();
	}
}
