<?php

namespace App\Actions;

use App\Models\Api\OrderPartsVehicleAvarias;

final class CreateAvariasOrderAction
{

    public function run(int $orderPartsVehicleId, array|string $avariasData)
    {
        if (!$orderPartsVehicleId || !$avariasData) return;
        if (gettype($avariasData) === "string") $avariasData = json_decode($avariasData, true); 
        
        foreach ( $avariasData as $data ) {
            OrderPartsVehicleAvarias::insert([
                'order_parts_vehicle_id' => $orderPartsVehicleId,
                'name' => $data,
            ]);
        }
    }

}