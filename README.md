Documentação da API do Kairo IA
Introdução
A API do Kairo IA é um serviço backend desenvolvido em Laravel que permite a interação com um chatbot alimentado pela API da OpenAI. A API processa mensagens do usuário, mantém o histórico da conversa e retorna respostas geradas pelo modelo gpt-3.5-turbo. O widget do Kairo IA, implementado em JavaScript, se comunica com esta API para fornecer uma interface de chat interativa em sites.
URL Base: https://kairo.teconectapi.it.ao/api
Endpoints
POST /kairo
Processa uma mensagem do usuário e retorna uma resposta gerada pelo Kairo IA.
Parâmetros de Requisição

Método: POST
Cabeçalhos:
Content-Type: application/json
Accept: application/json


Corpo da Requisição (JSON):
message (string, obrigatório): A mensagem enviada pelo usuário.
history (array, opcional): O histórico da conversa, contendo objetos com role (user ou assistant) e content (texto da mensagem).
api_token (string, opcional): Token de autenticação para maior segurança.



Exemplo de Requisição
{
    "message": "Olá, como posso ajudar?",
    "history": [
        {"role": "user", "content": "Qual é a capital do Brasil?"},
        {"role": "assistant", "content": "A capital do Brasil é Brasília."}
    ],
    "api_token": "seu-token-secreto"
}

Resposta

Código de Status: 200 OK
Corpo da Resposta (JSON):
response: A resposta gerada pelo Kairo IA.



{
    "response": "Estou aqui para ajudar! Como posso responder à sua pergunta?"
}


Códigos de Erro:
400 Bad Request: Parâmetros inválidos (ex.: message ausente).{
    "message": "The message field is required."
}


401 Unauthorized: Token de API inválido (se configurado).{
    "response": "Token inválido"
}


500 Internal Server Error: Erro na comunicação com a OpenAI ou falha interna.{
    "response": "Desculpe, algo deu errado. Tente novamente mais tarde."
}





Autenticação
Para proteger a API, é recomendado usar um token de autenticação. Configure o token no arquivo .env do backend:
KAIRO_API_TOKEN=seu-token-secreto

Inclua o api_token no corpo da requisição, como mostrado no exemplo acima.
Configuração do Backend
A API é construída em Laravel e utiliza a biblioteca guzzlehttp/guzzle para se comunicar com a API da OpenAI. Abaixo estão os detalhes de configuração:

Dependências:

Laravel 8.x ou superior
guzzlehttp/guzzle para chamadas HTTP


Variáveis de Ambiente:

OPENAI_API_KEY: Chave da API da OpenAI (obtida em https://platform.openai.com).

KAIRO_API_TOKEN: Token para autenticação da API.

Exemplo de .env:
APP_NAME="Kairo API"
APP_ENV=production
APP_KEY=base64:SUA_CHAVE_GERADA
APP_DEBUG=false
APP_URL=https://kairo.teconectapi.it.ao
OPENAI_API_KEY=sua-chave-openai-aqui
KAIRO_API_TOKEN=seu-token-secreto




CORS:

Configurado em config/cors.php para permitir requisições de https://tecnideia.ao:
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




Rate Limiting (Opcional):

Para limitar requisições, adicione o middleware throttle à rota em routes/api.php:
Route::post('/kairo', [KairoController::class, 'handleMessage'])->middleware('throttle:60,1');





Integração do Widget
O widget Kairo IA é um arquivo JavaScript (kairo-widget.js) que fornece a interface do chatbot. Ele pode ser integrado em qualquer site, como https://tecnideia.ao.
Passos para Integração

Hospedar o Widget:

O arquivo kairo-widget.js está hospedado em https://kairo.teconectapi.it.ao/js/kairo-widget.js.
A imagem do avatar está em https://kairo.teconectapi.it.ao/images/kairo.jpg. Certifique-se de que a imagem existe no diretório public/images.


Adicionar o Script ao Site:

Inclua o script do widget no final do <body> da página HTML ou Blade:
<script src="https://kairo.teconectapi.it.ao/js/kairo-widget.js" async></script>




Configurar o Widget:

O widget se comunica com a API em https://kairo.teconectapi.it.ao/api/kairo.

Se a autenticação com api_token estiver habilitada, atualize o método callKairoApi em kairo-widget.js:
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
            api_token: 'seu-token-secreto' // Substitua pelo token configurado
        })
    });

    if (!response.ok) {
        throw new Error(`Erro HTTP: ${response.status}`);
    }

    const data = await response.json();
    return data.response;
}


Certifique-se de que o apiUrl e a URL da imagem no widget estão corretos:
this.apiUrl = 'https://kairo.teconectapi.it.ao/api/kairo';
// Imagem
<img src="https://kairo.teconectapi.it.ao/images/kairo.jpg" alt="Kairo IA Avatar" class="kairo-bot-avatar-image">




Estrutura do Widget:

O widget injeta automaticamente o HTML e CSS no DOM.
Mantém o histórico da conversa no localStorage do navegador, com um limite de 10 mensagens.
Inclui animações, responsividade para dispositivos móveis e um indicador de digitação.



Exemplo de Integração em Laravel
No projeto Laravel do site tecnideia.ao, edite a view principal (ex.: resources/views/layouts/app.blade.php):
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tecnideia</title>
</head>
<body>
    <h1>Bem-vindo à Tecnideia</h1>
    <p>Converse com o Kairo IA clicando no ícone no canto inferior direito!</p>
    <script src="https://kairo.teconectapi.it.ao/js/kairo-widget.js" async></script>
</body>
</html>

Configuração do Servidor

Hospedagem:

Implante o projeto Laravel da API em https://kairo.teconectapi.it.ao, apontando para a pasta public.

Exemplo de configuração para Nginx:
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




HTTPS:

Obtenha um certificado SSL (ex.: via Let's Encrypt):
sudo certbot --nginx -d kairo.teconectapi.it.ao




Cache:

Configure cache para arquivos estáticos (kairo-widget.js, kairo.jpg) para melhorar o desempenho, como mostrado na configuração Nginx acima.



Testes

Testar a API:

Use cURL ou Postman para enviar uma requisição POST para https://kairo.teconectapi.it.ao/api/kairo:
curl -X POST https://kairo.teconectapi.it.ao/api/kairo \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{"message":"Teste","history":[],"api_token":"seu-token-secreto"}'


Verifique se a resposta contém a mensagem gerada pelo OpenAI.



Testar o Widget:

Acesse https://tecnideia.ao no navegador.
Confirme que o ícone do chatbot aparece no canto inferior direito.
Clique no ícone, envie uma mensagem e verifique se a resposta é exibida.
Teste o histórico da conversa fechando e reabrindo o chatbot.
Teste a responsividade em diferentes tamanhos de tela.


Debugging:

Verifique o console do navegador (F12) para erros de JavaScript ou CORS.
Consulte os logs do Laravel em storage/logs/laravel.log para erros na comunicação com a OpenAI.



Notas Adicionais

Chave da OpenAI: Mantenha a chave segura no arquivo .env da API, nunca no frontend.

Imagem do Avatar: Certifique-se de que kairo.jpg esteja disponível em public/images/kairo.jpg.

Segurança:

Use o api_token para proteger a rota /kairo.
Considere adicionar rate limiting para evitar abusos.


Otimização:

Minifique o kairo-widget.js com UglifyJS:
npm install -g uglify-js
uglifyjs public/js/kairo-widget.js -o public/js/kairo-widget.min.js


Atualize o script para usar a versão minificada:
<script src="https://kairo.teconectapi.it.ao/js/kairo-widget.min.js" async></script>




Manutenção: Monitore os logs do Laravel e a performance da API para garantir estabilidade.

