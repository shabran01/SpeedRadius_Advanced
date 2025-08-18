<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{Lang::T('Admin Forgot Password')} - {$_c['CompanyName']}</title>
    <link rel="shortcut icon" href="ui/ui/images/logo.png" type="image/x-icon" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html.dark {
            background: linear-gradient(135deg, #434343, #1e1e1e);
        }

        html.light {
            background: linear-gradient(135deg, #e9ecef, #f8f9fa);
        }

        body {
            font-family: 'Mulish', sans-serif;
        }

        /* Button Styling */
        .btn-primary {
            background: linear-gradient(to right, #f29f67, #ff8c66);
            transition: background 0.3s ease-in-out;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #ff8c66, #ff6b66);
        }

        /* Input Fields */
        .form-input {
            background: #fff;
            transition: all 0.3s ease-in-out;
        }

        .form-input:focus {
            box-shadow: 0 4px 20px rgba(242, 159, 103, 0.4);
        }

        .loader {
            border-top-color: transparent;
            animation: spin 0.8s ease infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen px-4">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <div class="mb-6 text-center relative">
            <div class="absolute top-0 right-0 p-2">
                <button id="modeSwitch" class="text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white rounded-full p-1">
                    <svg id="lightIcon" class="hidden dark:block w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 11a1 1 0 100-2 1 1 0 000 2zM12 11a1 1 0 100-2 1 1 0 000 2zM16 11a1 1 0 100-2 1 1 0 000 2zM17.657 15.243a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 17H1a1 1 0 100 2h17a1 1 0 100-2zM11 17a1 1 0 10-2 0v1a1 1 0 102 0v-1zM4.343 15.243a1 1 0 011.414-1.414l.707.707a1 1 0 01-1.414 1.414l-.707-.707z" />
                    </svg>
                    <svg id="darkIcon" class="block dark:hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                    </svg>
                </button>
            </div>
            <h1 class="text-3xl font-bold text-[#1e1e2c] dark:text-white">{$_c['CompanyName']}</h1>
        </div>
        
        <div>
            {if isset($notify)}
            <div class="mb-4 p-4 rounded-md {if $notify_t == 's'}bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-400{else}bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-400{/if}">
                <p>{$notify}</p>
            </div>
            {/if}

            <form action="{$_url}admin/forgot-password&step={$step+1}" method="post" id="forgotForm">
                {if $step == 1}
                    <!-- Step 1: Verification Code Input -->
                    <div class="mb-4 text-center">
                        <i class="fas fa-shield-alt text-4xl text-[#f29f67] mb-2"></i>
                        <p class="text-[#1e1e2c] dark:text-white font-bold text-lg">{Lang::T('Verification Code')}</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">{Lang::T('Enter the verification code sent to your email/WhatsApp')}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[#1e1e2c] dark:text-white font-bold mb-2">
                            {Lang::T('Username')}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-[#1e1e2c] dark:text-white"></i>
                            </div>
                            <input class="form-input w-full pl-10 pr-3 py-2 rounded-lg border border-[#1e1e2c] dark:border-white focus:outline-none focus:ring-2 focus:ring-[#f29f67] focus:border-transparent transition-all duration-300 dark:bg-gray-700 dark:text-white bg-gray-100" type="text" name="username" value="{$username}" readonly>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-[#1e1e2c] dark:text-white font-bold mb-2">
                            {Lang::T('Verification Code')}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-[#1e1e2c] dark:text-white"></i>
                            </div>
                            <input class="form-input w-full pl-10 pr-3 py-2 rounded-lg border border-[#1e1e2c] dark:border-white focus:outline-none focus:ring-2 focus:ring-[#f29f67] focus:border-transparent transition-all duration-300 dark:bg-gray-700 dark:text-white" type="text" name="otp_code" placeholder="{Lang::T('Enter verification code')}" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-all duration-300" id="validateBtn">
                        <i class="fas fa-check-circle mr-2"></i>
                        {Lang::T('Validate')}
                    </button>

                    <div class="mt-4 text-center">
                        <a href="{$_url}admin/forgot-password&step=-1" class="text-[#f29f67] hover:text-[#f29f67]/80 text-sm transition-colors duration-300">
                            <i class="fas fa-times mr-1"></i>
                            {Lang::T('Cancel')}
                        </a>
                    </div>

                {elseif $step == 2}
                    <!-- Step 2: Success - Show New Password -->
                    <div class="mb-4 text-center">
                        <i class="fas fa-check-circle text-4xl text-green-500 mb-2"></i>
                        <p class="text-[#1e1e2c] dark:text-white font-bold text-lg">{Lang::T('Success')}</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">{Lang::T('Password has been reset successfully')}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[#1e1e2c] dark:text-white font-bold mb-2">
                            {Lang::T('Username')}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-[#1e1e2c] dark:text-white"></i>
                            </div>
                            <input class="form-input w-full pl-10 pr-3 py-2 rounded-lg border border-[#1e1e2c] dark:border-white focus:outline-none focus:ring-2 focus:ring-[#f29f67] focus:border-transparent transition-all duration-300 dark:bg-gray-700 dark:text-white bg-gray-100" type="text" value="{$username}" readonly>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-[#1e1e2c] dark:text-white font-bold mb-2">
                            {Lang::T('Your new password is')}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-[#1e1e2c] dark:text-white"></i>
                            </div>
                            <input class="form-input w-full pl-10 pr-10 py-2 rounded-lg border border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300 dark:bg-gray-700 dark:text-white bg-green-50 text-green-700 font-mono text-lg" type="text" value="{$new_password}" readonly onclick="this.select()">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" onclick="copyToClipboard('{$new_password}')" class="text-green-600 hover:text-green-800 focus:outline-none" title="{Lang::T('Copy to clipboard')}">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            {Lang::T('Use this password to login, then change it from your profile settings')}
                        </p>
                    </div>

                    <a href="{$_url}admin" class="btn-primary w-full text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-all duration-300 text-center block">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        {Lang::T('Back to Login')}
                    </a>

                {else}
                    <!-- Step 0: Username Input -->
                    <div class="mb-4 text-center">
                        <i class="fas fa-key text-4xl text-[#f29f67] mb-2"></i>
                        <p class="text-[#1e1e2c] dark:text-white font-bold text-lg">{Lang::T('Admin Forgot Password')}</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">{Lang::T('Enter your admin username to receive a verification code')}</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-[#1e1e2c] dark:text-white font-bold mb-2">
                            {Lang::T('Username')}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-[#1e1e2c] dark:text-white"></i>
                            </div>
                            <input class="form-input w-full pl-10 pr-3 py-2 rounded-lg border border-[#1e1e2c] dark:border-white focus:outline-none focus:ring-2 focus:ring-[#f29f67] focus:border-transparent transition-all duration-300 dark:bg-gray-700 dark:text-white" type="text" name="username" placeholder="{Lang::T('Enter your admin username')}" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-all duration-300" id="resetBtn">
                        <i class="fas fa-paper-plane mr-2"></i>
                        {Lang::T('Send Verification Code')}
                    </button>

                    <div class="mt-4 text-center">
                        <a href="{$_url}admin" class="text-[#f29f67] hover:text-[#f29f67]/80 text-sm transition-colors duration-300">
                            <i class="fas fa-arrow-left mr-1"></i>
                            {Lang::T('Back to Login')}
                        </a>
                    </div>
                {/if}
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#forgotForm').submit(function() {
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');
            });

            const modeSwitch = document.getElementById('modeSwitch');
            const lightIcon = document.getElementById('lightIcon');
            const darkIcon = document.getElementById('darkIcon');
            const html = document.documentElement;

            modeSwitch.addEventListener('click', () => {
                if (html.classList.contains('dark')) {
                    html.classList.remove('dark');
                    html.classList.add('light');
                    lightIcon.classList.add('hidden');
                    darkIcon.classList.remove('hidden');
                } else {
                    html.classList.remove('light');
                    html.classList.add('dark');
                    lightIcon.classList.remove('hidden');
                    darkIcon.classList.add('hidden');
                }
            });
        });

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show temporary success message
                const button = event.target.closest('button');
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.className = button.className.replace('text-green-600', 'text-green-800');
                
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.className = button.className.replace('text-green-800', 'text-green-600');
                }, 1000);
            });
        }
    </script>
</body>

</html>
