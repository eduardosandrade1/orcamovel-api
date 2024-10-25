<?php

namespace App\Http\Controllers;

use App\Actions\CreateClientAction;
use App\Actions\CreateOrderAction;
use App\Models\Api\Client;
use App\Models\Api\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function getAll()
    {
        try {
            $userId = auth()->user()->id;
            return Order::where('user_id', $userId)->get();
        } catch (Exception $e) {
            Log::error($e->getMessage() . " " . $e->getLine());
            return response()->json('error_generic', 400);
        }
    }

    public function create(Request $request)
    {
        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'bandVehicle.id' => 'required|integer',
            'breakDowns' => 'array',
            'clientName' => 'required|string|max:255',
            'colorVehicle' => 'required|string|max:255',
            'contactValue' => 'required|string|max:255',
            'infosVehicle' => 'array',
            'plateName' => 'required|string|max:10',
            'prevDate' => 'nullable|date',
            'priceParts' => 'required|string',
            'typeParts' => 'required|array',
            'typeService' => 'required|array',
            'typeVehicle' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        $clientId = (new CreateClientAction())->run($request);

        $order = (new CreateOrderAction())->run($clientId, auth()->user()->id, $request);

        return response()->json([
            'success' => true,
            'message' => 'Pedido criado com sucesso',
            'data' => $order
        ], 201);
    }

    // Exibe um pedido específico
    public function show($id)
    {
        // Tenta encontrar o pedido pelo ID
        $order = Order::find($id);

        // Verifica se o pedido foi encontrado
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pedido não encontrado'
            ], 404);
        }

        // Retorna os detalhes do pedido
        return response()->json([
            'success' => true,
            'data' => $order
        ], 200);
    }
}
