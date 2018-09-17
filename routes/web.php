<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();

Route::get('/', 'DocumentController@index');

Route::resource('documents', "DocumentController");
Route::get('view-documents', "DocumentController@getDocuments");
Route::get('all-documents', "DocumentController@allDocuments");
Route::post('edit-documents', "DocumentController@edit");

Route::resource('document-types', "DocumentTypeController");
Route::post('view-document-types', "DocumentTypeController@getDocumentTypes");
Route::post('get-step', "DocumentTypeController@getStep");

Route::resource('routes', "RouteController");
Route::post('view-routes', "RouteController@getRoutes");
Route::post('get-routes', "RouteController@populateRoutes");

Route::post('view-receive', "RouteController@getReceive");
Route::post('store-receive', "RouteController@storeReceive");

Route::post('release', "RouteController@release");

//printer
Route::post('print-form', 'DocumentController@printForm');
Route::get("pdf","DocumentController@open_pdf");

// reports
Route::get('released-documents', "DocumentController@releasedDocuments");
Route::get('received-documents', "DocumentController@receivedDocuments");
Route::get('unacted-documents', "DocumentController@unactedDocuments");

//calculate time
Route::get('calculate-time', "DocumentController@calculateTime");

Route::post('get-subdocument', "DocumentController@getSubDocument");

// route edit document
// get all subdocuments in that particular document via barcode
Route::post('get-subdocuments', "DocumentController@getSubDocuments");

Route::post('get-offices', function(){
    return App\Office::orderBy('office_name','ASC')->get();
});

Route::post('get-user', function(){
    return Auth::user()->load('office');
});

Route::post('get-office', function(){
    return Auth::user()->office;
});


Route::post('update-password', function(){

   $old     = request()->get('old_password');
   $new     = request()->get('new_password');
   $confirm = request()->get('confirm_password');

    if (!Hash::check($old, \Auth::user()->password))
    {
        return response()->json(array(
                    'code'      =>  400,
                    'message'   =>  "Password given incorrect"
                ), 400);
    }

    if($new !== $confirm)
    {
       return response()->json(array(
                    'code'      =>  401,
                    'message'   =>  "New password not matched"
                ), 401);
    }

    $user = App\User::findOrFail( \Auth::user()->id);
    $user->password = bcrypt($new);
    $user->save();

    return $user;
});


