(function () {
    if (window.KairoWidget) return;
    window.KairoWidget = true;

    const styles = `
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --primary-color: #2563eb; --secondary-color: #0ea5e9; --accent-color: #06b6d4; --dark-color: #1e293b; --light-color: #f8fafc; --text-primary: #0f172a; --text-secondary: #64748b; --bg-primary: #ffffff; --bg-secondary: #f8fafc; --border-color: #e2e8f0; --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); --gradient-primary: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%); }
        .kairo-chatbot-widget { position: fixed; bottom: 15px; right: 15px; z-index: 1000; }
        .kairo-chatbot-toggle { width: 50px; height: 50px; border-radius: 50%; background: var(--gradient-primary); border: none; cursor: pointer; box-shadow: var(--shadow); display: flex; align-items: center; justify-content: center; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; }
        .kairo-chatbot-toggle:hover { transform: scale(1.15); box-shadow: 0 0 25px rgba(37, 99, 235, 0.5); }
        .kairo-chatbot-toggle.active { background: linear-gradient(135deg, #ff6b6b, #ffa500); }
        .kairo-toggle-icon { width: 24px; height: 24px; fill: var(--light-color); transition: all 0.3s ease; }
        .kairo-chatbot-toggle.active .kairo-toggle-icon { transform: rotate(45deg); }
        .kairo-notification-badge { position: absolute; top: -5px; right: -5px; width: 18px; height: 18px; background: #ff4757; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; color: white; font-weight: bold; box-shadow: 0 0 10px rgba(255, 71, 87, 0.7); animation: kairo-pulse 2s infinite; }
        @keyframes kairo-pulse { 0% { transform: scale(1); } 50% { transform: scale(1.2); } 100% { transform: scale(1); } }
        .kairo-chatbot-container { position: absolute; bottom: 70px; right: 10px; width: 360px; height: 500px; background: var(--bg-secondary); border-radius: 15px; box-shadow: var(--shadow); border: 1px solid var(--border-color); display: flex; flex-direction: column; overflow: hidden; transform: translateY(20px) scale(0.8); opacity: 0; visibility: hidden; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .kairo-chatbot-container.active { transform: translateY(0) scale(1); opacity: 1; visibility: visible; }
        .kairo-chatbot-header { background: var(--gradient-primary); color: var(--light-color); padding: 15px; display: flex; align-items: center; gap: 10px; position: relative; }
        .kairo-bot-avatar { width: 50px; height: 50px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; border: 3px solid rgba(255, 255, 255, 0.3); box-shadow: 0 0 15px rgba(37, 99, 235, 0.5); animation: kairo-glow 2s ease-in-out infinite alternate; }
        .kairo-bot-avatar-image { width: 100%; height: 100%; object-fit: cover; }
        @keyframes kairo-glow { from { box-shadow: 0 0 10px rgba(37, 99, 235, 0.5); } to { box-shadow: 0 0 20px rgba(37, 99, 235, 0.8); } }
        .kairo-bot-info h2 { font-size: 1.3em; margin-bottom: 2px; }
        .kairo-bot-status { display: flex; align-items: center; font-size: 0.8em; opacity: 0.9; }
        .kairo-status-indicator { width: 8px; height: 8px; background: #00ff99; border-radius: 50%; margin-right: 6px; animation: kairo-pulse 2s infinite; }
        .kairo-close-button { position: absolute; top: 10px; right: 10px; background: rgba(255, 255, 255, 0.2); border: none; width: 25px; height: 25px; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; }
        .kairo-close-button:hover { background: rgba(255, 255, 255, 0.4); transform: rotate(90deg); }
        .kairo-chat-messages { flex: 1; padding: 15px; overflow-y: auto; background: var(--bg-primary); }
        .kairo-message { margin-bottom: 10px; animation: kairo-slideIn 0.4s ease-out; display: flex; }
        @keyframes kairo-slideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .kairo-message.kairo-user-message { justify-content: flex-end; }
        .kairo-message.kairo-bot-message { justify-content: flex-start; }
        .kairo-message-bubble { display: inline-block; max-width: 85%; padding: 10px 12px; border-radius: 15px; font-size: 13px; line-height: 1.4; position: relative; color: var(--text-primary); }
        .kairo-message.kairo-user-message .kairo-message-bubble { background: var(--gradient-primary); color: var(--light-color); border-bottom-right-radius: 5px; }
        .kairo-message.kairo-bot-message .kairo-message-bubble { background: var(--light-color); color: var(--text-primary); border: 1px solid var(--border-color); box-shadow: var(--shadow); border-bottom-left-radius: 5px; }
        .kairo-message-indicator { font-weight: bold; margin-right: 5px; }
        .kairo-message.kairo-user-message .kairo-message-indicator { color: var(--light-color); }
        .kairo-message.kairo-bot-message .kairo-message-indicator { color: var(--primary-color); }
        .kairo-message-bubble a { color: var(--primary-color); text-decoration: underline; }
        .kairo-message-bubble a:hover { color: var(--secondary-color); }
        .kairo-chat-input-container { padding: 15px; background: var(--bg-secondary); border-top: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 8px; }
        .kairo-chat-input-form { display: flex; gap: 10px; align-items: flex-end; }
        .kairo-chat-input { flex: 1; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 20px; font-size: 13px; outline: none; transition: all 0.3s ease; resize: none; min-height: 40px; max-height: 80px; font-family: inherit; background: var(--bg-primary); color: var(--text-primary); }
        .kairo-chat-input:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2); }
        .kairo-chat-input::placeholder { color: var(--text-secondary); }
        .kairo-send-button { width: 40px; height: 40px; background: var(--gradient-primary); border: none; border-radius: 50%; color: var(--light-color); cursor: pointer; font-size: 14px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .kairo-send-button:hover { transform: scale(1.1); box-shadow: 0 0 15px rgba(37, 99, 235, 0.5); }
        .kairo-send-button:active { transform: scale(0.95); }
        .kairo-typing-indicator { display: none; padding: 10px 12px; background: var(--light-color); border-radius: 15px; margin-bottom: 10px; border: 1px solid var(--border-color); box-shadow: var(--shadow); max-width: 85%; border-bottom-left-radius: 5px; }
        .kairo-typing-dots { display: flex; gap: 3px; }
        .kairo-typing-dots span { width: 7px; height: 7px; background: var(--primary-color); border-radius: 50%; animation: kairo-typing 1.4s infinite ease-in-out; }
        .kairo-typing-dots span:nth-child(1) { animation-delay: -0.32s; }
        .kairo-typing-dots span:nth-child(2) { animation-delay: -0.16s; }
        @keyframes kairo-typing { 0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; } 40% { transform: scale(1); opacity: 1; } }
        .kairo-welcome-message { text-align: center; color: var(--text-secondary); font-style: italic; margin-bottom: 15px; padding: 10px; background: rgba(37, 99, 235, 0.1); border-radius: 10px; border-left: 3px solid var(--primary-color); }
        @media (max-width: 480px) {
            .kairo-chatbot-widget { bottom: 10px; right: 10px; }
            .kairo-chatbot-toggle { width: 45px; height: 45px; }
            .kairo-toggle-icon { width: 20px; height: 20px; }
            .kairo-notification-badge { width: 16px; height: 16px; font-size: 9px; }
            .kairo-chatbot-container { width: calc(100vw - 20px); height: calc(100vh - 80px); right: 10px; bottom: 65px; border-radius: 12px; }
            .kairo-chatbot-header { padding: 12px; gap: 8px; }
            .kairo-bot-avatar { width: 40px; height: 40px; }
            .kairo-bot-info h2 { font-size: 1.1em; }
            .kairo-bot-status { font-size: 0.7em; }
            .kairo-status-indicator { width: 7px; height: 7px; margin-right: 5px; }
            .kairo-close-button { width: 22px; height: 22px; top: 8px; right: 8px; }
            .kairo-chat-messages { padding: 12px; }
            .kairo-message-bubble { font-size: 12px; padding: 8px 10px; border-radius: 12px; }
            .kairo-chat-input-container { padding: 12px; }
            .kairo-chat-input { font-size: 12px; padding: 8px 10px; min-height: 36px; max-height: 70px; border-radius: 18px; }
            .kairo-send-button { width: 36px; height: 36px; font-size: 13px; }
            .kairo-typing-indicator { padding: 8px 10px; border-radius: 12px; }
            .kairo-typing-dots span { width: 6px; height: 6px; }
            .kairo-welcome-message { font-size: 12px; padding: 8px; margin-bottom: 12px; }
        }
    `;

    const widgetHtml = `
        <div class="kairo-chatbot-widget">
            <button class="kairo-chatbot-toggle intro-animation" id="kairoChatbotToggle">
                <svg class="kairo-toggle-icon" viewBox="0 0 24 24">
                    <path d="M12 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm0 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-6-8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm12 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" />
                </svg>
                <div class="kairo-notification-badge" id="kairoNotificationBadge">1</div>
            </button>
            <div class="kairo-chatbot-container" id="kairoChatbotContainer">
                <div class="kairo-chatbot-header">
                    <div class="kairo-bot-avatar">
                        <img id="kairoAvatarImage" src="" alt="Kairo IA Avatar" class="kairo-bot-avatar-image">
                    </div>
                    <div class="kairo-bot-info">
                        <h2>Kairo IA</h2>
                        <div class="kairo-bot-status">
                            <span class="kairo-status-indicator"></span>
                            Conectado
                        </div>
                    </div>
                    <button class="kairo-close-button" id="kairoCloseButton">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="kairo-chat-messages" id="kairoChatMessages">
                    <div class="kairo-welcome-message">Bem-vindo ao Kairo IA! Estou aqui para responder suas perguntas com inteligência e estilo. Como posso ajudar?</div>
                </div>
                <div class="kairo-typing-indicator" id="kairoTypingIndicator">
                    <div class="kairo-typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
                <div class="kairo-chat-input-container">
                    <form class="kairo-chat-input-form">
                        <textarea class="kairo-chat-input" id="kairoChatInput" placeholder="Digite sua mensagem..." rows="1"></textarea>
                        <button type="submit" class="kairo-send-button">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    `;

    const styleElement = document.createElement('style');
    styleElement.textContent = styles;
    document.head.appendChild(styleElement);

    const widgetContainer = document.createElement('div');
    widgetContainer.innerHTML = widgetHtml;
    document.body.appendChild(widgetContainer);

    class KairoWidget {
        constructor() {
            this.apiToken = document.querySelector('script[data-api-token]')?.getAttribute('data-api-token') || '';
            console.log('Token inicializado:', this.apiToken);
            this.chatbotToggle = document.getElementById('kairoChatbotToggle');
            this.chatbotContainer = document.getElementById('kairoChatbotContainer');
            this.closeButton = document.getElementById('kairoCloseButton');
            this.messagesContainer = document.getElementById('kairoChatMessages');
            this.chatForm = document.querySelector('.kairo-chat-input-form');
            this.chatInput = document.getElementById('kairoChatInput');
            this.typingIndicator = document.getElementById('kairoTypingIndicator');
            this.notificationBadge = document.getElementById('kairoNotificationBadge');
            this.toggleIcon = document.querySelector('.kairo-toggle-icon');
            this.avatarImage = document.getElementById('kairoAvatarImage');
            this.isOpen = false;
            this.hasUnreadMessages = true;
            this.conversationHistory = [];
            this.maxHistoryLength = 10;
            this.apiUrl = '';
            this.avatarUrl = '';

            this.loadConfig().then(() => {
                this.initializeMessages();
                this.initializeEventListeners();
                this.autoGreeting();
            });
        }

        async loadConfig() {
            try {
                if (!this.apiToken) throw new Error('Token da API não encontrado');
                const response = await fetch(`http://127.0.0.1:8016/api/config?api_token=${encodeURIComponent(this.apiToken)}`);
                if (!response.ok) throw new Error(`Falha ao carregar configurações: ${response.status}`);
                const config = await response.json();
                this.apiUrl = config.api_endpoint;
                this.avatarUrl = config.avatar_url;
                this.botName = config.bot_name || 'Kairo IA';
                this.primaryColor = config.primary_color || '#2563eb';
                this.secondaryColor = config.secondary_color || '#0ea5e9';
                this.welcomeMessage = config.welcome_message || 'Bem-vindo ao Kairo IA! Como posso ajudar?';

                document.querySelector('.kairo-bot-info h2').textContent = this.botName;
                this.avatarImage.src = this.avatarUrl;

                document.documentElement.style.setProperty('--primary-color', this.primaryColor);
                document.documentElement.style.setProperty('--secondary-color', this.secondaryColor);
                document.documentElement.style.setProperty('--gradient-primary', `linear-gradient(135deg, ${this.primaryColor} 0%, ${this.secondaryColor} 100%)`);
            } catch (error) {
                console.error('Erro ao carregar configurações:', error);
                this.displayMessage('Erro ao carregar configurações.', 'assistant');
            }
        }

        initializeMessages() {
            const savedMessages = localStorage.getItem('kairoChatMessages');
            if (savedMessages) {
                this.conversationHistory = JSON.parse(savedMessages);
                this.conversationHistory.forEach(msg => this.displayMessage(msg.content, msg.role));
            }
        }

        initializeEventListeners() {
            this.chatbotToggle.addEventListener('click', () => this.toggleChatbot());
            this.closeButton.addEventListener('click', () => this.closeChatbot());
            this.chatForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleUserMessage();
            });
            this.chatInput.addEventListener('input', () => this.autoResize());
            this.chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.handleUserMessage();
                }
            });
        }

        autoGreeting() {
            setTimeout(() => {
                if (!this.isOpen && this.hasUnreadMessages) {
                    this.showNotification();
                }
            }, 3000);
        }

        showNotification() {
            this.notificationBadge.style.display = 'flex';
            this.hasUnreadMessages = true;
        }

        hideNotification() {
            this.notificationBadge.style.display = 'none';
            this.hasUnreadMessages = false;
        }

        toggleChatbot() {
            this.isOpen = !this.isOpen;
            this.chatbotContainer.classList.toggle('active', this.isOpen);
            this.chatbotToggle.classList.toggle('active', this.isOpen);
            if (this.isOpen) {
                this.hideNotification();
                setTimeout(() => this.chatInput.focus(), 400);
            }
        }

        closeChatbot() {
            this.isOpen = false;
            this.chatbotContainer.classList.remove('active');
            this.chatbotToggle.classList.remove('active');
        }

        autoResize() {
            this.chatInput.style.height = 'auto';
            this.chatInput.style.height = Math.min(this.chatInput.scrollHeight, 100) + 'px';
        }

        async handleUserMessage() {
            const message = this.chatInput.value.trim();
            if (!message) return;

            this.displayMessage(message, 'user');
            this.conversationHistory.push({ role: 'user', content: message });
            this.chatInput.value = '';
            this.autoResize();
            this.showTypingIndicator();

            try {
                const response = await this.callKairoApi(message);
                this.displayMessage(response, 'assistant');
                this.conversationHistory.push({ role: 'assistant', content: response });
            } catch (error) {
                console.error('Erro ao processar mensagem:', error);
                this.displayMessage('Desculpe, algo deu errado. Tente novamente mais tarde.', 'assistant');
                this.conversationHistory.push({ role: 'assistant', content: 'Desculpe, algo deu errado. Tente novamente mais tarde.' });
            } finally {
                this.saveMessages();
                this.hideTypingIndicator();
            }
        }

        async callKairoApi(message) {
            const limitedHistory = this.conversationHistory.slice(-this.maxHistoryLength);
            const payload = {
                message: message,
                history: limitedHistory,
                api_token: this.apiToken
            };
            console.log('Enviando para /api/kairo:', payload);
            try {
                const response = await fetch(this.apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                console.log('Resposta do servidor:', data);
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status} - ${data.message || 'Erro desconhecido'}`);
                }
                return data.response;
            } catch (error) {
                console.error('Erro ao chamar a API:', error);
                throw error;
            }
        }

        displayMessage(message, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `kairo-message kairo-${sender}-message`;
            const bubbleDiv = document.createElement('div');
            bubbleDiv.className = 'kairo-message-bubble';

            // Adicionar avatar
            const avatar = document.createElement('img');
            avatar.className = 'kairo-message-avatar';
            avatar.src = sender === 'user' ? '/images/user-avatar.png' : this.avatarUrl;
            avatar.style.width = '30px';
            avatar.style.height = '30px';
            avatar.style.borderRadius = '50%';
            avatar.style.margin = sender === 'user' ? '0 0 0 10px' : '0 10px 0 0';
            messageDiv.appendChild(avatar);

            // Adicionar indicador
            const indicator = document.createElement('span');
            indicator.className = 'kairo-message-indicator';
            indicator.textContent = sender === 'user' ? 'Você: ' : 'Kairo IA: ';
            bubbleDiv.appendChild(indicator);

            // Adicionar mensagem
            const contentSpan = document.createElement('span');
            contentSpan.innerHTML = this.sanitizeHTML(message);
            bubbleDiv.appendChild(contentSpan);

            messageDiv.appendChild(bubbleDiv);
            this.messagesContainer.appendChild(messageDiv);
            this.scrollToBottom();
        }

        sanitizeHTML(str) {
            const div = document.createElement('div');
            div.textContent = str;
            const allowedTags = ['a', 'b', 'i', 'strong', 'em'];
            const allowedAttributes = ['href', 'target'];
            const temp = document.createElement('div');
            temp.innerHTML = str;
            const nodes = temp.childNodes;
            let sanitized = '';

            nodes.forEach(node => {
                if (node.nodeType === Node.TEXT_NODE) {
                    sanitized += node.textContent;
                } else if (node.nodeType === Node.ELEMENT_NODE && allowedTags.includes(node.tagName.toLowerCase())) {
                    let attrs = '';
                    for (const attr of node.attributes) {
                        if (allowedAttributes.includes(attr.name)) {
                            attrs += ` ${attr.name}="${attr.value}"`;
                        }
                    }
                    sanitized += `<${node.tagName.toLowerCase()}${attrs}>${node.innerHTML}</${node.tagName.toLowerCase()}>`;
                }
            });

            return sanitized;
        }

        saveMessages() {
            this.conversationHistory = this.conversationHistory.slice(-this.maxHistoryLength);
            localStorage.setItem('kairoChatMessages', JSON.stringify(this.conversationHistory));
        }

        showTypingIndicator() {
            this.typingIndicator.style.display = 'block';
            this.scrollToBottom();
        }

        hideTypingIndicator() {
            this.typingIndicator.style.display = 'none';
        }

        scrollToBottom() {
            this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        new KairoWidget();
    });
})();