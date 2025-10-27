<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Shipment extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['badge'];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function shipment_detail()
    {
        return $this->hasMany('App\Shipment_detail', 'no_shipment', 'no_shipment');
    }

    public function shipment_ledger()
    {
        return $this->hasMany('App\Shipment_ledger', 'no_shipment', 'no_shipment');
    }

    public function sender_location()
    {
        return $this->belongsTo('App\Location', 'sender', 'code');
    }

    public function receiver_location()
    {
        return $this->belongsTo('App\Location', 'destination', 'code');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by', 'username');
    }

    public function updater()
    {
        return $this->belongsTo('App\User', 'updated_by', 'username');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shipment) {
            if (empty($shipment->no_shipment)) {
                $shipment->no_shipment = self::generateNoShipment();
            }
        });
    }

    public function getBadgeAttribute()
    {
        if ($this->status == '1') {
            return [
                'class' => 'bg-info',
                'icon'  => 'fas fa-box',
                'label' => 'New'
            ];
        }

        if ($this->status == '2') {
            return [
                'class' => 'bg-secondary',
                'icon'  => 'fas fa-truck-loading',
                'label' => 'On Loading'
            ];
        }

        if ($this->status == '3') {
            return [
                'class' => 'bg-warning text-light',
                'icon'  => 'fas fa-shipping-fast',
                'label' => 'On Delivery'
            ];
        }

        if ($this->status == '4') {
            return [
                'class' => 'bg-primary',
                'icon'  => 'fas fa-people-carry',
                'label' => 'Delivered'
            ];
        }

        if ($this->status == '5') {
            return [
                'class' => 'bg-success',
                'icon'  => 'fas fa-box-open',
                'label' => 'Finished'
            ];
        }

        return [
            'class' => 'bg-danger',
            'icon'  => 'fas fa-times',
            'label' => 'Cancelled'
        ];
    }

    public static function generateNoShipment()
    {
        $prefix = 'BSTB';
        $datePart = now()->format('ymd'); // contoh: 251010
        $random = random_int(10000, 99999); // 5 digit acak

        return "{$prefix}{$datePart}{$random}";
    }
}