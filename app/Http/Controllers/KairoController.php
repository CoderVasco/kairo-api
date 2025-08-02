<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KairoController extends Controller
{
    public function config(Request $request)
    {
        $token = $request->query('api_token');
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Token inválido'], 401);
        }

        return response()->json([
            'api_endpoint' => config('app.kairo_api_endpoint', 'http://127.0.0.1:8016/api/kairo'),
            'avatar_url' => config('app.kairo_avatar_url', 'http://127.0.0.1:8016/images/kairo.jpg'),
        ]);
    }

    public function handleMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'api_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $user = User::where('api_token', $request->api_token)->first();

        if (!$user) {
            return response()->json(['response' => 'Token inválido'], 401);
        }

        $messages = [
            ['role' => 'system', 'content' => 'Você é Kairo IA, um assistente amigável da Tecnideia. Responda em português com clareza e precisão.'],
        ];

        if (!empty($request->history)) {
            $messages = array_merge($messages, array_slice($request->history, -10));
        }

        $messages[] = ['role' => 'user', 'content' => $request->message];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
            ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'];
                return response()->json(['response' => $content]);
            }

            return response()->json(['response' => 'Desculpe, algo deu errado. Tente novamente mais tarde.'], 500);
        } catch (\Exception $e) {
            Log::error('Erro ao chamar a API da OpenAI: ' . $e->getMessage());
            return response()->json(['response' => 'Desculpe, algo deu errado. Tente novamente mais tarde.'], 500);
        }
    }
}