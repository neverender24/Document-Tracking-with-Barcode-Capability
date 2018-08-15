<?php

namespace App\Http\Controllers;

use App\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function index()
	{
		$index = Office::orderBy('created_at', 'desc')->paginate(10);

    	return $index;
	}
}
