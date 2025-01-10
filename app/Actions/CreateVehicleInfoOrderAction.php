<?php

namespace App\Actions;

use App\Models\Api\VehicleInfo;

final class CreateVehicleInfoOrderAction
{

    public function run($orderID, $infosVehicle)
    {
        if (!$orderID || !$infosVehicle) return;
        VehicleInfo::insert([
            'order_id' => $orderID,
            "price" => $infosVehicle['price'],
            "brand" =>  $infosVehicle['brand'],
            "model" =>  $infosVehicle['model'],
            "model_year" =>  $infosVehicle['modelYear'],
            "fuel" =>  $infosVehicle['fuel'],
            "code_fipe" => $infosVehicle['codeFipe'],
            "reference_date" => $infosVehicle['referenceMonth'],
        ]);
    }

}