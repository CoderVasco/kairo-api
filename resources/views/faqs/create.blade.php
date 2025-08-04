<!DOCTYPE html>
<html>
<head>
    <title>Adicionar FAQ</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #0f172a;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #2563eb;
        }

        .alert {
            padding: 15px;
            background-color: #ef4444;
            color: #fff;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #0f172a;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #fff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #1e40af;
        }

        .btn-secondary {
            background-color: #64748b;
            color: #fff;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #475569;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }

            .btn {
                padding: 8px 16px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Adicionar FAQ</h1>
        @if ($errors->any())
            <div class="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('faqs.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="question">Pergunta</label>
                <input type="text" name="question" id="question" required>
            </div>
            <div class="form-group">
                <label for="answer">Resposta</label>
                <textarea name="answer" id="answer" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('faqs.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>