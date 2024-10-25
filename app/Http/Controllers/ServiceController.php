<?php

namespace App\Http\Controllers;

use App\Models\Api\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    // Função para listar todos os serviços (já implementada)
    public function getAll()
    {
        $companyId = auth()->user()->company_id;
        Log::alert($companyId);
        $services = Service::where('company_id', $companyId)->get();

        return response()->json($services);
    }

    // Função para mostrar um serviço específico
    public function show($id)
    {
        $companyId = auth()->user()->company_id;
        $service = Service::where('id', $id)->where('company_id', $companyId)->first();

        if (!$service) {
            return response()->json(['message' => 'Serviço não encontrado'], 404);
        }

        return response()->json($service);
    }

    // Função para criar um novo serviço
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        $service = new Service();
        $service->name = $request->name;
        $service->price = $request->price;
        $service->company_id = auth()->user()->company_id; // Associa à empresa autenticada
        $service->save();

        return response()->json([
            'success' => true,
            'message' => 'Serviço criado com sucesso',
            'data' => $service
        ], 201);
    }

    // Função para atualizar um serviço existente
    public function update(Request $request, $id)
    {
        $companyId = auth()->user()->company_id;
        $service = Service::where('id', $id)->where('company_id', $companyId)->first();

        if (!$service) {
            return response()->json(['message' => 'Serviço não encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        $service->name = $request->name;
        $service->price = $request->price;
        $service->save();

        return response()->json([
            'success' => true,
            'message' => 'Serviço atualizado com sucesso',
            'data' => $service
        ], 200);
    }

    // Função para deletar um serviço
    public function destroy($id)
    {
        $companyId = auth()->user()->company_id;
        $service = Service::where('id', $id)->where('company_id', $companyId)->first();

        if (!$service) {
            return response()->json(['message' => 'Serviço não encontrado'], 404);
        }

        $service->delete();

        return response()->json(['message' => 'Serviço deletado com sucesso'], 200);
    }
}
