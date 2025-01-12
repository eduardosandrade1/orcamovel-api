<?php

namespace App\Actions;

use App\Models\Api\Order;

final class SavePdfOrderAction
{

    public function run(int $orderID, string $path)
    {
        if (!$orderID || !$path) return;
        $order = Order::find($orderID);
        $order->pdf_path = $path;

        $order->save();

        return $order;
    }

}