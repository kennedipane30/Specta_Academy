<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – Spekta Academy</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
        --red:      #C50337;
        --red-dark: #8f0229;
        --red-deep: #3a0013;
        --white:    #ffffff;
        --off:      #f9f6f7;
        --muted:    #9a8f92;
        --border:   #e8dfe1;
        --text:     #2b1f23;
    }

    body {
        font-family: 'DM Sans', sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: stretch;
        justify-content: center;
        background: #f0eaec;
        overflow: hidden;
    }

    /* ── OUTER WRAPPER ── */
    .wrapper {
        width: 100%;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        animation: fadeUp 0.55s cubic-bezier(.22,1,.36,1) both;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── CARD ── */
    .card {
        display: flex;
        width: 100%;
        max-width: 980px;
        min-height: 580px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(58,0,19,0.18), 0 4px 16px rgba(0,0,0,0.08);
    }

    /* ── LEFT PANEL ── */
    .left {
        width: 36%;
        background: linear-gradient(165deg, #C50337 0%, #8f0229 48%, #3a0013 100%);
        position: relative;
        display: flex;
        flex-direction: column;
        padding: 36px 32px 30px;
        overflow: hidden;
    }

    .deco-ring {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }
    .r1 {
        width: 280px; height: 280px;
        border: 42px solid rgba(255,255,255,0.08);
        top: -100px; left: -100px;
    }
    .r2 {
        width: 180px; height: 180px;
        border: 28px solid rgba(255,255,255,0.07);
        bottom: 20px; right: -60px;
    }
    .r3 {
        width: 110px; height: 110px;
        background: rgba(255,255,255,0.06);
        top: 100px; right: 28px;
    }
    .deco-dots {
        position: absolute;
        bottom: 80px; left: 24px;
        width: 80px; height: 80px;
        background-image: radial-gradient(rgba(255,255,255,0.20) 1.5px, transparent 1.5px);
        background-size: 13px 13px;
        pointer-events: none;
    }

    .logo {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 9px;
    }
    .logo-icon {
        width: 36px; height: 36px;
        border-radius: 8px;
        background: rgba(255,255,255,0.18);
        border: 1.5px solid rgba(255,255,255,0.32);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .logo-icon svg { width: 16px; height: 16px; stroke: white; fill: none; stroke-width: 1.8; }
    .logo-text strong {
        display: block;
        font-family: 'Syne', sans-serif;
        font-size: 0.92rem;
        font-weight: 800;
        color: white;
        letter-spacing: 0.1em;
        line-height: 1.1;
    }
    .logo-text span {
        font-size: 0.56rem;
        letter-spacing: 0.28em;
        color: rgba(255,255,255,0.6);
        text-transform: uppercase;
    }

    .left-body {
        position: relative;
        z-index: 2;
        margin-top: auto;
        margin-bottom: 12px;
    }
    .left-body h1 {
        font-family: 'Syne', sans-serif;
        font-size: 2.8rem;
        font-weight: 800;
        color: white;
        line-height: 1;
        margin-bottom: 6px;
    }
    .left-body h1 .dim {
        color: rgba(255,255,255,0.45);
    }
    .left-body .underline-bar {
        width: 36px; height: 3px;
        background: rgba(255,255,255,0.45);
        border-radius: 2px;
        margin-bottom: 16px;
    }
    .left-body p {
        font-size: 0.78rem;
        color: rgba(255,255,255,0.68);
        line-height: 1.65;
        max-width: 200px;
    }

    .left-footer {
        position: relative;
        z-index: 2;
        margin-top: 24px;
        font-size: 0.56rem;
        color: rgba(255,255,255,0.30);
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    /* ── RIGHT PANEL ── */
    .right {
        width: 64%;
        background: var(--white);
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 44px 56px;
    }

    .right-header {
        margin-bottom: 28px;
    }
    .right-header h2 {
        font-family: 'Syne', sans-serif;
        font-size: 1.7rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 5px;
    }
    .right-header p {
        font-size: 0.79rem;
        color: var(--muted);
    }

    .form-group { margin-bottom: 16px; }
    .form-group label {
        display: block;
        font-size: 0.65rem;
        font-weight: 600;
        color: var(--text);
        letter-spacing: 0.12em;
        text-transform: uppercase;
        margin-bottom: 7px;
    }
    .input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .input-wrap .icon {
        position: absolute;
        left: 13px;
        color: var(--muted);
        display: flex; align-items: center;
        pointer-events: none;
    }
    .input-wrap .icon svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 1.8; }
    .input-wrap input {
        width: 100%;
        padding: 12px 13px 12px 42px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        font-size: 0.86rem;
        font-family: 'DM Sans', sans-serif;
        color: var(--text);
        background: var(--off);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .input-wrap input::placeholder { color: #c2b8bb; font-size: 0.82rem; }
    .input-wrap input:focus {
        border-color: var(--red);
        background: white;
        box-shadow: 0 0 0 3px rgba(197,3,55,0.09);
    }
    .input-wrap .toggle-pw {
        position: absolute; right: 13px;
        background: none; border: none; cursor: pointer;
        color: var(--muted); display: flex; align-items: center; padding: 0;
        transition: color 0.15s;
    }
    .input-wrap .toggle-pw:hover { color: var(--red); }
    .input-wrap .toggle-pw svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.8; }

    .row-extra {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 8px 0 24px;
    }
    .remember {
        display: flex; align-items: center; gap: 7px;
        font-size: 0.79rem; color: var(--muted); cursor: pointer;
    }
    .remember input[type=checkbox] {
        width: 14px; height: 14px;
        accent-color: var(--red); cursor: pointer;
        border-radius: 3px;
    }
    .forgot {
        font-size: 0.79rem;
        color: var(--red);
        font-weight: 600;
        text-decoration: none;
        transition: opacity 0.15s;
    }
    .forgot:hover { opacity: 0.7; }

    .btn-signin {
        width: 100%;
        padding: 14px;
        background: var(--red);
        color: white;
        border: none;
        border-radius: 8px;
        font-family: 'Syne', sans-serif;
        font-size: 0.86rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        cursor: pointer;
        transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        box-shadow: 0 4px 16px rgba(197,3,55,0.28);
        position: relative; overflow: hidden;
    }
    .btn-signin::after {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(to right, transparent 0%, rgba(255,255,255,0.10) 50%, transparent 100%);
        transform: translateX(-100%);
        transition: transform 0.45s;
    }
    .btn-signin:hover { background: var(--red-dark); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(197,3,55,0.35); }
    .btn-signin:hover::after { transform: translateX(100%); }
    .btn-signin:active { transform: translateY(0); }

    .divider {
        display: flex; align-items: center; gap: 10px;
        margin-top: 24px;
    }
    .divider hr { flex: 1; border: none; border-top: 1px solid var(--border); }
    .divider span {
        font-size: 0.6rem;
        color: #c2b8bb;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .right-footer {
        margin-top: 16px;
        text-align: center;
        font-size: 0.74rem;
        color: var(--muted);
        line-height: 1.9;
    }
    .right-footer a {
        color: var(--red);
        text-decoration: none;
        font-weight: 500;
    }
    .right-footer a:hover { text-decoration: underline; }

    .alert-error {
        background: #fff0f3;
        border: 1px solid rgba(197,3,55,0.22);
        color: var(--red);
        border-radius: 7px;
        padding: 10px 14px;
        font-size: 0.78rem;
        margin-bottom: 18px;
        display: flex; align-items: center; gap: 8px;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
        body { overflow: auto; }
        .wrapper { padding: 16px; align-items: flex-start; min-height: 100vh; }
        .card { flex-direction: column; min-height: auto; }
        .left { width: 100%; padding: 32px 28px; min-height: 260px; }
        .left-body h1 { font-size: 2.2rem; }
        .right { width: 100%; padding: 36px 28px 32px; justify-content: flex-start; }
    }
    </style>
</head>
<body>

<div class="wrapper">
<div class="card">

    <!-- ── LEFT ── -->
    <div class="left">
        <div class="deco-ring r1"></div>
        <div class="deco-ring r2"></div>
        <div class="deco-ring r3"></div>
        <div class="deco-dots"></div>

        <div class="logo">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <div class="logo-text">
                <strong>Spekta Academy</strong>
                <span>Admin Portal</span>
            </div>
        </div>

        <div class="left-body">
            <h1>Hello,<br><span class="dim">welcome!</span></h1>
            <div class="underline-bar"></div>
            <p>Platform pembelajaran terbaik untuk mengembangkan skill dan karir profesional Anda bersama kami.</p>
        </div>

        <div class="left-footer">© 2024 Spekta Academy v2.4.0</div>
    </div>

    <!-- ── RIGHT ── -->
    <div class="right">

        @if(session('error'))
        <div class="alert-error">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
        @endif

        <div class="right-header">
            <h2>Administrator Login</h2>
            <p>Please enter your credentials to access the secure portal.</p>
        </div>

        <form action="{{ url('/login') }}" method="POST" id="loginForm">
            @csrf

            <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrap">
                    <span class="icon">
                        <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </span>
                    <input type="email" name="email" placeholder="admin@gmail.com" required autocomplete="email">
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <span class="icon">
                        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <input type="password" name="password" id="pwInput" placeholder="••••••••••••" required autocomplete="current-password">
                    <button type="button" class="toggle-pw" id="togglePw" aria-label="Toggle password">
                        <svg id="eyeIcon" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <div class="row-extra">
                <label class="remember">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="#" class="forgot">Forgot password?</a>
            </div>

            <button type="submit" class="btn-signin">Sign In</button>

        </form>

        <div class="divider">
            <hr><span>Security Assured</span><hr>
        </div>

        <div class="right-footer">
            Unauthorized access is strictly prohibited.<br>
            <a href="#">Privacy Policy</a> &nbsp;·&nbsp; <a href="#">Support</a>
        </div>

    </div>
</div>
</div>

<script>
    const pw = document.getElementById('pwInput');
    const btn = document.getElementById('togglePw');
    const eyeOn  = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
    const eyeOff = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
    btn.addEventListener('click', () => {
        const show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        btn.innerHTML = show ? eyeOff : eyeOn;
    });
</script>

</body>
</html>