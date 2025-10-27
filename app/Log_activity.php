<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log_activity extends Model
{
    protected $guarded = ['id'];

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by', 'username');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by', 'username');
    }
}