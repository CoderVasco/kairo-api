<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kairo IA</title>
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
        p {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
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
            margin-bottom: 10px;
        }
        button.logout {
            background-color: #dc2626;
        }
        button:hover {
            background-color: #1d4ed8;
        }
        button.logout:hover {
            background-color: #b91c1c;
        }
        a {
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
            display: block;
            text-align: center;
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
            p, button, a {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bem-vindo, {{ Auth::user()->name }}!</h1>
        <p>Gerencie seu token da API para integrar o Kairo IA ao seu site.</p>
        <a href="{{ route('api-token') }}">Gerenciar Token da API</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout">Sair</button>
        </form>
    </div>
</body>
</html>