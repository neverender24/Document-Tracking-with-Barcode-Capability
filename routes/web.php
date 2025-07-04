<?php
use Illuminate\Support\Facades\DB;
Auth::routes();

Route::get('/', 'DocumentController@index');

Route::post('delete-route', 'RouteController@deleteRoute');

Route::resource('documents', "DocumentController");
Route::get('view-documents', "DocumentController@getDocuments");
Route::get('all-documents', "DocumentController@allDocuments");
Route::post('edit-documents', "DocumentController@edit");
Route::post('delete-document', "DocumentController@deleteDocument");
Route::get('returned-documents', "DocumentController@returnedMyDocuments");
Route::get('returned-all-documents', "DocumentController@returnedAllDocuments");

Route::resource('document-types', "DocumentTypeController");
Route::post('view-document-types', "DocumentTypeController@getDocumentTypes");
Route::post('get-step', "DocumentTypeController@getStep");

Route::resource('routes', "RouteController");
Route::post('view-routes', "RouteController@getRoutes");
Route::post('get-routes', "RouteController@populateRoutes");
Route::post('track/fast-track', "RouteController@fastTrack");

Route::get('apiv2/get-route', "RouteController@fastTrack");
Route::post('apiv2/add-document', "RouteController@apiStore");

Route::get('track/{id}', "RouteController@track");

Route::get('update-settings', function(){
    return view('layouts.verify');
});

Route::get('view-receive', "RouteController@getReceive");
Route::post('store-receive', "RouteController@storeReceive");

Route::post('release', "RouteController@release");

//printer
Route::get("pdf","DocumentController@open_pdf");

// reports
Route::get('released-documents', "RouteController@releasedDocuments");
Route::get('received-documents', "RouteController@receivedDocuments");
Route::get('unacted_documents', "RouteController@unacted_documents")->name('unacted_documents');

//user related
Route::get('users', "UserController@index");
Route::post('user', "UserController@setActiveStatus");
Route::post('user-reset', "UserController@resetPassword");


//file
Route::post('file_document', 'DocumentController@file_document')->name('file_document');

//approve PO
Route::post('approve_po', 'DocumentController@approve_po')->name('approve_po');

//calculate time
Route::get('calculate-time', "DocumentController@calculateTime");
Route::post('get-time-summary', "RouteController@getTimeSummary");

Route::post('get-subdocument', "DocumentController@getSubDocument");

// route edit document
// get all subdocuments in that particular document via barcode
Route::post('get-subdocuments', "DocumentController@getSubDocuments");

Route::post('get-offices', function(){
    return App\Office::orderBy('office_prefix','ASC')->get();
});

Route::post('get-user', function(){
    return auth()->user()->load('office');
});

Route::post('get-office', function(){
    return auth()->user()->office;
});

// change password
Route::post('update-password', 'UserController@changePassword');

// generate barcodes
Route::post('generate-barcodes', 'DocumentController@generateBarcodes');

// updates
Route::post('seen', "UpdateController@seen");
Route::get('seen-badge', "UpdateController@seenBadge");
Route::resource('updates', 'UpdateController');
Route::post('versions', "UpdateController@getVersion");

Route::post('get_work_summary', 'RouteController@get_work_summary')->name('get_work_summary');

Route::post('get_all_routes', 'RouteController@get_all_routes');

Route::post('getTransactionDetails', 'RouteController@getTransactionDetails')->name('getTransactionDetails');

Route::post('confirm-verification', 'UserController@confirm');

Route::get('test-view', function() {
    return DB::table('vw_doctype')->get();
});

Route::get('/api/received-not-acted', function() {

    $subQuery = DB::table('routes')
    ->select(DB::raw('MAX(id) as max_id'))
    ->groupBy('barcode');

    $received_not_acted = \App\Route::joinSub($subQuery, 'latest', function ($join) {
        $join->on('routes.id', '=', 'latest.max_id');
    })
    ->where('routes.office_id', 8)
    ->whereNull('routes.receive_at')
    ->count();

    $received_and_released = \App\Route::joinSub($subQuery, 'latest', function ($join) {
        $join->on('routes.id', '=', 'latest.max_id');
    })
    ->where('routes.office_id', 8)
    ->whereNotNull('routes.receive_at')
    ->whereNotNull('routes.release_at')
    ->count();

    return [
        'received_not_acted' => $received_not_acted,
        'received_and_released' => $received_and_released,
    ];
});

