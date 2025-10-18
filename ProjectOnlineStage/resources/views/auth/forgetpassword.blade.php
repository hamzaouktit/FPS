@extends('layouts.app')

@section('title', 'Mot de passe oublié')

@section('content')
<style>
    /* Style spécifique pour la page de mot de passe oublié */
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

    /* Container principal */
    .forgot-main {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 120px 1rem 3rem 1rem;
        position: relative;
        z-index: 10;
    }

    .forgot-container {
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
        position: relative;
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

    .forgot-container-header {
        background: linear-gradient(135deg, var(--ofppt-green) 0%, #3ca870 100%);
        color: white;
        padding: 45px 35px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .forgot-container-header::before {
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

    .forgot-container-header h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 12px;
        position: relative;
        z-index: 2;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .forgot-container-header p {
        margin: 0;
        opacity: 0.95;
        position: relative;
        z-index: 2;
        font-size: 1.05rem;
    }

    .forgot-form {
        padding: 50px 40px;
        position: relative;
    }

    /* Alerts */
    .alert {
        padding: 16px 20px;
        border-radius: 15px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        font-weight: 500;
        border: none;
        animation: slideDown 0.5s ease;
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

    .alert i {
        margin-right: 12px;
        font-size: 1.3rem;
    }

    .alert-danger {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.1) 100%);
        color: #dc3545;
        border-left: 4px solid #dc3545;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.15) 0%, rgba(40, 167, 69, 0.1) 100%);
        color: #28a745;
        border-left: 4px solid #28a745;
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

    .form-control:focus ~ .input-icon {
        color: var(--ofppt-green);
        transform: translateY(-50%) scale(1.15);
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

    .btn-submit {
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
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-submit::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(46, 139, 87, 0.4);
    }

    .btn-submit:hover::before {
        left: 100%;
    }

    .btn-submit:active {
        transform: translateY(-1px);
    }

    .back-link {
        text-align: center;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #e0e7ed;
    }

    .back-link a {
        color: var(--ofppt-blue);
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 12px;
    }

    .back-link a:hover {
        color: var(--ofppt-green);
        background-color: rgba(46, 139, 87, 0.1);
        transform: translateY(-2px);
    }

    .back-link a i {
        font-size: 1.1rem;
        transition: transform 0.3s ease;
    }

    .back-link a:hover i {
        transform: translateX(-5px);
    }

    /* Info box */
    .info-box {
        background: linear-gradient(135deg, rgba(0, 123, 255, 0.1) 0%, rgba(0, 123, 255, 0.05) 100%);
        border-left: 4px solid var(--ofppt-blue);
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 25px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .info-box i {
        color: var(--ofppt-blue);
        font-size: 1.4rem;
        margin-top: 2px;
    }

    .info-box-content {
        flex: 1;
    }

    .info-box-content p {
        margin: 0;
        color: var(--ofppt-dark-blue);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .forgot-main {
            padding: 100px 1rem 2rem 1rem;
        }

        .forgot-container {
            margin: 0;
            border-radius: 20px;
        }
        
        .forgot-container-header {
            padding: 35px 25px;
        }

        .header-icon {
            width: 70px;
            height: 70px;
            font-size: 2rem;
        }
        
        .forgot-form {
            padding: 35px 25px;
        }

        .form-control {
            padding: 14px 45px 14px 45px;
        }

        .btn-submit {
            padding: 16px;
            font-size: 1.05rem;
        }
    }

    @media (max-width: 576px) {
        .forgot-main {
            padding: 90px 0.5rem 1.5rem 0.5rem;
        }

        .forgot-container-header h2 {
            font-size: 1.6rem;
        }

        .forgot-container-header p {
            font-size: 0.95rem;
        }

        .info-box {
            padding: 15px;
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
<div class="forgot-main">
    <div class="forgot-container">
        <div class="forgot-container-header">
            <div class="header-icon">
                <i class="fas fa-key"></i>
            </div>
            <h2>Mot de passe oublié</h2>
            <p>Récupérez l'accès à votre compte</p>
        </div>

        <div class="forgot-form">
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <div class="info-box-content">
                    <p>Entrez votre adresse email et nous vous enverrons un code de vérification pour réinitialiser votre mot de passe.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('forgot.password.send') }}">
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
                        <i class="fas fa-at input-icon"></i>
                    </div>
                    @error('email')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Button -->
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    Envoyer le code
                </button>
            </form>

            <!-- Retour à la connexion -->
            <div class="back-link">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la page de connexion
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Animation des icônes au focus
    document.querySelectorAll('.form-control').forEach(function(input) {
        input.addEventListener('focus', function() {
            const icon = this.nextElementSibling;
            if (icon && icon.classList.contains('input-icon')) {
                icon.style.color = 'var(--ofppt-green)';
            }
        });
        
        input.addEventListener('blur', function() {
            const icon = this.nextElementSibling;
            if (icon && icon.classList.contains('input-icon')) {
                icon.style.color = 'var(--ofppt-blue)';
            }
        });
    });

    // Animation du bouton
    const submitBtn = document.querySelector('.btn-submit');
    if (submitBtn) {
        submitBtn.addEventListener('mousedown', function() {
            this.style.transform = 'translateY(-1px)';
        });
        
        submitBtn.addEventListener('mouseup', function() {
            this.style.transform = 'translateY(-3px)';
        });
    }
</script>
@endpush
@endsection