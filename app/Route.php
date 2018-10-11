<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = ['receive_at','release_at','route_date', 'route_status', 'barcode','user_id','office_id','step','receive_by','remarks'];

    /**
     * Define relationships
     */
    public function document(){
        return $this->belongsTo('App\Document', 'barcode','document_code');
    }

    public function subDocument(){
        return $this->belongsTo('App\Document', 'barcode','document_id');
    }

    public function office(){
        return $this->belongsTo('App\Office','release_to','id');
    }

    public function receivedBy(){
        return $this->belongsTo('App\User', 'receive_by', 'id');
    }

    public function releasedBy(){
        return $this->belongsTo('App\User', 'released_by', 'id');
    }

    /**
     * Define scopes
     */
    public function scopeBarcode($query, $barcode){
        return $query->where('barcode', $barcode);
    }

    public function scopeNotNull($query){
        return $query->where('routes.receive_at','!=','IS NULL');
    }

    public function scopeIsNull($query){
        return $query->where('routes.receive_at','IS NULL');
    }

    public function scopeReceivedBy($query, $user){
        return $query->where('routes.receive_by', $user);
    }

    public function scopeReleasedBy($query, $user){
        return $query->where('routes.released_by', $user);
    }

    public function scopeSorted($query, $type){
        return $query->orderBy('routes.created_at', $type);
    }

}
