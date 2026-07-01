<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Arun Kumar Portfolio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #1d4ed8;
            --accent: #60a5fa;
            --bg: #060913;
            --surface: #0d1117;
            --surface2: #161b27;
            --border: rgba(255,255,255,0.07);
            --text: #f3f4f6;
            --muted: #6b7280;
        }

        body {
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated background */
        .bg-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(29,78,216,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(29,78,216,0.05) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
        }

        .bg-glow-1 {
            position: fixed;
            top: -200px;
            left: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(29,78,216,0.12) 0%, transparent 70%);
            filter: blur(60px);
            pointer-events: none;
            animation: float1 8s ease-in-out infinite;
        }
        .bg-glow-2 {
            position: fixed;
            bottom: -200px;
            right: -200px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(96,165,250,0.08) 0%, transparent 70%);
            filter: blur(80px);
            pointer-events: none;
            animation: float2 10s ease-in-out infinite;
        }

        @keyframes float1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(30px,30px)} }
        @keyframes float2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-20px)} }

        .login-card {
            position: relative;
            z-index: 10;
            background: rgba(13,17,23,0.85);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 48px 44px;
            width: 100%;
            max-width: 420px;
            backdrop-filter: blur(20px);
            box-shadow: 0 25px 80px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.03);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 36px;
        }
        .brand-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: white;
            box-shadow: 0 0 20px rgba(29,78,216,0.3);
        }
        .brand-info h1 {
            font-family: 'Syne', sans-serif;
            font-size: 17px;
            font-weight: 800;
            color: #fff;
            line-height: 1.1;
        }
        .brand-info span {
            font-size: 11px;
            color: var(--accent);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .login-title {
            font-size: 26px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
        }
        .login-subtitle {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            margin-bottom: 8px;
        }
        .form-input {
            width: 100%;
            padding: 13px 16px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.2s ease;
        }
        .form-input:focus {
            border-color: rgba(29,78,216,0.6);
            background: rgba(29,78,216,0.05);
            box-shadow: 0 0 0 3px rgba(29,78,216,0.1);
        }
        .form-input::placeholder { color: #374151; }

        .input-icon-wrap {
            position: relative;
        }
        .input-icon-wrap .icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            margin-top: 8px;
        }
        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
            opacity: 0;
            transition: opacity 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(29,78,216,0.35);
        }
        .btn-login:hover::before { opacity: 1; }
        .btn-login:active { transform: translateY(0); }
        .btn-login.loading { opacity: 0.7; pointer-events: none; }

        .btn-login .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin: 0 auto;
        }
        .btn-login.loading .btn-text { display: none; }
        .btn-login.loading .spinner { display: block; }

        @keyframes spin { to { transform: rotate(360deg); } }

        .error-msg {
            display: none;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            color: #f87171;
            margin-top: 16px;
            text-align: center;
        }
        .error-msg.show { display: block; animation: fadeIn 0.3s ease; }

        @keyframes fadeIn { from{opacity:0;transform:translateY(-5px)} to{opacity:1;transform:translateY(0)} }

        .divider {
            height: 1px;
            background: var(--border);
            margin: 28px 0;
        }

        .hint {
            text-align: center;
            font-size: 12px;
            color: var(--muted);
        }
        .hint code {
            background: var(--surface2);
            padding: 2px 8px;
            border-radius: 6px;
            font-family: monospace;
            color: var(--accent);
        }

        .view-site {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 20px;
            font-size: 13px;
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        .view-site:hover { color: var(--accent); }
        .view-site svg { width: 14px; height: 14px; }

        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px;
                border-radius: 16px;
                margin: 16px;
                width: calc(100% - 32px);
            }
            .login-title {
                font-size: 22px;
            }
            .brand {
                margin-bottom: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-grid"></div>
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>

    <div class="login-card">
        <div class="brand">
            <div class="brand-avatar">AKJ</div>
            <div class="brand-info">
                <h1>Arun Kumar</h1>
                <span>Portfolio CMS</span>
            </div>
        </div>

        <p class="login-title">Welcome back</p>
        <p class="login-subtitle">Sign in to manage your portfolio content</p>

        <form id="loginForm" onsubmit="handleLogin(event)">
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div class="input-icon-wrap">
                    <input
                        id="username"
                        class="form-input"
                        type="text"
                        placeholder="Enter username"
                        autocomplete="username"
                        required
                    >
                    <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-icon-wrap">
                    <input
                        id="password"
                        class="form-input"
                        type="password"
                        placeholder="Enter password"
                        autocomplete="current-password"
                        required
                    >
                    <svg class="icon" id="togglePwd" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" onclick="togglePassword()">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
            </div>

            <button class="btn-login" type="submit" id="loginBtn">
                <span class="btn-text">Sign In</span>
                <div class="spinner"></div>
            </button>

            <div class="error-msg" id="errorMsg"></div>
        </form>

        <div class="divider"></div>

        <div class="hint">
            Default credentials: <code>admin</code> / <code>admin123</code>
        </div>

        <a class="view-site" href="../index.php" target="_blank">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            View Live Site
        </a>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            pwd.type = pwd.type === 'password' ? 'text' : 'password';
        }

        async function handleLogin(e) {
            e.preventDefault();
            const btn = document.getElementById('loginBtn');
            const errorMsg = document.getElementById('errorMsg');
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            btn.classList.add('loading');
            errorMsg.classList.remove('show');

            try {
                const response = await fetch('api/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = 'dashboard.php';
                } else {
                    errorMsg.textContent = data.error || 'Login failed. Please try again.';
                    errorMsg.classList.add('show');
                    btn.classList.remove('loading');
                }
            } catch (err) {
                errorMsg.textContent = 'Connection error. Please try again.';
                errorMsg.classList.add('show');
                btn.classList.remove('loading');
            }
        }
    </script>
</body>
</html>
