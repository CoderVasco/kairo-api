📚 Documentação da API do Kairo IA
Bem-vindo à documentação da API do Kairo IA! Este guia explica como configurar e integrar o chatbot Kairo IA, um assistente virtual alimentado pela API da OpenAI, em seu site. A API processa mensagens do usuário e retorna respostas inteligentes, enquanto o widget oferece uma interface de chat moderna e responsiva.
URL Base: https://kairo.teconectapi.it.ao/api
Widget: Hospedado em https://kairo.teconectapi.it.ao/js/kairo-widget.js
Site de Integração: https://tecnideia.ao

🚀 Visão Geral
O Kairo IA é um chatbot desenvolvido em Laravel que utiliza o modelo gpt-3.5-turbo da OpenAI para responder perguntas de forma clara e amigável. O widget JavaScript, integrado via um único <script>, proporciona uma experiência de chat interativa com histórico persistente e design adaptável.
Características

Respostas Inteligentes: Geradas pela API da OpenAI com tom profissional e acolhedor.
Histórico de Conversa: Armazenado no localStorage (limite de 10 mensagens).
Interface Responsiva: Compatível com desktops e dispositivos móveis.
Segurança: Autenticação obrigatória via token e configurações de CORS.
Configuração Dinâmica: URLs e tokens obtidos via endpoint /api/config.


🛠️ Configuração da API
A API é construída em Laravel e hospedada em https://kairo.teconectapi.it.ao. Abaixo estão os passos para configurá-la.
Pré-requisitos

Laravel: Versão 8.x ou superior.
PHP: 7.4 ou superior.
Guzzle: Para chamadas HTTP (composer require guzzlehttp/guzzle).
Chave da OpenAI: Obtenha em https://platform.openai.com.

Configuração do Ambiente
Edite o arquivo .env do projeto Laravel:
APP_NAME="Kairo API"
APP_ENV=production
APP_KEY=base64:SUA_CHAVE_GERADA
APP_DEBUG=false
APP_URL=https://kairo.teconectapi.it.ao
OPENAI_API_KEY=sua-chave-openai-aqui
KAIRO_API_TOKEN=seu-token-secreto
KAIRO_API_ENDPOINT=https://kairo.teconectapi.it.ao/api/kairo
KAIRO_AVATAR_URL=https://kairo.teconectapi.it.ao/images/kairo.jpg


Gere a chave da aplicação (se necessário):php artisan key:generate



Configuração de CORS
Permita requisições de https://tecnideia.ao em config/cors.php:
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['https://tecnideia.ao'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];

Configuração do Servidor
Hospede a API com um servidor web (ex.: Nginx) e HTTPS.
Exemplo de Configuração para Nginx:
server {
    listen 443 ssl;
    server_name kairo.teconectapi.it.ao;
    root /caminho/para/kairo-api/public;

    ssl_certificate /caminho/para/certificado.crt;
    ssl_certificate_key /caminho/para/chave.key;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock; # Ajuste para sua versão do PHP
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~* \.(js|jpg|png|css)$ {
        expires 30d;
        access_log off;
    }

    location ~ /\.ht {
        deny all;
    }
}

Configurar HTTPS:

Use Let's Encrypt:sudo certbot --nginx -d kairo.teconectapi.it.ao




🌐 Endpoints da API
GET /config
Retorna as configurações públicas do chatbot, como URL da API, URL do avatar e token.
Parâmetros de Requisição

Método: GET
URL: https://kairo.teconectapi.it.ao/api/config
Cabeçalhos:
Accept: application/json



Resposta

Código de Status: 200 OK
Corpo da Resposta:
api_endpoint: URL do endpoint /kairo.
avatar_url: URL da imagem do avatar.
api_token: Token de autenticação.



Exemplo de Resposta:
{
    "api_endpoint": "https://kairo.teconectapi.it.ao/api/kairo",
    "avatar_url": "https://kairo.teconectapi.it.ao/images/kairo.jpg",
    "api_token": "seu-token-secreto"
}

Erros

500 Internal Server Error: Falha ao carregar configurações.{
    "message": "Erro interno do servidor"
}



POST /kairo
Processa uma mensagem do usuário e retorna uma resposta do Kairo IA.
Parâmetros de Requisição

Método: POST
URL: https://kairo.teconectapi.it.ao/api/kairo
Cabeçalhos:
Content-Type: application/json
Accept: application/json


Corpo da Requisição:
message (string, obrigatório): Mensagem do usuário.
history (array, opcional): Histórico da conversa (máximo de 10 mensagens).
api_token (string, obrigatório): Token de autenticação.



Exemplo de Requisição:
{
    "message": "Qual é a capital do Brasil?",
    "history": [
        {"role": "user", "content": "Oi, tudo bem?"},
        {"role": "assistant", "content": "Tudo ótimo por aqui! E contigo?"}
    ],
    "api_token": "seu-token-secreto"
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




🤖 Integração do Widget
O widget Kairo IA é um arquivo JavaScript hospedado em https://kairo.teconectapi.it.ao/js/kairo-widget.js. Ele se comunica com o endpoint /api/config para obter configurações dinâmicas.
Passos para Integração

Hospedar Arquivos:

Widget: Coloque kairo-widget.js em public/js/kairo-widget.js.
Imagem do Avatar: Hospede kairo.jpg em public/images/kairo.jpg.


Adicionar o Script:

No site https://tecnideia.ao, adicione o script no final do <body>:
<script src="https://kairo.teconectapi.it.ao/js/kairo-widget.js" async></script>




Funcionalidades do Widget:

Obtém configurações dinamicamente via /api/config.
Persiste o histórico no localStorage (limite de 10 mensagens).
Inclui animações, responsividade e indicador de digitação.
Usa fallback para configurações padrão em caso de falha no /api/config.



Exemplo de Integração em Laravel
Edite a view principal (ex.: resources/views/layouts/app.blade.php):
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
    <script src="https://kairo.teconectapi.it.ao/js/kairo-widget.js" async></script>
</body>
</html>


🧪 Testes
Testar a API

Endpoint /config:
curl -X GET https://kairo.teconectapi.it.ao/api/config \
-H "Accept: application/json"

Esperado:
{
    "api_endpoint": "https://kairo.teconectapi.it.ao/api/kairo",
    "avatar_url": "https://kairo.teconectapi.it.ao/images/kairo.jpg",
    "api_token": "seu-token-secreto"
}


Endpoint /kairo:
curl -X POST https://kairo.teconectapi.it.ao/api/kairo \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{"message":"Teste","history":[],"api_token":"seu-token-secreto"}'

Esperado: Resposta com mensagem do OpenAI.


Testar o Widget

Acesse https://tecnideia.ao.
Verifique o ícone do chatbot no canto inferior direito.
Envie uma mensagem e confirme a resposta.
Teste o histórico da conversa e a responsividade.

Debugging

Navegador: Use o console (F12) para erros de JavaScript ou CORS.
Laravel: Consulte storage/logs/laravel.log para erros da API.


🔒 Segurança

Chave da OpenAI: Armazenada no .env, nunca exposta no frontend.
Autenticação: O api_token é obrigatório no endpoint /kairo.
Rate Limiting: Adicione o middleware throttle:Route::post('/kairo', [KairoController::class, 'handleMessage'])->middleware('throttle:60,1');




⚡ Otimização

Minificação:
npm install -g uglify-js
uglifyjs public/js/kairo-widget.js -o public/js/kairo-widget.min.js

Atualize o script:
<script src="https://kairo.teconectapi.it.ao/js/kairo-widget.min.js" async></script>


Cache: Configure cache para arquivos estáticos no Nginx (expiração de 30 dias).



📋 Notas Finais

Imagem do Avatar: Certifique-se de que kairo.jpg está em public/images/kairo.jpg.
Manutenção: Monitore os logs do Laravel e a performance da API.
Suporte: Contate a equipe de desenvolvimento para dúvidas.
