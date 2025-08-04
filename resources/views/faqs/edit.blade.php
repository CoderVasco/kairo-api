<!DOCTYPE html>
<html>
<head>
    <title>Editar FAQ</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Editar FAQ</h1>
        <form action="{{ route('faqs.update', $faq) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="question">Pergunta</label>
                <input type="text" name="question" id="question" value="{{ $faq->question }}" required>
            </div>
            <div class="form-group">
                <label for="answer">Resposta</label>
                <textarea name="answer" id="answer" required>{{ $faq->answer }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('faqs.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>