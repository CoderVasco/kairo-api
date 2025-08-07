<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ConfigController extends Controller
{
    public function getConfig(Request $request)
    {
        $apiToken = $request->query('api_token');
        $user = User::where('api_token', $apiToken)->first();

        if (!$user) {
            return response()->json(['message' => 'Token inválido'], 401);
        }

        return response()->json([
            'api_endpoint' => env('KAIRO_API_ENDPOINT', 'http://127.0.0.1:8016/api/kairo'),
            'avatar_url' => env('KAIRO_AVATAR_URL', 'http://127.0.0.1:8016/images/kairo.jpg'),
            'bot_name' => $user->bot_name ?? 'Kairo IA',
            'primary_color' => $user->primary_color ?? '#2563eb',
            'secondary_color' => $user->secondary_color ?? '#0ea5e9',
            'welcome_message' => $user->welcome_message ?? 'Bem-vindo ao Kairo IA! Como posso ajudar?'
        ]);
    }
    public function saveConfig(Request $request)
    {
        $user = Auth::user();
        $user->bot_name = $request->input('bot_name');
        $user->primary_color = $request->input('primary_color');
        $user->secondary_color = $request->input('secondary_color');
        $user->welcome_message = $request->input('welcome_message');
        $user->avatar_url = $request->input('avatar_url');
        $user->save();
        return redirect()->back()->with('success', 'Configurações salvas com sucesso!');
    }
}
