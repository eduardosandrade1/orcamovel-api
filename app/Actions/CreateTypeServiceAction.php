<?php

namespace App\Actions;

use App\Models\Api\OrderTypeService;

final class CreateTypeServiceAction
{
    public function run(int $orderId, array $typeServices)
    {
        if (empty($typeServices) || !$orderId) return;
    
        foreach ( $typeServices as $service ) {
            OrderTypeService::insert([
                'order_id' => $orderId,
                'name' => $service,
            ]);
        }
    }
}