<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue - Système de Pilotage OFPPT</title>
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
                radial-gradient(circle at 20% 30%, rgba(46, 139, 87, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(46, 139, 87, 0.08) 0%, transparent 50%);
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
        .welcome-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px 0;
            position: relative;
            z-index: 10;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
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
            text-decoration: none;
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
            transform: scale(1.05) rotate(5deg);
            box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        }

        .auth-buttons .btn {
            margin-left: 10px;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-login {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-login:hover {
            background: white;
            color: var(--ofppt-blue);
            border-color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
        }

        .btn-logout {
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: white;
            border: none;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        }

        /* Main content */
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 1rem;
            position: relative;
            z-index: 10;
            min-height: 70vh;
        }

        .welcome-container {
            text-align: center;
            color: white;
            max-width: 800px;
        }

        .welcome-message {
            font-size: 3.5rem;
            font-weight: 300;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        .subtitle {
            font-size: 1.3rem;
            margin-bottom: 3rem;
            opacity: 0.9;
            text-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .success-message {
            background: rgba(40, 167, 69, 0.9);
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            display: inline-block;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInDown 0.8s ease-out;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            padding: 2.5rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: fadeInUp 1s ease-out;
            position: relative;
        }

        .user-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            border-radius: 20px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .user-info:hover::before {
            opacity: 1;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, var(--ofppt-green), #3ca870);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .user-info h2 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .user-details {
            font-size: 1.1rem;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .role-badge {
            background: linear-gradient(45deg, var(--ofppt-green), #3ca870);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 500;
            display: inline-block;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(46, 139, 87, 0.3);
        }

        .dashboard-link {
            background: linear-gradient(45deg, var(--ofppt-green), #3ca870);
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            display: inline-block;
            margin-top: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .dashboard-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .dashboard-link:hover::before {
            left: 100%;
        }

        .dashboard-link:hover {
            color: white;
            text-decoration: none;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(46, 139, 87, 0.4);
        }

        /* Features section */
        .features-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 4rem 0;
            position: relative;
            z-index: 10;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            color: white;
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--ofppt-green);
        }

        .feature-card h4 {
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .feature-card p {
            opacity: 0.9;
            margin: 0;
        }

        /* Footer */
        .welcome-footer {
            background: linear-gradient(135deg, var(--ofppt-dark-blue) 0%, #0f2a44 100%);
            color: white;
            padding: 3rem 0 1rem;
            position: relative;
            z-index: 10;
        }

        .footer-section h5 {
            color: var(--ofppt-green);
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 8px;
        }

        .footer-section ul li a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: var(--ofppt-green);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: 2rem;
            padding-top: 1.5rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
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

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
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
            .welcome-message {
                font-size: 2.5rem;
            }
            
            .subtitle {
                font-size: 1.1rem;
            }
            
            .user-info {
                padding: 2rem 1.5rem;
            }
            
            .ofppt-brand {
                font-size: 1.2rem;
            }
            
            .ofppt-logo {
                width: 45px;
                height: 45px;
                font-size: 1.3rem;
            }
            
            .main-content {
                padding: 2rem 1rem;
            }
        }

        @media (max-width: 576px) {
            .ofppt-top-bar .d-none {
                display: none !important;
            }
            
            .welcome-message {
                font-size: 2rem;
            }
            
            .feature-card {
                margin-bottom: 1rem;
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
    <div class="welcome-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a class="ofppt-brand" href="{{ route('welcome') }}">
                    <div class="ofppt-logo">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    Système de Pilotage
                </a>
                
                <div class="auth-buttons">
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-logout">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Se déconnecter
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Se connecter
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="main-content">
        <div class="welcome-container">
            @if(session('success'))
                <div class="success-message">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @auth
                <div class="user-info">
                    <div class="user-avatar">
                        @if(Auth::user()->role == 'directeur_complexe')
                            <i class="fas fa-building"></i>
                        @elseif(Auth::user()->role == 'directeur_etablissement')
                            <i class="fas fa-school"></i>
                        @else
                            <i class="fas fa-user"></i>
                        @endif
                    </div>
                    
                    <h2>Bienvenue, {{ Auth::user()->nom }} !</h2>
                    
                    <div class="user-details">
                        <i class="fas fa-user-circle me-2"></i>
                        Vous êtes connecté en tant que : 
                    </div>
                    
                    <div class="role-badge">
                        @if(Auth::user()->role == 'directeur_complexe')
                            <i class="fas fa-building me-2"></i>Directeur de Complexe
                        @elseif(Auth::user()->role == 'directeur_etablissement')
                            <i class="fas fa-school me-2"></i>Directeur d'Établissement
                        @else
                            <i class="fas fa-user me-2"></i>Utilisateur
                        @endif
                    </div>
                    
                    @if(Auth::user()->role == 'directeur_complexe')
                        <a href="{{ route('administration.complexe.dashboard') }}" class="dashboard-link">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Accéder au tableau de bord du complexe
                        </a>
                    @elseif(Auth::user()->role == 'directeur_etablissement')
                        <a href="{{ route('administration.etablissement.dashboard') }}" class="dashboard-link">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Accéder au tableau de bord de l'établissement
                        </a>
                    @endif
                </div>
            @else
                <h1 class="welcome-message fade-in-up">
                    Bienvenue dans notre Système de Pilotage
                </h1>
                <p class="subtitle fade-in-up">
                    Connectez-vous pour accéder à votre espace de travail personnalisé
                </p>
                <div class="fade-in-up">
                    <a href="{{ route('login') }}" class="dashboard-link">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Se connecter
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Section des fonctionnalités -->
    @guest
        <div class="features-section">
            <div class="container">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="text-white mb-3">Fonctionnalités de la Plateforme</h2>
                        <p class="text-white-50">Découvrez les outils de gestion intégrés pour l'OFPPT</p>
                    </div>
                </div>
                
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <h4>Gestion des Complexes</h4>
                            <p>Administration complète des complexes de formation avec suivi des établissements affiliés.</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <h4>Gestion des Établissements</h4>
                            <p>Outils de pilotage pour les directeurs d'établissements : formations, groupes, formateurs.</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h4>Tableaux de Bord</h4>
                            <p>Visualisation en temps réel des données et indicateurs clés de performance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endguest

    <!-- Footer -->
    <footer class="welcome-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 footer-section">
                    <h5><i class="fas fa-building me-2"></i>À propos de l'OFPPT</h5>
                    <p class="text-light">
                        L'Office de la Formation Professionnelle et de la Promotion du Travail, 
                        établissement public créé en 1974, est le premier opérateur de formation professionnelle au Maroc.
                    </p>
                </div>
                <div class="col-md-4 footer-section">
                    <h5><i class="fas fa-link me-2"></i>Liens utiles</h5>
                    <ul>
                        <li><a href="https://www.ofppt.ma" target="_blank">Site officiel OFPPT</a></li>
                        <li><a href="https://www.myway.ac.ma" target="_blank">MyWay - Orientation</a></li>
                        <li><a href="#" onclick="return false;">Formation continue</a></li>
                        <li><a href="#" onclick="return false;">Insertion professionnelle</a></li>
                    </ul>
                </div>
                <div class="col-md-4 footer-section">
                    <h5><i class="fas fa-envelope me-2"></i>Contact</h5>
                    <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>Angle Bd Bir Anzarane et Rue Bachir Lazrak, Casablanca</p>
                    <p class="mb-2"><i class="fas fa-phone me-2"></i>+212 537 76 42 00</p>
                    <p><i class="fas fa-envelope me-2"></i>contact@ofppt.ma</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} OFPPT - Office de la Formation Professionnelle et de la Promotion du Travail. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animation des cartes de fonctionnalités
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = `all 0.6s ease ${index * 0.2}s`;
            observer.observe(card);
        });

        // Animation du logo OFPPT
        document.querySelector('.ofppt-logo').addEventListener('mouseenter', function() {
            this.style.animation = 'none';
            setTimeout(() => {
                this.style.animation = 'pulse 1s ease-in-out';
            }, 10);
        });

        // Animation des boutons au clic
        document.querySelectorAll('.dashboard-link, .btn-login').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.3);
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    pointer-events: none;
                `;
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Animation CSS pour l'effet ripple
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>