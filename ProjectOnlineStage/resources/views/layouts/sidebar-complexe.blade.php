{{-- Sidebar Overlay --}}
<div class="sidebar-overlay" onclick="closeSidebar()"></div>

{{-- Sidebar pour Directeur Complexe --}}
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-building"></i>
        </div>
        <h5><i class="fas fa-city me-2"></i>Directeur Complexe</h5>
        <p class="mb-0 text-light opacity-75">{{ Auth::user()->nom }}</p>
        <button class="sidebar-close" onclick="closeSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <div class="sidebar-nav-item">
            <a href="{{ route('administration.complexe.dashboard') }}" 
               class="sidebar-nav-link {{ request()->routeIs('administration.complexe.dashboard') ? 'active' : '' }}">
                <div class="nav-icon-wrapper">
                    <i class="fas fa-tachometer-alt sidebar-nav-icon"></i>
                </div>
                <span>Tableau de Bord</span>
                <div class="nav-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
        </div>
        
        <div class="sidebar-nav-item">
            <a href="#" 
               class="sidebar-nav-link">
                <div class="nav-icon-wrapper">
                    <i class="fas fa-user-tie sidebar-nav-icon"></i>
                </div>
                <span>Gestion Directeurs</span>
                <div class="nav-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
        </div>
        
       <div class="sidebar-nav-item">
            <a href="{{ route('administration.complexe.etablissements.index') }}" 
            class="sidebar-nav-link {{ request()->routeIs('administration.complexe.etablissements.*') ? 'active' : '' }}">
                <div class="nav-icon-wrapper">
                    <i class="fas fa-school sidebar-nav-icon"></i>
                </div>
                <span>Gestion Établissements</span>
                <div class="nav-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
        </div>
        <div class="sidebar-nav-item">
            <a href="#" class="sidebar-nav-link">
                <div class="nav-icon-wrapper">
                    <i class="fas fa-chart-line sidebar-nav-icon"></i>
                </div>
                <span>Rapports Globaux</span>
                <div class="nav-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
        </div>

        <div class="sidebar-nav-item">
            <a href="#" class="sidebar-nav-link">
                <div class="nav-icon-wrapper">
                    <i class="fas fa-users sidebar-nav-icon"></i>
                </div>
                <span>Effectifs par Établissement</span>
                <div class="nav-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
        </div>

        <div class="sidebar-nav-item">
            <a href="#" class="sidebar-nav-link">
                <div class="nav-icon-wrapper">
                    <i class="fas fa-graduation-cap sidebar-nav-icon"></i>
                </div>
                <span>Formations du Complexe</span>
                <div class="nav-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>
        </div>
    </nav>
        
    <div class="sidebar-footer">
        <div class="sidebar-footer-content">
            <i class="fas fa-info-circle me-2"></i>
            <span>Version 2.0</span>
        </div>
    </div>
</div>

<style>
    /* Styles pour la sidebar complexe - Version optimisée */
    :root {
        --sidebar-width: 280px;
        --ofppt-blue: #1e3a8a;
        --ofppt-dark-blue: #1e40af;
        --ofppt-green: #2e8b57;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: var(--sidebar-width);
        background: linear-gradient(180deg, 
            var(--ofppt-blue) 0%, 
            var(--ofppt-dark-blue) 50%, 
            #0f2a44 100%);
        transform: translateX(-100%);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        z-index: 999;
        overflow-y: auto;
        padding-top: 0;
        box-shadow: 4px 0 20px rgba(0,0,0,0.15);
        border-right: 3px solid var(--ofppt-green);
    }

    .sidebar.show {
        transform: translateX(0);
    }

    /* Header de la sidebar amélioré */
    .sidebar-header {
        padding: 25px 20px;
        border-bottom: 2px solid rgba(46,139,87,0.3);
        color: white;
        text-align: center;
        position: relative;
        background: linear-gradient(135deg, 
            rgba(46,139,87,0.1) 0%, 
            rgba(46,139,87,0.05) 100%);
    }

    .sidebar-logo {
        width: 60px;
        height: 60px;
        background: linear-gradient(45deg, var(--ofppt-green), #3ca870);
        border-radius: 50%;
        margin: 0 auto 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
        box-shadow: 0 8px 25px rgba(46,139,87,0.3);
        transition: all 0.3s ease;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .sidebar-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,0.1);
        border: none;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        color: white;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sidebar-close:hover {
        background: rgba(220,53,69,0.8);
        transform: rotate(90deg);
    }

    .sidebar-header h5 {
        margin: 0 0 8px 0;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .sidebar-header p {
        font-size: 0.9rem;
        margin: 0;
    }

    /* Navigation améliorée */
    .sidebar-nav {
        padding: 25px 0;
        flex: 1;
    }

    .sidebar-nav-item {
        margin-bottom: 2px;
        position: relative;
    }

    .sidebar-nav-item:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: var(--ofppt-green);
        transform: scaleY(0);
        transition: transform 0.3s ease;
        border-radius: 0 4px 4px 0;
    }

    .sidebar-nav-item:hover:before,
    .sidebar-nav-item:has(.active):before {
        transform: scaleY(1);
    }

    .sidebar-nav-link {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
    }

    .sidebar-nav-link:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, 
            transparent, 
            rgba(255,255,255,0.1), 
            transparent);
        transition: left 0.5s ease;
    }

    .sidebar-nav-link:hover:before {
        left: 100%;
    }

    .sidebar-nav-link:hover {
        background: linear-gradient(135deg, 
            rgba(46,139,87,0.2) 0%, 
            rgba(255,255,255,0.1) 100%);
        color: white;
        transform: translateX(8px);
        text-decoration: none;
    }

    .sidebar-nav-link.active {
        background: linear-gradient(135deg, 
            rgba(46,139,87,0.3) 0%, 
            rgba(46,139,87,0.15) 100%);
        color: white;
        box-shadow: inset 0 0 20px rgba(46,139,87,0.2);
    }

    .nav-icon-wrapper {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.1);
        border-radius: 12px;
        margin-right: 15px;
        transition: all 0.3s ease;
        position: relative;
    }

    .sidebar-nav-link:hover .nav-icon-wrapper,
    .sidebar-nav-link.active .nav-icon-wrapper {
        background: var(--ofppt-green);
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 4px 15px rgba(46,139,87,0.3);
    }

    .sidebar-nav-icon {
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }

    .sidebar-nav-link span {
        flex: 1;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .nav-arrow {
        opacity: 0;
        transform: translateX(-10px);
        transition: all 0.3s ease;
        color: var(--ofppt-green);
    }

    .sidebar-nav-link:hover .nav-arrow,
    .sidebar-nav-link.active .nav-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    /* Footer de la sidebar */
    .sidebar-footer {
        padding: 20px;
        border-top: 1px solid rgba(255,255,255,0.1);
        margin-top: auto;
    }

    .sidebar-footer-content {
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255,255,255,0.6);
        font-size: 0.85rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            width: 100vw;
        }
        
        .sidebar-header {
            padding: 20px;
        }
        
        .sidebar-logo {
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
        }
        
        .nav-icon-wrapper {
            width: 40px;
            height: 40px;
        }
    }
</style>

<script>
    // Fonctions pour contrôler la sidebar complexe - Version améliorée
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        if (sidebar && overlay) {
            const isOpen = sidebar.classList.contains('show');
            
            if (isOpen) {
                closeSidebar();
            } else {
                sidebar.classList.add('show');
                overlay.classList.add('show');
            }
        }
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        if (sidebar && overlay) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }
    }
</script>