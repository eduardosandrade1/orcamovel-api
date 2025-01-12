<?php

namespace App\Models\Api;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{

    protected $with = [
        'client',
        'vehicleInfos',
        'typeParts',
        'typeService',
    ];

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function vehicleInfos(): HasOne
    {
        return $this->HasOne(VehicleInfo::class);
    }

    public function typeParts(): HasMany 
    {
        return $this->hasMany(OrderPartsVehicle::class);
    }

    public function typeService(): HasMany
    {
        return $this->hasMany(OrderTypeService::class);
    }
}
