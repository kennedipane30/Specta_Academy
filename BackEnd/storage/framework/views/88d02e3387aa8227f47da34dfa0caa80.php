<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login | Spekta Academy</title>
    
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&amp;family=Inter:wght@400;500&amp;display=swap" rel="stylesheet"/>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                      "outline": "#8e706c",
                      "on-primary-fixed-variant": "#910a0c",
                      "outline-variant": "#e2beba",
                      "inverse-primary": "#ffb4aa",
                      "tertiary": "#535252",
                      "surface-container-highest": "#e1e3e4",
                      "tertiary-fixed": "#e5e2e1",
                      "surface-dim": "#d9dadb",
                      "primary-fixed": "#ffdad5",
                      "secondary-fixed-dim": "#68d7da",
                      "inverse-surface": "#2e3132",
                      "error-container": "#ffdad6",
                      "on-secondary": "#ffffff",
                      "on-surface": "#191c1d",
                      "surface-container": "#edeeef",
                      "surface-container-lowest": "#ffffff",
                      "on-primary": "#ffffff",
                      "surface-variant": "#e1e3e4",
                      "surface-tint": "#b32821",
                      "surface-bright": "#f8f9fa",
                      "on-tertiary-container": "#eeebeb",
                      "surface-container-high": "#e7e8e9",
                      "on-surface-variant": "#5a413d",
                      "on-primary-container": "#ffe7e4",
                      "tertiary-fixed-dim": "#c8c6c5",
                      "error": "#ba1a1a",
                      "primary": "#a21b17",
                      "on-tertiary-fixed-variant": "#474746",
                      "on-secondary-fixed-variant": "#004f51",
                      "on-secondary-container": "#007072",
                      "secondary-fixed": "#86f4f7",
                      "primary-fixed-dim": "#ffb4aa",
                      "on-tertiary": "#ffffff",
                      "on-error-container": "#93000a",
                      "inverse-on-surface": "#f0f1f2",
                      "surface": "#f8f9fa",
                      "secondary": "#00696c",
                      "primary-container": "#c5352c",
                      "on-secondary-fixed": "#002021",
                      "secondary-container": "#86f4f7",
                      "on-primary-fixed": "#410001",
                      "tertiary-container": "#6b6a6a",
                      "background": "#f8f9fa",
                      "on-tertiary-fixed": "#1c1b1b",
                      "surface-container-low": "#f3f4f5",
                      "on-error": "#ffffff",
                      "on-background": "#191c1d"
              },
              "borderRadius": {
                      "DEFAULT": "0.125rem",
                      "lg": "0.25rem",
                      "xl": "0.5rem",
                      "full": "0.75rem"
              },
              "spacing": {
                      "md": "16px",
                      "gutter": "24px",
                      "xs": "4px",
                      "xl": "40px",
                      "lg": "24px",
                      "margin-mobile": "16px",
                      "margin-desktop": "64px",
                      "sm": "8px",
                      "base": "4px"
              },
              "fontFamily": {
                      "label-sm": ["Hanken Grotesk"],
                      "body-lg": ["Inter"],
                      "label-md": ["Hanken Grotesk"],
                      "body-md": ["Inter"],
                      "headline-md": ["Hanken Grotesk"],
                      "display-lg": ["Hanken Grotesk"],
                      "headline-lg-mobile": ["Hanken Grotesk"],
                      "headline-lg": ["Hanken Grotesk"]
              },
              "fontSize": {
                      "label-sm": ["12px", {"lineHeight": "16px", "letterSpacing": "0.04em", "fontWeight": "700"}],
                      "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                      "label-md": ["14px", {"lineHeight": "20px", "letterSpacing": "0.02em", "fontWeight": "700"}],
                      "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                      "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                      "display-lg": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                      "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                      "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}]
              }
            },
          },
        }
    </script>
    <style>
        .glass-island {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 12px 40px 0 rgba(0, 0, 0, 0.04);
        }
        .mesh-gradient {
            background-color: #f8f9fa;
            background-image: 
                radial-gradient(at 0% 0%, rgba(197, 53, 44, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(0, 105, 108, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(197, 53, 44, 0.1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(0, 105, 108, 0.1) 0px, transparent 50%);
            animation: meshFlow 20s ease-in-out infinite alternate;
        }
        @keyframes meshFlow {
            0% { background-position: 0% 0%; }
            100% { background-position: 100% 100%; }
        }
        .input-glow:focus-within {
            box-shadow: 0 0 15px rgba(46, 168, 171, 0.25);
            border-color: #2ea8ab;
        }
        .portal-pill {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Kunci Font-Family Icon */
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined' !important;
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24 !important;
            display: inline-block;
            direction: ltr;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
        }
    </style>
</head>
<body class="font-body-md text-on-surface mesh-gradient min-h-screen flex flex-col justify-between">

    <main class="flex-grow flex items-center justify-center px-margin-mobile md:px-margin-desktop py-12">
        <!-- Glass Island Login Card -->
        <div class="glass-island w-full max-w-[440px] p-lg md:p-xl rounded-xl animate-in fade-in slide-in-from-bottom-8 duration-700">
            
            <div class="text-center mb-xl">
                <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight mb-xs">Spekta Academy</h1>
                <p class="font-label-md text-label-md text-on-surface-variant">Enter your credentials to access the portal</p>
            </div>

            <!-- Portal Selector -->
            <div class="flex p-base bg-surface-container rounded-full mb-lg relative">
                <button type="button" class="portal-pill flex-1 py-sm font-label-md text-label-md text-on-secondary-container rounded-full z-10" id="portal-admin" onclick="switchPortal('admin')">
                    Admin
                </button>
                <button type="button" class="portal-pill flex-1 py-sm font-label-md text-label-md text-on-surface-variant rounded-full z-10" id="portal-teacher" onclick="switchPortal('teacher')">
                    Teacher
                </button>
                <!-- Penanda Slider Latar Belakang -->
                <div class="absolute top-base bottom-base left-base w-[calc(50%-4px)] bg-secondary-container rounded-full transition-transform duration-300" id="portal-slider"></div>
            </div>

            <!-- ALERT ERROR (Style Glassmorphism) -->
            <?php if(session('error')): ?>
                <div class="flex items-center gap-2 p-md bg-red-100 text-red-800 rounded-lg text-xs font-semibold border border-red-200 mb-6">
                    <span class="material-symbols-outlined text-red-600 text-[18px]">error</span>
                    <span><?php echo e(session('error')); ?></span>
                </div>
            <?php endif; ?>

            <!-- Login Form (Mengarahkan ke URL /login) -->
            <form class="space-y-lg" method="POST" action="<?php echo e(url('/login')); ?>" id="loginForm">
                <?php echo csrf_field(); ?> 

                <!-- Hidden Input untuk Role yang dinamis saat slider di-klik -->
                <input type="hidden" name="role" id="roleInput" value="admin">

                <!-- Input Email -->
                <div class="space-y-sm">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase ml-xs">Institutional Email</label>
                    <div class="input-glow relative flex items-center bg-surface-container-lowest border border-outline-variant rounded-lg transition-all">
                        <span class="material-symbols-outlined ml-md text-on-surface-variant">alternate_email</span>
                        <input class="w-full bg-transparent border-none focus:ring-0 py-md px-md text-body-md font-body-md text-on-surface placeholder:text-outline" placeholder="name@spekta.edu" type="email" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="email" autofocus/>
                    </div>
                </div>

                <!-- Input Password -->
                <div class="space-y-sm">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase ml-xs">Password</label>
                    <div class="input-glow relative flex items-center bg-surface-container-lowest border border-outline-variant rounded-lg transition-all">
                        <span class="material-symbols-outlined ml-md text-on-surface-variant">lock</span>
                        <input id="passwordInput" class="w-full bg-transparent border-none focus:ring-0 py-md px-md text-body-md font-body-md text-on-surface placeholder:text-outline" placeholder="••••••••" type="password" name="password" required autocomplete="current-password"/>
                        <!-- Tombol Intip Sandi Interaktif -->
                        <button class="mr-md text-on-surface-variant hover:text-secondary transition-colors" type="button" onclick="togglePasswordVisibility()">
                            <span class="material-symbols-outlined" id="visibilityIcon">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Tombol Sign In (Sangat Minimalis Tanpa Checkbox) -->
                <button type="submit" class="w-full py-md bg-primary text-on-primary font-headline-md text-headline-md rounded-lg shadow-lg hover:bg-primary-container transition-all active:scale-[0.98] mt-xl">
                    Sign In
                </button>
            </form>
        </div>
    </main>

    <!-- Footer Minimalis -->
    <footer class="flex flex-col items-center py-8 px-margin-mobile md:px-margin-desktop w-full bg-transparent">
        <p class="font-label-sm text-label-sm text-on-tertiary-fixed-variant opacity-80">© <?php echo e(date('Y')); ?> Spekta Academy. All rights reserved.</p>
    </footer>

    <!-- Background Decorative Bubbles -->
    <div class="fixed inset-0 -z-10 pointer-events-none overflow-hidden">
        <div class="absolute top-[-10%] right-[-5%] w-[400px] h-[400px] rounded-full bg-secondary opacity-5 blur-[100px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[400px] h-[400px] rounded-full bg-primary opacity-5 blur-[100px] animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- JS Logic -->
    <script>
        // Logika Perpindahan Portal Slider Sempurna
        function switchPortal(type) {
            const adminBtn = document.getElementById('portal-admin');
            const teacherBtn = document.getElementById('portal-teacher');
            const slider = document.getElementById('portal-slider');
            const roleInput = document.getElementById('roleInput');

            if (type === 'admin') {
                slider.style.transform = 'translateX(0)';
                adminBtn.classList.remove('text-on-surface-variant');
                adminBtn.classList.add('text-on-secondary-container');
                teacherBtn.classList.remove('text-on-secondary-container');
                teacherBtn.classList.add('text-on-surface-variant');
                if (roleInput) roleInput.value = 'admin';
            } else {
                slider.style.transform = 'translateX(calc(100% + 4px))';
                teacherBtn.classList.remove('text-on-surface-variant');
                teacherBtn.classList.add('text-on-secondary-container');
                adminBtn.classList.remove('text-on-secondary-container');
                adminBtn.classList.add('text-on-surface-variant');
                if (roleInput) roleInput.value = 'teacher';
            }
        }

        // Logika Intip Password (Show / Hide Password)
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('passwordInput');
            const visibilityIcon = document.getElementById('visibilityIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                visibilityIcon.innerText = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                visibilityIcon.innerText = 'visibility';
            }
        }

        // Efek Atmosfer Mouse-Move Interaktif pada Latar Belakang Mesh
        document.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth) * 100;
            const y = (e.clientY / window.innerHeight) * 100;
            document.body.style.backgroundImage = `
                radial-gradient(at ${x}% ${y}%, rgba(197, 53, 44, 0.12) 0px, transparent 50%),
                radial-gradient(at ${100-x}% ${100-y}%, rgba(0, 105, 108, 0.12) 0px, transparent 50%),
                radial-gradient(at 0% 0%, rgba(197, 53, 44, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(0, 105, 108, 0.1) 0px, transparent 50%)
            `;
        });
    </script>
</body>
</html><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/auth/login.blade.php ENDPATH**/ ?>