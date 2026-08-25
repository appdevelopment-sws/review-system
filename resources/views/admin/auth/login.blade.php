<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Control Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #090d16;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(168, 85, 247, 0.15) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(14, 165, 233, 0.1) 0px, transparent 50%);
            color: #f3f4f6;
            padding: 1.5rem;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            box-shadow: 
                0 20px 25px -5px rgba(0, 0, 0, 0.5),
                0 8px 10px -6px rgba(0, 0, 0, 0.5),
                inset 0 1px 1px rgba(255, 255, 255, 0.1);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
        }

        .brand-icon svg {
            width: 28px;
            height: 28px;
            color: #ffffff;
        }

        .header h1 {
            font-size: 1.65rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.025em;
            margin-bottom: 0.35rem;
        }

        .header p {
            font-size: 0.875rem;
            color: #9ca3af;
        }

        .alert {
            padding: 0.875rem 1rem;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.625rem;
            line-height: 1.4;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.825rem;
            font-weight: 600;
            color: #d1d5db;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper svg.field-icon {
            position: absolute;
            left: 1rem;
            width: 18px;
            height: 18px;
            color: #6b7280;
            transition: color 0.2s;
        }

        .input-wrapper input {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            color: #ffffff;
            font-size: 0.925rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-wrapper input:focus {
            border-color: #6366f1;
            background: rgba(15, 23, 42, 0.9);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }

        .input-wrapper input:focus + svg.field-icon {
            color: #818cf8;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: #d1d5db;
        }

        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #9ca3af;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-label input[type="checkbox"] {
            accent-color: #6366f1;
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }

        .btn-submit {
            width: 100%;
            padding: 0.9rem 1.5rem;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            border-radius: 14px;
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.4);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px 0 rgba(99, 102, 241, 0.5);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .demo-helpers {
            margin-top: 1.75rem;
            padding-top: 1.25rem;
            border-top: 1px dashed rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .demo-helpers p {
            font-size: 0.775rem;
            color: #6b7280;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .demo-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .btn-demo {
            padding: 0.45rem 0.75rem;
            font-size: 0.775rem;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #9ca3af;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-demo:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="header">
        <div class="brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <h1>Admin Control Panel</h1>
        <p>Enter your administrator credentials to sign in</p>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email Address</label>
            <div class="input-wrapper">
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@admin.com" required autofocus autocomplete="email">
                <svg class="field-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrapper">
                <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                <svg class="field-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <button type="button" class="toggle-password" id="togglePasswordBtn" title="Toggle password visibility">
                    <svg id="eyeIcon" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="options-row">
            <label class="checkbox-label">
                <input type="checkbox" name="remember">
                <span>Remember me</span>
            </label>
        </div>

        <button type="submit" class="btn-submit">
            <span>Sign In to Admin Panel</span>
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </button>
    </form>

    <div class="demo-helpers">
        <p>Quick Credential Fill</p>
        <div class="demo-buttons">
            <button type="button" class="btn-demo" onclick="fillCredentials('admin@admin.com', 'password123')">🔑 Admin Credentials</button>
        </div>
    </div>
</div>

<script>
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('password');

    togglePasswordBtn.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
    });

    function fillCredentials(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>

</body>
</html>
