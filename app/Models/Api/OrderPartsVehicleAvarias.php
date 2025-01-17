<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPartsVehicleAvarias extends Model
{
    public function typeParts(): BelongsTo
    {
        return $this->belongsTo(OrderPartsVehicle::class);
    }
}
