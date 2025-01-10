<?php

namespace App\Models\Api;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function vehicleInfos(): HasMany
    {
        return $this->hasMany(VehicleInfo::class);
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
