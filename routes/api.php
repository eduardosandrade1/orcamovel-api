<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Rotas de autenticação
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::middleware('jwt-auth')->group(function () {

    // Rotas para pedidos (Order)
    Route::prefix('order')->group(function () {
        Route::get('/', [OrderController::class, 'getAll']);  // Listar todos os pedidos
        Route::post('/', [OrderController::class, 'create']); // Criar um novo pedido
        Route::get('/{id}', [OrderController::class, 'show']); // Exibir um pedido específico
    });

    // Rotas para usuários (User)
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index']);  // Listar todos os usuários
        // Route::get('/{id}', [UserController::class, 'show']); // Exibir um usuário específico
        Route::get('/allByCompany', [UserController::class, 'allByCompany']);
        Route::post('/', [UserController::class, 'store']);  // Criar um novo usuário
        Route::post('/{id}', [UserController::class, 'update']); // Atualizar um usuário existente
        Route::delete('/{id}', [UserController::class, 'destroy']); // Deletar um usuário
    });

    // Outras rotas de usuário
    Route::get('user/profile', [UserController::class, 'getUser']);  // Obter o perfil do usuário autenticado
    Route::post('logout', [UserController::class, 'logout']);        // Fazer logout
});
