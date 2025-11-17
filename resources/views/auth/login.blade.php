<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ShanKoMee Provider | Login</title>

    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
            position: relative;
            background: #0a0a0f;
        }

        /* Animated Gaming Background */
        .gaming-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background: 
                linear-gradient(135deg, #1a0033 0%, #0a0a0f 50%, #001122 100%),
                radial-gradient(circle at 20% 50%, rgba(138, 43, 226, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(0, 191, 255, 0.1) 0%, transparent 50%);
            background-size: 100% 100%, 200% 200%, 200% 200%;
            animation: bgShift 15s ease infinite;
        }

        @keyframes bgShift {
            0%, 100% { background-position: 0% 0%, 0% 0%, 100% 100%; }
            50% { background-position: 100% 100%, 50% 50%, 0% 0%; }
        }

        /* Animated Grid Pattern */
        .grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            background-image: 
                linear-gradient(rgba(0, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
            pointer-events: none;
        }

        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        /* Scan Line Effect */
        .scan-line {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 255, 0.5), transparent);
            z-index: 10;
            animation: scanMove 3s linear infinite;
            pointer-events: none;
        }

        @keyframes scanMove {
            0% { top: 0; opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }

        .login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            z-index: 2;
        }

        .login-container {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 3;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-logo {
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
        }

        .login-logo h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 3rem;
            font-weight: 900;
            color: #00ffff;
            text-shadow: 
                0 0 10px #00ffff,
                0 0 20px #00ffff,
                0 0 30px #00ffff,
                0 0 40px #8a2be2;
            letter-spacing: 4px;
            margin-bottom: 0.5rem;
            animation: neonPulse 2s ease-in-out infinite alternate;
            position: relative;
        }

        @keyframes neonPulse {
            from {
                text-shadow: 
                    0 0 10px #00ffff,
                    0 0 20px #00ffff,
                    0 0 30px #00ffff,
                    0 0 40px #8a2be2;
            }
            to {
                text-shadow: 
                    0 0 20px #00ffff,
                    0 0 30px #00ffff,
                    0 0 40px #00ffff,
                    0 0 50px #8a2be2,
                    0 0 60px #8a2be2;
            }
        }

        .login-subtitle {
            color: rgba(0, 255, 255, 0.8);
            font-size: 1rem;
            font-weight: 300;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Gaming Card with Neon Border */
        .glass-card {
            background: rgba(10, 10, 15, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 2px solid transparent;
            background-clip: padding-box;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.5),
                inset 0 0 20px rgba(0, 255, 255, 0.1);
            padding: 3rem 2.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 20px;
            padding: 2px;
            background: linear-gradient(135deg, #00ffff, #8a2be2, #00ffff);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            animation: borderGlow 3s ease infinite;
            z-index: -1;
        }

        @keyframes borderGlow {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 12px 48px rgba(0, 0, 0, 0.7),
                0 0 30px rgba(0, 255, 255, 0.3),
                inset 0 0 30px rgba(0, 255, 255, 0.15);
        }

        .login-box-msg {
            text-align: center;
            font-size: 1.2rem;
            color: rgba(0, 255, 255, 0.9);
            margin-bottom: 2rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Enhanced Input Groups */
        .modern-input-group {
            position: relative;
            margin-bottom: 2rem;
        }

        .modern-input-group label {
            display: block;
            color: rgba(0, 255, 255, 0.8);
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        .modern-input-group input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            background: rgba(0, 255, 255, 0.05);
            border: 2px solid rgba(0, 255, 255, 0.3);
            border-radius: 12px;
            color: #00ffff;
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            font-family: 'Poppins', sans-serif;
        }

        .modern-input-group input::placeholder {
            color: rgba(0, 255, 255, 0.4);
        }

        .modern-input-group input:focus {
            outline: none;
            border-color: #00ffff;
            background: rgba(0, 255, 255, 0.1);
            box-shadow: 
                0 0 20px rgba(0, 255, 255, 0.4),
                inset 0 0 10px rgba(0, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .modern-input-group input:valid {
            border-color: rgba(0, 255, 0, 0.5);
        }

        .modern-input-group .input-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(0, 255, 255, 0.7);
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            pointer-events: auto;
        }

        .modern-input-group .input-icon:hover {
            color: #00ffff;
            transform: translateY(-50%) scale(1.2);
            text-shadow: 0 0 10px #00ffff;
        }

        /* Enhanced Alert Styles */
        .modern-alert {
            background: rgba(255, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 0, 0, 0.5);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            color: #ff4444;
            animation: slideDown 0.5s ease-out, shake 0.5s ease;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .modern-alert .close {
            color: #ff4444;
            opacity: 0.8;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: none;
            border: none;
            padding: 0;
            line-height: 1;
        }

        .modern-alert .close:hover {
            opacity: 1;
            transform: scale(1.2);
        }

        /* Gaming Checkbox */
        .modern-checkbox {
            display: flex;
            align-items: center;
            color: rgba(0, 255, 255, 0.9);
            font-size: 0.95rem;
            cursor: pointer;
            user-select: none;
        }

        .modern-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
            accent-color: #00ffff;
            filter: drop-shadow(0 0 5px #00ffff);
        }

        /* Gaming Button */
        .modern-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #00ffff 0%, #8a2be2 100%);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 
                0 4px 15px rgba(0, 255, 255, 0.4),
                inset 0 0 20px rgba(255, 255, 255, 0.1);
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            overflow: hidden;
            font-family: 'Orbitron', sans-serif;
        }

        .modern-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .modern-btn:hover::before {
            left: 100%;
        }

        .modern-btn:hover {
            transform: translateY(-3px);
            box-shadow: 
                0 6px 25px rgba(0, 255, 255, 0.6),
                0 0 30px rgba(138, 43, 226, 0.4),
                inset 0 0 30px rgba(255, 255, 255, 0.2);
            background: linear-gradient(135deg, #00ffff 0%, #8a2be2 50%, #00ffff 100%);
            background-size: 200% 100%;
            animation: gradientShift 2s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .modern-btn:active {
            transform: translateY(-1px);
        }

        .modern-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            animation: none;
        }

        .modern-btn .btn-loader {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 10px;
        }

        .modern-btn.loading .btn-loader {
            display: inline-block;
        }

        .modern-btn.loading .btn-text {
            opacity: 0.7;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
        }

        /* Error Messages */
        .error-message {
            color: #ff4444;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: block;
            animation: fadeIn 0.3s ease;
            text-shadow: 0 0 10px rgba(255, 68, 68, 0.5);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Floating Gaming Particles */
        .particle {
            position: fixed;
            width: 4px;
            height: 4px;
            background: #00ffff;
            border-radius: 50%;
            pointer-events: none;
            animation: float 4s infinite ease-in-out;
            z-index: 1;
            box-shadow: 0 0 10px #00ffff;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) translateX(50px) scale(1);
                opacity: 0;
            }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .login-logo h2 {
                font-size: 2rem;
            }

            .glass-card {
                padding: 2rem 1.5rem;
            }

            .form-row {
                flex-direction: column;
                gap: 1rem;
            }

            .modern-checkbox {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .login-logo h2 {
                font-size: 1.7rem;
            }

            .glass-card {
                padding: 1.5rem 1.2rem;
            }
        }
    </style>
</head>

<body class="login-page">
    <div class="gaming-bg"></div>
    <div class="grid-overlay"></div>
    <div class="scan-line"></div>
    
    <div class="login-container">
        <div class="login-logo">
            <h2>ShanKoMeProvider BO</h2>
            <p class="login-subtitle">Welcome to Gaming Portal</p>
        </div>
        
        <div class="glass-card">
            <p class="login-box-msg">Sign in to Continue</p>
            
            @if(session('error'))
                <div class="modern-alert" role="alert" id="errorAlert">
                    <span>{{ session('error') }}</span>
                    <button type="button" class="close" onclick="this.parentElement.remove()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                
                <div class="modern-input-group">
                    <label for="user_name">Username</label>
                    <input 
                        type="text"
                        id="user_name"
                        class="@error('user_name') is-invalid @enderror" 
                        name="user_name"
                        value="{{ old('user_name') }}" 
                        required 
                        placeholder="Enter your username" 
                        autofocus
                        autocomplete="username">
                    <span class="input-icon">
                        <i class="fas fa-user"></i>
                    </span>
                    @error('user_name')
                        <span class="error-message">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="modern-input-group">
                    <label for="password">Password</label>
                    <input 
                        id="password" 
                        type="password"
                        class="@error('password') is-invalid @enderror" 
                        name="password" 
                        required
                        placeholder="Enter your password"
                        autocomplete="current-password">
                    <span class="input-icon" onclick="togglePassword()" id="toggleIcon" title="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </span>
                    @error('password')
                        <span class="error-message">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="form-row">
                    <div class="modern-checkbox">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember Me</label>
                    </div>
                </div>
                
                <button type="submit" class="modern-btn" id="submitBtn">
                    <span class="btn-loader"></span>
                    <span class="btn-text">Sign In</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Password Toggle Function
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const toggleIcon = document.getElementById("toggleIcon");
            const iconElement = toggleIcon.querySelector('i');

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                iconElement.classList.remove('fa-eye');
                iconElement.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = "password";
                iconElement.classList.remove('fa-eye-slash');
                iconElement.classList.add('fa-eye');
            }
        }

        // Form Submission Handler
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        loginForm.addEventListener('submit', function(e) {
            const username = document.getElementById('user_name').value.trim();
            const password = document.getElementById('password').value;

            if (!username || !password) {
                e.preventDefault();
                return false;
            }

            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });

        // Input Validation Feedback
        const inputs = document.querySelectorAll('.modern-input-group input');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.classList.add('valid');
                } else {
                    this.classList.remove('valid');
                }
            });

            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Auto-dismiss error alert after 5 seconds
        const errorAlert = document.getElementById('errorAlert');
        if (errorAlert) {
            setTimeout(() => {
                errorAlert.style.animation = 'slideDown 0.5s ease-out reverse';
                setTimeout(() => errorAlert.remove(), 500);
            }, 5000);
        }

        // Floating Gaming Particles Effect
        function createParticles() {
            const colors = ['#00ffff', '#8a2be2', '#00ff00', '#ff00ff'];
            for (let i = 0; i < 15; i++) {
                setTimeout(() => {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.left = Math.random() * window.innerWidth + 'px';
                    particle.style.top = window.innerHeight + 'px';
                    particle.style.background = colors[Math.floor(Math.random() * colors.length)];
                    particle.style.boxShadow = `0 0 10px ${particle.style.background}`;
                    particle.style.animationDelay = Math.random() * 2 + 's';
                    particle.style.animationDuration = (3 + Math.random() * 3) + 's';
                    document.body.appendChild(particle);

                    setTimeout(() => {
                        particle.remove();
                    }, 10000);
                }, i * 300);
            }
        }

        // Create particles periodically
        createParticles();
        setInterval(createParticles, 8000);

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Enter to submit
            if (e.key === 'Enter' && document.activeElement.tagName !== 'BUTTON') {
                const form = document.getElementById('loginForm');
                if (form) {
                    form.requestSubmit();
                }
            }
        });

        // Prevent form resubmission on back button
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

</body>

</html>
