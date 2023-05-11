<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function changePassword() {
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
     
         $user = User::findOrFail( auth()->user()->id);
         $user->password = bcrypt($new);
         $user->save();
     
         return $user;
    }

    public function confirm(Request $request)
    {
        $cats = $request->cats;
        $user = User::findOrFail(auth()->user()->id);
        $user->update(['cats' => $cats]);
    }
}
