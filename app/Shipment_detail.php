<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shipment_detail extends Model
{
    protected $guarded = ['id'];

    public function shipment()
    {
        return $this->belongsTo('App\Shipment', 'no_shipment', 'no_shipment');
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