📚 Documentação da API do Kairo IA
Bem-vindo à documentação da API do Kairo IA! Este guia foi criado para ajudar você a configurar e integrar o chatbot Kairo IA, um assistente virtual alimentado pela API da OpenAI, em seu site. A API processa mensagens do usuário e retorna respostas inteligentes, enquanto o widget oferece uma interface de chat interativa e responsiva.
URL Base: https://kairo.teconectapi.it.ao/api
Widget: Hospedado em https://kairo.teconectapi.it.ao/js/kairo-widget.js
Site de Integração: https://tecnideia.ao

🚀 Visão Geral
O Kairo IA é um chatbot desenvolvido em Laravel que se comunica com a API da OpenAI (gpt-3.5-turbo) para responder às perguntas dos usuários de forma clara e amigável. O widget JavaScript pode ser facilmente integrado em qualquer site, como https://tecnideia.ao, para proporcionar uma experiência de chat fluida e moderna.
Características

Respostas Inteligentes: Respostas geradas pela API da OpenAI com tom profissional e acolhedor.
Histórico de Conversa: Armazenado no localStorage do navegador (limite de 10 mensagens).
Interface Responsiva: Design adaptável para desktops e dispositivos móveis.
Segurança: Suporte a autenticação por token e configurações de CORS.
Fácil Integração: Basta adicionar um <script> ao site.


🛠️ Configuração da API
A API é construída em Laravel e hospedada em https://kairo.teconectapi.it.ao. Abaixo estão os passos para configurá-la.
Pré-requisitos

Laravel: Versão 8.x ou superior.
PHP: 7.4 ou superior.
Guzzle: Para chamadas HTTP (composer require guzzlehttp/guzzle).
Chave da OpenAI: Obtenha em https://platform.openai.com.

Configuração do Ambiente
Edite o arquivo .env do projeto Laravel para incluir as seguintes variáveis:
APP_NAME="Kairo API"
APP_ENV=production
APP_KEY=base64:SUA_CHAVE_GERADA
APP_DEBUG=false
APP_URL=https://kairo.teconectapi.it.ao
OPENAI_API_KEY=sua-chave-openai-aqui
KAIRO_API_TOKEN=seu-token-secreto


Gere a chave da aplicação (se necessário):php artisan key:generate



Configuração de CORS
Para permitir requisições do site https://tecnideia.ao, edite config/cors.php:
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
Hospede a API em https://kairo.teconectapi.it.ao com um servidor web (ex.: Nginx) e HTTPS configurado.
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

Use o Let's Encrypt para obter um certificado SSL:sudo certbot --nginx -d kairo.teconectapi.it.ao




🌐 Endpoint da API
POST /kairo
Este endpoint recebe mensagens do usuário, processa-as com a API da OpenAI e retorna uma resposta.
Parâmetros de Requisição

Método: POST
URL: https://kairo.teconectapi.it.ao/api/kairo
Cabeçalhos:
Content-Type: application/json
Accept: application/json


Corpo da Requisição (JSON):
message (string, obrigatório): Mensagem enviada pelo usuário.
history (array, opcional): Histórico da conversa (máximo de 10 mensagens).
api_token (string, opcional): Token de autenticação.



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
response: Resposta gerada pelo Kairo IA.



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
O widget Kairo IA é um arquivo JavaScript que adiciona um chatbot interativo ao site. Ele é hospedado em https://kairo.teconectapi.it.ao/js/kairo-widget.js e pode ser integrado com uma única linha de código.
Passos para Integração

Hospedar Arquivos:

Widget: Coloque kairo-widget.js em public/js/kairo-widget.js.
Imagem do Avatar: Hospede kairo.jpg em public/images/kairo.jpg.


Adicionar o Script:

No site (ex.: https://tecnideia.ao), adicione o script no final do <body>:
<script src="https://kairo.teconectapi.it.ao/js/kairo-widget.js" async></script>




Configurar Autenticação (se aplicável):

Se a API exigir um api_token, atualize o método callKairoApi em kairo-widget.js:
async callKairoApi(message) {
    const limitedHistory = this.conversationHistory.slice(-this.maxHistoryLength);
    const response = await fetch(this.apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            message: message,
            history: limitedHistory,
            api_token: 'seu-token-secreto'
        })
    });

    if (!response.ok) {
        throw new Error(`Erro HTTP: ${response.status}`);
    }

    const data = await response.json();
    return data.response;
}




Verificar URLs:

Certifique-se de que o widget usa as URLs corretas:
this.apiUrl = 'https://kairo.teconectapi.it.ao/api/kairo';
<img src="https://kairo.teconectapi.it.ao/images/kairo.jpg" alt="Kairo IA Avatar" class="kairo-bot-avatar-image">





Exemplo de Integração em Laravel
No projeto Laravel do site tecnideia.ao, edite a view principal (ex.: resources/views/layouts/app.blade.php):
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

Use cURL ou Postman para enviar uma requisição POST:
curl -X POST https://kairo.teconectapi.it.ao/api/kairo \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{"message":"Teste","history":[],"api_token":"seu-token-secreto"}'


Esperado: Resposta com a mensagem gerada pelo OpenAI.


Testar o Widget

Acesse https://tecnideia.ao.
Verifique se o ícone do chatbot aparece no canto inferior direito.
Clique no ícone, envie uma mensagem e confirme a resposta.
Teste o histórico da conversa fechando e reabrindo o chatbot.
Teste a responsividade em dispositivos móveis.

Debugging

Navegador: Use o console (F12) para verificar erros de JavaScript ou CORS.
Laravel: Consulte storage/logs/laravel.log para erros na API.


🔒 Segurança

Chave da OpenAI: Armazenada no .env da API, nunca exposta no frontend.

Autenticação: Use o KAIRO_API_TOKEN para proteger a rota /kairo.

Rate Limiting: Adicione o middleware throttle para limitar requisições:
Route::post('/kairo', [KairoController::class, 'handleMessage'])->middleware('throttle:60,1');




⚡ Otimização

Minificação: Minifique o kairo-widget.js para melhorar o desempenho:
npm install -g uglify-js
uglifyjs public/js/kairo-widget.js -o public/js/kairo-widget.min.js

Atualize o script:
<script src="https://kairo.teconectapi.it.ao/js/kairo-widget.min.js" async></script>


Cache: Configure cache para arquivos estáticos no Nginx (expiração de 30 dias).



📋 Notas Finais

Imagem do Avatar: Certifique-se de que kairo.jpg está em public/images/kairo.jpg.
Manutenção: Monitore os logs do Laravel e a performance da API.
Suporte: Para dúvidas ou ajustes, entre em contato com a equipe de desenvolvimento.
