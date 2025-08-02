📚 Documentação da API do Kairo IA
Bem-vindo à documentação da API do Kairo IA! Este guia explica como configurar e integrar o chatbot Kairo IA, um assistente virtual alimentado pela API da OpenAI, em seu site. A API processa mensagens do usuário e retorna respostas inteligentes, enquanto o widget oferece uma interface de chat moderna e responsiva.
URL Base (Local): http://127.0.0.1:8016/api
Widget (Local): http://127.0.0.1:8016/js/kairo-widget.js
Site de Integração: http://127.0.0.1:8017 (futuramente https://tecnideia.ao)

🚀 Visão Geral
O Kairo IA é um chatbot desenvolvido em Laravel que utiliza o modelo gpt-3.5-turbo da OpenAI para responder perguntas de forma clara e amigável. O widget JavaScript, integrado via um único <script>, proporciona uma experiência de chat interativa com histórico persistente e design adaptável. As interfaces de autenticação e gerenciamento de tokens são simples, usando apenas HTML e CSS puro.
Características

Respostas Inteligentes: Geradas pela API da OpenAI com tom profissional e acolhedor.
Histórico de Conversa: Armazenado no localStorage (limite de 10 mensagens).
Interface Responsiva: Compatível com desktops e dispositivos móveis.
Segurança: Autenticação obrigatória via token associado a usuários registrados.
Configuração Dinâmica: URLs obtidas via endpoint /api/config.


🛠️ Configuração da API
A API é construída em Laravel e hospedada localmente em http://127.0.0.1:8016. Abaixo estão os passos para configurá-la.
Pré-requisitos

Laravel: Versão 8.x ou superior.
PHP: 7.4 ou superior.
Guzzle: Para chamadas HTTP (composer require guzzlehttp/guzzle).
Chave da OpenAI: Obtenha em https://platform.openai.com.
Laravel Breeze: Para autenticação (composer require laravel/breeze --dev).

Configuração do Ambiente
Edite o arquivo .env:
APP_NAME="Kairo API"
APP_ENV=local
APP_KEY=base64:SUA_CHAVE_GERADA
APP_DEBUG=true
APP_URL=http://127.0.0.1:8016
OPENAI_API_KEY=sua-chave-openai-aqui
KAIRO_API_ENDPOINT=http://127.0.0.1:8016/api/kairo
KAIRO_AVATAR_URL=http://127.0.0.1:8016/images/kairo.jpg
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kairo_api
DB_USERNAME=root
DB_PASSWORD=


Gere a chave da aplicação:
php artisan key:generate


Configure o banco de dados e execute as migrations:
php artisan migrate



Configuração de CORS
Permita requisições de http://127.0.0.1:8017 em config/cors.php:
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://127.0.0.1:8017'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];

Configuração do Servidor
Hospede a API localmente com:
php artisan serve --port=8016

Configuração das Views
Certifique-se de que as views estão no diretório correto:

resources/views/auth/login.blade.php
resources/views/auth/register.blade.php
resources/views/dashboard.blade.php
resources/views/api-token.blade.php

Nota: Se a view api-token.blade.php não for encontrada, verifique se ela está em resources/views/api-token.blade.php e não em um subdiretório como resources/views/auth/. Caso esteja em um subdiretório, atualize o ApiTokenController para usar view('auth.api-token').

🌐 Endpoints da API
GET /api/config?api_token={token}
Retorna as configurações públicas do chatbot para um token válido.
Parâmetros de Requisição

Método: GET
URL: http://127.0.0.1:8016/api/config?api_token={seu-token}
Cabeçalhos:
Accept: application/json



Resposta

Código de Status: 200 OK
Corpo da Resposta:
api_endpoint: URL do endpoint /kairo.
avatar_url: URL da imagem do avatar.



Exemplo de Resposta:
{
    "api_endpoint": "http://127.0.0.1:8016/api/kairo",
    "avatar_url": "http://127.0.0.1:8016/images/kairo.jpg"
}

Erros

401 Unauthorized: Token inválido.{
    "message": "Token inválido"
}



POST /api/kairo
Processa uma mensagem do usuário e retorna uma resposta do Kairo IA.
Parâmetros de Requisição

Método: POST
URL: http://127.0.0.1:8016/api/kairo
Cabeçalhos:
Content-Type: application/json
Accept: application/json


Corpo da Requisição:
message (string, obrigatório): Mensagem do usuário.
history (array, opcional): Histórico da conversa (máximo de 10 mensagens).
api_token (string, obrigatório): Token de autenticação do usuário.



Exemplo de Requisição:
{
    "message": "Qual é a capital do Brasil?",
    "history": [
        {"role": "user", "content": "Oi, tudo bem?"},
        {"role": "assistant", "content": "Tudo ótimo por aqui! E contigo?"}
    ],
    "api_token": "seu-token-unico"
}

Resposta

Código de Status: 200 OK
Corpo da Resposta:
response: Resposta gerada.



Exemplo de Resposta:
{
    "response": "A capital do Brasil é Brasília."
}

Erros

400 Bad Request: Parâmetros inválidos.{
    "message": "The message field is required."
}


401 Unauthorized: Token inválido.{
    "response": "Token inválido"
}


500 Internal Server Error: Falha na comunicação com a OpenAI.{
    "response": "Desculpe, algo deu errado. Tente novamente mais tarde."
}




🤝 Registro e Geração de Token
Para usar a API, os usuários devem se inscrever e gerar um token único.
Passos para Registro

Acesse http://127.0.0.1:8016/register.
Preencha o formulário com nome, e-mail e senha.
Após o registro, você será redirecionado para http://127.0.0.1:8016/api-token, onde o token da API será exibido.
Para usuários existentes, faça login em http://127.0.0.1:8016/login e acesse http://127.0.0.1:8016/api-token para visualizar ou regenerar o token.

Interface de Autenticação

Login: Interface simples com campos para e-mail e senha, opção "Lembrar-me" e link para recuperação de senha.
Registro: Formulário com campos para nome, e-mail, senha e confirmação de senha.
Estilo: Usa HTML e CSS puro, com cores consistentes (#2563eb para azul, #64748b para cinza) e design responsivo.

Painel de Tokens

URL: http://127.0.0.1:8016/api-token
Funcionalidades:
Exibir o token atual.
Gerar ou regenerar um novo token.


Nota: O token é gerado automaticamente no registro e armazenado no banco de dados. Verifique se api-token.blade.php está em resources/views/.


🤖 Integração do Widget
O widget Kairo IA é hospedado em http://127.0.0.1:8016/js/kairo-widget.js e requer um api_token para funcionar.
Passos para Integração

Hospedar Arquivos:

Widget: Coloque kairo-widget.js em public/js/kairo-widget.js.
Imagem do Avatar: Hospede kairo.jpg em public/images/kairo.jpg.


Adicionar o Script:

No site (ex.: http://127.0.0.1:8017), adicione o script com o atributo data-api-token:
<script src="http://127.0.0.1:8016/js/kairo-widget.js" data-api-token="seu-token-unico" async></script>




Funcionalidades do Widget:

Obtém configurações via /api/config?api_token={token}.
Persiste o histórico no localStorage (limite de 10 mensagens).
Inclui animações, responsividade e indicador de digitação.
Exibe mensagem de erro se o api_token não for fornecido.



Exemplo de Integração
Adicione ao HTML do site (ex.: index.html em http://127.0.0.1:8017):
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tecnideia</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1 { color: #2563eb; }
        p { color: #64748b; }
    </style>
</head>
<body>
    <h1>Bem-vindo à Tecnideia</h1>
    <p>Converse com o Kairo IA clicando no ícone no canto inferior direito!</p>
    <script src="http://127.0.0.1:8016/js/kairo-widget.js" data-api-token="seu-token-unico" async></script>
</body>
</html>


🧪 Testes
Testar Registro e Login

Registro:
Acesse http://127.0.0.1:8016/register.
Preencha o formulário e envie.
Verifique o redirecionamento para http://127.0.0.1:8016/api-token e a exibição do token.


Login:
Acesse http://127.0.0.1:8016/login.
Faça login e confirme o redirecionamento para http://127.0.0.1:8016/api-token.



Testar a API

Endpoint /api/config:
curl -X GET "http://127.0.0.1:8016/api/config?api_token=seu-token-unico"

Esperado:
{
    "api_endpoint": "http://127.0.0.1:8016/api/kairo",
    "avatar_url": "http://127.0.0.1:8016/images/kairo.jpg"
}


Endpoint /api/kairo:
curl -X POST http://127.0.0.1:8016/api/kairo \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{"message":"Teste","history":[],"api_token":"seu-token-unico"}'

Esperado: Resposta com mensagem do OpenAI.


Testar o Widget

Acesse http://127.0.0.1:8017.
Verifique o ícone do chatbot.
Envie uma mensagem e confirme a resposta.
Teste o histórico e a responsividade.

Debugging

Navegador: Use o console (F12) para erros de JavaScript ou CORS.
Laravel: Consulte storage/logs/laravel.log para erros da API.
Erro de View: Se a view api-token não for encontrada, verifique o diretório resources/views/ e limpe o cache com:php artisan view:clear
php artisan cache:clear




🔒 Segurança

Chave da OpenAI: Armazenada no .env, nunca exposta no frontend.
Autenticação: O api_token é obrigatório e validado contra a tabela users.
Rate Limiting: Aplicado no endpoint /kairo:Route::post('/kairo', [KairoController::class, 'handleMessage'])->middleware('throttle:60,1');




⚡ Otimização

Minificação do Widget:
npm install -g uglify-js
uglifyjs public/js/kairo-widget.js -o public/js/kairo-widget.min.js

Atualize o script:
<script src="http://127.0.0.1:8016/js/kairo-widget.min.js" data-api-token="seu-token-unico" async></script>


Cache: Configure cache para arquivos estáticos no servidor.



📋 Notas Finais

Imagem do Avatar: Certifique-se de que kairo.jpg está em public/images/kairo.jpg.
Manutenção: Monitore os logs do Laravel e a performance da API.
Produção: Atualize o .env para https://kairo.teconectapi.it.ao quando implantar.
