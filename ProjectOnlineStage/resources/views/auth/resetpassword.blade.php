@extends('layouts.app')

@section('title', 'Réinitialiser le mot de passe')

@section('content')
<style>
    /* Style spécifique pour la page de réinitialisation */
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
    .reset-main {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 120px 1rem 3rem 1rem;
        position: relative;
        z-index: 10;
    }

    .reset-container {
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

    .reset-container-header {
        background: linear-gradient(135deg, var(--ofppt-green) 0%, #3ca870 100%);
        color: white;
        padding: 45px 35px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .reset-container-header::before {
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

    .reset-container-header h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 12px;
        position: relative;
        z-index: 2;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .reset-container-header p {
        margin: 0;
        opacity: 0.95;
        position: relative;
        z-index: 2;
        font-size: 1.05rem;
    }

    .reset-form {
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

    .info-box-content ul {
        margin: 8px 0 0 0;
        padding-left: 20px;
        color: var(--ofppt-dark-blue);
        font-size: 0.9rem;
    }

    .info-box-content ul li {
        margin: 4px 0;
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

    /* Password strength indicator */
    .password-strength {
        margin-top: 10px;
        height: 6px;
        background-color: #e0e7ed;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
    }

    .password-strength-bar {
        height: 100%;
        width: 0%;
        transition: all 0.3s ease;
        border-radius: 10px;
    }

    .strength-weak { 
        width: 33%; 
        background: linear-gradient(90deg, #dc3545, #ff6b7a);
    }

    .strength-medium { 
        width: 66%; 
        background: linear-gradient(90deg, #ffc107, #ffda6a);
    }

    .strength-strong { 
        width: 100%; 
        background: linear-gradient(90deg, #28a745, #5cb85c);
    }

    .strength-text {
        font-size: 0.85rem;
        margin-top: 6px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .strength-text.weak { color: #dc3545; }
    .strength-text.medium { color: #ffc107; }
    .strength-text.strong { color: #28a745; }

    .btn-reset {
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
        margin-top: 30px;
    }

    .btn-reset::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-reset:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(46, 139, 87, 0.4);
    }

    .btn-reset:hover::before {
        left: 100%;
    }

    .btn-reset:active {
        transform: translateY(-1px);
    }

    /* Match indicator */
    .match-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        padding: 8px 12px;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .match-indicator.match {
        color: #28a745;
        background-color: rgba(40, 167, 69, 0.1);
    }

    .match-indicator.no-match {
        color: #dc3545;
        background-color: rgba(220, 53, 69, 0.1);
    }

    .match-indicator i {
        font-size: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .reset-main {
            padding: 100px 1rem 2rem 1rem;
        }

        .reset-container {
            margin: 0;
            border-radius: 20px;
        }
        
        .reset-container-header {
            padding: 35px 25px;
        }

        .header-icon {
            width: 70px;
            height: 70px;
            font-size: 2rem;
        }
        
        .reset-form {
            padding: 35px 25px;
        }

        .form-control {
            padding: 14px 45px 14px 45px;
        }

        .btn-reset {
            padding: 16px;
            font-size: 1.05rem;
        }
    }

    @media (max-width: 576px) {
        .reset-main {
            padding: 90px 0.5rem 1.5rem 0.5rem;
        }

        .reset-container-header h2 {
            font-size: 1.6rem;
        }

        .reset-container-header p {
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
<div class="reset-main">
    <div class="reset-container">
        <div class="reset-container-header">
            <div class="header-icon">
                <i class="fas fa-lock-open"></i>
            </div>
            <h2>Nouveau mot de passe</h2>
            <p>Créez un mot de passe sécurisé</p>
        </div>

        <div class="reset-form">
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
                <i class="fas fa-shield-alt"></i>
                <div class="info-box-content">
                    <p><strong>Votre mot de passe doit contenir :</strong></p>
                    <ul>
                        <li>Au moins 8 caractères</li>
                        <li>Une lettre majuscule et minuscule</li>
                        <li>Un chiffre</li>
                        <li>Un caractère spécial (@$!%*?&)</li>
                    </ul>
                </div>
            </div>

            <form method="POST" action="{{ route('forgot.password.reset') }}" id="resetForm">
                @csrf
                
                <!-- New Password -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-key"></i>
                        Nouveau mot de passe
                    </label>
                    <div class="input-wrapper">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Entrez votre nouveau mot de passe"
                               required>
                        <i class="fas fa-lock input-icon"></i>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                    
                    <!-- Password Strength Indicator -->
                    <div class="password-strength" id="passwordStrength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="strength-text" id="strengthText"></div>
                    
                    @error('password')
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock"></i>
                        Confirmer le mot de passe
                    </label>
                    <div class="input-wrapper">
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="form-control"
                               placeholder="Confirmez votre mot de passe"
                               required>
                        <i class="fas fa-check-double input-icon"></i>
                        <i class="fas fa-eye toggle-password" id="togglePasswordConfirm"></i>
                    </div>
                    
                    <!-- Match Indicator -->
                    <div class="match-indicator" id="matchIndicator" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                        <span id="matchText"></span>
                    </div>
                </div>

                <!-- Button -->
                <button type="submit" class="btn-reset">
                    <i class="fas fa-sync-alt"></i>
                    Réinitialiser le mot de passe
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordConfirmInput = document.getElementById('password_confirmation');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    if (togglePasswordConfirm && passwordConfirmInput) {
        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        
        if (password.length === 0) {
            strengthBar.className = 'password-strength-bar';
            strengthText.innerHTML = '';
            return;
        }
        
        // Length check
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        
        // Character variety checks
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[@$!%*?&]/.test(password)) strength++;
        
        // Update UI
        strengthBar.className = 'password-strength-bar';
        strengthText.className = 'strength-text';
        
        if (strength <= 2) {
            strengthBar.classList.add('strength-weak');
            strengthText.classList.add('weak');
            strengthText.innerHTML = '<i class="fas fa-times-circle"></i> Faible';
        } else if (strength <= 4) {
            strengthBar.classList.add('strength-medium');
            strengthText.classList.add('medium');
            strengthText.innerHTML = '<i class="fas fa-exclamation-circle"></i> Moyen';
        } else {
            strengthBar.classList.add('strength-strong');
            strengthText.classList.add('strong');
            strengthText.innerHTML = '<i class="fas fa-check-circle"></i> Fort';
        }
    }

    // Check password match
    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = passwordConfirmInput.value;
        const matchIndicator = document.getElementById('matchIndicator');
        const matchText = document.getElementById('matchText');
        
        if (confirmPassword.length === 0) {
            matchIndicator.style.display = 'none';
            return;
        }
        
        matchIndicator.style.display = 'flex';
        
        if (password === confirmPassword) {
            matchIndicator.className = 'match-indicator match';
            matchIndicator.innerHTML = '<i class="fas fa-check-circle"></i><span>Les mots de passe correspondent</span>';
        } else {
            matchIndicator.className = 'match-indicator no-match';
            matchIndicator.innerHTML = '<i class="fas fa-times-circle"></i><span>Les mots de passe ne correspondent pas</span>';
        }
    }

    // Event listeners
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordMatch();
        });
    }

    if (passwordConfirmInput) {
        passwordConfirmInput.addEventListener('input', checkPasswordMatch);
    }

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
    const resetBtn = document.querySelector('.btn-reset');
    if (resetBtn) {
        resetBtn.addEventListener('mousedown', function() {
            this.style.transform = 'translateY(-1px)';
        });
        
        resetBtn.addEventListener('mouseup', function() {
            this.style.transform = 'translateY(-3px)';
        });
    }
</script>
@endpush
@endsection