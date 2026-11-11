<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error — SpeedRadius</title>
    <link rel="shortcut icon" href="ui/ui/images/logo.png" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes bgShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .bg-animated {
            background: linear-gradient(-45deg, #1a0533, #0d1b4b, #1a0533, #2d0a3e, #0a1628, #3b0764);
            background-size: 400% 400%;
            animation: bgShift 12s ease infinite;
        }
        .blob { position:fixed; border-radius:50%; filter:blur(80px); opacity:0.18; pointer-events:none; z-index:0; }
        .blob-1 { width:520px;height:520px;top:-120px;left:-180px;background:radial-gradient(circle,#ff416c,transparent); }
        .blob-2 { width:420px;height:420px;bottom:-100px;right:-120px;background:radial-gradient(circle,#7b2ff7,transparent); }
        .blob-3 { width:300px;height:300px;top:40%;left:55%;background:radial-gradient(circle,#0072ff,transparent); }
        @keyframes slideUp {
            from { opacity:0; transform:translateY(36px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .animate-slide-up { animation: slideUp 0.5s cubic-bezier(0.22,1,0.36,1) both; }
        .glass { background: rgba(255,255,255,0.06); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .error-scroll { max-height: 200px; overflow-y: auto; }
        .error-scroll::-webkit-scrollbar { width: 5px; }
        .error-scroll::-webkit-scrollbar-track { background: transparent; }
        .error-scroll::-webkit-scrollbar-thumb { background: rgba(255,65,108,0.4); border-radius: 99px; }
        .btn-hover:hover { transform: translateY(-2px); opacity: 0.9; }
        .btn-hover { transition: transform 0.15s, opacity 0.15s; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-animated relative"
      style="font-family: 'Inter', sans-serif;">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- Card -->
    <div class="w-full max-w-3xl rounded-3xl overflow-hidden animate-slide-up relative z-10"
         style="box-shadow: 0 32px 80px rgba(0,0,0,0.55), 0 0 0 1px rgba(255,80,80,0.18); border: 1px solid rgba(255,255,255,0.1);">

        <!-- Header -->
        <div class="flex items-center gap-4 px-8 py-7"
             style="background: linear-gradient(135deg, #ff416c, #ff4b2b);">
            <div class="flex-shrink-0 flex items-center justify-center text-2xl rounded-xl"
                 style="width:52px;height:52px;background:rgba(255,255,255,0.18);">🔥</div>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">{$error_title}</h1>
                <p class="text-sm mt-1" style="color:rgba(255,255,255,0.72);">An unexpected error occurred. Please review the details below.</p>
            </div>
        </div>

        <!-- Body -->
        <div class="px-8 py-7 glass">

            <!-- Error image centered above details -->
            <div class="flex justify-center mb-6">
                <img src="./ui/ui/images/error.png"
                     alt="Error"
                     class="w-48 md:w-56 object-contain"
                     style="filter: drop-shadow(0 8px 24px rgba(255,65,108,0.35));">
            </div>

            <!-- Error message -->
            <div class="mb-6">
                <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color:rgba(255,255,255,0.38);">Error Details</p>
                <div class="error-scroll rounded-xl p-4 text-sm leading-relaxed break-words"
                     style="background:rgba(255,65,108,0.09);border:1px solid rgba(255,65,108,0.28);border-left:4px solid #ff416c;color:#f0aab4;">
                    {$error_message}
                </div>
            </div>

            <!-- Tips -->
            <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color:rgba(255,255,255,0.38);">MikroTik Troubleshooting Tips</p>
            <ul class="space-y-2 mb-6">
                <li class="flex items-start gap-3 text-sm" style="color:rgba(255,255,255,0.65);">
                    <span class="flex-shrink-0 flex items-center justify-center w-5 h-5 rounded-full text-xs font-bold mt-0.5"
                          style="background:rgba(56,239,125,0.18);color:#38ef7d;">✓</span>
                    {Lang::T('Make sure you use API Port, Default 8728')}
                </li>
                <li class="flex items-start gap-3 text-sm" style="color:rgba(255,255,255,0.65);">
                    <span class="flex-shrink-0 flex items-center justify-center w-5 h-5 rounded-full text-xs font-bold mt-0.5"
                          style="background:rgba(56,239,125,0.18);color:#38ef7d;">✓</span>
                    {Lang::T('Make sure Username and Password are correct')}
                </li>
                <li class="flex items-start gap-3 text-sm" style="color:rgba(255,255,255,0.65);">
                    <span class="flex-shrink-0 flex items-center justify-center w-5 h-5 rounded-full text-xs font-bold mt-0.5"
                          style="background:rgba(56,239,125,0.18);color:#38ef7d;">✓</span>
                    {Lang::T('Make sure your hosting not blocking port to external')}
                </li>
                <li class="flex items-start gap-3 text-sm" style="color:rgba(255,255,255,0.65);">
                    <span class="flex-shrink-0 flex items-center justify-center w-5 h-5 rounded-full text-xs font-bold mt-0.5"
                          style="background:rgba(56,239,125,0.18);color:#38ef7d;">✓</span>
                    {Lang::T('Make sure your Mikrotik accessible from SpeedRadius')}
                </li>
            </ul>

            <!-- Update note -->
            <div class="rounded-xl px-4 py-3 text-sm mb-7"
                 style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:rgba(255,255,255,0.45);">
                💡 {Lang::T('If you just update SpeedRadius from upload files, try click Update')} Database
            </div>

            <!-- Action buttons -->
            <div class="flex flex-wrap gap-3">
                <a href="./update.php?step=4"
                   class="btn-hover inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold text-white no-underline"
                   style="background:linear-gradient(135deg,#0072ff,#00c6ff);box-shadow:0 6px 20px rgba(0,114,255,0.35);">
                    🗄 {Lang::T('Update')} Database
                </a>
                <a href="{$_url}community#update"
                   class="btn-hover inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold text-white no-underline"
                   style="background:linear-gradient(135deg,#11998e,#38ef7d);box-shadow:0 6px 20px rgba(56,239,125,0.3);">
                    ⬆ {Lang::T('Update SpeedRadius')}
                </a>
                <a href="/?_route=dashboard"
                   class="btn-hover inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold no-underline"
                   style="background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.75);border:1px solid rgba(255,255,255,0.12);">
                    ← {Lang::T('Back')}
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-xs px-8 py-4"
             style="border-top:1px solid rgba(255,255,255,0.06);color:rgba(255,255,255,0.22);">
            SpeedRadius &nbsp;·&nbsp; {$error_title}
        </div>
    </div>

</body>
</html>
