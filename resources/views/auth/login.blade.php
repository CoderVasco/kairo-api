<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kairo IA</title>
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
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            color: #0f172a;
            font-size: 14px;
            margin-bottom: 5px;
        }
        input[type="email"],
        input[type="password"],
        input[type="checkbox"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 14px;
        }
        input[type="checkbox"] {
            width: auto;
            margin-right: 5px;
        }
        .form-group.checkbox {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
        }
        .error {
            color: #dc2626;
            font-size: 12px;
            margin-top: 5px;
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
        }
        a:hover {
            text-decoration: underline;
        }
        .text-center {
            text-align: center;
            margin-top: 15px;
        }
        @media (max-width: 480px) {
            .container {
                padding: 20px;
                max-width: 90%;
            }
            h1 {
                font-size: 20px;
            }
            input[type="email"],
            input[type="password"],
            button {
                font-size: 12px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login no Kairo IA</h1>

        @if (session('status'))
            <div class="success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group checkbox">
                <label>
                    <input type="checkbox" name="remember"> Lembrar-me
                </label>
                <a href="{{ route('password.request') }}">Esqueceu a senha?</a>
            </div>

            <button type="submit">Entrar</button>
        </form>

        <p class="text-center">
            Não tem uma conta? <a href="{{ route('register') }}">Registre-se</a>
        </p>
    </div>
</body>
</html>