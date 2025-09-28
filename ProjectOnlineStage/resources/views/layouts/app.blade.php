<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Système de Pilotage') - OFPPT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
    --ofppt-green: #2E8B57;
    --ofppt-blue: #1E5F99;
    --ofppt-gray: #6B7280;
    --ofppt-light-gray: #F8F9FA;
    --ofppt-dark-blue: #1a4a75;
    --sidebar-width: 280px;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--ofppt-light-gray);
}

/* Header Principal */
.ofppt-header {
    background: linear-gradient(135deg, var(--ofppt-blue) 0%, var(--ofppt-dark-blue) 100%);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 0;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    width: 100%;
}

/* Top bar */
.ofppt-top-bar {
    background-color: var(--ofppt-green);
    color: white;
    padding: 8px 0;
    font-size: 0.9rem;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1001;
    width: 100%;
    transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
}

/* Décalage header + top bar quand sidebar ouverte (desktop) */
@media (min-width: 992px) {
    body.sidebar-open .ofppt-top-bar,
    body.sidebar-open .ofppt-header {
        left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
    }

    body.sidebar-open .ofppt-header { z-index: 1051; }
    body.sidebar-open .ofppt-top-bar { z-index: 1052; }
}

/* Navbar */
.ofppt-navbar {
    padding: 15px 0;
    margin-top: 50px; /* espace pour top bar */
}

.ofppt-brand {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: white !important;
    font-weight: bold;
    font-size: 1.3rem;
}
.ofppt-brand:hover { color: #e0e0e0 !important; }

.ofppt-logo {
    width: 45px;
    height: 45px;
    background: linear-gradient(45deg, var(--ofppt-green) 0%, #ffffff 50%, var(--ofppt-blue) 100%);
    border-radius: 12px;
    margin-right: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ofppt-blue);
    font-weight: bold;
    font-size: 1.4rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}
.ofppt-logo:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
}

/* Liens */
.navbar-nav .nav-link {
    color: rgba(255,255,255,0.9) !important;
    font-weight: 500;
    margin: 0 5px;
    transition: all 0.3s ease;
}
.navbar-nav .nav-link:hover {
    color: white !important;
    transform: translateY(-1px);
}

/* User info */
.user-info {
    background-color: rgba(255,255,255,0.1);
    border-radius: 25px;
    padding: 8px 15px;
    margin-right: 10px;
    font-size: 0.9rem;
}

/* Logout */
.btn-logout {
    background: linear-gradient(45deg, #dc3545, #c82333);
    border: none;
    border-radius: 20px;
    padding: 8px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
}
.btn-logout:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(220,53,69,0.3);
}

/* Sidebar toggle */
.sidebar-toggle {
    background: linear-gradient(45deg, rgba(255,255,255,0.15), rgba(255,255,255,0.05));
    color: white;
    border: 2px solid rgba(255,255,255,0.2);
    border-radius: 12px;
    padding: 12px 15px;
    font-size: 1.3rem;
    transition: all 0.3s ease;
    display: none;
    margin-right: 15px;
    position: relative;
    overflow: hidden;
}
.sidebar-toggle:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}
.sidebar-toggle:hover:before { left: 100%; }
.sidebar-toggle:hover {
    background: linear-gradient(45deg, rgba(255,255,255,0.25), rgba(255,255,255,0.15));
    border-color: rgba(255,255,255,0.4);
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.sidebar-toggle.show { display: inline-block; animation: slideInLeft 0.4s ease-out; }
@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}

/* Breadcrumb */
.ofppt-breadcrumb {
    background: white;
    border-radius: 10px;
    padding: 15px 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.breadcrumb { margin: 0; }
.breadcrumb-item a { color: var(--ofppt-blue); text-decoration: none; }
.breadcrumb-item.active { color: var(--ofppt-gray); }

/* Main content */
.main-content {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 30px;
    margin-top: 140px;
}

/* Footer */
.ofppt-footer {
    background: linear-gradient(135deg, var(--ofppt-dark-blue) 0%, var(--ofppt-blue) 100%);
    color: white;
    padding: 40px 0 20px;
    margin-top: 50px;
    position: relative;
    z-index: 100;
}
.footer-section h5 {
    color: var(--ofppt-green);
    margin-bottom: 20px;
    font-weight: 600;
}
.footer-section ul { list-style: none; padding: 0; }
.footer-section ul li { margin-bottom: 8px; }
.footer-section ul li a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: color 0.3s ease;
}
.footer-section ul li a:hover { color: white; }
.footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.2);
    margin-top: 30px;
    padding-top: 20px;
    text-align: center;
    color: rgba(255,255,255,0.7);
}

/* Overlay */
.sidebar-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1050;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}
.sidebar-overlay.show {
    opacity: 1;
    visibility: visible;
}

/* Sidebar */
#sidebar {
    position: fixed !important;
    top: 0; left: -280px;
    width: var(--sidebar-width);
    height: 100vh;
    z-index: 1060;
    transition: left 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    box-shadow: 2px 0 15px rgba(0,0,0,0.1);
}
#sidebar.show { left: 0; }

/* Animations */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.fade-in-up { animation: fadeInUp 0.6s ease-out; }

/* Responsive */
@media (max-width: 768px) {
    .ofppt-logo { width: 35px; height: 35px; font-size: 1rem; }
    .ofppt-brand { font-size: 1.1rem; }
    .main-content { padding: 20px; margin: 130px 10px 20px; }
    .ofppt-top-bar .col-md-4 { display: none; }
    .sidebar-toggle { padding: 10px 12px; font-size: 1.2rem; }
}

/* Rôle */
.role-indicator {
    background: rgba(255,255,255,0.15);
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    margin-left: 10px;
    display: inline-block;
}

/* Sidebar open body */
body.sidebar-open { overflow-x: hidden; }

    </style>
</head>
<body>
    {{-- Inclusion de la sidebar selon le rôle de l'utilisateur --}}
    @auth
        @if(Auth::user()->role === 'directeur_etablissement')
            @include('layouts.sidebar-etablissement')
        @elseif(Auth::user()->role === 'directeur_complexe')
            @include('layouts.sidebar-complexe')
        @endif
    @endauth

    {{-- Overlay pour fermer la sidebar en cliquant à l'extérieur --}}
    <div class="sidebar-overlay" onclick="closeSidebar()"></div>

    {{-- Barre supérieure --}}
    <div class="ofppt-top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Office de la Formation Professionnelle et de la Promotion du Travail
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-phone me-2"></i>
                    <span class="me-3">0537 76 42 00</span>
                    <i class="fas fa-envelope me-2"></i>
                    <span>contact@ofppt.ma</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Header Principal --}}
    <header class="ofppt-header">
        <nav class="navbar navbar-expand-lg ofppt-navbar">
            <div class="container">
                {{-- Bouton toggle sidebar et brand ensemble --}}
                <div class="d-flex align-items-center">
                    @auth
                        @if(Auth::user()->role === 'directeur_etablissement' || Auth::user()->role === 'directeur_complexe')
                            <button class="sidebar-toggle show" onclick="toggleSidebar()">
                                <i class="fas fa-bars"></i>
                            </button>
                        @endif
                    @endauth
                    
                    <a class="ofppt-brand" href="@auth
                        @if(Auth::user()->role === 'directeur_complexe')
                            {{ route('administration.complexe.dashboard') }}
                        @elseif(Auth::user()->role === 'directeur_etablissement')
                            {{ route('administration.etablissement.dashboard') }}
                        @else
                            {{ route('administration.complexe.dashboard') }}
                        @endif
                    @else
                        {{ route('login') }}
                    @endauth">
                        <div class="ofppt-logo">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        Système de Pilotage
                    </a>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                        style="border: none; padding: 4px 8px;">
                    <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        @auth
                            <li class="nav-item">
                                <span class="nav-link user-info">
                                    <i class="fas fa-user-circle me-2"></i>
                                    Bonjour, {{ Auth::user()->nom }}
                                    
                                    @if(Auth::user()->role === 'directeur_complexe')
                                        <span class="role-indicator">
                                            <i class="fas fa-building me-1"></i>Directeur Complexe
                                        </span>
                                    @elseif(Auth::user()->role === 'directeur_etablissement')
                                        <span class="role-indicator">
                                            <i class="fas fa-school me-1"></i>Directeur Établissement
                                        </span>
                                    @endif
                                </span>
                            </li>
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-logout">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Déconnexion
                                    </button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Connexion
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    {{-- Fil d'Ariane --}}
    @hasSection('breadcrumb')
        <div class="container mt-4">
            <div class="ofppt-breadcrumb">
                @yield('breadcrumb')
            </div>
        </div>
    @endif

    {{-- Contenu principal --}}
    <div class="container mt-4">
        <div class="main-content fade-in-up">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    {{-- Footer --}}
    <footer class="ofppt-footer">
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
                        <li><a href="https://www.ofppt.ma">Site officiel OFPPT</a></li>
                        <li><a href="https://www.myway.ac.ma">MyWay - Orientation</a></li>
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
        // Animation au scroll
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.ofppt-header');
            if (window.scrollY > 100) {
                header.style.boxShadow = '0 4px 20px rgba(0,0,0,0.15)';
            } else {
                header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            }
        });

        // Animation pour les alertes
        document.querySelectorAll('.alert').forEach(function(alert) {
            setTimeout(function() {
                if (alert.classList.contains('show')) {
                    alert.classList.remove('show');
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                }
            }, 5000);
        });

        // Afficher le bouton toggle seulement si une sidebar est présente
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.querySelector('.sidebar-toggle');
            
            if (sidebar && toggleButton) {
                // Animation d'entrée améliorée
                setTimeout(() => {
                    toggleButton.style.transform = 'scale(1.1) rotate(5deg)';
                    setTimeout(() => {
                        toggleButton.style.transform = 'scale(1) rotate(0deg)';
                    }, 200);
                }, 500);
            }
        });

        // Gestion du redimensionnement de la fenêtre
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar && window.innerWidth > 768 && sidebar.classList.contains('show')) {
                closeSidebar();
            }
        });

        // Fonction globale pour toggle la sidebar
        window.toggleSidebar = function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const body = document.body;
            
            if (sidebar && overlay) {
                const isOpen = sidebar.classList.contains('show');
                
                if (isOpen) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                    body.classList.remove('sidebar-open');
                } else {
                    sidebar.classList.add('show');
                    overlay.classList.add('show');
                    body.classList.add('sidebar-open');
                }
            }
        };

        // Fonction globale pour fermer la sidebar
        window.closeSidebar = function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const body = document.body;
            
            if (sidebar && overlay) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                body.classList.remove('sidebar-open');
            }
        };

        // Fermer la sidebar avec la touche Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>