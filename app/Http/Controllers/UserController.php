<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(User $model) {
        $this->model = $model;
    }

    public function index(Request $request)
    {

        $length = $request->length;
        $searchValue = $request->search;

        $data = $this->model->with(['office'])
        ->where(function ($query) use ($searchValue) {
            $query->orWhere('email', 'LIKE', '%' . $searchValue . '%')
                ->orWhere('cats', 'LIKE', '%' . $searchValue . '%')
                ->orWhere('name', 'LIKE', '%' . $searchValue . '%');
        })
        ->paginate($length);


        return ['data' => $data, 'draw' => $request->draw];
    }

    public function setActiveStatus(Request $request)
    {
        $user = $this->model->findOrFail($request->id);
        $value = $user->is_active == null ? 1 : null;

        $user->update([
            'is_active' => $value
        ]);
    }

    public function resetPassword(Request $request)
    {
        $user = $this->model->findOrFail($request->id);
        $user->password = bcrypt('123456');
        $user->save();
    }
    
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
