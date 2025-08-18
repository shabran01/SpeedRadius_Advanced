<?php
/* Smarty version 4.5.3, created on 2025-08-17 10:12:48
  from '/var/www/html/ISP/ui/ui/sections/footer.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a180f0d55858_89984786',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '473d3933e5af1e842c10a2baee565060310556e5' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/sections/footer.tpl',
      1 => 1755414537,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a180f0d55858_89984786 (Smarty_Internal_Template $_smarty_tpl) {
?></section>
        </div>
        <footer class="main-footer">
            <div class="pull-right" id="version" onclick="location.href = '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
community#latestVersion';"></div>
            Mikrotik API by <a href="https://chat.whatsapp.com/HjnLYIEN6h0A0KMXbfNYP5" rel="nofollow noreferrer noopener"
                target="_blank">SpeedRadius</a>, Developed by <a href="https://speedcomwifi.xyz/" rel="nofollow noreferrer noopener"
                target="_blank">Shabran</a>
        </footer>
</div>
<?php echo '<script'; ?>
 src="ui/ui/scripts/jquery.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="ui/ui/scripts/bootstrap.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="ui/ui/scripts/adminlte.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="ui/ui/scripts/plugins/select2.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="ui/ui/scripts/pace.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="ui/ui/summernote/summernote.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="ui/ui/scripts/theme-switcher.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="ui/ui/scripts/custom.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="ui/ui/scripts/fix-tabs.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="ui/ui/scripts/panel-fix.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="ui/ui/scripts/comprehensive-panel-fix.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="ui/ui/scripts/button-fallback.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="ui/ui/scripts/blue-panel-button.js"><?php echo '</script'; ?>
>

<!-- Additional direct fix for settings page -->
<?php echo '<script'; ?>
>
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
<?php echo '</script'; ?>
>

<?php echo '<script'; ?>
>
    document.getElementById('openSearch').addEventListener('click', function () {
        document.getElementById('searchOverlay').style.display = 'flex';
    });

    document.getElementById('closeSearch').addEventListener('click', function () {
        document.getElementById('searchOverlay').style.display = 'none';
    });

    document.getElementById('searchTerm').addEventListener('keyup', function () {
        let query = this.value;
        $.ajax({
            url: '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
search_user',
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
<?php echo '</script'; ?>
>

<?php if ((isset($_smarty_tpl->tpl_vars['xfooter']->value))) {?>
    <?php echo $_smarty_tpl->tpl_vars['xfooter']->value;?>

<?php }?>

    <?php echo '<script'; ?>
>
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
    <?php echo '</script'; ?>
>


</body>

</html>
<?php }
}
