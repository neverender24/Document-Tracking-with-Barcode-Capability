<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = ['document_type', 'document_type_prefix', 'process', 'process_count'];

    protected $casts = [
        'process' => 'object',
    ];

     public function documents()
    {
        return $this->hasMany('App\Document');
    }
}
