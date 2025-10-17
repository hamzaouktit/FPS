@extends('layouts.app')

@section('title', 'Accueil - Complexe de Formation OFPPT Marrakech')

@section('content')
<style>
    .hero-section {
        background: linear-gradient(135deg, #1E5F99 0%, #2E8B57 100%);
        color: white;
        padding: 80px 0 60px;
        margin: -30px -30px 40px -30px;
        border-radius: 15px 15px 0 0;
        position: relative;
        overflow: hidden;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }
    
    .hero-content {
        position: relative;
        z-index: 1;
    }
    
    .hero-title {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }
    
    .hero-subtitle {
        font-size: 1.3rem;
        opacity: 0.95;
        margin-bottom: 30px;
    }
    
    .stats-row {
        margin-top: 40px;
    }
    
    .stat-card {
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        background: rgba(255,255,255,0.25);
        transform: translateY(-5px);
    }
    
    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
        opacity: 0.9;
    }
    
    .stat-number {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 1rem;
        opacity: 0.9;
    }
    
    .etablissement-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        height: 100%;
        border: 2px solid transparent;
    }
    
    .etablissement-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        border-color: #2E8B57;
    }
    
    .etablissement-header {
        background: linear-gradient(135deg, #1E5F99 0%, #2E8B57 100%);
        color: white;
        padding: 30px 25px;
        position: relative;
        overflow: hidden;
    }
    
    .etablissement-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    
    .etablissement-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.9;
    }
    
    .etablissement-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 10px;
        line-height: 1.4;
    }
    
    .etablissement-code {
        background: rgba(255,255,255,0.2);
        display: inline-block;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .etablissement-body {
        padding: 25px;
    }
    
    .info-item {
        display: flex;
        align-items: start;
        margin-bottom: 15px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .info-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }
    
    .info-icon {
        font-size: 1.2rem;
        color: #1E5F99;
        margin-right: 12px;
        margin-top: 2px;
        min-width: 25px;
    }
    
    .info-text {
        flex: 1;
        font-size: 0.95rem;
        color: #495057;
        line-height: 1.5;
    }
    
    .quick-links {
        margin-top: 25px;
        padding-top: 20px;
        border-top: 2px solid #e9ecef;
    }
    
    .quick-links-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1E5F99;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    
    .quick-links-title i {
        margin-right: 8px;
    }
    
    .btn-link-external {
        display: inline-flex;
        align-items: center;
        padding: 10px 18px;
        background: linear-gradient(135deg, #1E5F99 0%, #2E8B57 100%);
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s ease;
        margin: 5px;
        border: none;
    }
    
    .btn-link-external:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(30, 95, 153, 0.3);
        color: white;
    }
    
    .btn-link-external i {
        margin-right: 8px;
        font-size: 0.85rem;
    }
    
    .resources-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 20px;
        padding: 40px;
        margin-top: 50px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
    
    .resources-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1E5F99;
        margin-bottom: 30px;
        text-align: center;
    }
    
    .resource-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        height: 100%;
    }
    
    .resource-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .resource-icon {
        font-size: 3.5rem;
        color: #2E8B57;
        margin-bottom: 20px;
    }
    
    .resource-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #1E5F99;
        margin-bottom: 15px;
    }
    
    .resource-description {
        color: #6c757d;
        margin-bottom: 20px;
        line-height: 1.6;
    }
    
    .btn-resource {
        display: inline-flex;
        align-items: center;
        padding: 12px 25px;
        background: linear-gradient(135deg, #2E8B57 0%, #1E5F99 100%);
        color: white;
        text-decoration: none;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-resource:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 20px rgba(46, 139, 87, 0.3);
        color: white;
    }
    
    .btn-resource i {
        margin-left: 8px;
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
        }
        
        .etablissement-card {
            margin-bottom: 30px;
        }
        
        .stat-card {
            margin-bottom: 15px;
        }
    }
    
    .section-divider {
        height: 4px;
        background: linear-gradient(90deg, transparent, #2E8B57, transparent);
        margin: 50px 0;
        border-radius: 2px;
    }
</style>

<!-- Section Hero -->
<div class="hero-section">
    <div class="hero-content">
        <div class="container">
            <div class="text-center">
                <h1 class="hero-title">
                    <i class="fas fa-graduation-cap me-3"></i>
                    Complexe de Formation Professionnelle
                </h1>
                <p class="hero-subtitle">
                    OFPPT - Région Marrakech-Safi
                </p>
                <p class="lead mb-4">
                    4 Instituts spécialisés dédiés à l'excellence et à l'innovation pédagogique
                </p>
            </div>
            
            <!-- Statistiques -->
            <div class="row stats-row">
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-number">4</div>
                        <div class="stat-label">Instituts</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number">2000+</div>
                        <div class="stat-label">Stagiaires</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-number">150+</div>
                        <div class="stat-label">Formateurs</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="stat-number">25+</div>
                        <div class="stat-label">Filières</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Nos Établissements -->
<div class="container mt-5">
    <div class="text-center mb-5">
        <h2 class="display-5 fw-bold text-primary">
            <i class="fas fa-school me-3"></i>
            Nos Instituts de Formation
        </h2>
        <p class="lead text-muted">Découvrez nos 4 établissements d'excellence</p>
    </div>
    
    <div class="row g-4">
        <!-- ISGI Marrakech -->
        <div class="col-lg-6">
            <div class="etablissement-card">
                <a href="https://www.ofppt.ma/fr/etablissements?region=7&ville=42" target="_blank" style="text-decoration: none; color: inherit;">
                    <div class="etablissement-header">
                        <div class="etablissement-icon">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <h3 class="etablissement-title">
                            Institut Spécialisé de Gestion et d'Informatique
                        </h3>
                        <span class="etablissement-code">ISGI MARRAKECH</span>
                    </div>
                </a>
                <div class="etablissement-body">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt info-icon"></i>
                        <div class="info-text">
                            <strong>Adresse :</strong> Marrakech, Maroc
                        </div>
                    </div>
                    <a href="https://www.myway.ac.ma/etablissements?search=ISGI+Marrakech" target="_blank" style="text-decoration: none; color: inherit;">
                        <div class="info-item">
                            <i class="fas fa-briefcase info-icon"></i>
                            <div class="info-text">
                                <strong>Spécialités :</strong> Informatique, Gestion, Commerce, Management
                                <i class="fas fa-external-link-alt ms-2" style="font-size: 0.8rem; color: #2E8B57;"></i>
                            </div>
                        </div>
                    </a>
                    <div class="info-item">
                        <i class="fas fa-star info-icon"></i>
                        <div class="info-text">
                            <strong>Points forts :</strong> Formation en développement web, applications mobiles, gestion d'entreprise
                        </div>
                    </div>
                    
                    <div class="quick-links">
                        <div class="quick-links-title">
                            <i class="fas fa-external-link-alt"></i>
                            Accès rapide
                        </div>
                        <div class="d-flex flex-wrap">
                            <a href="https://www.myway.ac.ma/etablissements?search=ISGI+Marrakech" target="_blank" class="btn-link-external">
                                <i class="fas fa-compass"></i>
                                Voir sur MyWay
                            </a>
                            <a href="https://www.ofppt.ma/fr/etablissements?region=7&ville=42" target="_blank" class="btn-link-external">
                                <i class="fas fa-info-circle"></i>
                                Infos établissement
                            </a>
                            <a href="https://www.ofppt.ma/fr/candidature" target="_blank" class="btn-link-external">
                                <i class="fas fa-file-alt"></i>
                                S'inscrire
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ISTA Bab Doukkala -->
        <div class="col-lg-6">
            <div class="etablissement-card">
                <div class="etablissement-header">
                    <div class="etablissement-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3 class="etablissement-title">
                        Institut Spécialisé de Technologie Appliquée Bab Doukkala
                    </h3>
                    <span class="etablissement-code">ISTA BAB DOUKKALA</span>
                </div>
                <div class="etablissement-body">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt info-icon"></i>
                        <div class="info-text">
                            <strong>Adresse :</strong> Bab Doukkala, Marrakech
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-briefcase info-icon"></i>
                        <div class="info-text">
                            <strong>Spécialités :</strong> Électromécanique, Maintenance industrielle, Énergies renouvelables
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-star info-icon"></i>
                        <div class="info-text">
                            <strong>Points forts :</strong> Ateliers modernes, équipements industriels de pointe
                        </div>
                    </div>
                    
                    <div class="quick-links">
                        <div class="quick-links-title">
                            <i class="fas fa-external-link-alt"></i>
                            Accès rapide
                        </div>
                        <div class="d-flex flex-wrap">
                            <a href="https://www.myway.ac.ma" target="_blank" class="btn-link-external">
                                <i class="fas fa-compass"></i>
                                MyWay - Orientation
                            </a>
                            <a href="https://www.ofppt.ma" target="_blank" class="btn-link-external">
                                <i class="fas fa-globe"></i>
                                Site OFPPT
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ISTA Textile Daoudiate -->
        <div class="col-lg-6">
            <div class="etablissement-card">
                <div class="etablissement-header">
                    <div class="etablissement-icon">
                        <i class="fas fa-cut"></i>
                    </div>
                    <h3 class="etablissement-title">
                        Institut Spécialisé de Technologie Appliquée Textile et Confection Daoudiate
                    </h3>
                    <span class="etablissement-code">ISTA TEXTILE DAOUDIATE</span>
                </div>
                <div class="etablissement-body">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt info-icon"></i>
                        <div class="info-text">
                            <strong>Adresse :</strong> Daoudiate, Marrakech
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-briefcase info-icon"></i>
                        <div class="info-text">
                            <strong>Spécialités :</strong> Textile, Confection, Modélisme, Stylisme
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-star info-icon"></i>
                        <div class="info-text">
                            <strong>Points forts :</strong> Atelier de création, machines professionnelles, partenariats industrie
                        </div>
                    </div>
                    
                    <div class="quick-links">
                        <div class="quick-links-title">
                            <i class="fas fa-external-link-alt"></i>
                            Accès rapide
                        </div>
                        <div class="d-flex flex-wrap">
                            <a href="https://www.myway.ac.ma" target="_blank" class="btn-link-external">
                                <i class="fas fa-compass"></i>
                                MyWay - Orientation
                            </a>
                            <a href="https://www.ofppt.ma" target="_blank" class="btn-link-external">
                                <i class="fas fa-globe"></i>
                                Site OFPPT
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ISTA NTIC Sidi Youssef Ben Ali -->
        <div class="col-lg-6">
            <div class="etablissement-card">
                <div class="etablissement-header">
                    <div class="etablissement-icon">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <h3 class="etablissement-title">
                        Institut Spécialisé de Technologie Appliquée NTIC Sidi Youssef Ben Ali
                    </h3>
                    <span class="etablissement-code">ISTA NTIC SYBA</span>
                </div>
                <div class="etablissement-body">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt info-icon"></i>
                        <div class="info-text">
                            <strong>Adresse :</strong> Sidi Youssef Ben Ali, Marrakech
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-briefcase info-icon"></i>
                        <div class="info-text">
                            <strong>Spécialités :</strong> Nouvelles Technologies, Réseaux, Cybersécurité, IA
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-star info-icon"></i>
                        <div class="info-text">
                            <strong>Points forts :</strong> Labs high-tech, formation en technologies émergentes, certifications internationales
                        </div>
                    </div>
                    
                    <div class="quick-links">
                        <div class="quick-links-title">
                            <i class="fas fa-external-link-alt"></i>
                            Accès rapide
                        </div>
                        <div class="d-flex flex-wrap">
                            <a href="https://www.myway.ac.ma" target="_blank" class="btn-link-external">
                                <i class="fas fa-compass"></i>
                                MyWay - Orientation
                            </a>
                            <a href="https://www.ofppt.ma" target="_blank" class="btn-link-external">
                                <i class="fas fa-globe"></i>
                                Site OFPPT
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section Divider -->
<div class="section-divider"></div>

<!-- Ressources et Services -->
<div class="resources-section">
    <h2 class="resources-title">
        <i class="fas fa-rocket me-3"></i>
        Ressources et Services OFPPT
    </h2>
    <div class="row g-4">
        <div class="col-md-3 col-sm-6">
            <div class="resource-card">
                <div class="resource-icon">
                    <i class="fas fa-compass"></i>
                </div>
                <h4 class="resource-title">MyWay</h4>
                <p class="resource-description">
                    Plateforme d'orientation pour découvrir les filières et métiers
                </p>
                <a href="https://www.myway.ac.ma" target="_blank" class="btn-resource">
                    Accéder
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="resource-card">
                <div class="resource-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h4 class="resource-title">Formation Continue</h4>
                <p class="resource-description">
                    Perfectionnez vos compétences avec nos programmes de formation
                </p>
                <a href="https://www.ofppt.ma" target="_blank" class="btn-resource">
                    Découvrir
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="resource-card">
                <div class="resource-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h4 class="resource-title">Insertion Pro</h4>
                <p class="resource-description">
                    Accompagnement vers l'emploi et services d'insertion professionnelle
                </p>
                <a href="https://www.ofppt.ma" target="_blank" class="btn-resource">
                    Explorer
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="resource-card">
                <div class="resource-icon">
                    <i class="fas fa-globe"></i>
                </div>
                <h4 class="resource-title">Site Officiel</h4>
                <p class="resource-description">
                    Toutes les informations sur l'OFPPT et ses services
                </p>
                <a href="https://www.ofppt.ma" target="_blank" class="btn-resource">
                    Visiter
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="text-center mt-5 mb-4">
    <div class="alert alert-info" style="background: linear-gradient(135deg, #E3F2FD 0%, #E8F5E9 100%); border: none; border-radius: 15px; padding: 30px;">
        <h4 class="alert-heading">
            <i class="fas fa-info-circle me-2"></i>
            Besoin d'aide ou d'informations ?
        </h4>
        <p class="mb-3">Notre équipe est à votre disposition pour répondre à toutes vos questions</p>
        <div class="d-flex justify-content-center flex-wrap gap-3">
            <a href="tel:0537764200" class="btn btn-primary btn-lg">
                <i class="fas fa-phone me-2"></i>
                0537 76 42 00
            </a>
            <a href="mailto:contact@ofppt.ma" class="btn btn-success btn-lg">
                <i class="fas fa-envelope me-2"></i>
                contact@ofppt.ma
            </a>
        </div>
    </div>
</div>

<script>
    // Animation au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.etablissement-card, .resource-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease-out';
        observer.observe(card);
    });
</script>
@endsection