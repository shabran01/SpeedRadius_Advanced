<?php
include '../../config.php';
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Function to get a setting value
function getSettingValue($mysqli, $setting) {
    $query = $mysqli->prepare("SELECT value FROM tbl_appconfig WHERE setting = ?");
    $query->bind_param("s", $setting);
    $query->execute();
    $result = $query->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['value'];
    }
    return '';
}

// Fetch hotspot title and description from tbl_appconfig
$hotspotTitle = getSettingValue($mysqli, 'hotspot_title');
$description = getSettingValue($mysqli, 'description');
$phone = getSettingValue($mysqli, 'phone');
$company = getSettingValue($mysqli, 'CompanyName');


// Fetch router name and router ID from tbl_appconfig
$routerName = getSettingValue($mysqli, 'router_name');
$routerId = getSettingValue($mysqli, 'router_id');

// Fetch available plans
$planQuery = "SELECT id, name_plan, price, validity, validity_unit FROM tbl_plans WHERE routers = ? AND type = 'Hotspot'";
$planStmt = $mysqli->prepare($planQuery);
$planStmt->bind_param("s", $routerName);
$planStmt->execute();
$planResult = $planStmt->get_result();

// Initialize HTML content variable
$htmlContent = "<!DOCTYPE html>\n";
$htmlContent .= "<html lang=\"en\">\n";
$htmlContent .= "<head>\n";
$htmlContent .= "    <meta charset=\"UTF-8\">\n";
$htmlContent .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
$htmlContent .= "    <title>" . htmlspecialchars($hotspotTitle) . " Hotspot Template - Index</title>\n";
$htmlContent .= "    <script src=\"https://cdn.tailwindcss.com\"></script>\n";
$htmlContent .= "    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css\">\n";
$htmlContent .= "    <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/glider-js@1.7.7/glider.min.css\" />\n";
$htmlContent .= "    <script src=\"https://cdn.jsdelivr.net/npm/glider-js@1.7.7/glider.min.js\"></script>\n";
$htmlContent .= "    <link rel=\"preconnect\" href=\"https://cdn.jsdelivr.net\">\n";
$htmlContent .= "    <link rel=\"preconnect\" href=\"https://cdnjs.cloudflare.com\" crossorigin>\n";
$htmlContent .= "    <link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\">\n";
$htmlContent .= "</head>\n";


$htmlContent .= "<body class=\"font-sans antialiased text-gray-900\">\n";
$htmlContent .= "    <!-- Sticky Header -->\n";
$htmlContent .= "    <header class=\"bg-[#7851a9] text-white fixed w-full z-10\">\n";
$htmlContent .= "        <div class=\"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5\">\n";
$htmlContent .= "            <div class=\"flex items-center justify-between h-16\">\n";
$htmlContent .= "                <!-- Logo and title area -->\n";
$htmlContent .= "                <div class=\"flex items-center\">\n";
$htmlContent .= "                    <img src=\"logo.png\" alt=\"Your Company Logo\" class=\"h-8 w-8 mr-2\">\n";
$htmlContent .= "                    <h1 class=\"text-xl font-bold\">" . htmlspecialchars($hotspotTitle) . " </h1>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "                <!-- Navigation Links -->\n";
$htmlContent .= "                <div class=\"block\">\n";
$htmlContent .= "                <div>\n";
$htmlContent .= "                    <button class=\"bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded\" onclick=\"redeemVoucher()\">Redeem Voucher</button>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n"; 
$htmlContent .= "    </header>\n";


$htmlContent .= "                <div>\n";
$htmlContent .= "                    <button class=\"bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded\" onclick=\"redeemVoucher()\">Redeem Voucher</button>\n";
$htmlContent .= "                </div>\n";

$htmlContent .= "    <!-- Main content -->\n";
$htmlContent .= "    <main class=\"pt-24\">\n";
$htmlContent .= "        <section class=\"bg-white\">\n";
$htmlContent .= "            <div class=\"max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8\">\n";
$htmlContent .= "                <h2 class=\"text-3xl font-extrabold text-gray-900 mb-6\">" . htmlspecialchars($description) . "</h2>\n";
$htmlContent .= "                <!-- How to Purchase Section -->\n";
$htmlContent .= "                <div class=\"text-center justify-center items-center mx-auto\">\n";
$htmlContent .= "                    <div class=\"card3 bg-black p-6 rounded-lg shadow-lg\">\n";
$htmlContent .= "                        <h6 class=\"texth text-white\">How To Purchase:-.</h6>\n";
$htmlContent .= "                        <h6 class=\"text-white text-left\">1. Tap on your preferred package.</h6>\n";
$htmlContent .= "                        <h6 class=\"text-white text-left\">2. Enter your phone number.</h6>\n";
$htmlContent .= "                        <h6 class=\"text-white text-left\">3. Click \"PAY NOW\".</h6>\n";
$htmlContent .= "                        <h6 class=\"text-white text-left\">4. Enter your M-Pesa PIN, wait for 30 seconds for M-Pesa authentication.</h6>\n";
$htmlContent .= "                        <p id=\"paytype\" class=\"text-center text-white mt-2\"></p>\n";
$htmlContent .= "                        <!-- <h1 id=\"paybill\" class=\"text-5xl font-serif\"></h1> -->\n";
$htmlContent .= "                    </div>\n";

$htmlContent .= "                </div>\n";

$htmlContent .= "                <!-- Free Trial Section -->\n";
$htmlContent .= "                <div class=\"text-center mt-10\">\n";
$htmlContent .= "                    " . (($trial == 'yes') ? "<a href=\"${link_login_only}?dst=${link_orig_esc}&username=T-${mac_esc}\" class=\"btn btn-logout text-space\">FREE TRIAL 5 MINS</a>" : "") . "\n";
$htmlContent .= "                </div>\n";

$htmlContent .= "                <!-- Pricing Section -->\n";
$htmlContent .= "                <div class=\"mt-10\">\n";
$htmlContent .= "                    <div class=\"text-center\">\n";
$htmlContent .= "                        <h3 class=\"text-2xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-3xl sm:leading-9\">\n";
$htmlContent .= "                            CHECK OUR PRICING\n";
$htmlContent .= "                        </h3>\n";
$htmlContent .= "                        <p class=\"mt-4 max-w-2xl text-xl leading-7 text-gray-500 lg:mx-auto\">\n";
$htmlContent .= "                            Choose the plan that fits your needs.\n";
$htmlContent .= "                        </p>\n";
$htmlContent .= "                    </div>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </section>\n";
$htmlContent .= "    </main>\n";



$htmlContent .= "<div class=\"mt-10 max-w-7xl mx-auto grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-5\">\n";

while ($plan = $planResult->fetch_assoc()) {
    $htmlContent .= "    <div class=\"flex flex-col rounded-lg shadow-xl overflow-hidden transform transition duration-500 hover:scale-105\">\n";
    $htmlContent .= "        <div class=\"px-4 py-5 bg-gradient-to-tr from-green-50 to-green-200 text-center\">\n";
    $htmlContent .= "            <span class=\"inline-flex px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase bg-green-800 text-green-50\">\n";
    $htmlContent .=                  htmlspecialchars($plan['name_plan']) . "\n";
    $htmlContent .= "            </span>\n";
    $htmlContent .= "            <div class=\"mt-4 text-4xl leading-none font-extrabold text-green-800\">\n";
    $htmlContent .= "                <span class=\"text-lg font-medium text-green-600\">ksh</span>\n";
    $htmlContent .=                  htmlspecialchars($plan['price']) . "\n";
    $htmlContent .= "            </div>\n";
    $htmlContent .= "            <p class=\"mt-2 text-md leading-5 text-green-700 text-center\">\n";
    $htmlContent .=                  htmlspecialchars($plan['validity']) . " " . htmlspecialchars($plan['validity_unit']) . " Unlimited\n";
    $htmlContent .= "            </p>\n";
    $htmlContent .= "        </div>\n";
    $htmlContent .= "        <div class=\"px-4 pt-4 pb-6 bg-green-500 text-center\">\n";
    $htmlContent .= "            <a href=\"#\" class=\"inline-block text-green-800 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-opacity-50 transform transition duration-150 ease-in-out rounded-lg font-semibold px-3 py-2 text-xs shadow-lg cursor-pointer\"\n";
    $htmlContent .= "               onclick=\"handlePhoneNumberSubmission(this.getAttribute('data-plan-id'), this.getAttribute('data-router-id')); return false;\" data-plan-id=\"" . $plan['id'] . "\" data-router-id=\"" . $routerId . "\">\n";
    $htmlContent .= "                Click Here To Connect\n";
    $htmlContent .= "            </a>\n";
    $htmlContent .= "        </div>\n";
    $htmlContent .= "    </div>\n";
}

$htmlContent .= "</div>\n";








$htmlContent .= "<!-- FAQ Section -->\n";
$htmlContent .= "<div class=\"mt-10 mx-auto px-4 sm:px-6 lg:px-8\">\n";
$htmlContent .= "    <div class=\"text-center\">\n";
$htmlContent .= "        <h3 class=\"text-2xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-3xl sm:leading-9\">\n";
$htmlContent .= "        Support Number \n";
$htmlContent .= "        </h3>\n";
$htmlContent .= "        <p class=\"mt-4 max-w-2xl text-xl leading-7 text-gray-500 lg:mx-auto\">\n";
$htmlContent .= "            Call Admin : 0708451707.\n";
$htmlContent .= "        </p>\n";
$htmlContent .= "    </div>\n";
$htmlContent .= "    <div class=\"mt-6\">\n";
$htmlContent .= "        <dl class=\"space-y-6\">\n";



$htmlContent .= "        </dl>\n";
$htmlContent .= "    </div>\n";
$htmlContent .= "</div>\n";


$htmlContent .= "<div class=\"container mx-auto px-4\">\n";
$htmlContent .= "    <div class=\"max-w-md mx-auto bg-white rounded-lg overflow-hidden md:max-w-lg\">\n";
$htmlContent .= "        <div class=\"md:flex\">\n";
$htmlContent .= "            <div class=\"w-full p-5\">\n";
$htmlContent .= "                <div class=\"text-center\">\n";
$htmlContent .= "                    <h3 class=\"text-2xl text-gray-900\">Already Have an Active Package?</h3>\n";
$htmlContent .= "                </div>\n";




$htmlContent .= "                <form id=\"loginForm\" class=\"form\" name=\"login\" action=\"$(link-login-only)\" method=\"post\" $(if chap-id)onSubmit=\"return doLogin()\"$(endif)>\n";
$htmlContent .= "                    <input type=\"hidden\" name=\"dst\" value=\"$(link-orig)\" />\n";
$htmlContent .= "                    <input type=\"hidden\" name=\"popup\" value=\"true\" />\n";
$htmlContent .= "                    <div class=\"mb-4\">\n";
$htmlContent .= "                        <label class=\"block text-gray-700 text-sm font-bold mb-2\" for=\"username\">Username</label>\n";
$htmlContent .= "                        <input id=\"usernameInput\" class=\"shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline\" name=\"username\" type=\"text\" value=\"\" placeholder=\"Username\">\n";
$htmlContent .= "                    </div>\n";
$htmlContent .= "                    <div class=\"mb-6\">\n";
$htmlContent .= "                        <label class=\"block text-gray-700 text-sm font-bold mb-2\" for=\"password\">Password</label>\n";
$htmlContent .= "                        <input id=\"passwordInput\" class=\"shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline\" name=\"password\" type=\"password\" placeholder=\"******************\">\n";
$htmlContent .= "                    </div>\n";
$htmlContent .= "                    <div class=\"flex items-center justify-between\">\n";
$htmlContent .= "                        <button id=\"submitBtn\" class=\"bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline\" type=\"button\">\n";
$htmlContent .= "                            Click Here To Connect\n";
$htmlContent .= "                        </button>\n";
$htmlContent .= "                    </div>\n";
$htmlContent .= "                </form>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";
$htmlContent .= "</div>\n";

// Add the FREE TRIAL section



$htmlContent .= "<script>\n";
$htmlContent .= "document.addEventListener('DOMContentLoaded', function() {\n";
$htmlContent .= "    function autofillLogin() {\n";
$htmlContent .= "        var phoneNumber = '2547xxxxxxx';\n";
$htmlContent .= "        var password = '1234';\n";
$htmlContent .= "        document.querySelector('input[name=\"username\"]').value = phoneNumber;\n";
$htmlContent .= "        document.querySelector('input[name=\"password\"]').value = password;\n";
$htmlContent .= "        setTimeout(function() {\n";
$htmlContent .= "            document.querySelector('button[type=\"submit\"]').click();\n";
$htmlContent .= "        }, 15000);\n";
$htmlContent .= "    }\n";
$htmlContent .= "    autofillLogin();\n";
$htmlContent .= "});\n";
$htmlContent .= "</script>\n";





$htmlContent .= "<script>\n";
$htmlContent .= "function toggleFAQ(faqId) {\n";
$htmlContent .= "    var element = document.getElementById(faqId);\n";
$htmlContent .= "    if (element.style.display === \"block\") {\n";
$htmlContent .= "        element.style.display = \"none\";\n";
$htmlContent .= "    } else {\n";
$htmlContent .= "        element.style.display = \"block\";\n";
$htmlContent .= "    }\n";
$htmlContent .= "}\n";
$htmlContent .= "</script>\n";









$htmlContent .= "</section>\n";
$htmlContent .= "</main>\n";

$htmlContent .= "<!-- Footer -->\n";
$htmlContent .= "<footer class=\"bg-blue-900 text-white\">\n";
$htmlContent .= "    <div class=\"max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8\">\n";

$htmlContent .= "<p class=\"mt-8 text-base leading-6 text-gray-400 md:mt-0 md:order-1\">\n";
$htmlContent .= "                &copy; 2024 " . htmlspecialchars($company) . " All rights reserved.\n";
$htmlContent .= "            </p>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";
$htmlContent .= "</footer>\n";


$htmlContent .= "<script src=\"https://cdn.jsdelivr.net/npm/sweetalert2@11\"></script>\n";

$htmlContent .= "<script>\n";
$htmlContent .= "    function formatPhoneNumber(phoneNumber) {\n";
$htmlContent .= "        if (phoneNumber.startsWith('+')) {\n";
$htmlContent .= "            phoneNumber = phoneNumber.substring(1);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        if (phoneNumber.startsWith('0')) {\n";
$htmlContent .= "            phoneNumber = '254' + phoneNumber.substring(1);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        if (phoneNumber.match(/^(7|1)/)) {\n";
$htmlContent .= "            phoneNumber = '254' + phoneNumber;\n";
$htmlContent .= "        }\n";
$htmlContent .= "        return phoneNumber;\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";
$htmlContent .= "    function handlePhoneNumberSubmission(planId, routerId) {\n";
$htmlContent .= "        Swal.fire({\n";
$htmlContent .= "            title: 'Enter Your Phone Number',\n";
$htmlContent .= "            input: 'text',\n";
$htmlContent .= "            inputPlaceholder: 'Your phone number here',\n";
$htmlContent .= "            inputAttributes: {\n";
$htmlContent .= "                autocapitalize: 'off'\n";
$htmlContent .= "            },\n";
$htmlContent .= "            showCancelButton: true,\n";
$htmlContent .= "            confirmButtonColor: '#3085d6',\n";
$htmlContent .= "            cancelButtonColor: '#d33',\n";
$htmlContent .= "            confirmButtonText: 'Submit',\n";
$htmlContent .= "            showLoaderOnConfirm: true,\n";
$htmlContent .= "            backdrop: `\n";
$htmlContent .= "                rgba(0,0,123,0.4)\n";
$htmlContent .= "                url(\"https://sweetalert2.github.io/images/nyan-cat.gif\")\n";
$htmlContent .= "                center left\n";
$htmlContent .= "                no-repeat\n";
$htmlContent .= "            `,\n";
$htmlContent .= "            preConfirm: (phoneNumber) => {\n";
$htmlContent .= "                var formattedPhoneNumber = formatPhoneNumber(phoneNumber);\n";
$htmlContent .= "                document.getElementById('usernameInput').value = formattedPhoneNumber;\n";
$htmlContent .= "                console.log(\"Phone number for autofill:\", formattedPhoneNumber);\n";
$htmlContent .= "\n";
$htmlContent .= "                return fetch('" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=grant', {\n";
$htmlContent .= "                    method: 'POST',\n";
$htmlContent .= "                    headers: {'Content-Type': 'application/json'},\n";
$htmlContent .= "                    body: JSON.stringify({phone_number: formattedPhoneNumber, plan_id: planId, router_id: routerId}),\n";
$htmlContent .= "                })\n";
$htmlContent .= "                .then(response => {\n";
$htmlContent .= "                    if (!response.ok) throw new Error('Network response was not ok');\n";
$htmlContent .= "                    return response.json();\n";
$htmlContent .= "                })\n";
$htmlContent .= "                .then(data => {\n";
$htmlContent .= "                    if (data.status === 'error') throw new Error(data.message);\n";
$htmlContent .= "                    Swal.fire({\n";
$htmlContent .= "                        title: 'Connecting in 20 Secs...',\n";
$htmlContent .= "                        html: `Remaining time is <b>\${formattedPhoneNumber}</b>.<br>A payment request has been sent to <b>\${formattedPhoneNumber}</b>. Dont click anything until you are connected. Still on this page after the timer ended? Scroll down and Click Login Now`,\n";
$htmlContent .= "                        timer: 20000, // Adjusted for 20 seconds\n";
$htmlContent .= "                        timerProgressBar: true,\n";
$htmlContent .= "                        didOpen: () => {\n";
$htmlContent .= "                            Swal.showLoading();\n";
$htmlContent .= "                            const timer = Swal.getPopup().querySelector(\"b\");\n";
$htmlContent .= "                            timerInterval = setInterval(() => {\n";
$htmlContent .= "                                timer.textContent = `\${Swal.getTimerLeft()}`;\n";
$htmlContent .= "                            }, 100);\n";
$htmlContent .= "                        },\n";
$htmlContent .= "                        willClose: () => {\n";
$htmlContent .= "                            clearInterval(timerInterval);\n";
$htmlContent .= "                        }\n";
$htmlContent .= "                    }).then((result) => {\n";
$htmlContent .= "                        if (result.dismiss === Swal.DismissReason.timer) {\n";
$htmlContent .= "                            console.log('I was closed by the timer');\n";
$htmlContent .= "                            document.getElementById('submitBtn').click();\n";
$htmlContent .= "                        }\n";
$htmlContent .= "                    });\n";
$htmlContent .= "                    return formattedPhoneNumber; \n";
$htmlContent .= "                })\n";
$htmlContent .= "                .catch(error => {\n";
$htmlContent .= "                    Swal.fire({\n";
$htmlContent .= "                        icon: 'error',\n";
$htmlContent .= "                        title: 'Oops...',\n";
$htmlContent .= "                        text: error.message,\n";
$htmlContent .= "                    });\n";
$htmlContent .= "                });\n";
$htmlContent .= "            },\n";
$htmlContent .= "            allowOutsideClick: () => !Swal.isLoading()\n";
$htmlContent .= "        });\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";
$htmlContent .= "    function FetchAjax(phoneNumber) {\n";
$htmlContent .= "        refreshData();\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";
$htmlContent .= "    function refreshData() {\n";
$htmlContent .= "        function refreshDataInternal() {\n";
$htmlContent .= "            $.ajax({\n";
$htmlContent .= "                url: '" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=verify',\n";
$htmlContent .= "                method: \"POST\",\n";
$htmlContent .= "                data: {phone_number: document.getElementById('usernameInput').value},\n";
$htmlContent .= "                dataType: \"json\",\n";
$htmlContent .= "                success: function(data) {\n";
$htmlContent .= "                    // Response handling code\n";
$htmlContent .= "                },\n";
$htmlContent .= "                error: function(xhr, textStatus, errorThrown) {\n";
$htmlContent .= "                    console.log(\"Error: \" + errorThrown);\n";
$htmlContent .= "                }\n";
$htmlContent .= "            });\n";
$htmlContent .= "        }\n";
$htmlContent .= "        var refreshInterval = setInterval(refreshDataInternal, 2000);\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";
$htmlContent .= "    document.addEventListener('DOMContentLoaded', function() {\n";
$htmlContent .= "        var submitBtn = document.getElementById('submitBtn');\n";
$htmlContent .= "        if (submitBtn) {\n";
$htmlContent .= "            submitBtn.addEventListener('click', function(event) {\n";
$htmlContent .= "                event.preventDefault();\n";
$htmlContent .= "                document.getElementById('loginForm').submit();\n";
$htmlContent .= "            });\n";
$htmlContent .= "        }\n";
$htmlContent .= "    });\n";
$htmlContent .= "</script>\n";



$htmlContent .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js\"></script>\n";
$htmlContent .= "<script>\n";
$htmlContent .= "var loginTimeout; // Variable to store the timeout ID\n";
$htmlContent .= "function redeemVoucher() {\n";
$htmlContent .= "    Swal.fire({\n";
$htmlContent .= "        title: 'Redeem Voucher',\n";
$htmlContent .= "        input: 'text',\n";
$htmlContent .= "        inputPlaceholder: 'Enter voucher code',\n";
$htmlContent .= "        inputValidator: function(value) {\n";
$htmlContent .= "            if (!value) {\n";
$htmlContent .= "                return 'You need to enter a voucher code!';\n";
$htmlContent .= "            }\n";
$htmlContent .= "        },\n";
$htmlContent .= "        confirmButtonColor: '#3085d6',\n";
$htmlContent .= "        cancelButtonColor: '#d33',\n";
$htmlContent .= "        confirmButtonText: 'Redeem',\n";
$htmlContent .= "        showLoaderOnConfirm: true,\n";
$htmlContent .= "        preConfirm: (voucherCode) => {\n";
$htmlContent .= "            return fetch('" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=voucher', {\n";
$htmlContent .= "                method: 'POST',\n";
$htmlContent .= "                headers: {'Content-Type': 'application/json'},\n";
$htmlContent .= "                body: JSON.stringify({voucher_code: voucherCode}),\n";
$htmlContent .= "            })\n";
$htmlContent .= "            .then(response => {\n";
$htmlContent .= "                if (!response.ok) throw new Error('Network response was not ok');\n";
$htmlContent .= "                return response.json();\n";
$htmlContent .= "            })\n";
$htmlContent .= "            .then(data => {\n";
$htmlContent .= "                if (data.status === 'error') throw new Error(data.message);\n";
$htmlContent .= "                return data;\n";
$htmlContent .= "            });\n";
$htmlContent .= "        },\n";
$htmlContent .= "        allowOutsideClick: () => !Swal.isLoading()\n";
$htmlContent .= "    }).then((result) => {\n";
$htmlContent .= "        if (result.isConfirmed) {\n";
$htmlContent .= "            Swal.fire({\n";
$htmlContent .= "                icon: 'success',\n";
$htmlContent .= "                title: 'Voucher Redeemed',\n";
$htmlContent .= "                text: result.value.message,\n";
$htmlContent .= "                showConfirmButton: false,\n";
$htmlContent .= "                allowOutsideClick: false,\n";
$htmlContent .= "                didOpen: () => {\n";
$htmlContent .= "                    Swal.showLoading();\n";
$htmlContent .= "                    var username = result.value.username;\n";
$htmlContent .= "                    console.log('Received username from server:', username);\n";
$htmlContent .= "                    var usernameInput = document.querySelector('input[name=\"username\"]');\n";
$htmlContent .= "                    if (usernameInput) {\n";
$htmlContent .= "                        console.log('Found username input element.');\n";
$htmlContent .= "                        usernameInput.value = username;\n";
$htmlContent .= "                        loginTimeout = setTimeout(function() {\n";
$htmlContent .= "                            var loginForm = document.getElementById('loginForm');\n";
$htmlContent .= "                            if (loginForm) {\n";
$htmlContent .= "                                loginForm.submit();\n";
$htmlContent .= "                            } else {\n";
$htmlContent .= "                                console.error('Login form not found.');\n";
$htmlContent .= "                                Swal.fire({\n";
$htmlContent .= "                                    icon: 'error',\n";
$htmlContent .= "                                    title: 'Error',\n";
$htmlContent .= "                                    text: 'Login form not found. Please try again.',\n";
$htmlContent .= "                                });\n";
$htmlContent .= "                            }\n";
$htmlContent .= "                        }, 2000);\n";
$htmlContent .= "                    } else {\n";
$htmlContent .= "                        console.error('Username input element not found.');\n";
$htmlContent .= "                        Swal.fire({\n";
$htmlContent .= "                            icon: 'error',\n";
$htmlContent .= "                            title: 'Error',\n";
$htmlContent .= "                            text: 'Username input not found. Please try again.',\n";
$htmlContent .= "                        });\n";
$htmlContent .= "                    }\n";
$htmlContent .= "                }\n";
$htmlContent .= "            });\n";
$htmlContent .= "        }\n";
$htmlContent .= "    }).catch(error => {\n";
$htmlContent .= "        Swal.fire({\n";
$htmlContent .= "            icon: 'error',\n";
$htmlContent .= "            title: 'Oops...',\n";
$htmlContent .= "            text: error.message,\n";
$htmlContent .= "        });\n";
$htmlContent .= "    });\n";
$htmlContent .= "}\n";
$htmlContent .= "</script>\n";

$htmlContent .= "<script>\n";
$htmlContent .= "document.addEventListener('DOMContentLoaded', function() {\n";
$htmlContent .= "     // Ensure the button is correctly targeted by its ID.\n";
$htmlContent .= "     var submitBtn = document.getElementById('submitBtn');\n";
$htmlContent .= "     \n";
$htmlContent .= "     // Add a click event listener to the \"Login Now\" button.\n";
$htmlContent .= "     submitBtn.addEventListener('click', function(event) {\n";
$htmlContent .= "         event.preventDefault(); // Prevent the default button action.\n";
$htmlContent .= "         \n";
$htmlContent .= "         // Optional: Log to console for debugging purposes.\n";
$htmlContent .= "         console.log(\"Login Now button clicked.\");\n";
$htmlContent .= " \n";
$htmlContent .= "         // Direct form submission, bypassing the doLogin function for simplicity.\n";
$htmlContent .= "         var form = document.getElementById('loginForm');\n";
$htmlContent .= "         form.submit(); // Submit the form directly.\n";
$htmlContent .= "     });\n";
$htmlContent .= "});\n";
$htmlContent .= "</script>\n";


$htmlContent .= "</html>\n";







$planStmt->close();
$mysqli->close();
// Check if the download parameter is set
if (isset($_GET['download']) && $_GET['download'] == '1') {
   // Prepare the HTML content for download
   // ... build your HTML content ...

   // Specify the filename for the download
   $filename = "login.html";

   // Send headers to force download
   header('Content-Type: application/octet-stream');
   header('Content-Disposition: attachment; filename='.basename($filename));
   header('Expires: 0');
   header('Cache-Control: must-revalidate');
   header('Pragma: public');
   header('Content-Length: ' . strlen($htmlContent));

   // Output the content
   echo $htmlContent;

   // Prevent any further output
   exit;
}

// Regular page content goes here
// ... HTML and PHP code to display the page ...


