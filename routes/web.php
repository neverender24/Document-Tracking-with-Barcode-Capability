<?php

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

Route::get('view-receive', "RouteController@getReceive");
Route::post('store-receive', "RouteController@storeReceive");

Route::post('release', "RouteController@release");

//printer
Route::get("pdf","DocumentController@open_pdf");

// reports
Route::get('released-documents', "RouteController@releasedDocuments");
Route::get('received-documents', "RouteController@receivedDocuments");
Route::get('unacted-documents', "RouteController@unactedDocuments");

//calculate time
Route::get('calculate-time', "DocumentController@calculateTime");

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


Route::post('update-password', function(){

   $old     = request()->get('old_password');
   $new     = request()->get('new_password');
   $confirm = request()->get('confirm_password');

    if (!Hash::check($old, auth()->user()->password))
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

    $user = App\User::findOrFail( auth()->user()->id);
    $user->password = bcrypt($new);
    $user->save();

    return $user;
});

// updates
Route::post('seen', "UpdateController@seen");
Route::get('seen-badge', "UpdateController@seenBadge");
Route::resource('updates', 'UpdateController');
Route::post('versions', "UpdateController@getVersion");