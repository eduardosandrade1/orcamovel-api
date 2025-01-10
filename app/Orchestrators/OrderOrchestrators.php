<?php

namespace App\Orchestrators;

use App\Actions\CreateAvariasOrderAction;
use App\Actions\CreateClientAction;
use App\Actions\CreateFilesOrderAction;
use App\Actions\CreateOrderAction;
use App\Actions\CreateTypePartsOrderAction;
use App\Actions\CreateTypeServiceAction;
use App\Actions\CreateVehicleInfoOrderAction;
use Illuminate\Support\Facades\Log;

class OrderOrchestrators
{
    public function handler($validateData, int $userID): void
    {

        $clientId = (new CreateClientAction())->run($validateData);

        $order = (new CreateOrderAction())->run($clientId, $userID, $validateData);

        (new CreateVehicleInfoOrderAction())->run($order->id, $validateData['infosVehicle']);

        (new CreateTypeServiceAction())->run($order->id, $validateData['typeService']);

        foreach ($validateData['typeParts'] as $parts) {

            $orderPartsVehicleID = (new CreateTypePartsOrderAction())->run($order->id, $parts)->id;

            if (!empty($parts['files'])) {
                (new CreateFilesOrderAction())->run($orderPartsVehicleID, $parts['files']);
            }
            if (!empty($parts['avarias'])) {
                (new CreateAvariasOrderAction())->run($orderPartsVehicleID, $parts['avarias']);
            }
        }

    }

}