<?php

     namespace App\Http\Controllers;

     use Illuminate\Http\Request;
     use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

     class KairoController extends Controller
     {
         public function handleMessage(Request $request)
         {
             $request->validate([
                 'message' => 'required|string',
                 'history' => 'array'
             ]);

             $message = $request->input('message');
             $history = $request->input('history', []);

             // Adicionar mensagem de sistema
             $messages = [
                 ['role' => 'system', 'content' => 'Você é Kairo IA, um assistente virtual útil e amigável. Responda em português, de forma clara, concisa e com um tom profissional, mas acolhedor.'],
                 ...$history,
                 ['role' => 'user', 'content' => $message]
             ];

             try {
                 $response = Http::withHeaders([
                     'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                     'Content-Type' => 'application/json'
                 ])->post('https://api.openai.com/v1/chat/completions', [
                     'model' => 'gpt-3.5-turbo',
                     'messages' => $messages,
                     'max_tokens' => 500,
                     'temperature' => 0.7
                 ]);

                 if ($response->failed()) {
                     return response()->json(['response' => 'Desculpe, algo deu errado. Tente novamente mais tarde.'], 500);
                 }

                 return response()->json(['response' => $response->json()['choices'][0]['message']['content']]);
             } catch (\Exception $e) {
                 Log::error('Erro na API OpenAI: ' . $e->getMessage());
                 return response()->json(['response' => 'Desculpe, algo deu errado. Tente novamente mais tarde.'], 500);
             }
         }
     }