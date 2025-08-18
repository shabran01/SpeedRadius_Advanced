<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{Lang::T('Login')} - {$_c['CompanyName']}</title>
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
        #loginBtn {
            background: linear-gradient(to right, #f29f67, #ff8c66);
            transition: background 0.3s ease-in-out;
        }

        #loginBtn:hover {
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
            <p class="text-[#1e1e2c] dark:text-white font-bold text-lg text-center mb-6">{Lang::T('Enter Admin Area')}</p>
            <form action="{$_url}admin/post" method="post" id="loginForm">
                <div class="mb-4">
                    <label class="block text-[#1e1e2c] dark:text-white font-bold mb-2" for="username">
                        {Lang::T('Username')}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-[#1e1e2c] dark:text-white"></i>
                        </div>
                        <input class="form-input w-full pl-10 pr-3 py-2 rounded-lg border border-[#1e1e2c] dark:border-white focus:outline-none focus:ring-2 focus:ring-[#f29f67] focus:border-transparent transition-all duration-300 dark:bg-gray-700 dark:text-white" type="text" name="username" placeholder="{Lang::T('Username')}" required>
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-[#1e1e2c] dark:text-white font-bold mb-2" for="password">
                        {Lang::T('Password')}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-[#1e1e2c] dark:text-white"></i>
                        </div>
                        <input class="form-input w-full pl-10 pr-3 py-2 rounded-lg border border-[#1e1e2c] dark:border-white focus:outline-none focus:ring-2 focus:ring-[#f29f67] focus:border-transparent transition-all duration-300 dark:bg-gray-700 dark:text-white" type="password" name="password" placeholder="{Lang::T('Password')}" required>
                    </div>
                </div>
                <button type="submit" class="w-full bg-[#f29f67] hover:bg-[#f29f67]/80 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-all duration-300 relative" id="loginBtn">
                    <span class="icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </span>
                    <div class="loader absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 hidden">
                        <div class="w-6 h-6 border-4 border-white border-t-4 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </button>
                <div class="mt-4 text-center">
                    <a href="{$_url}admin/forgot-password" class="text-[#f29f67] hover:text-[#f29f67]/80 text-sm transition-colors duration-300">
                        <i class="fas fa-key mr-1"></i>
                        {Lang::T('Forgot Password')}?
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#loginForm').submit(function() {
                $('#loginBtn .loader').removeClass('hidden');
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
    </script>
</body>

</html>
