<?php

     namespace App\Http\Controllers;

     use Illuminate\Http\Request;

     class ConfigController extends Controller
     {
        // Ambiente de Teste
         public function getConfig()
         {
             return response()->json([
                 'api_endpoint' => env('KAIRO_API_ENDPOINT', 'http://127.0.0.1:8016/api/kairo'),
                 'avatar_url' => env('KAIRO_AVATAR_URL', 'http://127.0.0.1:8016/images/kairo.jpg'),
                 'api_token' => env('KAIRO_API_TOKEN', '')
             ]);
         }
        //  Ambiente de Produção
        //  public function getConfig()
        //  {
        //      return response()->json([
        //          'api_endpoint' => env('KAIRO_API_ENDPOINT', 'https://kairo.teconectapi.it.ao/api/kairo'),
        //          'avatar_url' => env('KAIRO_AVATAR_URL', 'https://kairo.teconectapi.it.ao/images/kairo.jpg'),
        //          'api_token' => env('KAIRO_API_TOKEN', '')
        //      ]);
        //  }
     }