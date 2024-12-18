<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function sendResetCode(Request $request)
    {
        // Validação do e-mail
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;

        // Gerar um código de 6 dígitos
        $code = mt_rand(100000, 999999);

        // Definir tempo de expiração (15 minutos)
        $expiresAt = Carbon::now()->addMinutes(15);

        // Salvar o código no banco de dados (sobrescreve se já existir)
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'code' => $code,
                'expires_at' => $expiresAt,
                'updated_at' => Carbon::now(),
            ]
        );

        // Enviar o e-mail com o código
        Mail::raw("Seu código de recuperação é: $code", function ($message) use ($email) {
            $message->to($email)
                ->subject('Código de recuperação de senha');
        });

        return response()->json(['message' => 'Código enviado para o e-mail.'], 200);
    }

    public function validateResetCode(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|numeric',
        ]);
    
        $email = $request->email;
        $code = $request->code;
    
        // Buscar o código no banco
        $record = DB::table('password_resets')
            ->where('email', $email)
            ->where('code', $code)
            ->first();
    
        // Verificar se o código existe e não expirou
        if (!$record || Carbon::now()->greaterThan($record->expires_at)) {
            return response()->json(['error' => 'Código inválido ou expirado.'], 400);
        }
    
        return response()->json(['message' => 'Código válido.', 'success' => true], 200);
    }

    public function resetPassword(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|numeric',
            'password' => 'required|min:8',
        ]);

        $email = $request->email;
        $code = $request->code;

        // Buscar o código no banco
        $record = DB::table('password_resets')
            ->where('email', $email)
            ->where('code', $code)
            ->first();

        if (!$record || Carbon::now()->greaterThan($record->expires_at)) {
            return response()->json(['error' => 'Código inválido ou expirado.'], 400);
        }

        // Atualizar a senha do usuário
        $user = \App\Models\User::where('email', $email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        // Deletar o registro do código usado
        DB::table('password_resets')->where('email', $email)->delete();

        return response()->json(['message' => 'Senha redefinida com sucesso.'], 200);
    }

    
}
