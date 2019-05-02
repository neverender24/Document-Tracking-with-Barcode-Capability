<?php

namespace App\Http\Controllers;

use App\Update;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function __construct(Update $model) {
        $this->model = $model;
    }

    public function seen() {
        $update = $this->model->orderBy('id','desc')->first();

        if ( !$update->users->contains(auth()->user()->id) ) {
            $update->users()->attach(auth()->user()->id);
        }
    }

    public function seenBadge() {
        $latest = $this->model->orderBy('id','desc')->first();

        return $this->model->whereHas('users', function($q){
            $q->where('users.id', auth()->user()->id);
        })->where('id', $latest->id)->orderBy('id','desc')->first();
    }

    public function store(Request $request) {
        $this->model->get()->each->delete();
        return $this->model->create($request->all());
    }

    public function index() {
        return $this->model->orderBy('id','desc')->limit(5)->get();
    }

    public function getVersion() {
        return $this->model->first();
    }
}
