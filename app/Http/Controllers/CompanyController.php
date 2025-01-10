<?php

namespace App\Http\Controllers;

use App\Models\Api\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function register(Request $request)
    {
        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|unique:companies,email',
            'company_address' => 'nullable|string|max:255',
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Criando a empresa
            $company = Company::create([
                'name' => $request->company_name,
                'email' => $request->company_email,
                'address' => $request->company_address,
            ]);

            // Criando o usuário vinculado à empresa
            $user = User::create([
                'name' => $request->user_name,
                'email' => $request->user_email,
                'password' => Hash::make($request->password),
                'company_id' => $company->id,
            ]);

            return response()->json([
                'message' => 'Empresa e usuário cadastrados com sucesso!',
                'company' => $company,
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'Erro ao registrar a empresa e o usuário.'], 500);
        }
    }
}
