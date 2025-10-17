@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<style>
    /* Style spécifique pour la page de connexion */
    body {
        background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        min-height: 100vh;
    }

    /* Masquer le main-content par défaut */
    .main-content {
        background: transparent;
        box-shadow: none;
        padding: 0;
        margin-top: 0;
    }

    /* Particules animées en arrière-plan */
    .particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 1;
        pointer-events: none;
    }

    .particle {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 15s infinite ease-in-out;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) translateX(0) rotate(0deg); opacity: 0; }
        10% { opacity: 0.3; }
        50% { transform: translateY(-100vh) translateX(50px) rotate(180deg); opacity: 0.5; }
        90% { opacity: 0.3; }
    }

    /* Container principal - CORRIGÉ */
    .login-main {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 120px 1rem 3rem 1rem; /* Marge en haut pour éviter le header */
        position: relative;
        z-index: 10;
    }

    .login-container {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        border-radius: 25px;
        box-shadow: 
            0 25px 50px rgba(0,0,0,0.3),
            0 0 0 1px rgba(255,255,255,0.3),
            inset 0 1px 0 rgba(255,255,255,0.5);
        width: 100%;
        max-width: 480px;
        padding: 0;
        overflow: hidden;
        animation: slideUp 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative; /* AJOUTÉ pour fixer la carte */
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(50px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .login-container-header {
        background: linear-gradient(135deg, var(--ofppt-green) 0%, #3ca870 100%);
        color: white;
        padding: 45px 35px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .login-container-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .header-icon {
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2.5rem;
        position: relative;
        z-index: 2;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
        50% { transform: scale(1.05); box-shadow: 0 12px 30px rgba(0,0,0,0.3); }
    }

    .login-container-header h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 12px;
        position: relative;
        z-index: 2;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .login-container-header p {
        margin: 0;
        opacity: 0.95;
        position: relative;
        z-index: 2;
        font-size: 1.05rem;
    }

    .login-form {
        padding: 50px 40px;
        position: relative;
    }

    .form-group {
        margin-bottom: 25px;
        position: relative;
    }

    .form-label {
        display: block;
        margin-bottom: 10px;
        color: var(--ofppt-dark-blue);
        font-weight: 600;
        font-size: 1rem;
        display: flex;
        align-items: center;
    }

    .form-label i {
        margin-right: 8px;
        color: var(--ofppt-green);
    }

    .input-wrapper {
        position: relative;
    }

    .form-control {
        width: 100%;
        padding: 16px 50px 16px 50px;
        border: 2px solid #e0e7ed;
        border-radius: 15px;
        font-size: 1.05rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: #f8f9fb;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--ofppt-green);
        box-shadow: 
            0 0 0 4px rgba(46, 139, 87, 0.1),
            0 8px 20px rgba(46, 139, 87, 0.15);
        background-color: white;
        transform: translateY(-2px);
    }

    .input-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--ofppt-blue);
        font-size: 1.2rem;
        transition: all 0.3s ease;
        pointer-events: none;
    }

    .toggle-password {
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--ofppt-blue);
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .toggle-password:hover {
        color: var(--ofppt-green);
        transform: translateY(-50%) scale(1.15);
    }

    .form-control:focus ~ .input-icon {
        color: var(--ofppt-green);
        transform: translateY(-50%) scale(1.15);
    }

    /* Checkbox Remember Me */
    .remember-forgot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .remember-me {
        display: flex;
        align-items: center;
    }

    .remember-me input[type="checkbox"] {
        width: 20px;
        height: 20px;
        margin-right: 10px;
        cursor: pointer;
        accent-color: var(--ofppt-green);
    }

    .remember-me label {
        color: var(--ofppt-dark-blue);
        font-weight: 500;
        cursor: pointer;
        margin: 0;
    }

    .forgot-password {
        color: var(--ofppt-blue);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .forgot-password:hover {
        color: var(--ofppt-green);
        text-decoration: underline;
    }

    .btn-login {
        width: 100%;
        padding: 18px;
        background: linear-gradient(135deg, var(--ofppt-green) 0%, #3ca870 100%);
        color: white;
        border: none;
        border-radius: 15px;
        font-size: 1.15rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(46, 139, 87, 0.3);
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .btn-login::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-login:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(46, 139, 87, 0.4);
    }

    .btn-login:hover::before {
        left: 100%;
    }

    .btn-login:active {
        transform: translateY(-1px);
    }

    .error-message {
        color: #dc3545;
        font-size: 0.9rem;
        margin-top: 10px;
        display: flex;
        align-items: center;
        padding: 10px 15px;
        background-color: rgba(220, 53, 69, 0.1);
        border-radius: 10px;
        border-left: 4px solid #dc3545;
        animation: shake 0.5s ease;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }

    .error-message i {
        margin-right: 8px;
        font-size: 1.1rem;
    }

    /* Aide et À propos */
    .help-links {
        text-align: center;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #e0e7ed;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .help-link {
        color: var(--ofppt-blue);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 15px;
        border-radius: 10px;
    }

    .help-link:hover {
        color: var(--ofppt-green);
        background-color: rgba(46, 139, 87, 0.1);
        transform: translateY(-2px);
    }

    .help-link i {
        font-size: 1.1rem;
        transition: transform 0.3s ease;
    }

    .help-link:hover i {
        transform: scale(1.2);
    }

    .separator {
        color: #d0d0d0;
        font-size: 1.2rem;
        font-weight: bold;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .login-main {
            padding: 100px 1rem 2rem 1rem; /* Ajusté pour mobile */
        }

        .login-container {
            margin: 0;
            border-radius: 20px;
        }
        
        .login-container-header {
            padding: 35px 25px;
        }

        .header-icon {
            width: 70px;
            height: 70px;
            font-size: 2rem;
        }
        
        .login-form {
            padding: 35px 25px;
        }

        .form-control {
            padding: 14px 45px 14px 45px;
        }

        .btn-login {
            padding: 16px;
            font-size: 1.05rem;
        }

        .remember-forgot {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }

        .help-links {
            flex-direction: row;
            gap: 8px;
        }

        .help-link {
            font-size: 0.9rem;
            padding: 6px 12px;
        }
    }

    @media (max-width: 576px) {
        .login-main {
            padding: 90px 0.5rem 1.5rem 0.5rem; /* Plus compact sur petit écran */
        }

        .login-container-header h2 {
            font-size: 1.6rem;
        }

        .login-container-header p {
            font-size: 0.95rem;
        }
    }
</style>

<!-- Particules animées -->
<div class="particles">
    <div class="particle" style="width: 10px; height: 10px; left: 10%; animation-delay: 0s;"></div>
    <div class="particle" style="width: 15px; height: 15px; left: 20%; animation-delay: 2s;"></div>
    <div class="particle" style="width: 8px; height: 8px; left: 30%; animation-delay: 4s;"></div>
    <div class="particle" style="width: 12px; height: 12px; left: 40%; animation-delay: 1s;"></div>
    <div class="particle" style="width: 10px; height: 10px; left: 50%; animation-delay: 3s;"></div>
    <div class="particle" style="width: 14px; height: 14px; left: 60%; animation-delay: 5s;"></div>
    <div class="particle" style="width: 9px; height: 9px; left: 70%; animation-delay: 2.5s;"></div>
    <div class="particle" style="width: 11px; height: 11px; left: 80%; animation-delay: 4.5s;"></div>
    <div class="particle" style="width: 13px; height: 13px; left: 90%; animation-delay: 1.5s;"></div>
</div>

<!-- Contenu principal -->
<div class="login-main">
    <div class="login-container">
        <div class="login-container-header">
            <div class="header-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <h2>Bienvenue</h2>
            <p>Connectez-vous pour accéder à votre espace de pilotage</p>
        </div>

        <div class="login-form">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i>
                        Adresse Email
                    </label>
                    <div class="input-wrapper">
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}"
                               placeholder="votre.email@ofppt.ma"
                               required 
                               autofocus>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    @error('email')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i>
                        Mot de Passe
                    </label>
                    <div class="input-wrapper">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Entrez votre mot de passe"
                               required>
                        <i class="fas fa-key input-icon"></i>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                    @error('password')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">Se souvenir de moi</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-password">
                        Mot de passe oublié ?
                    </a>
                </div>

                <!-- Button -->
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Se Connecter
                </button>
            </form>

            <!-- Aide et À propos -->
            <div class="help-links">
                <a href="#" class="help-link" id="openAide">
                    <i class="fas fa-question-circle"></i>
                    Aide
                </a>
                <span class="separator">•</span>
                <a href="#" class="help-link" id="openAbout">
                    <i class="fas fa-info-circle"></i>
                    À propos
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    // Animation des icônes au focus
    document.querySelectorAll('.form-control').forEach(function(input) {
        input.addEventListener('focus', function() {
            const icon = this.previousElementSibling;
            if (icon && icon.classList.contains('input-icon')) {
                icon.style.color = 'var(--ofppt-green)';
            }
        });
        
        input.addEventListener('blur', function() {
            const icon = this.previousElementSibling;
            if (icon && icon.classList.contains('input-icon')) {
                icon.style.color = 'var(--ofppt-blue)';
            }
        });
    });

    // Animation du bouton
    const loginBtn = document.querySelector('.btn-login');
    if (loginBtn) {
        loginBtn.addEventListener('mousedown', function() {
            this.style.transform = 'translateY(-1px)';
        });
        
        loginBtn.addEventListener('mouseup', function() {
            this.style.transform = 'translateY(-3px)';
        });
    }

    // Désactiver l'effet parallaxe pour éviter les conflits avec le positionnement fixe
    // Si vous souhaitez le garder, décommentez le code ci-dessous
    /*
    if (window.innerWidth > 768) {
        document.addEventListener('mousemove', function(e) {
            const container = document.querySelector('.login-container');
            if (container) {
                const x = (e.clientX - window.innerWidth / 2) / 50;
                const y = (e.clientY - window.innerHeight / 2) / 50;
                container.style.transform = `translateX(${x}px) translateY(${y}px)`;
            }
        });
    }
    */
</script>
@endpush
@endsection