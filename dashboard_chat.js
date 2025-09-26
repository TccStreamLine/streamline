document.addEventListener('DOMContentLoaded', () => {
    const chatLauncher = document.getElementById('chat-launcher');
    const chatContainer = document.getElementById('chat-container');
    const closeChatBtn = document.getElementById('close-chat');
    const chatForm = document.getElementById('ai-chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    const typingIndicator = document.getElementById('typing-indicator');

    // Abre e fecha o chat
    chatLauncher.addEventListener('click', () => {
        chatContainer.classList.toggle('hidden');
    });

    closeChatBtn.addEventListener('click', () => {
        chatContainer.classList.add('hidden');
    });

    // Envia a mensagem do formulário
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const userMessage = chatInput.value.trim();
        if (!userMessage) return;

        // Adiciona a mensagem do usuário ao chat
        addMessageToChat(userMessage, 'user');
        chatInput.value = '';
        typingIndicator.classList.remove('hidden');

        try {
            // Envia a pergunta para o backend
            const response = await fetch('chat_dashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ pergunta: userMessage })
            });

            if (!response.ok) {
                throw new Error('Erro na comunicação com o servidor.');
            }

            const data = await response.json();
            
            // Adiciona a resposta da IA ao chat
            addMessageToChat(data.resposta, 'ai');

        } catch (error) {
            console.error('Erro:', error);
            addMessageToChat('Desculpe, ocorreu um erro. Tente novamente mais tarde.', 'ai');
        } finally {
            typingIndicator.classList.add('hidden');
        }
    });

    // Função para adicionar uma nova mensagem na tela
    function addMessageToChat(text, sender) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', sender);
        messageElement.textContent = text;
        chatMessages.appendChild(messageElement);
        // Rola para a mensagem mais recente
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});