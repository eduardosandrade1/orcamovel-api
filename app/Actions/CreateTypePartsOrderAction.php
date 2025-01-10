<?php

namespace App\Actions;

use App\Models\Api\OrderPartsVehicle;

final class CreateTypePartsOrderAction
{
    public function run(int $orderID, $typePartsData)
    {
        if (!$orderID || !$typePartsData) return;

        // create type parts pattern
        $orderParts = new OrderPartsVehicle();
        $orderParts->order_id = $orderID;
        $orderParts->label = $typePartsData['label'];
        $orderParts->value = $typePartsData['value'];
        $orderParts->complement = $typePartsData['complemento'];

        $orderParts->save();

        return $orderParts;

    }
}