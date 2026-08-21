{include file="sections/header.tpl"}

{literal}
<style>
.spexpert-container {
    display: flex;
    height: calc(100vh - 120px);
    gap: 20px;
}
.spexpert-sidebar {
    width: 260px;
    flex-shrink: 0;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    padding: 20px;
    overflow-y: auto;
}
.spexpert-chat {
    flex: 1;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    display: flex;
    flex-direction: column;
}
.spexpert-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.spexpert-header .ai-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 18px;
}
.spexpert-header h4 { margin: 0; font-weight: 700; }
.spexpert-header .badge { font-size: 10px; margin-left: 8px; }
.spexpert-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.spexpert-msg {
    max-width: 80%;
    padding: 12px 16px;
    border-radius: 14px;
    line-height: 1.55;
    font-size: 14px;
    animation: fadeInUp 0.25s ease;
}
@keyframes fadeInUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
.spexpert-msg.user {
    align-self: flex-end;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    border-bottom-right-radius: 4px;
}
.spexpert-msg.ai {
    align-self: flex-start;
    background: #f7fafc;
    color: #2d3748;
    border: 1px solid #e2e8f0;
    border-bottom-left-radius: 4px;
}
.spexpert-msg.ai code {
    background: #edf2f7;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 13px;
    color: #e53e3e;
}
.spexpert-msg.ai pre {
    background: #1a202c;
    color: #e2e8f0;
    padding: 12px;
    border-radius: 8px;
    overflow-x: auto;
    font-size: 12px;
    margin: 8px 0;
}
.spexpert-msg.ai pre code { background: none; padding: 0; color: #e2e8f0; }
.spexpert-msg.ai strong { color: #2d3748; }
.spexpert-msg.ai ul, .spexpert-msg.ai ol { padding-left: 20px; margin: 4px 0; }
.spexpert-input-area {
    padding: 14px 20px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 10px;
}
.spexpert-input-area textarea {
    flex: 1;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 14px;
    resize: none;
    height: 44px;
    max-height: 120px;
    outline: none;
    transition: border 0.2s;
}
.spexpert-input-area textarea:focus { border-color: #667eea; }
.spexpert-input-area button {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 0 18px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
}
.spexpert-input-area button:hover { opacity: 0.9; }
.spexpert-input-area button:disabled { opacity: 0.5; cursor: not-allowed; }
.typing-dots { display: flex; gap: 4px; padding: 12px 16px; }
.typing-dots span {
    width: 7px; height: 7px;
    background: #a0aec0;
    border-radius: 50%;
    animation: bounce 1.2s infinite;
}
.typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.typing-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes bounce { 0%,60%,100% { transform:translateY(0); } 30% { transform:translateY(-8px); } }

.spexpert-sidebar h5 { font-size: 13px; color: #718096; text-transform: uppercase; letter-spacing: 1px; margin: 16px 0 8px; }
.spexpert-sidebar .topic-btn {
    display: block;
    width: 100%;
    text-align: left;
    padding: 8px 12px;
    border: none;
    background: none;
    color: #4a5568;
    font-size: 13px;
    border-radius: 6px;
    cursor: pointer;
    margin-bottom: 2px;
    transition: background 0.15s;
}
.spexpert-sidebar .topic-btn:hover { background: #edf2f7; color: #667eea; }
.spexpert-sidebar .token-info { font-size: 11px; color: #a0aec0; margin-top: 16px; text-align: center; }

@media (max-width: 768px) {
    .spexpert-container { flex-direction: column; height: auto; }
    .spexpert-sidebar { width: 100%; }
    .spexpert-chat { min-height: 60vh; }
}
</style>
{/literal}

<div class="spexpert-container">
    <div class="spexpert-sidebar">
        <h5><i class="fa fa-cog"></i> Setup</h5>
        <a href="{$_url}plugin/spexpert_ai/config" class="topic-btn" style="color:#667eea;font-weight:600;">
            ⚙️ Configure API Key
        </a>

        <h5><i class="fa fa-lightbulb-o"></i> Quick Topics</h5>
        <button class="topic-btn" onclick="askTopic('How do I add a new customer?')">➕ Add Customer</button>
        <button class="topic-btn" onclick="askTopic('How do I create a Hotspot plan?')">📶 Hotspot Plan</button>
        <button class="topic-btn" onclick="askTopic('How does M-Pesa payment work?')">💳 M-Pesa Payment</button>
        <button class="topic-btn" onclick="askTopic('How do I fix SMS not sending?')">📱 SMS Troubleshooting</button>
        <button class="topic-btn" onclick="askTopic('How do I set up WhatsApp gateway?')">💬 WhatsApp Setup</button>
        <button class="topic-btn" onclick="askTopic('What are the cron jobs and how do I set them up?')">⏰ Cron Jobs</button>
        <button class="topic-btn" onclick="askTopic('How do I sync customers to routers?')">🔄 Sync to Router</button>
        <button class="topic-btn" onclick="askTopic('How do I troubleshoot expired customers not disconnecting?')">🔌 Expiry Issues</button>
        <button class="topic-btn" onclick="askTopic('Explain the database tables in this system')">🗄️ Database Schema</button>
        <button class="topic-btn" onclick="askTopic('How do I create and print vouchers?')">🎫 Vouchers</button>
        <button class="topic-btn" onclick="askTopic('How do I configure SMS Gate gateway?')">📲 SMS Gate Setup</button>
        <button class="topic-btn" onclick="askTopic('How does the notification system work?')">🔔 Notifications</button>

        <h5><i class="fa fa-info-circle"></i> About</h5>
        <p style="font-size:12px;color:#718096;line-height:1.6;">
            SpeedRad Expert AI knows this system inside out. It answers questions about configuration, troubleshooting, features, and workflows. It will NOT answer questions unrelated to SpeedRadius.
        </p>
        <div class="token-info" id="tokenInfo"></div>
    </div>

    <div class="spexpert-chat">
        <div class="spexpert-header">
            <div class="ai-icon"><i class="fa fa-lightbulb-o"></i></div>
            <div>
                <h4>SpeedRad Expert AI</h4>
                <small class="text-muted">Knows everything about this system</small>
            </div>
        </div>

        <div class="spexpert-messages" id="chatMessages">
            {if !$api_key_configured}
            <div class="spexpert-msg ai" style="background:#fff3cd;border-color:#ffc107;color:#856404;">
                ⚠️ <strong>API key not configured!</strong><br>
                Click <a href="{$_url}plugin/spexpert_ai/config" style="color:#667eea;font-weight:700;">⚙️ Configure API Key</a> in the sidebar to add your DeepSeek API key.<br>
                <small>Get a free key at <a href="https://platform.deepseek.com/api_keys" target="_blank">platform.deepseek.com</a></small>
            </div>
            {else}
            <div class="spexpert-msg ai">
                👋 Hi! I'm <strong>SpeedRad Expert AI</strong>. I know every detail about your SpeedRadius ISP billing system — customers, plans, routers, payments, SMS, WhatsApp, cron jobs, troubleshooting, and more.<br><br>
                <strong>Ask me anything about your system!</strong> Try one of the quick topics on the left, or type your own question.
            </div>
            {/if}
        </div>

        <div class="spexpert-input-area">
            <textarea id="userInput" placeholder="Ask about SpeedRadius..." rows="1"></textarea>
            <button id="sendBtn">
                <i class="fa fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<script>
var _spexpert_url = '{$_url}plugin/spexpert_ai/api';
</script>
{literal}
<script>
var chatHistory = [];
var isProcessing = false;

document.getElementById('sendBtn').addEventListener('click', sendMessage);
document.getElementById('userInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});
document.getElementById('userInput').addEventListener('input', function() {
    this.style.height = '44px';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

function askTopic(question) {
    document.getElementById('userInput').value = question;
    sendMessage();
}

function sendMessage() {
    if (isProcessing) return;
    var input = document.getElementById('userInput');
    var msg = input.value.trim();
    if (!msg) return;

    isProcessing = true;
    document.getElementById('sendBtn').disabled = true;

    addMessage(msg, 'user');
    input.value = '';
    input.style.height = '44px';

    var typingId = addTyping();

    fetch(_spexpert_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: msg, history: chatHistory })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        removeTyping(typingId);
        if (data.error) {
            addMessage('\u274C ' + data.error, 'ai');
        } else {
            addMessage(data.reply, 'ai');
            chatHistory.push({ role: 'user', content: msg });
            chatHistory.push({ role: 'assistant', content: data.reply });
            if (data.tokens) {
                document.getElementById('tokenInfo').textContent = 'Tokens used: ' + data.tokens;
            }
        }
        isProcessing = false;
        document.getElementById('sendBtn').disabled = false;
    })
    .catch(function(err) {
        removeTyping(typingId);
        addMessage('\u274C Connection error. Check your network.', 'ai');
        isProcessing = false;
        document.getElementById('sendBtn').disabled = false;
    });
}

function addMessage(text, role) {
    var div = document.createElement('div');
    div.className = 'spexpert-msg ' + role;
    div.innerHTML = formatMarkdown(text);
    document.getElementById('chatMessages').appendChild(div);
    scrollDown();
}

function addTyping() {
    var div = document.createElement('div');
    div.className = 'typing-dots';
    div.id = 'typing-' + Date.now();
    div.innerHTML = '<span></span><span></span><span></span>';
    document.getElementById('chatMessages').appendChild(div);
    scrollDown();
    return div.id;
}

function removeTyping(id) {
    var el = document.getElementById(id);
    if (el) el.remove();
}

function scrollDown() {
    var msgs = document.getElementById('chatMessages');
    msgs.scrollTop = msgs.scrollHeight;
}

function formatMarkdown(text) {
    text = text.replace(/```(\w*)\n?([\s\S]*?)```/g, '<pre><code>$2</code></pre>');
    text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
    text = text.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
    text = text.replace(/\*([^*]+)\*/g, '<em>$1</em>');
    text = text.replace(/^- (.+)$/gm, '<li>$1</li>');
    text = text.replace(/(<li>.*<\/li>\n?)+/g, '<ul>$&</ul>');
    text = text.replace(/\n/g, '<br>');
    return text;
}
</script>
{/literal}

{include file="sections/footer.tpl"}
