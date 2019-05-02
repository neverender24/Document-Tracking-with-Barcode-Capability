<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Update extends Model
{
    protected $fillable = ['updates'];

    public function users() {
        return $this->belongsToMany('App\User','update_user');
    }
}
