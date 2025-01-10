<?php

namespace App\Http\Controllers;

use App\Actions\CreateClientAction;
use App\Actions\CreateFilesOrderAction;
use App\Actions\CreateOrderAction;
use App\Actions\CreateTypePartsOrderAction;
use App\Actions\CreateTypeServiceAction;
use App\Models\Api\Client;
use App\Models\Api\Order;
use App\Orchestrators\OrderOrchestrators;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function getAll(Request $request)
    {
        try {
            $userId = auth()->user()->id;

            // Verifica se os parâmetros de mês e ano foram passados na requisição
            $month = $request->input('month'); // Exemplo: '10' para Outubro
            $year = $request->input('year');   // Exemplo: '2024' para o ano de 2024

            // Inicia a query com o filtro pelo usuário autenticado
            $query = Order::where('user_id', $userId);

            // Adiciona filtros de mês e ano se ambos estiverem definidos
            if ($month) {
                $query->whereMonth('created_at', $month);
            }
            if ($year) {
                $query->whereYear('created_at', $year);
            }
            
            // Executa a query e retorna os resultados
            return $query->orderBy('created_at', 'desc')->get();
        } catch (Exception $e) {
            Log::error($e->getMessage() . " " . $e->getLine());
            return response()->json('error_generic', 400);
        }
    }

    public function create(Request $request)
    {
        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'clientName' => 'required|string|max:255',
            'colorVehicle' => 'required|string|max:255',
            'contactValue' => 'required|string|max:255',
            'infosVehicle' => 'array',
            'plateName' => 'required|string|max:10',
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

        (new OrderOrchestrators())->handler($request, auth()->user()->id);


        return response()->json([
            'success' => true,
            'message' => 'Pedido criado com sucesso',
            'data' => [],
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

    public function changeStatus($id)
    {
        # seeing order status
        $orderStatus = Order::find($id);
        $statusOrderToChange = "input";

        switch ($orderStatus->status) {
            case "input":
                $statusOrderToChange = "output";
                break;
            default:
                $statusOrderToChange = "input";
                break;
        }
        
        $order = Order::where('id', $id)->update(['status' => $statusOrderToChange]);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'a error occurred while try it.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => Order::find($id),
        ], 200);
    }
}
