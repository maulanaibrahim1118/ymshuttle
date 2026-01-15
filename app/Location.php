<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['clean_name'];

    public function user()
    {
        return $this->hasOne('App\User', 'code', 'location_code');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by', 'username');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by', 'username');
    }
    
    public function getCleanNameAttribute()
    {
        if (preg_match('/^(yogya|yomart)\b/i', $this->name)) {
            $cleanName = preg_replace('/^(yogya|yomart)\s*/i', '', $this->name);

            return $this->site . ' - ' . $cleanName;
        }

        return $this->name;
    }
}