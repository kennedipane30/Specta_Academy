<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Spekta Academy</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'DM Sans', sans-serif;
        min-height: 100vh;
        overflow: hidden;
        background:
            radial-gradient(circle at 10% 85%, rgba(197,3,55,0.25) 0 90px, transparent 91px),
            radial-gradient(circle at 90% 85%, rgba(197,3,55,0.22) 0 150px, transparent 151px),
            radial-gradient(circle at 85% 10%, rgba(197,3,55,0.2) 0 150px, transparent 151px),
            linear-gradient(135deg, #fff0f4 0%, #f7c5d1 45%, #ffffff 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* MAIN CARD */
    .auth-card {
        width: 1050px;
        height: 620px;
        display: flex;
        box-shadow: 0 18px 40px rgba(0,0,0,0.22);
        background: white;
    }

    /* LEFT PANEL */
    .left {
        width: 58%;
        position: relative;
        background: linear-gradient(135deg, #C50337 0%, #8f0229 55%, #3a0013 100%);
        padding: 70px 70px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    /* decorative shapes */
    .left::before {
        content: '';
        position: absolute;
        width: 340px;
        height: 340px;
        border-radius: 50%;
        border: 44px solid rgba(255,255,255,0.12);
        top: -130px;
        left: -120px;
    }

    .left::after {
        content: '';
        position: absolute;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
        bottom: -70px;
        right: 70px;
    }

    .circle-mid {
        position: absolute;
        width: 210px;
        height: 210px;
        border-radius: 50%;
        border: 35px solid rgba(255,255,255,0.08);
        top: 120px;
        right: 40px;
    }

    .circle-small {
        position: absolute;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
        top: 70px;
        left: 80px;
    }

    .dots {
        position: absolute;
        width: 130px;
        height: 130px;
        right: 45px;
        bottom: 90px;
        background-image: radial-gradient(rgba(255,255,255,0.25) 2px, transparent 2px);
        background-size: 16px 16px;
    }

    /* logo */
    .logo {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 120px;
    }

    .logo-dot {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgba(255,255,255,0.25);
        border: 2px solid rgba(255,255,255,0.45);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logo-dot::after {
        content: '';
        width: 15px;
        height: 15px;
        background: white;
        border-radius: 50%;
    }

    .logo-name {
        font-family: 'Syne', sans-serif;
        font-size: 1.05rem;
        font-weight: 800;
        color: white;
        letter-spacing: 0.12em;
        line-height: 1.1;
    }

    .logo-name small {
        display: block;
        font-size: 0.62rem;
        font-weight: 400;
        letter-spacing: 0.32em;
        opacity: 0.75;
    }

    /* headline */
    .headline {
        position: relative;
        z-index: 2;
        margin-top: auto;
        margin-bottom: 90px;
    }

    .headline h1 {
        font-family: 'Syne', sans-serif;
        font-size: 4.2rem;
        line-height: 0.95;
        color: white;
        font-weight: 800;
        margin-bottom: 30px;
    }

    .headline p {
        color: rgba(255,255,255,0.8);
        max-width: 330px;
        line-height: 1.6;
        font-size: 0.9rem;
    }

    /* RIGHT PANEL */
    .right {
        width: 42%;
        background: white;
        padding: 0 70px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* input */
    .input-group {
        margin-bottom: 12px;
    }

    .input-group label {
        display: block;
        font-size: 0.75rem;
        color: #777;
        margin-bottom: 6px;
    }

    .input-group input {
        width: 100%;
        border: 2px solid #C50337;
        border-radius: 3px;
        padding: 14px 16px;
        font-size: 0.9rem;
        outline: none;
        color: #333;
    }

    .input-group input::placeholder {
        color: #b7b7b7;
    }

    .input-group input:focus {
        box-shadow: 0 0 0 3px rgba(197,3,55,0.12);
    }

    .row-extra {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 10px 0 25px;
    }

    .remember {
        font-size: 0.78rem;
        color: #777;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .remember input {
        accent-color: #C50337;
    }

    .forgot {
        font-size: 0.78rem;
        color: #C50337;
        text-decoration: none;
        font-weight: 600;
    }

    .btn-row {
        display: flex;
        justify-content: center;
    }

    .btn-login,
    .btn-signup {
        width: 50%;
        padding: 13px;
        border-radius: 3px;
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        cursor: pointer;
        letter-spacing: 0.03em;
    }

    .btn-login {
        background: #C50337;
        border: 2px solid #C50337;
        color: white;
    }

    .btn-login:hover {
        background: #9f022d;
    }

    .btn-signup {
        background: white;
        border: 2px solid #C50337;
        color: #C50337;
    }

    .btn-signup:hover {
        background: #C50337;
        color: white;
    }

    /* social */
    .social-row {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .social-label {
        font-size: 0.7rem;
        color: #999;
        letter-spacing: 0.2em;
        text-transform: uppercase;
    }

    .social-icon {
        width: 31px;
        height: 31px;
        border-radius: 50%;
        border: 1px solid #ddd;
        color: #999;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 0.8rem;
    }

    .social-icon:hover {
        border-color: #C50337;
        color: #C50337;
    }

    .alert-error {
        background: #fff0f3;
        border: 1px solid #f5c2cc;
        color: #C50337;
        border-radius: 4px;
        padding: 10px 14px;
        font-size: 0.8rem;
        margin-bottom: 18px;
    }

    /* responsive */
    @media (max-width: 1200px) {
        body {
            overflow: auto;
            padding: 30px 16px;
        }

        .auth-card {
            width: 100%;
            height: auto;
            flex-direction: column;
        }

        .left,
        .right {
            width: 100%;
        }

        .left {
            min-height: 420px;
            padding: 45px 35px;
        }

        .headline {
            margin-bottom: 40px;
        }

        .headline h1 {
            font-size: clamp(2.6rem, 9vw, 4rem);
        }

        .right {
            padding: 45px 35px;
        }
    }
    </style>
</head>
<body>

<div class="auth-card">

    <div class="left">
        <div class="circle-mid"></div>
        <div class="circle-small"></div>
        <div class="dots"></div>

        <div class="logo">
            <div class="logo-dot"></div>
            <div class="logo-name">
                SPEKTA
                <small>ACADEMY</small>
            </div>
        </div>

        <div class="headline">
            <h1>Hello,<br>welcome!</h1>
            <p>Platform pembelajaran terbaik untuk mengembangkan skill dan karir profesional Anda bersama kami.</p>
        </div>
    </div>

    <div class="right">

        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
            @csrf

            <div class="input-group">
                <label>Email address</label>
                <input type="email" name="email" placeholder="name@mail.com" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••••••••••" required>
            </div>

            <div class="row-extra">
                <label class="remember">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="#" class="forgot">Forgot password?</a>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn-login">Login</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>