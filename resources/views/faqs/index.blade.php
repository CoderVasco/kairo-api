<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #34d399;
            color: #fff;
        }

        .alert-error {
            background-color: #ef4444;
            color: #fff;
        }

        .alert-loading {
            background-color: #f59e0b;
            color: #fff;
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
            border: none;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #1e40af;
        }

        .btn-warning {
            background-color: #f59e0b;
            color: #fff;
        }

        .btn-warning:hover {
            background-color: #d97706;
        }

        .btn-danger {
            background-color: #ef4444;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #b91c1c;
        }

        .btn-scrape {
            background-color: #10b981;
            color: #fff;
        }

        .btn-scrape:hover {
            background-color: #059669;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .pagination a {
            padding: 8px 16px;
            background-color: #2563eb;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
        }

        .pagination a:hover {
            background-color: #1e40af;
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
        <div style="margin-bottom: 20px;">
            <a href="{{ route('faqs.create') }}" class="btn btn-primary" aria-label="Adicionar nova FAQ">Adicionar FAQ</a>
            <button class="btn btn-scrape" onclick="runScrape()" aria-label="Executar scraping do site Tecnideia">Executar Scraping</button>
        </div>
        <div id="scrape-output" class="alert alert-loading" style="display: none;"></div>
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
        <div class="alert alert-error">
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
                    <th scope="col">Pergunta</th>
                    <th scope="col">Resposta</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($faqs as $faq)
                <tr>
                    <td>{{ e($faq->question) }}</td>
                    <td>{{ e(Str::limit($faq->answer, 50)) }}</td>
                    <td>
                        <a href="{{ route('faqs.edit', $faq) }}" class="btn btn-warning" aria-label="Editar FAQ {{ $faq->question }}">Editar</a>
                        <form action="{{ route('faqs.destroy', $faq) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta FAQ?')" aria-label="Excluir FAQ {{ $faq->question }}">Excluir</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3">Nenhuma FAQ encontrada.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

<script>
    async function runScrape() {
        const output = document.getElementById('scrape-output');
        output.style.display = 'block';
        output.className = 'alert alert-loading';
        output.innerText = 'Executando scraping...';

        try {
            const response = await fetch("/scrape-tecnideia", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Authorization': `Bearer {{ env('SCRAPE_API_TOKEN') }}`,  // Adicionando o header de autorização
                },
            });

            const data = await response.json();
            output.className = 'alert alert-success';
            output.innerText = data.message + '\n' + data.output;
        } catch (error) {
            output.className = 'alert alert-error';
            output.innerText = 'Erro ao executar scraping: ' + error.message;
        }
    }
</script>

</body>

</html>