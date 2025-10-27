<?php

namespace App;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $guarded = ['id'];

    public function location()
    {
        return $this->belongsTo('App\Location', 'location_code', 'code');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}