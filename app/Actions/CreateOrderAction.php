<?php

namespace App\Actions;

use App\Models\Api\Order;

final class CreateOrderAction
{

  public function run(int $clientId, int $userId, $data)
  {
    $formattedPriceParts = str_replace(',', '.', $data->priceParts);

    // Criação do pedido (order)
    $order = new Order();
    $order->client_id = $clientId;
    $order->vehicle_color = $data->colorVehicle;
    $order->plate = $data->plateName;
    $order->price_parts = $formattedPriceParts;
    $order->type_vehicle = $data->typeVehicle;

    // Assumindo que você tem um campo total_price calculado
    $order->total_price = $this->calculateTotalPrice($formattedPriceParts, $data->typeParts, $data->breakdowns);

    // Associa o pedido ao usuário logado
    $order->user_id = $userId;

    // Salva o pedido
    $order->save();

    return $order;
  }


  // Função para calcular o preço total (exemplo simples)
  private function calculateTotalPrice($priceParts, $typeParts, $breakdowns)
  {
    return count($typeParts) * $priceParts;
  }
}
