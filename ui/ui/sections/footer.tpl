</section>
        </div>
        <footer class="main-footer">
            <div class="pull-right" id="version" onclick="location.href = '{$_url}community#latestVersion';"></div>
            Mikrotik API by <a href="https://chat.whatsapp.com/HjnLYIEN6h0A0KMXbfNYP5" rel="nofollow noreferrer noopener"
                target="_blank">SpeedRadius</a>, Developed by <a href="https://speedcomwifi.xyz/" rel="nofollow noreferrer noopener"
                target="_blank">Shabran</a>
        </footer>
</div>

{if !empty($config.deepseek_api_key)}
<!-- DeepSeek AI Floating Chat Widget -->
<style>
{literal}
#ds-fab{position:fixed;bottom:24px;right:24px;z-index:9999;width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);border:none;color:#fff;font-size:22px;cursor:pointer;box-shadow:0 4px 16px rgba(102,126,234,0.5);transition:transform .2s,box-shadow .2s;display:flex;align-items:center;justify-content:center}
#ds-fab:hover{transform:scale(1.1);box-shadow:0 6px 20px rgba(102,126,234,0.65)}
#ds-fab .ds-badge{position:absolute;top:-4px;right:-4px;width:16px;height:16px;background:#e74c3c;border-radius:50%;font-size:9px;display:none;align-items:center;justify-content:center;color:#fff;font-weight:700}
#ds-panel{position:fixed;bottom:86px;right:24px;z-index:9998;width:360px;height:500px;background:#fff;border-radius:16px;box-shadow:0 8px 40px rgba(0,0,0,0.18);display:none;flex-direction:column;overflow:hidden;border:1px solid #e8eaf6}
#ds-panel.open{display:flex}
#ds-ph{background:linear-gradient(135deg,#1a1a2e,#0f3460);padding:13px 16px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
#ds-messages{flex:1;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:10px;background:#f8f9fa}
#ds-messages::-webkit-scrollbar{width:4px}
#ds-messages::-webkit-scrollbar-thumb{background:#ccc;border-radius:3px}
.ds-msg{display:flex;align-items:flex-start;gap:8px}
.ds-msg.user{flex-direction:row-reverse}
.ds-av{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0}
.ds-av.ai{background:linear-gradient(135deg,#667eea,#764ba2);color:#fff}
.ds-av.user{background:linear-gradient(135deg,#11998e,#38ef7d);color:#fff}
.ds-bub{max-width:78%;padding:9px 12px;border-radius:12px;font-size:13px;line-height:1.5;word-break:break-word;box-shadow:0 1px 4px rgba(0,0,0,0.07)}
.ds-bub.ai{background:#fff;border-bottom-left-radius:3px;color:#333}
.ds-bub.user{background:linear-gradient(135deg,#667eea,#764ba2);border-bottom-right-radius:3px;color:#fff}
.ds-bub.err{background:#fff5f5;border-left:3px solid #e74c3c;color:#c0392b}
.ds-bub pre{background:#1e1e2e;color:#cdd6f4;padding:8px;border-radius:6px;overflow-x:auto;font-size:11px;margin:6px 0 0}
.ds-bub code{background:rgba(0,0,0,0.07);padding:1px 4px;border-radius:3px;font-size:11px}
.ds-bub.user code{background:rgba(255,255,255,0.2)}
.ds-dots span{display:inline-block;width:6px;height:6px;background:#667eea;border-radius:50%;animation:dsbounce 1.2s infinite}
.ds-dots span:nth-child(2){animation-delay:.2s}.ds-dots span:nth-child(3){animation-delay:.4s}
@keyframes dsbounce{0%,80%,100%{transform:translateY(0)}40%{transform:translateY(-6px)}}
#ds-input-wrap{background:#fff;padding:10px;border-top:1px solid #eee;display:flex;gap:8px;flex-shrink:0;align-items:flex-end}
#ds-input{flex:1;border:2px solid #e9ecef;border-radius:10px;padding:8px 12px;font-size:13px;resize:none;outline:none;max-height:80px;transition:border-color .2s;font-family:inherit}
#ds-input:focus{border-color:#667eea}
#ds-send{width:36px;height:36px;background:linear-gradient(135deg,#667eea,#764ba2);border:none;border-radius:50%;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;transition:transform .15s}
#ds-send:hover{transform:scale(1.1)}
#ds-send:disabled{opacity:.5;cursor:not-allowed}
@media(max-width:480px){
#ds-fab{bottom:16px;right:16px;width:48px;height:48px;font-size:20px}
#ds-panel{width:calc(100vw - 20px);right:10px;bottom:74px;height:72vh;border-radius:12px}
}
{/literal}
</style>

<!-- Floating button -->
<button id="ds-fab" onclick="dsToggle()" title="AI Assistant">
    <i class="ion ion-chatbubbles"></i>
    <span class="ds-badge" id="dsBadge"></span>
</button>

<!-- Chat panel -->
<div id="ds-panel">
    <div id="ds-ph">
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px"><i class="ion ion-chatbubbles"></i></div>
            <div>
                <div style="color:#fff;font-weight:600;font-size:14px">AI Assistant</div>
                <div style="color:#7080a0;font-size:11px">DeepSeek &bull; ISP Support</div>
            </div>
        </div>
        <div style="display:flex;gap:6px;align-items:center">
            <a href="{$_url}plugin/deepseek_ai" target="_blank" style="color:#aaa;font-size:13px;text-decoration:none" title="Open full chat"><i class="fa fa-expand"></i></a>
            <button onclick="dsClear()" style="background:none;border:none;color:#aaa;cursor:pointer;font-size:13px;padding:2px 4px" title="Clear chat"><i class="fa fa-trash"></i></button>
            <button onclick="dsToggle()" style="background:none;border:none;color:#aaa;cursor:pointer;font-size:16px;padding:2px 4px;line-height:1" title="Close">&times;</button>
        </div>
    </div>
    <div id="ds-messages">
        <div id="ds-welcome" style="text-align:center;padding:30px 16px;color:#aaa">
            <div style="font-size:40px;opacity:.3"><i class="ion ion-chatbubbles"></i></div>
            <div style="font-size:13px;margin-top:8px;color:#888">Ask me anything about your ISP system</div>
        </div>
        <div class="ds-msg" id="ds-typing" style="display:none">
            <div class="ds-av ai"><i class="ion ion-chatbubbles"></i></div>
            <div class="ds-bub ai" style="padding:11px 14px"><div class="ds-dots"><span></span><span></span><span></span></div></div>
        </div>
    </div>
    <div id="ds-input-wrap">
        <textarea id="ds-input" rows="1" placeholder="Ask about MikroTik, billing, customers..." onkeydown="dsKey(event)" oninput="dsResize(this)"></textarea>
        <button id="ds-send" onclick="dsSend()" title="Send"><i class="fa fa-paper-plane"></i></button>
    </div>
</div>

{literal}
<script>
var dsHistory = [];
var dsBase = {/literal}'{$_url}'{literal};
var dsOpen = false;
var dsUnread = 0;

function dsToggle() {
    dsOpen = !dsOpen;
    document.getElementById('ds-panel').classList.toggle('open', dsOpen);
    if (dsOpen) {
        dsUnread = 0;
        document.getElementById('dsBadge').style.display = 'none';
        var msgs = document.getElementById('ds-messages');
        msgs.scrollTop = msgs.scrollHeight;
        document.getElementById('ds-input').focus();
    }
}

function dsKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); dsSend(); }
}

function dsResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 80) + 'px';
}

function dsEsc(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function dsFmt(text) {
    text = text.replace(/```(\w*)\n?([\s\S]*?)```/g, function(m,l,c){ return '<pre><code>'+dsEsc(c.trim())+'</code></pre>'; });
    text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
    text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    text = text.replace(/\n/g, '<br>');
    return text;
}

function dsAddMsg(role, content, isErr) {
    document.getElementById('ds-welcome').style.display = 'none';
    var msgs = document.getElementById('ds-messages');
    var typing = document.getElementById('ds-typing');
    var row = document.createElement('div');
    row.className = 'ds-msg ' + role;
    var av = document.createElement('div');
    av.className = 'ds-av ' + (role==='user'?'user':'ai');
    av.innerHTML = role==='user' ? '<i class="fa fa-user"></i>' : '<i class="ion ion-chatbubbles"></i>';
    var bub = document.createElement('div');
    bub.className = 'ds-bub ' + (isErr ? 'err' : role);
    bub.innerHTML = role==='user' ? dsEsc(content).replace(/\n/g,'<br>') : dsFmt(content);
    row.appendChild(av); row.appendChild(bub);
    msgs.insertBefore(row, typing);
    msgs.scrollTop = msgs.scrollHeight;
    if (!dsOpen) {
        dsUnread++;
        var badge = document.getElementById('dsBadge');
        badge.textContent = dsUnread > 9 ? '9+' : dsUnread;
        badge.style.display = 'flex';
    }
}

function dsTyping(show) {
    document.getElementById('ds-typing').style.display = show ? 'flex' : 'none';
    if (show) { var m = document.getElementById('ds-messages'); m.scrollTop = m.scrollHeight; }
}

function dsSend() {
    var input = document.getElementById('ds-input');
    var msg = input.value.trim();
    if (!msg) return;
    var btn = document.getElementById('ds-send');
    btn.disabled = true;
    input.value = ''; input.style.height = 'auto';
    dsAddMsg('user', msg);
    dsHistory.push({role:'user', content:msg});
    dsTyping(true);

    $.get(dsBase + 'plugin/deepseek_ai/token', function(data) {
        var token = (data && data.token) ? data.token : '';
        $.ajax({
            url: dsBase + 'plugin/deepseek_ai/api',
            method: 'POST',
            data: { message: msg, history: JSON.stringify(dsHistory.slice(0,-1)), token: token },
            success: function(res) {
                dsTyping(false); btn.disabled = false;
                if (res.reply) { dsHistory.push({role:'assistant',content:res.reply}); dsAddMsg('ai', res.reply); }
                else if (res.error) { dsAddMsg('ai','Error: '+res.error, true); }
            },
            error: function() { dsTyping(false); btn.disabled = false; dsAddMsg('ai','Request failed. Try again.', true); },
            dataType: 'json'
        });
    }).fail(function() { dsTyping(false); btn.disabled = false; dsAddMsg('ai','Could not reach AI API.', true); });
}

function dsClear() {
    dsHistory = [];
    var msgs = document.getElementById('ds-messages');
    var rows = msgs.querySelectorAll('.ds-msg:not(#ds-typing)');
    rows.forEach(function(r){ r.remove(); });
    document.getElementById('ds-welcome').style.display = '';
}

// Close panel on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && dsOpen) dsToggle();
});
</script>
{/literal}
{/if}

<script src="ui/ui/scripts/jquery.min.js"></script>
<script src="ui/ui/scripts/bootstrap.min.js"></script>
<script src="ui/ui/scripts/adminlte.min.js"></script>
<script src="ui/ui/scripts/plugins/select2.min.js"></script>
<script src="ui/ui/scripts/pace.min.js"></script>
<script src="ui/ui/summernote/summernote.min.js"></script>
<script src="ui/ui/scripts/theme-switcher.js"></script>
<script src="ui/ui/scripts/custom.js"></script>
<script src="ui/ui/scripts/fix-tabs.js"></script>
<script src="ui/ui/scripts/panel-fix.js"></script>
<script src="ui/ui/scripts/comprehensive-panel-fix.js"></script>
<script src="ui/ui/scripts/button-fallback.js"></script>
<script src="ui/ui/scripts/blue-panel-button.js"></script>

<!-- Additional direct fix for settings page -->
<script>
if (window.location.href.indexOf('settings/app') > -1) {
    // Final attempt to force all panels to be visible
    setTimeout(function() {
        document.querySelectorAll('.panel-collapse, [id^="collapse"]').forEach(function(panel) {
            panel.classList.remove('collapse', 'panel-collapse');
            panel.classList.add('panel-body-visible');
            panel.style.display = 'block';
            panel.style.height = 'auto';
            panel.style.visibility = 'visible';
        });
        
        // Replace any existing large refresh button with a smaller one
        var existingButton = document.querySelector('button.btn-lg[style*="position: fixed"]');
        if (existingButton) {
            existingButton.className = 'btn btn-info btn-sm';
            existingButton.style.padding = '5px 10px';
            existingButton.style.fontSize = '12px';
            existingButton.textContent = 'Refresh';
        }
    }, 100);
}
</script>

<script>
    document.getElementById('openSearch').addEventListener('click', function () {
        document.getElementById('searchOverlay').style.display = 'flex';
    });

    document.getElementById('closeSearch').addEventListener('click', function () {
        document.getElementById('searchOverlay').style.display = 'none';
    });

    document.getElementById('searchTerm').addEventListener('keyup', function () {
        let query = this.value;
        $.ajax({
            url: '{$_url}search_user',
            type: 'GET',
            data: { query: query },
            success: function (data) {
                if (data.trim() !== '') {
                    $('#searchResults').html(data).show();
                } else {
                    $('#searchResults').html('').hide();
                }
            }
        });
    });
</script>

{if isset($xfooter)}
    {$xfooter}
{/if}
{literal}
    <script>
        var listAttApi;
        var posAttApi = 0;
        $(document).ready(function() {
            $('.select2').select2({theme: "bootstrap"});
            $('.select2tag').select2({theme: "bootstrap", tags: true});
            var listAtts = document.querySelectorAll(`button[type="submit"]`);
            listAtts.forEach(function(el) {
                if (el.addEventListener) { // all browsers except IE before version 9
                    el.addEventListener("click", function() {
                        $(this).html(
                            `<span class="loading"></span>`
                        );
                        setTimeout(() => {
                            $(this).prop("disabled", true);
                        }, 100);
                    }, false);
                } else {
                    if (el.attachEvent) { // IE before version 9
                        el.attachEvent("click", function() {
                            $(this).html(
                                `<span class="loading"></span>`
                            );
                            setTimeout(() => {
                                $(this).prop("disabled", true);
                            }, 100);
                        });
                    }
                }

            });
            setTimeout(() => {
                listAttApi = document.querySelectorAll(`[api-get-text]`);
                apiGetText();
            }, 500);
        });

        function ask(field, text){
            if (confirm(text)) {
                setTimeout(() => {
                    field.innerHTML = field.innerHTML.replace(`<span class="loading"></span>`, '');
                    field.removeAttribute("disabled");
                }, 5000);
                return true;
            } else {
                setTimeout(() => {
                    field.innerHTML = field.innerHTML.replace(`<span class="loading"></span>`, '');
                    field.removeAttribute("disabled");
                }, 500);
                return false;
            }
        }

        function apiGetText(){
            var el = listAttApi[posAttApi];
            $.get(el.getAttribute('api-get-text'), function(data) {
                el.innerHTML = data;
                posAttApi++;
                if(posAttApi < listAttApi.length){
                    apiGetText();
                }
            });

        }

        function setKolaps() {
            var kolaps = getCookie('kolaps');
            if (kolaps) {
                setCookie('kolaps', false, 30);
            } else {
                setCookie('kolaps', true, 30);
            }
            return true;
        }

        function setCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
        $("[data-toggle=popover]").popover();

        // Dark Mode Toggle Functionality
        function initDarkModeToggle() {
            const toggleContainer = document.querySelector('.toggle-container');
            const toggleIcon = document.getElementById('toggleIcon');
            const body = document.body;
            
            // Check for saved dark mode preference or default to light mode
            const isDarkMode = localStorage.getItem('darkMode') === 'true';
            
            // Apply saved theme on page load
            if (isDarkMode) {
                body.classList.add('dark-mode');
                toggleIcon.textContent = '🌙';
            } else {
                body.classList.remove('dark-mode');
                toggleIcon.textContent = '🌞';
            }
            
            // Add click event listener to toggle
            if (toggleContainer) {
                toggleContainer.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Toggle dark mode class
                    body.classList.toggle('dark-mode');
                    
                    // Update icon and save preference
                    if (body.classList.contains('dark-mode')) {
                        toggleIcon.textContent = '🌙';
                        localStorage.setItem('darkMode', 'true');
                    } else {
                        toggleIcon.textContent = '🌞';
                        localStorage.setItem('darkMode', 'false');
                    }
                });
            }
        }
        
        // Initialize dark mode toggle when document is ready
        $(document).ready(function() {
            initDarkModeToggle();
        });
    </script>
{/literal}

</body>

</html>
