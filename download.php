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
$htmlContent = "";
$htmlContent .= "<!DOCTYPE html>\n";
$htmlContent .= "<html lang=\"en\">\n";
$htmlContent .= "<head>\n";
$htmlContent .= "    <meta charset=\"UTF-8\">\n";
$htmlContent .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
$htmlContent .= "    <title>$company</title>\n";
$htmlContent .= "    <script src=\"https://cdn.tailwindcss.com\"></script>\n";
$htmlContent .= "    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css\">\n";
$htmlContent .= "    <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/glider-js@1.7.7/glider.min.css\" />\n";
$htmlContent .= "    <script src=\"https://cdn.jsdelivr.net/npm/glider-js@1.7.7/glider.min.js\"></script>\n";
$htmlContent .= "    <link rel=\"preconnect\" href=\"https://cdn.jsdelivr.net\">\n";
$htmlContent .= "    <link rel=\"preconnect\" href=\"https://cdnjs.cloudflare.com\" crossorigin>\n";
$htmlContent .= "    <link rel=\"stylesheet\" href=\"https://rsms.me/inter/inter.css\">\n";
$htmlContent .= "    <!-- <link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\"> -->\n";

$htmlContent .= "</head>\n";






$htmlContent .= "<body class=\"font-sans antialiased text-gray-900 bg-gray-900 font-inter\">\n";
$htmlContent .= "    <!-- Main Content -->\n";
$htmlContent .= "    <div class=\"mx-auto max-w-screen-2xl px-4 md:px-4\">\n";
$htmlContent .= "        <div class=\"max-h-34 relative mx-auto mt-4 flex max-w-lg flex-1 shrink-0 items-center justify-center overflow-hidden shadow-lg rounded-lg bg-green-100\">\n";
$htmlContent .= "            <!-- overlay - start -->\n";
$htmlContent .= "            <!-- <div class=\"absolute inset-0  mix-blend-multiply\"></div> -->\n";
$htmlContent .= "            <!-- overlay - end -->\n";
$htmlContent .= "            <!-- text start -->\n";
$htmlContent .= "            <div class=\"relative flex flex-col items-center p-4 sm:max-w-xl\">\n";
$htmlContent .= "                <p class=\"mb-4 text-center text-2xl font-bold text-gray-800 sm:text-xl md:mb-2 \">$company </p>\n";
$htmlContent .= "                <h3 class=\"text-lg italic text-gray-700 mb-2\">How to Purchase:</h3>\n"; // Smaller and italicized title
$htmlContent .= "                <ol class=\"text-base text-left text-gray-800 mb-1 list-decimal pl-6\">\n";
$htmlContent .= "                    <li>Click on your preferred package Buy</li>\n";
$htmlContent .= "                    <li>Enter Your Mpesa No.</li>\n";
$htmlContent .= "                    <li>Enter pin</li>\n";
$htmlContent .= "                    <li>Wait for 30sec to be connected</li>\n";
$htmlContent .= "                </ol>\n";
$htmlContent .= "                <p class=\"mb-4 text-center text-lg font-medium text-gray-700 sm:text-1xl md:mb-1  md:text-xl\"> CUSTOMER CARE : $phone</p>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "            <!-- text end -->\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";










$htmlContent .= "    <div class=\"py-2 sm:py-4 lg:py-4\">\n";
$htmlContent .= "        <div class=\"mx-auto max-w-screen-2xl px-4 md:px-4\">\n";
$htmlContent .= "            <div class=\"mx-auto max-w-lg\">\n";
$htmlContent .= "                <div class=\"flex flex-col gap-4\">\n";
$htmlContent .= "                    <button type=\"button\" class=\"flex items-center justify-center gap-2 rounded-lg bg-green-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-green-300 transition duration-100 hover:bg-green-600 focus-visible:ring active:bg-green-700 md:text-base\" onclick=\"redeemVoucher()\">\n";
$htmlContent .= "                        <svg class=\"w-5 h-5 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\">\n";
$htmlContent .= "                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7\"></path>\n";
$htmlContent .= "                        </svg>\n";
$htmlContent .= "                        Click here to Redeem Voucher\n";
$htmlContent .= "                    </button>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";




// Free Trial Section
$htmlContent .= "    <div class=\"text-center\">\n";
$htmlContent .= "        $(if trial == 'yes')\n";
$htmlContent .= "        <a href=\"$(link-login-only)?dst=$(link-orig-esc)&amp;username=T-$(mac-esc)\" class=\"btn-3d text-space\" style=\"display: inline-block; background-color: #4CAF50; color: white; padding: 10px 20px; text-align: center; text-decoration: none; font-weight: bold; border-radius: 5px;\">\n";
$htmlContent .= "            FREE TRIAL 10 MINS\n";
$htmlContent .= "        </a>\n";
$htmlContent .= "        $(endif)\n";
$htmlContent .= "    </div>\n";
$htmlContent .= "    <div><br></div>\n"; // Added line break for spacing





$htmlContent .= "    <div class=\"py-2 sm:py-4 lg:py-6\">\n";
$htmlContent .= "        <div class=\"mx-auto max-w-screen-2xl px-4 md:px-8\">\n";
$htmlContent .= "            <div class=\"mx-auto max-w-lg grid grid-cols-2 sm:grid-cols-3 gap-1 p-1\" id=\"cards-container\">\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";



$htmlContent .= "    <div class=\"container mx-auto px-4 mb-4\">\n";
$htmlContent .= "        <div class=\"max-w-md mx-auto bg-white rounded-lg overflow-hidden md:max-w-lg\">\n";
$htmlContent .= "            <div class=\"md:flex\">\n";
$htmlContent .= "                <div class=\"w-full p-5\">\n";
$htmlContent .= "                    <div class=\"text-center\">\n";
$htmlContent .= "                        <h3 class=\"text-2xl text-gray-900\">Already Have an Active Package?</h3>\n";
$htmlContent .= "                    </div>\n";
$htmlContent .= "                    <form id=\"loginForm\" class=\"form\" name=\"login\" action=\"$(link-login-only)\" method=\"post\" $(if chap-id)onSubmit=\"return doLogin()\" $(endif)>\n";
$htmlContent .= "                        <input type=\"hidden\" name=\"dst\" value=\"$(link-orig)\" />\n";
$htmlContent .= "                        <input type=\"hidden\" name=\"popup\" value=\"true\" />\n";
$htmlContent .= "                        <div class=\"text-center\">\n";
$htmlContent .= "                            <label class=\"block text-gray-700 text-sm font-bold mb-2\" for=\"username\">Enter your account number or user name</label>\n";
$htmlContent .= "                            <div>\n";
$htmlContent .= "                                <input id=\"usernameInput\" name=\"username\" type=\"text\" value=\"\" placeholder=\"e.g ACC123456\" class=\"w-full rounded-lg border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring\" />\n";
$htmlContent .= "                                <button id=\"submitBtn\" class=\"w-full mt-3 flex items-center justify-center gap-2 rounded-lg bg-green-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-blue-300 transition duration-100 hover:bg-blue-600 focus-visible:ring active:bg-blue-700 md:text-base\" type=\"button\" onclick=\"submitLogin()\">\n";
$htmlContent .= "                                    Connect\n";
$htmlContent .= "                                </button>\n";
$htmlContent .= "                            </div>\n";
$htmlContent .= "                        </div>\n";
$htmlContent .= "                        <input type=\"hidden\" name=\"password\" value=\"1234\">\n";
$htmlContent .= "                    </form>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";




$htmlContent .= "    <div class=\"container mx-auto px-4 mb-4\">\n";
$htmlContent .= "        <div class=\"max-w-md mx-auto bg-white rounded-lg overflow-hidden md:max-w-lg\">\n";
$htmlContent .= "            <div class=\"md:flex\">\n";
$htmlContent .= "                <div class=\"w-full p-5\">\n";
$htmlContent .= "                    <div class=\"text-center\">\n";
$htmlContent .= "                        <h3 class=\"text-2xl text-gray-900\">User Login</h3>\n";
$htmlContent .= "                    </div>\n";

$htmlContent .= "                    <form class=\"login\" name=\"login\" action=\"$(link-login-only)\" method=\"post\">\n";
$htmlContent .= "                        <input type=\"hidden\" name=\"dst\" value=\"$(link-orig)\" />\n";
$htmlContent .= "                        <p class=\"logo-area text-center\" id=\"wifi_name\"></p>\n";
$htmlContent .= "                        <p class=\"text-center\" style=\"color: black;\">(Enter your username and password to login.)</p>\n";
$htmlContent .= "                        <input type=\"hidden\" name=\"popup\" value=\"true\" />\n";
$htmlContent .= "                        <div class=\"s-login\">\n";
$htmlContent .= "                            <hr/>\n";
$htmlContent .= "                            <input type=\"text\" name=\"username\" class=\"username w-full rounded-lg border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring\" placeholder=\"Username\" required />\n";
$htmlContent .= "                            <div class=\"relative mt-4\">\n";  // Added margin-top for spacing
$htmlContent .= "                                <input type=\"password\" name=\"password\" id=\"password\" class=\"password w-full rounded-lg border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring\" placeholder=\"Password\" required />\n";  // Removed value attribute to make it empty
$htmlContent .= "                                <input type=\"checkbox\" onclick=\"togglePasswordVisibility()\" class=\"absolute top-2 right-3\" style=\"cursor: pointer;\">  \n";
$htmlContent .= "                            </div>\n";
$htmlContent .= "                        </div>\n";
$htmlContent .= "                        <button type=\"submit\" class=\"btn btn-login w-full mt-3 flex items-center justify-center gap-2 rounded-lg bg-green-500 px-8 py-3 text-center text-sm font-semibold text-white outline-none ring-blue-300 transition duration-100 hover:bg-blue-600 focus-visible:ring active:bg-blue-700 md:text-base\"><b>LOGIN</b></button>\n";
$htmlContent .= "                    </form>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";

$htmlContent .= "<script>\n";
$htmlContent .= "function togglePasswordVisibility() {\n";
$htmlContent .= "    var passwordInput = document.getElementById('password');\n";
$htmlContent .= "    if (passwordInput.type === 'password') {\n";
$htmlContent .= "        passwordInput.type = 'text';\n";
$htmlContent .= "    } else {\n";
$htmlContent .= "        passwordInput.type = 'password';\n";
$htmlContent .= "    }\n";
$htmlContent .= "}\n";
$htmlContent .= "</script>\n";





$htmlContent .= "    <div class=\"mx-auto max-w-screen-2xl px-4 md:px-8\">\n";
$htmlContent .= "        <div class=\"mx-auto mb-4 max-w-lg\">\n";
$htmlContent .= "            <div class=\"border-t py-4\">\n";
$htmlContent .= "                <p class=\"text-xs text-center\" style=\"color: white; font-weight: bold;\">&copy;  All rights reserved. Created by SpeedRadius</p>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";
$htmlContent .= "</body>\n";






$htmlContent .= "<script>\n";
$htmlContent .= "    document.addEventListener('DOMContentLoaded', function() {\n";
$htmlContent .= "        var accountId = getCookie('accountid');\n";
$htmlContent .= "        if (accountId) {\n";
$htmlContent .= "            document.getElementById('usernameInput').value = accountId;\n";
$htmlContent .= "        }\n";
$htmlContent .= "    });\n";
$htmlContent .= "\n";
$htmlContent .= "</script>\n";

$htmlContent .= "<script>\n";
$htmlContent .= "function fetchData() {\n";
$htmlContent .= "    let domain = '" . APP_URL . "/';\n";
$htmlContent .= "    let siteUrl = domain + \"/index.php?_route=plugin/hotspot_plan\";\n";
$htmlContent .= "    let request = new XMLHttpRequest();\n";
$htmlContent .= "    const routerName = encodeURIComponent(\"$routerName\");\n";
$htmlContent .= "    const dataparams = `routername=\${routerName}`;\n";
$htmlContent .= "    request.open(\"POST\", siteUrl, true);\n";
$htmlContent .= "    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');\n";
$htmlContent .= "    request.onload = () => {\n";
$htmlContent .= "        if (request.readyState === XMLHttpRequest.DONE) {\n";
$htmlContent .= "            if (request.status === 200) {\n";
$htmlContent .= "                let fetchedData = JSON.parse(request.responseText);\n";
$htmlContent .= "                populateCards(fetchedData);\n";
$htmlContent .= "            } else {\n";
$htmlContent .= "                console.log(`Error \${request.status}: \${request.statusText}`);\n";
$htmlContent .= "            }\n";
$htmlContent .= "        }\n";
$htmlContent .= "    };\n";
$htmlContent .= "    request.onerror = () => {\n";
$htmlContent .= "        console.error(\"Network error\");\n";
$htmlContent .= "    };\n";
$htmlContent .= "    request.send(dataparams);\n";
$htmlContent .= "}\n";




$htmlContent .= "function populateCards(data) {\n";
$htmlContent .= "    var cardsContainer = document.getElementById('cards-container');\n";
$htmlContent .= "    cardsContainer.innerHTML = ''; // Clear existing content\n";
$htmlContent .= "    data.data.forEach(router => {\n";
$htmlContent .= "        router.plans_hotspot.forEach(item => {\n";
$htmlContent .= "            var cardDiv = document.createElement('div');\n";
$htmlContent .= "            cardDiv.className = 'bg-white border border-black rounded-lg shadow-md overflow-hidden transition duration-300 hover:shadow-lg flex flex-col items-center justify-between mx-auto mb-4 w-40';\n";
$htmlContent .= "            cardDiv.innerHTML = `\n";
$htmlContent .= "                <div class=\"bg-green-500 text-white w-full py-1\">\n";
$htmlContent .= "                    <h2 class=\"text-sm font-medium uppercase text-center\" style=\"font-size: clamp(0.75rem, 1.5vw, 1rem); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;\">\n";
$htmlContent .= "                        \${item.planname}\n";
$htmlContent .= "                    </h2>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "                <div class=\"px-4 py-2 flex-grow\">\n";
$htmlContent .= "                    <p class=\"text-2xl font-bold text-green-600 mb-1\">\n";
$htmlContent .= "                        <span class=\"text-lg font-medium text-black\">\${item.currency}</span>\n";
$htmlContent .= "                        \${item.price}\n";
$htmlContent .= "                    </p>\n";
$htmlContent .= "                    <p class=\"text-sm text-black mb-2\">\n";
$htmlContent .= "                        Valid for \${item.validity} \${item.timelimit}\n";
$htmlContent .= "                    </p>\n";
$htmlContent .= "                    <hr class=\"border-black mb-2\">\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "                <div class=\"px-4 py-2 flex-shrink-0\">\n";
$htmlContent .= "                    <a href=\"#\" class=\"inline-block bg-gray-900 text-white hover:bg-blue-600 font-semibold py-1 px-4 rounded-lg transition duration-300 text-md\"\n";
$htmlContent .= "                        onclick=\"handlePhoneNumberSubmission('\${item.planId}', '\${item.routerId}'); return false;\"\n";
$htmlContent .= "                        data-plan-id=\"\${item.planId}\"\n";
$htmlContent .= "                        data-router-id=\"\${item.routerId}\">\n";
$htmlContent .= "                            Buy\n";
$htmlContent .= "                    </a>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "            `;\n";
$htmlContent .= "            cardsContainer.appendChild(cardDiv);\n";
$htmlContent .= "        });\n";
$htmlContent .= "    });\n";
$htmlContent .= "}\n";
$htmlContent .= "fetchData();\n";
$htmlContent .= "</script>\n";





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
$htmlContent .= "    function setCookie(name, value, days) {\n";
$htmlContent .= "        var expires = \"\";\n";
$htmlContent .= "        if (days) {\n";
$htmlContent .= "            var date = new Date();\n";
$htmlContent .= "            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));\n";
$htmlContent .= "            expires = \"; expires=\" + date.toUTCString();\n";
$htmlContent .= "        }\n";
$htmlContent .= "        document.cookie = name + \"=\" + (value || \"\") + expires + \"; path=/\";\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";
$htmlContent .= "    function getCookie(name) {\n";
$htmlContent .= "        var nameEQ = name + \"=\";\n";
$htmlContent .= "        var ca = document.cookie.split(';');\n";
$htmlContent .= "        for (var i = 0; i < ca.length; i++) {\n";
$htmlContent .= "            var c = ca[i];\n";
$htmlContent .= "            while (c.charAt(0) == ' ') c = c.substring(1, c.length);\n";
$htmlContent .= "            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        return null;\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";
$htmlContent .= "    function generateAccountId() {\n";
$htmlContent .= "        return '' + Math.floor(10000 + Math.random() * 90000); // Generate a random number between 10000 and 99999\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";

$htmlContent .= "var loginTimeout; // Variable to store the timeout ID\n";
$htmlContent .= "function handlePhoneNumberSubmission(planId, routerId, price) {\n";

$htmlContent .= "    var msg = \"You are about to pay Ksh: ${amount}. Enter phonenumber below and click pay now to initialize payment\";\n";
$htmlContent .= "    const regexp = /\\\${([^{}]+)}/g;\n";
$htmlContent .= "    let result = msg.replace(regexp, function(ignore, key) {\n";
$htmlContent .= "        return eval(key);\n";
$htmlContent .= "    });\n";
$htmlContent .= "    swal.fire({\n";
$htmlContent .= "        title: 'Enter Your Mpesa Number',\n";
$htmlContent .= "        input: 'number',\n";
$htmlContent .= "        inputAttributes: {\n";
$htmlContent .= "            required: 'true'\n";
$htmlContent .= "        },\n";
$htmlContent .= "        inputValidator: function(value) {\n";
$htmlContent .= "            if (value === '') {\n";
$htmlContent .= "                return 'You need to write your phonenumber!';\n";
$htmlContent .= "            }\n";
$htmlContent .= "        },\n";
$htmlContent .= "        text: result,\n";
$htmlContent .= "        showCancelButton: true,\n";
$htmlContent .= "        confirmButtonColor: '#3085d6',\n";
$htmlContent .= "        cancelButtonColor: '#d33',\n";
$htmlContent .= "        confirmButtonText: 'Pay Now',\n";
$htmlContent .= "        showLoaderOnConfirm: true,\n";
$htmlContent .= "        preConfirm: (phoneNumber) => {\n";
$htmlContent .= "            var formattedPhoneNumber = formatPhoneNumber(phoneNumber);\n";
$htmlContent .= "            var accountId = getCookie('accountId');\n";
$htmlContent .= "            if (!accountId) {\n";
$htmlContent .= "                accountId = generateAccountId(); // Generate a new account ID\n";
$htmlContent .= "                setCookie('accountId', accountId, 4); // Set account ID as a cookie\n";
$htmlContent .= "            }\n";
$htmlContent .= "            document.getElementById('usernameInput').value = accountId; // Use account ID as the new username\n";
$htmlContent .= "            console.log(\"Phone number for autofill:\", formattedPhoneNumber);\n";
$htmlContent .= "\n";
$htmlContent .= "            return fetch('" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=grant', {\n";
$htmlContent .= "                method: 'POST',\n";
$htmlContent .= "                headers: {'Content-Type': 'application/json'},\n";
$htmlContent .= "                body: JSON.stringify({phone_number: formattedPhoneNumber, plan_id: planId, router_id: routerId, account_id: accountId}),\n";
$htmlContent .= "            })\n";
$htmlContent .= "            .then(response => {\n";
$htmlContent .= "                if (!response.ok) throw new Error('Network response was not ok');\n";
$htmlContent .= "                return response.json();\n";
$htmlContent .= "            })\n";
$htmlContent .= "            .then(data => {\n";
$htmlContent .= "                if (data.status === 'error') throw new Error(data.message);\n";
$htmlContent .= "                Swal.fire({\n";
$htmlContent .= "                    icon: 'info',\n";
$htmlContent .= "                    title: 'Processing..',\n";
$htmlContent .= "                    html: `A payment request has been sent to your phone. Please wait while we process your payment.`,\n";
$htmlContent .= "                    showConfirmButton: false,\n";
$htmlContent .= "                    allowOutsideClick: false,\n";
$htmlContent .= "                    didOpen: () => {\n";
$htmlContent .= "                        Swal.showLoading();\n";
$htmlContent .= "                        checkPaymentStatus(formattedPhoneNumber);\n";
$htmlContent .= "                    }\n";
$htmlContent .= "                });\n";
$htmlContent .= "                return formattedPhoneNumber;\n";
$htmlContent .= "            })\n";
$htmlContent .= "            .catch(error => {\n";
$htmlContent .= "                Swal.fire({\n";
$htmlContent .= "                    icon: 'error',\n";
$htmlContent .= "                    title: 'Oops...',\n";
$htmlContent .= "                    text: error.message,\n";
$htmlContent .= "                });\n";
$htmlContent .= "            });\n";
$htmlContent .= "        },\n";
$htmlContent .= "        allowOutsideClick: () => !Swal.isLoading()\n";
$htmlContent .= "    });\n";
$htmlContent .= "}\n";
$htmlContent .= "\n";
$htmlContent .= "function checkPaymentStatus(phoneNumber) {\n";
$htmlContent .= "    let checkInterval = setInterval(() => {\n";
$htmlContent .= "        $.ajax({\n";
$htmlContent .= "            url: '" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=verify',\n";
$htmlContent .= "            method: 'POST',\n";
$htmlContent .= "            data: JSON.stringify({account_id: document.getElementById('usernameInput').value}),\n";
$htmlContent .= "            contentType: 'application/json',\n";
$htmlContent .= "            dataType: 'json',\n";
$htmlContent .= "            success: function(data) {\n";
$htmlContent .= "                console.log('Raw Response:', data); // Debugging\n";
$htmlContent .= "                if (data.Resultcode === '3') { // Success\n";
$htmlContent .= "                    clearInterval(checkInterval);\n";
$htmlContent .= "                    Swal.fire({\n";
$htmlContent .= "                        icon: 'success',\n";
$htmlContent .= "                        title: 'Payment Successful',\n";
$htmlContent .= "                        text: data.Message,\n";
$htmlContent .= "                        showConfirmButton: false\n";
$htmlContent .= "                    });\n";
$htmlContent .= "                    if (loginTimeout) {\n";
$htmlContent .= "                        clearTimeout(loginTimeout);\n";
$htmlContent .= "                    }\n";
$htmlContent .= "                    loginTimeout = setTimeout(function() {\n";
$htmlContent .= "                        document.getElementById('loginForm').submit();\n";
$htmlContent .= "                    }, 2000);\n";
$htmlContent .= "                } else if (data.Resultcode === '2') { // Error\n";
$htmlContent .= "                    clearInterval(checkInterval);\n";
$htmlContent .= "                    let iconType = data.Status === 'danger' ? 'error' : data.Status;\n";
$htmlContent .= "                    Swal.fire({\n";
$htmlContent .= "                        icon: iconType,\n";
$htmlContent .= "                        title: 'Payment Issue',\n";
$htmlContent .= "                        text: data.Message,\n";
$htmlContent .= "                    });\n";
$htmlContent .= "                } else if (data.Resultcode === '1') { // Primary\n";
$htmlContent .= "                    // Continue checking\n";
$htmlContent .= "                }\n";
$htmlContent .= "            },\n";
$htmlContent .= "            error: function(xhr, textStatus, errorThrown) {\n";
$htmlContent .= "                console.log('Error: ' + errorThrown);\n";
$htmlContent .= "            }\n";
$htmlContent .= "        });\n";
$htmlContent .= "    }, 2000);\n";
$htmlContent .= "\n";
$htmlContent .= "    setTimeout(() => {\n";
$htmlContent .= "        clearInterval(checkInterval);\n";
$htmlContent .= "        Swal.fire({\n";
$htmlContent .= "            icon: 'warning',\n";
$htmlContent .= "            title: 'Timeout',\n";
$htmlContent .= "            text: 'Payment verification timed out. Please try again.',\n";
$htmlContent .= "        });\n";
$htmlContent .= "    }, 600000); // Stop checking after 60 seconds\n";
$htmlContent .= "}\n";
$htmlContent .= "</script>\n";

$htmlContent .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js\"></script>\n";

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
$htmlContent .= "            var accountId = getCookie('accountId');\n";
$htmlContent .= "            if (!accountId) {\n";
$htmlContent .= "                accountId = generateAccountId();\n";
$htmlContent .= "                setCookie('accountId', accountId, 7);\n";
$htmlContent .= "            }\n";
$htmlContent .= "            return fetch('" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=voucher', {\n";
$htmlContent .= "                method: 'POST',\n";
$htmlContent .= "                headers: {'Content-Type': 'application/json'},\n";
$htmlContent .= "                body: JSON.stringify({voucher_code: voucherCode, account_id: accountId}),\n";
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


// Google Analytics Tracking
$htmlContent .= "<script async src='https://www.googletagmanager.com/gtag/js?id=G-MKTVFMD7HE'></script>\n";
$htmlContent .= "<script>\n";
$htmlContent .= "  window.dataLayer = window.dataLayer || [];\n";
$htmlContent .= "  function gtag(){dataLayer.push(arguments);}\n";
$htmlContent .= "  gtag('js', new Date());\n";
$htmlContent .= "  gtag('config', 'G-MKTVFMD7HE');\n";
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



