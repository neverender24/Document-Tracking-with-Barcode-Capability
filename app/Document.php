<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['document_title', 'document_code', 'document_date', 'user_id', 'document_type_id', 'document_id', 'office_id'];
    //protected $primaryKey = 'document_code';

    public function routes()
    {
        return $this->hasMany('App\Route','barcode','document_code');
    }

    public function documentType()
    {
        return $this->belongsTo('App\DocumentType');
    }
}
