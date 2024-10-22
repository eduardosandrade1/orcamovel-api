<?php

namespace App\Http\Controllers;

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
    // Cria um novo pedido
    public function store(Request $request)
    {
        // Validação dos dados recebidos
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'services' => 'required|array',  // Exige um array de serviços
            'services.*.service_id' => 'required|exists:services,id',  // Cada serviço deve existir na tabela 'services'
        ]);

        // Retorna erro caso a validação falhe
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cria um novo pedido (Order)
            $order = new Order();
            $order->client_id = $request->client_id;
            $order->vehicle_id = $request->vehicle_id;
            $order->save();  // Salva o pedido para gerar o ID

            // Adiciona os serviços ao pedido
            foreach ($request->services as $service) {
                $order->orderServices()->create([
                    'service_id' => $service['service_id'],
                ]);
            }

            // Retorna sucesso e os dados do pedido criado
            return response()->json([
                'success' => true,
                'message' => 'Pedido criado com sucesso',
                'data' => $order->load('orderServices.service')  // Carrega os serviços associados ao pedido
            ], 201);
        } catch (\Exception $e) {
            // Trata qualquer exceção durante a criação do pedido
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar o pedido',
                'error' => $e->getMessage()
            ], 500);
        }
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
