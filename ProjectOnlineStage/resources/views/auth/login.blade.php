<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Système de Pilotage OFPPT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --ofppt-green: #2E8B57;
            --ofppt-blue: #1E5F99;
            --ofppt-dark-blue: #1a4a75;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--ofppt-blue) 0%, var(--ofppt-dark-blue) 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow-x: hidden;
        }

        /* Effet de fond avec motifs */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(46, 139, 87, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
        }

        /* Top bar */
        .ofppt-top-bar {
            background-color: var(--ofppt-green);
            color: white;
            padding: 8px 0;
            font-size: 0.9rem;
            position: relative;
            z-index: 10;
        }

        /* Header */
        .login-header {
            background: linear-gradient(135deg, var(--ofppt-blue) 0%, var(--ofppt-dark-blue) 100%);
            padding: 20px 0;
            position: relative;
            z-index: 10;
        }

        .ofppt-brand {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: white !important;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .ofppt-brand:hover {
            color: #e0e0e0 !important;
        }

        .ofppt-logo {
            width: 55px;
            height: 55px;
            background: linear-gradient(45deg, var(--ofppt-green) 0%, #ffffff 50%, var(--ofppt-blue) 100%);
            border-radius: 15px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ofppt-blue);
            font-weight: bold;
            font-size: 1.6rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }

        .ofppt-logo:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        }

        /* Container principal */
        .login-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            z-index: 10;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
            padding: 0;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .login-container-header {
            background: linear-gradient(135deg, var(--ofppt-green) 0%, #3ca870 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .login-container-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23pattern)"/></svg>');
            opacity: 0.3;
        }

        .login-container-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }

        .login-container-header p {
            margin: 0;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .login-form {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--ofppt-dark-blue);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--ofppt-green);
            box-shadow: 0 0 0 3px rgba(46, 139, 87, 0.1);
            background-color: white;
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ofppt-blue);
            opacity: 0.7;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--ofppt-green) 0%, #3ca870 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(46, 139, 87, 0.3);
        }

        .error-message {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 8px;
            display: flex;
            align-items: center;
        }

        .error-message i {
            margin-right: 5px;
        }

        .back-home {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .back-home a {
            color: var(--ofppt-blue);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .back-home a:hover {
            color: var(--ofppt-green);
            transform: translateX(-3px);
        }

        /* Footer */
        .login-footer {
            background: linear-gradient(135deg, var(--ofppt-dark-blue) 0%, var(--ofppt-blue) 100%);
            color: white;
            text-align: center;
            padding: 20px;
            position: relative;
            z-index: 10;
        }

        .login-footer p {
            margin: 0;
            opacity: 0.8;
            font-size: 0.9rem;
        }

        /* Animations */
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

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .ofppt-logo {
                width: 45px;
                height: 45px;
                font-size: 1.3rem;
            }
            
            .ofppt-brand {
                font-size: 1.2rem;
            }
            
            .login-container {
                margin: 1rem;
                border-radius: 15px;
            }
            
            .login-container-header {
                padding: 25px 20px;
            }
            
            .login-form {
                padding: 30px 25px;
            }
        }

        @media (max-width: 576px) {
            .ofppt-top-bar .d-none {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Barre supérieure -->
    <div class="ofppt-top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Office de la Formation Professionnelle et de la Promotion du Travail
                </div>
                <div class="col-md-4 text-end d-none d-md-block">
                    <i class="fas fa-phone me-2"></i>
                    <span class="me-3">0537 76 42 00</span>
                    <i class="fas fa-envelope me-2"></i>
                    <span>contact@ofppt.ma</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="login-header">
        <div class="container">
            <div class="d-flex justify-content-center">
                <a class="ofppt-brand" href="{{ route('welcome') }}">
                    <div class="ofppt-logo">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    Système de Pilotage
                </a>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="login-main">
        <div class="login-container fade-in-up">
            <div class="login-container-header">
                <h2><i class="fas fa-sign-in-alt me-2"></i>Connexion</h2>
                <p>Veuillez vous connecter pour accéder à votre espace de travail</p>
            </div>

            <div class="login-form">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Adresse Email
                        </label>
                        <div class="position-relative">
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control" 
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

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Mot de Passe
                        </label>
                        <div class="position-relative">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control"
                                   placeholder="Votre mot de passe"
                                   required>
                            <i class="fas fa-key input-icon"></i>
                        </div>
                        @error('password')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Se Connecter
                    </button>
                </form>

                <div class="back-home">
                    <a href="{{ route('welcome') }}">
                        <i class="fas fa-arrow-left me-2"></i>
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="login-footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} OFPPT - Office de la Formation Professionnelle et de la Promotion du Travail. Tous droits réservés.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animation au focus des inputs
        document.querySelectorAll('.form-control').forEach(function(input) {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('.input-icon').style.color = 'var(--ofppt-green)';
                this.parentElement.querySelector('.input-icon').style.transform = 'translateY(-50%) scale(1.1)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.querySelector('.input-icon').style.color = 'var(--ofppt-blue)';
                this.parentElement.querySelector('.input-icon').style.transform = 'translateY(-50%) scale(1)';
            });
        });

        // Animation du bouton de connexion
        document.querySelector('.btn-login').addEventListener('click', function() {
            this.style.transform = 'translateY(0)';
            setTimeout(() => {
                this.style.transform = 'translateY(-2px)';
            }, 150);
        });
    </script>
</body>
</html>