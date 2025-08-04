<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kairo IA - Documentação</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #2563eb;
            --secondary: #0ea5e9;
            --accent: #06b6d4;
            --dark: #1e293b;
            --light: #f8fafc;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --border: #e2e8f0;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --gradient: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            background: var(--bg-secondary);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header {
            background: var(--gradient);
            color: white;
            padding: 60px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .header h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .header h1 i {
            margin-right: 10px;
        }

        .header p {
            font-size: 1.25rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Navigation */
        .nav {
            background: var(--bg-primary);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow);
        }

        .nav-content {
            display: flex;
            justify-content: center;
            padding: 0;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 0;
            flex-wrap: wrap;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 20px 30px;
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            background: rgba(37, 99, 235, 0.05);
        }

        /* Main Content */
        .main {
            padding: 60px 0;
        }

        .section {
            margin-bottom: 80px;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 30px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .section-subtitle {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 40px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary);
            display: inline-block;
        }

        /* Cards */
        .card {
            background: var(--bg-primary);
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 30px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .card h3, .card h4 {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        /* Code Blocks */
        .code-block {
            background: var(--dark);
            color: #e2e8f0;
            padding: 25px;
            border-radius: 12px;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 14px;
            line-height: 1.5;
            overflow-x: auto;
            position: relative;
            border: 1px solid #334155;
        }

        .code-block::before {
            content: attr(data-lang);
            position: absolute;
            top: 10px;
            right: 15px;
            background: var(--primary);
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
        }

        .code-block pre {
            margin: 0;
            white-space: pre-wrap;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-primary {
            background: var(--gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
        }

        /* Alerts */
        .alert {
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-info {
            background: rgba(6, 182, 212, 0.1);
            border-color: var(--accent);
            color: var(--text-primary);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-color: var(--warning);
            color: var(--text-primary);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: var(--success);
            color: var(--text-primary);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-color: var(--error);
            color: var(--text-primary);
        }

        /* Tables */
        .table-container {
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: 12px;
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-primary);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        th {
            background: var(--bg-secondary);
            font-weight: 600;
            color: var(--text-primary);
        }

        tr:hover {
            background: rgba(37, 99, 235, 0.05);
        }

        /* Test Section */
        .test-section {
            background: var(--bg-primary);
            padding: 30px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .test-section input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
        }

        .test-section input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
            outline: none;
        }

        .spinner {
            display: none;
            border: 4px solid var(--border);
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
            margin: 10px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Footer */
        .footer {
            background: var(--dark);
            color: var(--light);
            text-align: center;
            padding: 40px 0;
            margin-top: 80px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5rem;
            }

            .nav-links a {
                padding: 15px 20px;
                font-size: 14px;
            }

            .section-title {
                font-size: 2rem;
            }

            .card-grid {
                grid-template-columns: 1fr;
            }

            .code-block {
                font-size: 12px;
                padding: 20px;
            }

            .test-section input {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 2rem;
            }

            .nav-links a {
                padding: 12px 15px;
                font-size: 12px;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .card {
                padding: 20px;
            }

            .btn {
                padding: 10px 20px;
                font-size: 12px;
            }
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Syntax highlighting */
        .keyword { color: #ff79c6; }
        .string { color: #f1fa8c; }
        .comment { color: #6272a4; font-style: italic; }
        .function { color: #50fa7b; }
        .number { color: #bd93f9; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <h1><i class="fas fa-robot"></i> Kairo IA</h1>
                <p>Chatbot inteligente e fácil de integrar. Transforme a experiência dos seus usuários com IA conversacional avançada.</p>
            </div>
        </div>
    </header>

    <nav class="nav">
        <div class="container">
            <div class="nav-content">
                <ul class="nav-links">
                    <li><a href="#overview" class="active"><i class="fas fa-eye"></i> Visão Geral</a></li>
                    <li><a href="#installation"><i class="fas fa-download"></i> Instalação</a></li>
                    <li><a href="#configuration"><i class="fas fa-cog"></i> Configuração</a></li>
                    <li><a href="#api"><i class="fas fa-code"></i> API</a></li>
                    <li><a href="#widget"><i class="fas fa-comment-alt"></i> Widget</a></li>
                    <li><a href="#test"><i class="fas fa-vial"></i> Testes</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="main">
        <div class="container">
            <!-- Overview Section -->
            <section id="overview" class="section">
                <h2 class="section-title"><i class="fas fa-eye"></i> Visão Geral</h2>
                
                <div class="card-grid">
                    <div class="card">
                        <h3><i class="fas fa-bolt"></i> Rápido e Leve</h3>
                        <p>Widget otimizado que carrega em segundos sem impactar a performance do seu site.</p>
                    </div>
                    <div class="card">
                        <h3><i class="fas fa-paint-brush"></i> Totalmente Customizável</h3>
                        <p>Interface moderna e responsiva que se adapta ao design do seu site.</p>
                    </div>
                    <div class="card">
                        <h3><i class="fas fa-brain"></i> IA Avançada</h3>
                        <p>Powered by OpenAI GPT-3.5 Turbo para conversas naturais e inteligentes.</p>
                    </div>
                    <div class="card">
                        <h3><i class="fas fa-mobile-alt"></i> Mobile First</h3>
                        <p>Design responsivo que funciona perfeitamente em todos os dispositivos.</p>
                    </div>
                    <div class="card">
                        <h3><i class="fas fa-shield-alt"></i> Seguro</h3>
                        <p>Autenticação via API token e comunicação criptografada.</p>
                    </div>
                    <div class="card">
                        <h3><i class="fas fa-memory"></i> Memória de Contexto</h3>
                        <p>Mantém o histórico da conversa para interações mais naturais.</p>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <span><strong>Dica:</strong> O Kairo IA funciona como um widget independente que você pode adicionar a qualquer site com apenas uma linha de código!</span>
                </div>
            </section>

            <!-- Installation Section -->
            <section id="installation" class="section">
                <h2 class="section-title"><i class="fas fa-download"></i> Instalação</h2>

                <h3 class="section-subtitle">Passo 1: Obtenha seu API Token</h3>
                <p>Para usar o Kairo IA, você precisa de um token de API. Siga estes passos:</p>
                <ol>
                    <li>Acesse <a href="http://127.0.0.1:8016/register" target="_blank" class="btn btn-primary"><i class="fas fa-user-plus"></i> Registrar</a> e crie uma conta.</li>
                    <li>Após o registro, você será redirecionado para <code>http://127.0.0.1:8016/api-token</code>, onde verá seu token de API.</li>
                    <li>Se já possui uma conta, faça <a href="http://127.0.0.1:8016/login" target="_blank" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Login</a> e acesse <code>/api-token</code> para visualizar ou gerar um novo token.</li>
                </ol>

                <h3 class="section-subtitle">Passo 2: Adicione o Script</h3>
                <p>Adicione o seguinte código antes do fechamento da tag <code>&lt;/body&gt;</code> no seu HTML:</p>

                <div class="code-block" data-lang="HTML">
                    <pre><code><span class="comment">&lt;!-- Kairo IA Widget --&gt;</span>
<span class="keyword">&lt;script</span> <span class="string">src="http://127.0.0.1:8016/js/kairo-widget.js"</span> 
        <span class="string">data-api-token="SEU_API_TOKEN_AQUI"</span> <span class="string">async</span><span class="keyword">&gt;&lt;/script&gt;</span></code></pre>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><strong>Importante:</strong> Substitua <code>SEU_API_TOKEN_AQUI</code> pelo seu token real obtido em <code>/api-token</code>.</span>
                </div>

                <h3 class="section-subtitle">Exemplo Completo</h3>
                <div class="code-block" data-lang="HTML">
                    <pre><code><span class="keyword">&lt;!DOCTYPE html&gt;</span>
<span class="keyword">&lt;html</span> <span class="string">lang="pt-BR"</span><span class="keyword">&gt;</span>
<span class="keyword">&lt;head&gt;</span>
    <span class="keyword">&lt;meta</span> <span class="string">charset="UTF-8"</span><span class="keyword">&gt;</span>
    <span class="keyword">&lt;title&gt;</span>Meu Site com Kairo IA<span class="keyword">&lt;/title&gt;</span>
<span class="keyword">&lt;/head&gt;</span>
<span class="keyword">&lt;body&gt;</span>
    <span class="keyword">&lt;h1&gt;</span>Bem-vindo ao meu site!<span class="keyword">&lt;/h1&gt;</span>
    
    <span class="comment">&lt;!-- Seu conteúdo aqui --&gt;</span>
    
    <span class="comment">&lt;!-- Kairo IA Widget --&gt;</span>
    <span class="keyword">&lt;script</span> <span class="string">src="http://127.0.0.1:8016/js/kairo-widget.js"</span> 
            <span class="string">data-api-token="e1WYC1kUWWJp1eUnZ8v6vSueb5kdlWnU3vpkLRTtZ7QGvk1seMFLBHCWokwBjSLgq7WcYX2U6fXO0PNA"</span> <span class="string">async</span><span class="keyword">&gt;&lt;/script&gt;</span>
<span class="keyword">&lt;/body&gt;</span>
<span class="keyword">&lt;/html&gt;</span></code></pre>
                </div>

                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><strong>Pronto!</strong> O widget aparecerá automaticamente no canto inferior direito da sua página.</span>
                </div>
            </section>

            <!-- Configuration Section -->
            <section id="configuration" class="section">
                <h2 class="section-title"><i class="fas fa-cog"></i> Configuração</h2>

                <h3 class="section-subtitle">Configurações do Servidor</h3>
                <p>Configure o arquivo <code>.env</code> com as seguintes variáveis:</p>

                <div class="code-block" data-lang="ENV">
                    <pre><code><span class="comment"># Configurações da OpenAI</span>
<span class="keyword">OPENAI_API_KEY</span>=<span class="string">sua_chave_openai_aqui</span>

<span class="comment"># Configurações do Kairo</span>
<span class="keyword">KAIRO_API_ENDPOINT</span>=<span class="string">http://127.0.0.1:8016/api/kairo</span>
<span class="keyword">KAIRO_AVATAR_URL</span>=<span class="string">http://127.0.0.1:8016/images/kairo.jpg</span></code></pre>
                </div>

                <h3 class="section-subtitle">CORS</h3>
                <p>Permita requisições de <code>http://127.0.0.1:8017</code> em <code>config/cors.php</code>:</p>

                <div class="code-block" data-lang="PHP">
                    <pre><code><span class="keyword">return</span> [
    <span class="string">'paths'</span> => [<span class="string">'api/*'</span>],
    <span class="string">'allowed_methods'</span> => [<span class="string">'*'</span>],
    <span class="string">'allowed_origins'</span> => [<span class="string">'http://127.0.0.1:8017'</span>],
    <span class="string">'allowed_origins_patterns'</span> => [],
    <span class="string">'allowed_headers'</span> => [<span class="string">'*'</span>],
    <span class="string">'exposed_headers'</span> => [],
    <span class="string">'max_age'</span> => <span class="number">0</span>,
    <span class="string">'supports_credentials'</span> => <span class="keyword">false</span>,
];</code></pre>
                </div>
            </section>

            <!-- API Section -->
            <section id="api" class="section">
                <h2 class="section-title"><i class="fas fa-code"></i> API Reference</h2>

                <h3 class="section-subtitle">GET /api/config</h3>
                <div class="card">
                    <h4><i class="fas fa-cogs"></i> Obter Configurações</h4>
                    <p>Retorna as configurações públicas do chatbot.</p>
                    
                    <strong>Parâmetros:</strong>
                    <div class="code-block" data-lang="Query">
                        <pre><code>api_token=<span class="string">seu_token_aqui</span></code></pre>
                    </div>

                    <strong>Resposta:</strong>
                    <div class="code-block" data-lang="JSON">
                        <pre><code>{
  <span class="string">"api_endpoint"</span>: <span class="string">"http://127.0.0.1:8016/api/kairo"</span>,
  <span class="string">"avatar_url"</span>: <span class="string">"http://127.0.0.1:8016/images/kairo.jpg"</span>
}</code></pre>
                    </div>
                </div>

                <div class="card">
                    <h4><i class="fas fa-comment"></i> POST /api/kairo</h4>
                    <p>Processa mensagens do usuário e retorna respostas da IA.</p>
                    
                    <strong>Payload:</strong>
                    <div class="code-block" data-lang="JSON">
                        <pre><code>{
  <span class="string">"message"</span>: <span class="string">"Olá, como você pode me ajudar?"</span>,
  <span class="string">"api_token"</span>: <span class="string">"seu_token_aqui"</span>,
  <span class="string">"history"</span>: [
    {
      <span class="string">"role"</span>: <span class="string">"user"</span>,
      <span class="string">"content"</span>: <span class="string">"Mensagem anterior"</span>
    },
    {
      <span class="string">"role"</span>: <span class="string">"assistant"</span>,
      <span class="string">"content"</span>: <span class="string">"Resposta anterior"</span>
    }
  ]
}</code></pre>
                    </div>

                    <strong>Resposta:</strong>
                    <div class="code-block" data-lang="JSON">
                        <pre><code>{
  <span class="string">"response"</span>: <span class="string">"Olá! Sou o Kairo IA e estou aqui para ajudar. Em que posso ser útil?"</span>
}</code></pre>
                    </div>
                </div>

                <div class="card">
                    <h4><i class="fas fa-key"></i> POST /api/verify-token</h4>
                    <p>Verifica se um token de API é válido.</p>
                    
                    <strong>Payload:</strong>
                    <div class="code-block" data-lang="JSON">
                        <pre><code>{
  <span class="string">"api_token"</span>: <span class="string">"e1WYC1kUWWJp1eUnZ8v6vSueb5kdlWnU3vpkLRTtZ7QGvk1seMFLBHCWokwBjSLgq7WcYX2U6fXO0PNA"</span>
}</code></pre>
                    </div>

                    <strong>Resposta (Sucesso):</strong>
                    <div class="code-block" data-lang="JSON">
                        <pre><code>{
  <span class="string">"message"</span>: <span class="string">"Token válido"</span>
}</code></pre>
                    </div>

                    <strong>Resposta (Erro):</strong>
                    <div class="code-block" data-lang="JSON">
                        <pre><code>{
  <span class="string">"error"</span>: <span class="string">"Token inválido"</span>
}</code></pre>
                    </div>
                </div>

                <h3 class="section-subtitle">Códigos de Status</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span style="color: var(--success);">200</span></td>
                                <td>Sucesso - Resposta gerada com sucesso</td>
                            </tr>
                            <tr>
                                <td><span style="color: var(--warning);">400</span></td>
                                <td>Bad Request - Parâmetros inválidos</td>
                            </tr>
                            <tr>
                                <td><span style="color: var(--error);">401</span></td>
                                <td>Unauthorized - Token inválido ou ausente</td>
                            </tr>
                            <tr>
                                <td><span style="color: var(--error);">500</span></td>
                                <td>Server Error - Erro interno do servidor</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Widget Section -->
            <section id="widget" class="section">
                <h2 class="section-title"><i class="fas fa-comment-alt"></i> Integração do Widget</h2>

                <h3 class="section-subtitle">Exemplo de Integração</h3>
                <div class="code-block" data-lang="HTML">
                    <pre><code><span class="keyword">&lt;!DOCTYPE html&gt;</span>
<span class="keyword">&lt;html</span> <span class="string">lang="pt"</span><span class="keyword">&gt;</span>
<span class="keyword">&lt;head&gt;</span>
    <span class="keyword">&lt;meta</span> <span class="string">charset="UTF-8"</span><span class="keyword">&gt;</span>
    <span class="keyword">&lt;meta</span> <span class="string">name="viewport"</span> <span class="string">content="width=device-width, initial-scale=1.0"</span><span class="keyword">&gt;</span>
    <span class="keyword">&lt;title&gt;</span>Tecnideia<span class="keyword">&lt;/title&gt;</span>
    <span class="keyword">&lt;style&gt;</span>
        <span class="keyword">body</span> { <span class="string">font-family</span>: <span class="string">Arial, sans-serif</span>; <span class="string">padding</span>: <span class="number">20px</span>; }
        <span class="keyword">h1</span> { <span class="string">color</span>: <span class="string">#2563eb</span>; }
        <span class="keyword">p</span> { <span class="string">color</span>: <span class="string">#64748b</span>; }
    <span class="keyword">&lt;/style&gt;</span>
<span class="keyword">&lt;/head&gt;</span>
<span class="keyword">&lt;body&gt;</span>
    <span class="keyword">&lt;h1&gt;</span>Bem-vindo à Tecnideia<span class="keyword">&lt;/h1&gt;</span>
    <span class="keyword">&lt;p&gt;</span>Converse com o Kairo IA clicando no ícone no canto inferior direito!<span class="keyword">&lt;/p&gt;</span>
    <span class="keyword">&lt;script</span> <span class="string">src="http://127.0.0.1:8016/js/kairo-widget.js"</span> 
            <span class="string">data-api-token="e1WYC1kUWWJp1eUnZ8v6vSueb5kdlWnU3vpkLRTtZ7QGvk1seMFLBHCWokwBjSLgq7WcYX2U6fXO0PNA"</span> <span class="string">async</span><span class="keyword">&gt;&lt;/script&gt;</span>
<span class="keyword">&lt;/body&gt;</span>
<span class="keyword">&lt;/html&gt;</span></code></pre>
                </div>

                <h3 class="section-subtitle">Funcionalidades do Widget</h3>
                <div class="card-grid">
                    <div class="card">
                        <h4><i class="fas fa-cog"></i> Configuração Dinâmica</h4>
                        <p>Obtém configurações via <code>/api/config</code>.</p>
                    </div>
                    <div class="card">
                        <h4><i class="fas fa-history"></i> Histórico Persistente</h4>
                        <p>Armazena até 10 mensagens no localStorage.</p>
                    </div>
                    <div class="card">
                        <h4><i class="fas fa-mobile-alt"></i> Responsividade</h4>
                        <p>Design adaptável para todos os dispositivos.</p>
                    </div>
                </div>
            </section>

            <!-- Test Section -->
            <section id="test" class="section">
                <h2 class="section-title"><i class="fas fa-vial"></i> Testes</h2>

                <div class="test-section">
                    <h3 class="section-subtitle">Testar o Widget</h3>
                    <p>Insira seu token de API abaixo e clique em "Verificar Token" para validar. Se válido, clique em "Testar Widget" para carregar o Kairo IA:</p>
                    <input type="text" id="apiTokenInput" placeholder="Digite seu API Token (64 caracteres alfanuméricos)">
                    <button id="verifyTokenButton" class="btn btn-primary"><i class="fas fa-check"></i> Verificar Token</button>
                    <button id="testWidgetButton" class="btn btn-primary" style="display: none;"><i class="fas fa-play"></i> Testar Widget</button>
                    <div id="testSpinner" class="spinner"></div>
                    <div id="testSuccess" class="alert alert-success" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                        <span><strong>Sucesso!</strong> Token válido. Clique em "Testar Widget" para carregar o widget.</span>
                    </div>
                    <div id="testError" class="alert alert-error" style="display: none;">
                        <i class="fas fa-exclamation-circle"></i>
                        <span id="testErrorMessage"><strong>Erro:</strong> Por favor, insira um token de API válido (64 caracteres alfanuméricos).</span>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <span><strong>Dica:</strong> Obtenha seu token em <a href="http://127.0.0.1:8016/api-token" target="_blank">http://127.0.0.1:8016/api-token</a> após fazer login ou registro.</span>
                    </div>
                </div>

                <h3 class="section-subtitle">Testes Manuais</h3>
                <div class="card-grid">
                    <div class="card">
                        <h4><i class="fas fa-user-plus"></i> Registro</h4>
                        <p>Acesse <a href="http://127.0.0.1:8016/register" target="_blank">http://127.0.0.1:8016/register</a>, preencha o formulário e verifique o token em <code>/api-token</code>.</p>
                    </div>
                    <div class="card">
                        <h4><i class="fas fa-sign-in-alt"></i> Login</h4>
                        <p>Faça login em <a href="http://127.0.0.1:8016/login" target="_blank">http://127.0.0.1:8016/login</a> e acesse <code>/api-token</code>.</p>
                    </div>
                    <div class="card">
                        <h4><i class="fas fa-code"></i> API</h4>
                        <p>Teste os endpoints com cURL:</p>
                        <div class="code-block" data-lang="Bash">
                            <pre><code><span class="comment"># Teste /api/verify-token</span>
curl -X POST http://127.0.0.1:8016/api/verify-token \
-H "Content-Type: application/json" \
-d '{"api_token":"e1WYC1kUWWJp1eUnZ8v6vSueb5kdlWnU3vpkLRTtZ7QGvk1seMFLBHCWokwBjSLgq7WcYX2U6fXO0PNA"}'

<span class="comment"># Teste /api/config</span>
curl -X GET "http://127.0.0.1:8016/api/config?api_token=e1WYC1kUWWJp1eUnZ8v6vSueb5kdlWnU3vpkLRTtZ7QGvk1seMFLBHCWokwBjSLgq7WcYX2U6fXO0PNA"

<span class="comment"># Teste /api/kairo</span>
curl -X POST http://127.0.0.1:8016/api/kairo \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{"message":"Teste","history":[],"api_token":"e1WYC1kUWWJp1eUnZ8v6vSueb5kdlWnU3vpkLRTtZ7QGvk1seMFLBHCWokwBjSLgq7WcYX2U6fXO0PNA"}'</code></pre>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Tecnideia. Todos os direitos reservados.</p>
            <p>Kairo IA - Transformando conversas em experiências extraordinárias.</p>
        </div>
    </footer>

    <script>
        // Smooth scroll para navegação
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                document.querySelectorAll('.nav-links a').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                const targetId = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Update active nav link on scroll
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('.section');
            const navLinks = document.querySelectorAll('.nav-links a');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').substring(1) === current) {
                    link.classList.add('active');
                }
            });
        });

        // Copy code functionality
        document.querySelectorAll('.code-block').forEach(block => {
            block.addEventListener('click', function() {
                const code = this.querySelector('pre code').textContent;
                navigator.clipboard.writeText(code).then(() => {
                    const originalContent = this.getAttribute('data-lang');
                    this.setAttribute('data-lang', 'Copiado!');
                    setTimeout(() => {
                        this.setAttribute('data-lang', originalContent);
                    }, 2000);
                });
            });
        });

        // Test widget functionality
        const tokenInput = document.getElementById('apiTokenInput');
        const verifyTokenButton = document.getElementById('verifyTokenButton');
        const testWidgetButton = document.getElementById('testWidgetButton');
        const testSpinner = document.getElementById('testSpinner');
        const testSuccess = document.getElementById('testSuccess');
        const testError = document.getElementById('testError');
        const testErrorMessage = document.getElementById('testErrorMessage');

        // Validação de formato do token
        function validateTokenFormat(token) {
            const tokenRegex = /^[a-zA-Z0-9]{64}$/;
            return tokenRegex.test(token);
        }

        // Verificação do token via AJAX
        verifyTokenButton.addEventListener('click', async function() {
            const token = tokenInput.value.trim();
            testSuccess.style.display = 'none';
            testError.style.display = 'none';
            testSpinner.style.display = 'block';
            testWidgetButton.style.display = 'none';

            if (!token) {
                testSpinner.style.display = 'none';
                testErrorMessage.innerHTML = '<strong>Erro:</strong> Por favor, insira um token de API.';
                testError.style.display = 'block';
                return;
            }

            if (!validateTokenFormat(token)) {
                testSpinner.style.display = 'none';
                testErrorMessage.innerHTML = '<strong>Erro:</strong> O token deve ter 64 caracteres alfanuméricos.';
                testError.style.display = 'block';
                return;
            }

            try {
                const response = await fetch('http://127.0.0.1:8016/api/verify-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ api_token: token })
                });

                const data = await response.json();

                testSpinner.style.display = 'none';

                if (response.ok) {
                    testSuccess.style.display = 'block';
                    testWidgetButton.style.display = 'inline-flex';
                } else {
                    testErrorMessage.innerHTML = `<strong>Erro:</strong> ${data.error || 'Token inválido ou servidor indisponível.'}`;
                    testError.style.display = 'block';
                }
            } catch (error) {
                testSpinner.style.display = 'none';
                testErrorMessage.innerHTML = '<strong>Erro:</strong> Falha na conexão com o servidor. Verifique se o servidor está rodando.';
                testError.style.display = 'block';
            }
        });

        // Carregar widget após validação
        testWidgetButton.addEventListener('click', function() {
            const token = tokenInput.value.trim();
            testSpinner.style.display = 'block';
            testSuccess.style.display = 'none';
            testError.style.display = 'none';

            // Remove existing widget script and container
            const existingScript = document.getElementById('kairoWidget');
            if (existingScript) {
                existingScript.remove();
            }
            const existingWidget = document.querySelector('.kairo-chatbot-widget');
            if (existingWidget) {
                existingWidget.remove();
            }

            // Reset global KairoWidget to allow reinitialization
            window.KairoWidget = false;

            // Create new script element
            const script = document.createElement('script');
            script.id = 'kairoWidget';
            script.src = 'http://127.0.0.1:8016/js/kairo-widget.js';
            script.setAttribute('data-api-token', token);
            script.async = true;

            // Handle script load
            script.onload = () => {
                testSpinner.style.display = 'none';
                testSuccess.style.display = 'block';
                testSuccess.innerHTML = '<i class="fas fa-check-circle"></i> <span><strong>Sucesso!</strong> Widget carregado. Clique no ícone no canto inferior direito para interagir.</span>';
            };

            // Handle script error
            script.onerror = () => {
                testSpinner.style.display = 'none';
                testErrorMessage.innerHTML = '<strong>Erro:</strong> Falha ao carregar o widget. Verifique o token e a conexão com o servidor.';
                testError.style.display = 'block';
            };

            // Append script to body
            document.body.appendChild(script);
        });

        // Show error if input is cleared
        tokenInput.addEventListener('input', function() {
            testSuccess.style.display = 'none';
            testError.style.display = 'none';
            testWidgetButton.style.display = 'none';
            if (!this.value.trim()) {
                testErrorMessage.innerHTML = '<strong>Erro:</strong> Por favor, insira um token de API.';
                testError.style.display = 'block';
            }
        });
    </script>