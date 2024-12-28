<?php
/* Smarty version 4.5.3, created on 2024-12-23 06:53:29
  from '/var/www/html/snootylique/ui/ui/customer/footer.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6768deb9671a86_10911032',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '48cb7abf0a99963be215e4d25e5949530fcf568b' => 
    array (
      0 => '/var/www/html/snootylique/ui/ui/customer/footer.tpl',
      1 => 1734662748,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6768deb9671a86_10911032 (Smarty_Internal_Template $_smarty_tpl) {
?></section>
</div>
<?php if ((isset($_smarty_tpl->tpl_vars['_c']->value['CompanyFooter']))) {?>
    <footer class="main-footer">
        <?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyFooter'];?>

        <div class="pull-right">
            <a href="javascript:showPrivacy()">Privacy</a>
            &bull;
            <a href="javascript:showTaC()">T &amp; C</a>
        </div>
    </footer>
<?php } else { ?>
    <footer class="main-footer">
        PHPNuxBill by <a href="https://github.com/hotspotbilling/phpnuxbill" rel="nofollow noreferrer noopener"
            target="_blank">iBNuX</a>, Theme by <a href="https://adminlte.io/" rel="nofollow noreferrer noopener"
            target="_blank">AdminLTE</a>
        <div class="pull-right">
            <a href="javascript:showPrivacy()">Privacy</a>
            &bull;
            <a href="javascript:showTaC()">T &amp; C</a>
        </div>
    </footer>
<?php }?>
</div>


<!-- Modal -->
<div class="modal fade" id="HTMLModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="HTMLModal_konten"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">&times;</button>
            </div>
        </div>
    </div>
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
 src="ui/ui/scripts/custom.js?v=2"><?php echo '</script'; ?>
>

<?php if ((isset($_smarty_tpl->tpl_vars['xfooter']->value))) {?>
    <?php echo $_smarty_tpl->tpl_vars['xfooter']->value;?>

<?php }?>

<?php if ($_smarty_tpl->tpl_vars['_c']->value['tawkto'] != '') {?>
    <!--Start of Tawk.to Script-->
    <?php echo '<script'; ?>
 type="text/javascript">
        var isLoggedIn = false;
        var Tawk_API = {
            onLoad: function() {
                Tawk_API.setAttributes({
                    'username'    : '<?php echo $_smarty_tpl->tpl_vars['_user']->value['username'];?>
',
                    'service'    : '<?php echo $_smarty_tpl->tpl_vars['_user']->value['service_type'];?>
',
                    'balance'    : '<?php echo $_smarty_tpl->tpl_vars['_user']->value['balance'];?>
',
                    'account'    : '<?php echo $_smarty_tpl->tpl_vars['_user']->value['account_type'];?>
',
                    'phone'    : '<?php echo $_smarty_tpl->tpl_vars['_user']->value['phonenumber'];?>
'
                }, function(error) {
                    console.log(error)
                });

                }
            };
            var Tawk_LoadStart = new Date();
            Tawk_API.visitor = {
                name: '<?php echo $_smarty_tpl->tpl_vars['_user']->value['fullname'];?>
',
                email: '<?php echo $_smarty_tpl->tpl_vars['_user']->value['email'];?>
',
                phone: '<?php echo $_smarty_tpl->tpl_vars['_user']->value['phonenumber'];?>
'
            };
            (function() {
                var s1 = document.createElement("script"),
                    s0 = document.getElementsByTagName("script")[0];
                s1.async = true;
                s1.src = 'https://embed.tawk.to/<?php echo $_smarty_tpl->tpl_vars['_c']->value['tawkto'];?>
';
                s1.charset = 'UTF-8';
                s1.setAttribute('crossorigin', '*');
                s0.parentNode.insertBefore(s1, s0);
            })();
        <?php echo '</script'; ?>
>
        <!--End of Tawk.to Script-->
    <?php }?>

    <?php echo '<script'; ?>
>
        const toggleIcon = document.getElementById('toggleIcon');
        const body = document.body;
        const savedMode = localStorage.getItem('mode');
        if (savedMode === 'dark') {
            body.classList.add('dark-mode');
            toggleIcon.textContent = '🌜';
        }

        function setMode(mode) {
            if (mode === 'dark') {
                body.classList.add('dark-mode');
                toggleIcon.textContent = '🌜';
            } else {
                body.classList.remove('dark-mode');
                toggleIcon.textContent = '🌞';
            }
        }

        toggleIcon.addEventListener('click', () => {
            if (body.classList.contains('dark-mode')) {
                setMode('light');
                localStorage.setItem('mode', 'light');
            } else {
                setMode('dark');
                localStorage.setItem('mode', 'dark');
            }
        });
    <?php echo '</script'; ?>
>



    <?php echo '<script'; ?>
>
        var listAtts = document.querySelectorAll(`[api-get-text]`);
        listAtts.forEach(function(el) {
            $.get(el.getAttribute('api-get-text'), function(data) {
                el.innerHTML = data;
            });
        });
        $(document).ready(function() {
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
                $(function() {
                    $('[data-toggle="tooltip"]').tooltip()
                })
            });
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
    <?php echo '</script'; ?>
>

<?php echo '<script'; ?>
>
setCookie('user_language', '<?php echo $_smarty_tpl->tpl_vars['user_language']->value;?>
', 365);
<?php echo '</script'; ?>
>
</body>

</html><?php }
}
