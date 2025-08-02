<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ConfigController extends Controller
{
    public function getConfig(Request $request)
    {
        $apiToken = $request->query('api_token');
        if (!$apiToken || !User::where('api_token', $apiToken)->exists()) {
            return response()->json(['message' => 'Token inválido'], 401);
        }

        return response()->json([
            'api_endpoint' => env('KAIRO_API_ENDPOINT', 'http://127.0.0.1:8016/api/kairo'),
            'avatar_url' => env('KAIRO_AVATAR_URL', 'http://127.0.0.1:8016/images/kairo.jpg')
        ]);
    }
}