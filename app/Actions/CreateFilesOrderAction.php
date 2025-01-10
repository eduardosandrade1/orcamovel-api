<?php

namespace App\Actions;

use App\Models\Api\OrderPartsVehicleImage;

final class CreateFilesOrderAction
{

    public function run($orderPartsVehicleId, array|null $files)
    {
        if (!$files || !$orderPartsVehicleId) return;

        foreach ($files as $file) {
            // move to path the files
            $path = $file->store('order_parts_vehicle_id/'. $orderPartsVehicleId);
            
            OrderPartsVehicleImage::insert([
                'order_parts_vehicle_id' => $orderPartsVehicleId,
                'path' => $path,
            ]);
        }
    }

}