<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = ['id'];

    public function shipment()
    {
        return $this->hasOne('App\Shipment');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by', 'username');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by', 'username');
    }
}