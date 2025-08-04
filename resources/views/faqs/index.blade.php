<!DOCTYPE html>
<html>
<head>
    <title>Gerenciar FAQs</title>
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
            background-color: #34d399;
            color: #fff;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .table th {
            background-color: #2563eb;
            color: #fff;
            font-weight: bold;
        }

        .table td {
            color: #0f172a;
        }

        .table tr:last-child td {
            border-bottom: none;
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

        .btn-warning {
            background-color: #f59e0b;
            color: #fff;
            border: none;
        }

        .btn-warning:hover {
            background-color: #d97706;
        }

        .btn-danger {
            background-color: #ef4444;
            color: #fff;
            border: none;
        }

        .btn-danger:hover {
            background-color: #b91c1c;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }

            .table th,
            .table td {
                padding: 10px;
                font-size: 14px;
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
        <h1>Minhas FAQs</h1>
        <a href="{{ route('faqs.create') }}" class="btn btn-primary">Adicionar FAQ</a>
        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert" style="background-color: #ef4444;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th>Pergunta</th>
                    <th>Resposta</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($faqs as $faq)
                    <tr>
                        <td>{{ $faq->question }}</td>
                        <td>{{ Str::limit($faq->answer, 50) }}</td>
                        <td>
                            <a href="{{ route('faqs.edit', $faq) }}" class="btn btn-warning">Editar</a>
                            <form action="{{ route('faqs.destroy', $faq) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Tem certeza?')">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>