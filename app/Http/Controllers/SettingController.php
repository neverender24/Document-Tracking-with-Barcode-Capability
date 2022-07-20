<?php

namespace App\Http\Controllers;

use App\User;
use App\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(Setting $model)
    {
        $this->model = $model;
    }

    public function verifyUser()
    {
        return view('layouts.verify');
    }

    public function getEmployeeApi()
    {
        return redirect('/');
    }

    public function confirmVerification(Request $request)
    {
        return User::where('id', auth()->user()->id)
                ->update([
                    'name' => $request->fullname,
                    'cats' => $request->cats,
                    'is_employee' => 1
                ]);
    }

    public function editSettings()
    {
        return $this->model->where('user_id', auth()->user()->id)->first();
    }

    public function updateSettings(Request $request)
    {
        $user_id = auth()->user()->id;

        $data = $this->model->where('user_id', $user_id)->first();

        $this->model->updateOrCreate(['user_id' => $user_id],
        [
            'alter_name' => $request->alter_name,
            'user_id' => $user_id,
            'preferred_name' => $request->preferred_name,
        ]);

        // if ($data) {
        //     $data->update([
        //         'alter_name' => $request->alter_name
        //     ]);
        // }
    }
}
