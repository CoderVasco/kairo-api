<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Token da API - Kairo IA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            color: #2563eb;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .success {
            background-color: #dcfce7;
            border: 1px solid #22c55e;
            color: #166534;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .token-box {
            background-color: #f8fafc;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 14px;
            word-break: break-all;
            margin-bottom: 20px;
            color: #0f172a;
        }
        .no-token {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 20px;
        }
        button {
            width: 100%;
            background-color: #2563eb;
            color: #ffffff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }
        button:hover {
            background-color: #1d4ed8;
        }
        a {
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
            display: block;
            text-align: center;
            margin-top: 15px;
        }
        a:hover {
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .container {
                padding: 20px;
                max-width: 90%;
            }
            h1 {
                font-size: 20px;
            }
            .token-box, .no-token, button, a {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gerenciar Token da API</h1>

        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <div>
            <label style="display: block; color: #0f172a; font-size: 14px; margin-bottom: 5px;">Seu Token da API</label>
            @if (Auth::user()->api_token)
                <div class="token-box">{{ Auth::user()->api_token }}</div>
            @else
                <p class="no-token">Nenhum token gerado.</p>
            @endif
        </div>

        <form action="{{ route('api-token.generate') }}" method="POST">
            @csrf
            <button type="submit">{{ Auth::user()->api_token ? 'Regenerar Token' : 'Gerar Token' }}</button>
        </form>

        <a href="{{ route('dashboard') }}">Voltar ao Dashboard</a>
    </div>
</body>
</html>