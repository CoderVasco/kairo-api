<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApiTokenController extends Controller
{
    public function index()
    {
        return view('api-token');
    }

    public function generateToken(Request $request)
    {
        $user = Auth::user();
        $user->api_token = Str::random(80);
        $user->save();

        return redirect()->route('api-token')->with('success', 'Token gerado com sucesso!');
    }
}