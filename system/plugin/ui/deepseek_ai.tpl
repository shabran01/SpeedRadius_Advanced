{include file="sections/header.tpl"}

<style>
{literal}
.ai-chat-wrap {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 160px);
    min-height: 400px;
    background: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.10);
}
.ai-chat-header {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    flex-wrap: wrap;
    gap: 8px;
}
.ai-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    -webkit-overflow-scrolling: touch;
}
.ai-chat-messages::-webkit-scrollbar { width: 5px; }
.ai-chat-messages::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
.msg-row { display: flex; align-items: flex-start; gap: 8px; }
.msg-row.user { flex-direction: row-reverse; }
.msg-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; flex-shrink: 0;
}
.msg-avatar.ai { background: linear-gradient(135deg,#667eea,#764ba2); color:#fff; }
.msg-avatar.user { background: linear-gradient(135deg,#11998e,#38ef7d); color:#fff; }
.msg-bubble {
    max-width: 78%;
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.6;
    word-break: break-word;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.msg-bubble.ai {
    background: #fff;
    border-bottom-left-radius: 4px;
    color: #333;
}
.msg-bubble.user {
    background: linear-gradient(135deg,#667eea,#764ba2);
    border-bottom-right-radius: 4px;
    color: #fff;
}
.msg-bubble.error {
    background: #fff5f5;
    border-left: 3px solid #e74c3c;
    color: #c0392b;
}
.msg-bubble pre {
    background: #1e1e2e;
    color: #cdd6f4;
    padding: 10px;
    border-radius: 8px;
    overflow-x: auto;
    font-size: 12px;
    margin: 8px 0 0 0;
    white-space: pre-wrap;
    word-break: break-all;
}
.msg-bubble code {
    background: rgba(0,0,0,0.08);
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 12px;
}
.msg-bubble.user code { background: rgba(255,255,255,0.2); }
.msg-time { font-size: 11px; opacity: 0.55; margin-top: 4px; }
.ai-typing {
    display: none;
    align-items: center; gap: 10px;
}
.ai-typing .dots span {
    display: inline-block; width: 8px; height: 8px;
    background: #667eea; border-radius: 50%;
    animation: bounce 1.2s infinite;
}
.ai-typing .dots span:nth-child(2) { animation-delay: 0.2s; }
.ai-typing .dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes bounce {
    0%,80%,100% { transform:translateY(0); }
    40% { transform:translateY(-8px); }
}
.ai-chat-input-wrap {
    background: #fff;
    padding: 10px 12px;
    border-top: 1px solid #e9ecef;
    display: flex;
    align-items: flex-end;
    gap: 8px;
    flex-shrink: 0;
}
.ai-chat-input-wrap textarea {
    flex: 1;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 10px 12px;
    font-size: 14px;
    resize: none;
    outline: none;
    max-height: 120px;
    transition: border-color 0.2s;
    font-family: inherit;
    -webkit-appearance: none;
}
.ai-chat-input-wrap textarea:focus { border-color: #667eea; }
.ai-send-btn {
    width: 42px; height: 42px;
    background: linear-gradient(135deg,#667eea,#764ba2);
    border: none; border-radius: 50%;
    color: #fff; font-size: 17px;
    cursor: pointer; transition: transform 0.15s, opacity 0.15s;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}
.ai-send-btn:hover { transform: scale(1.08); }
.ai-send-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.token-badge {
    font-size: 11px;
    color: #aaa;
    padding: 2px 8px;
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 20px;
    background: rgba(255,255,255,0.08);
    color: #ccc;
}
.ai-welcome {
    text-align:center; padding: 30px 16px; color: #999;
}
.ai-welcome .icon { font-size: 54px; margin-bottom: 10px; opacity: 0.4; }
.suggestion-chip {
    display: inline-block; margin: 4px;
    background: #fff; border: 1px solid #e0e0e0;
    color: #555; border-radius: 20px;
    padding: 6px 12px; font-size: 12px; cursor: pointer;
    transition: all 0.15s;
    -webkit-tap-highlight-color: transparent;
}
.suggestion-chip:hover, .suggestion-chip:active { background: #667eea; color: #fff; border-color: #667eea; }
.ai-sidebar { display: block; }

/* ── Mobile (≤767px) ─────────────────────────────────────── */
@media (max-width: 767px) {
    .ai-chat-wrap {
        height: calc(100vh - 120px);
        min-height: 300px;
        border-radius: 8px;
    }
    .ai-chat-header { padding: 10px 12px; }
    .ai-chat-messages { padding: 10px; gap: 10px; }
    .msg-bubble { max-width: 88%; font-size: 13px; padding: 9px 12px; }
    .msg-avatar { width: 30px; height: 30px; font-size: 13px; }
    .ai-welcome { padding: 20px 10px; }
    .ai-welcome .icon { font-size: 40px; }
    .suggestion-chip { font-size: 11px; padding: 5px 10px; }
    .ai-sidebar { display: none; }
    .ai-chat-col { width: 100% !important; padding: 0 !important; }
    .content { padding: 6px !important; }
    .ai-header-title { font-size: 13px !important; }
    .ai-header-sub { display: none; }
    .ai-header-controls .btn { padding: 3px 7px; font-size: 11px; }
    .token-badge { display: none; }
}

/* ── Tablet (768px–991px) ────────────────────────────────── */
@media (min-width: 768px) and (max-width: 991px) {
    .ai-chat-wrap { height: calc(100vh - 140px); }
    .msg-bubble { max-width: 82%; }
    .ai-sidebar { display: none; }
    .ai-chat-col { width: 100% !important; padding-right: 0 !important; }
}
{/literal}
</style>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="ai-chat-col col-xs-12 col-md-9 col-lg-10" style="padding-right:8px">
                <div class="ai-chat-wrap">
                    <!-- Header -->
                    <div class="ai-chat-header">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;font-size:19px;color:#fff;flex-shrink:0">
                                <i class="ion ion-chatbubbles"></i>
                            </div>
                            <div>
                                <div class="ai-header-title" style="color:#fff;font-weight:600;font-size:15px">AI Assistant</div>
                                <div class="ai-header-sub" style="color:#8090a0;font-size:11px">Powered by DeepSeek &bull; ISP Support</div>
                            </div>
                        </div>
                        <div class="ai-header-controls" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                            <span class="token-badge" id="tokenCount">0 tokens</span>
                            <button onclick="clearChat()" class="btn btn-xs btn-default" title="Clear conversation">
                                <i class="fa fa-trash"></i> <span class="hidden-xs">Clear</span>
                            </button>
                            <a href="{$_url}plugin/deepseek_ai/config" class="btn btn-xs btn-default" title="Settings">
                                <i class="fa fa-cog"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="ai-chat-messages" id="chatMessages">
                        <!-- Welcome state -->
                        <div class="ai-welcome" id="welcomeState">
                            <div class="icon"><i class="ion ion-chatbubbles"></i></div>
                            <div style="font-size:15px;font-weight:600;color:#555;margin-bottom:6px">How can I help you?</div>
                            <div style="font-size:13px;margin-bottom:16px">Ask me anything about your ISP billing system</div>
                            <div>
                                <span class="suggestion-chip" onclick="useSuggestion(this)">How to add a PPPoE customer?</span>
                                <span class="suggestion-chip" onclick="useSuggestion(this)">Why are SMS notifications failing?</span>
                                <span class="suggestion-chip" onclick="useSuggestion(this)">How to configure bandwidth limits?</span>
                                <span class="suggestion-chip" onclick="useSuggestion(this)">Explain MikroTik hotspot setup</span>
                                <span class="suggestion-chip" onclick="useSuggestion(this)">How does M-Pesa C2B work?</span>
                                <span class="suggestion-chip" onclick="useSuggestion(this)">Help me troubleshoot offline router</span>
                            </div>
                        </div>
                        <!-- Typing indicator -->
                        <div class="msg-row ai-typing" id="typingIndicator">
                            <div class="msg-avatar ai"><i class="ion ion-chatbubbles"></i></div>
                            <div class="msg-bubble ai" style="padding:14px 18px">
                                <div class="dots">
                                    <span></span><span></span><span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input area -->
                    <div class="ai-chat-input-wrap">
                        <textarea id="chatInput" rows="1" placeholder="Ask anything..." onkeydown="handleKey(event)" oninput="autoResize(this)"></textarea>
                        <button class="ai-send-btn" id="sendBtn" onclick="sendMessage()" title="Send (Enter)">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar — hidden on mobile/tablet via CSS -->
            <div class="ai-sidebar col-md-3 col-lg-2" style="padding-left:8px">
                <div class="panel panel-default" style="border-radius:10px;overflow:hidden">
                    <div class="panel-heading" style="background:linear-gradient(135deg,#1a1a2e,#0f3460);color:#fff;border:none">
                        <b><i class="fa fa-info-circle"></i> About</b>
                    </div>
                    <div class="panel-body" style="font-size:13px;padding:14px">
                        <p style="color:#666">DeepSeek AI can assist with ISP operations and general questions.</p>
                        <hr style="margin:10px 0">
                        <div style="color:#888;font-size:12px">
                            <div><i class="fa fa-check text-success"></i> Customer management</div>
                            <div><i class="fa fa-check text-success"></i> MikroTik configs</div>
                            <div><i class="fa fa-check text-success"></i> PPPoE / Hotspot</div>
                            <div><i class="fa fa-check text-success"></i> Billing &amp; SMS</div>
                            <div><i class="fa fa-check text-success"></i> Network troubleshoot</div>
                        </div>
                        <hr style="margin:10px 0">
                        <a href="{$_url}plugin/deepseek_ai/config" class="btn btn-block btn-default btn-xs">
                            <i class="fa fa-cog"></i> Configuration
                        </a>
                    </div>
                </div>

                <div class="panel panel-default" style="border-radius:10px;overflow:hidden">
                    <div class="panel-heading" style="background:#f8f9fa;border-bottom:1px solid #eee">
                        <b style="color:#555"><i class="fa fa-keyboard-o"></i> Shortcuts</b>
                    </div>
                    <div class="panel-body" style="font-size:12px;color:#888;padding:12px">
                        <div><kbd>Enter</kbd> Send message</div>
                        <div style="margin-top:6px"><kbd>Shift+Enter</kbd> New line</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{literal}
<script>
var chatHistory = [];
var csrfToken = {/literal}'{$csrf_token}'{literal};
var baseUrl = {/literal}'{$_url}'{literal};
var totalTokens = 0;

function now() {
    var d = new Date();
    return d.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
}

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

function handleKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
}

function useSuggestion(el) {
    document.getElementById('chatInput').value = el.textContent;
    sendMessage();
}

function escapeHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function formatMessage(text) {
    // Code blocks
    text = text.replace(/```(\w*)\n?([\s\S]*?)```/g, function(m, lang, code) {
        return '<pre><code>' + escapeHtml(code.trim()) + '</code></pre>';
    });
    // Inline code
    text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
    // Bold
    text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    // Newlines
    text = text.replace(/\n/g, '<br>');
    return text;
}

function appendMessage(role, content, isError) {
    var welcome = document.getElementById('welcomeState');
    if (welcome) welcome.style.display = 'none';

    var typing = document.getElementById('typingIndicator');
    var msgs = document.getElementById('chatMessages');

    var row = document.createElement('div');
    row.className = 'msg-row ' + role;

    var avatar = document.createElement('div');
    avatar.className = 'msg-avatar ' + (role === 'user' ? 'user' : 'ai');
    avatar.innerHTML = role === 'user'
        ? '<i class="fa fa-user"></i>'
        : '<i class="ion ion-chatbubbles"></i>';

    var bubble = document.createElement('div');
    bubble.className = 'msg-bubble ' + (isError ? 'error' : role);

    var inner = document.createElement('div');
    inner.innerHTML = role === 'user' ? escapeHtml(content).replace(/\n/g,'<br>') : formatMessage(content);

    var time = document.createElement('div');
    time.className = 'msg-time';
    time.textContent = now();

    bubble.appendChild(inner);
    bubble.appendChild(time);
    row.appendChild(avatar);
    row.appendChild(bubble);

    msgs.insertBefore(row, typing);
    msgs.scrollTop = msgs.scrollHeight;
}

function setTyping(show) {
    var t = document.getElementById('typingIndicator');
    t.style.display = show ? 'flex' : 'none';
    if (show) {
        var msgs = document.getElementById('chatMessages');
        msgs.scrollTop = msgs.scrollHeight;
    }
}

function sendMessage() {
    var input = document.getElementById('chatInput');
    var msg = input.value.trim();
    if (!msg) return;

    var btn = document.getElementById('sendBtn');
    btn.disabled = true;
    input.value = '';
    input.style.height = 'auto';

    appendMessage('user', msg);
    chatHistory.push({role: 'user', content: msg});
    setTyping(true);

    // Create a streaming AI bubble that will be updated word-by-word
    var streamBubble = createStreamingBubble();

    $.get(baseUrl + 'plugin/deepseek_ai/token', function(data) {
        var token = data.token || csrfToken;
        var formData = new FormData();
        formData.append('message', msg);
        formData.append('history', JSON.stringify(chatHistory.slice(0, -1)));
        formData.append('token', token);

        fetch(baseUrl + 'plugin/deepseek_ai/api', { method: 'POST', body: formData })
            .then(function(response) {
                if (!response.ok) throw new Error('HTTP ' + response.status);
                var reader = response.body.getReader();
                var decoder = new TextDecoder();
                var fullReply = '';

                function read() {
                    reader.read().then(function(result) {
                        if (result.done) { finish(); return; }
                        var text = decoder.decode(result.value, {stream: true});
                        var lines = text.split('\n');
                        for (var i = 0; i < lines.length; i++) {
                            var line = lines[i].trim();
                            if (!line || line === 'data: [DONE]') continue;
                            if (line.indexOf('data: ') === 0) {
                                try {
                                    var obj = JSON.parse(line.substring(6));
                                    if (obj.c) {
                                        fullReply += obj.c;
                                        streamBubble.innerHTML = formatMessage(fullReply);
                                        scrollDown();
                                    }
                                    if (obj.e) {
                                        streamBubble.innerHTML = '<span style="color:#e74c3c">Error: ' + escapeHtml(obj.e) + '</span>';
                                        finish();
                                        return;
                                    }
                                } catch(e) {}
                            }
                        }
                        read(); // next chunk
                    }).catch(function() { finish(); });
                }
                read();

                function finish() {
                    setTyping(false);
                    btn.disabled = false;
                    input.focus();
                    if (fullReply) {
                        chatHistory.push({role: 'assistant', content: fullReply});
                        // Remove streaming bubble, add final formatted one
                        streamBubble.parentElement.remove();
                        appendMessage('ai', fullReply);
                    } else if (!streamBubble.textContent.trim()) {
                        streamBubble.parentElement.remove();
                        appendMessage('ai', 'No response received.', true);
                    }
                }
            })
            .catch(function(err) {
                setTyping(false);
                btn.disabled = false;
                input.focus();
                if (streamBubble.parentElement) streamBubble.parentElement.remove();
                appendMessage('ai', 'Request failed: ' + err.message, true);
            });
    }).fail(function() {
        // Same flow with cached token
        sendMessageFallback(msg, streamBubble);
    });
}

function sendMessageFallback(msg, streamBubble) {
    var formData = new FormData();
    formData.append('message', msg);
    formData.append('history', JSON.stringify(chatHistory.slice(0, -1)));
    formData.append('token', csrfToken);

    fetch(baseUrl + 'plugin/deepseek_ai/api', { method: 'POST', body: formData })
        .then(function(response) {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            var reader = response.body.getReader();
            var decoder = new TextDecoder();
            var fullReply = '';
            function read() {
                reader.read().then(function(result) {
                    if (result.done) { finish(); return; }
                    var text = decoder.decode(result.value, {stream: true});
                    var lines = text.split('\n');
                    for (var i = 0; i < lines.length; i++) {
                        var line = lines[i].trim();
                        if (!line || line === 'data: [DONE]') continue;
                        if (line.indexOf('data: ') === 0) {
                            try {
                                var obj = JSON.parse(line.substring(6));
                                if (obj.c) { fullReply += obj.c; streamBubble.innerHTML = formatMessage(fullReply); scrollDown(); }
                                if (obj.e) { streamBubble.innerHTML = '<span style="color:#e74c3c">Error: ' + escapeHtml(obj.e) + '</span>'; finish(); return; }
                            } catch(e) {}
                        }
                    }
                    read();
                }).catch(function() { finish(); });
            }
            read();
            function finish() {
                setTyping(false);
                document.getElementById('sendBtn').disabled = false;
                if (fullReply) {
                    chatHistory.push({role: 'assistant', content: fullReply});
                    streamBubble.parentElement.remove();
                    appendMessage('ai', fullReply);
                } else if (!streamBubble.textContent.trim()) {
                    streamBubble.parentElement.remove();
                    appendMessage('ai', 'No response received.', true);
                }
            }
        })
        .catch(function(err) {
            setTyping(false);
            document.getElementById('sendBtn').disabled = false;
            if (streamBubble.parentElement) streamBubble.parentElement.remove();
            appendMessage('ai', 'Request failed: ' + err.message, true);
        });
}

// Create a temporary bubble for streaming (replaced by appendMessage when done)
function createStreamingBubble() {
    var welcome = document.getElementById('welcomeState');
    if (welcome) welcome.style.display = 'none';
    var typing = document.getElementById('typingIndicator');
    var msgs = document.getElementById('chatMessages');

    var row = document.createElement('div');
    row.className = 'msg-row ai';
    var avatar = document.createElement('div');
    avatar.className = 'msg-avatar ai';
    avatar.innerHTML = '<i class="ion ion-chatbubbles"></i>';
    var bubble = document.createElement('div');
    bubble.className = 'msg-bubble ai';
    bubble.innerHTML = '';

    row.appendChild(avatar);
    row.appendChild(bubble);
    msgs.insertBefore(row, typing);
    return bubble;
}

function scrollDown() {
    var msgs = document.getElementById('chatMessages');
    msgs.scrollTop = msgs.scrollHeight;
}

function clearChat() {
    if (!confirm('Clear this conversation?')) return;
    chatHistory = [];
    totalTokens = 0;
    document.getElementById('tokenCount').textContent = '0 tokens';
    var msgs = document.getElementById('chatMessages');
    // Remove all message rows, keep welcome + typing
    var rows = msgs.querySelectorAll('.msg-row:not(.ai-typing)');
    rows.forEach(function(r){ r.remove(); });
    var welcome = document.getElementById('welcomeState');
    welcome.style.display = '';
}

document.getElementById('chatInput').focus();
</script>
{/literal}

{include file="sections/footer.tpl"}
