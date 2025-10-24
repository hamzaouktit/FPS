<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $complexeInfo['nom'] ?? 'Complexe Marrakech' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #f59e0b;
            --light-bg: #f8fafc;
            --dark-text: #1e293b;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0 80px;
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,133.3C672,139,768,181,864,186.7C960,192,1056,160,1152,138.7C1248,117,1344,107,1392,101.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.95;
            margin-bottom: 2rem;
        }

        /* Section Styles */
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--accent-color);
            border-radius: 2px;
        }

        /* Cards */
        .custom-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
        }

        .custom-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .etablissement-card {
            background: white;
            border-left: 5px solid var(--primary-color);
        }

        .secteur-card {
            background: white;
            border-left: 5px solid var(--accent-color);
        }

        /* Info Items */
        .info-item {
            display: flex;
            align-items: start;
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .info-item:hover {
            background: var(--light-bg);
        }

        .info-icon {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-right: 1rem;
            margin-top: 0.2rem;
        }

        /* Filiere Badge */
        .filiere-badge {
            display: inline-block;
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            color: var(--dark-text);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin: 0.3rem;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s;
            border: 1px solid #d1d5db;
        }

        .filiere-badge:hover {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            transform: scale(1.05);
            text-decoration: none;
        }

        /* Buttons */
        .btn-myway {
            background: linear-gradient(135deg, var(--accent-color), #ea580c);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-myway:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4);
            color: white;
        }

        /* Contact Section */
        .contact-section {
            background: linear-gradient(135deg, var(--light-bg), #e0f2fe);
            padding: 60px 0;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .contact-icon {
            background: var(--primary-color);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.3rem;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: white;
            padding: 30px 0;
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

        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            .hero-subtitle {
                font-size: 1rem;
            }
            .section-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="hero-title animate-fade-in">
                        <i class="fas fa-graduation-cap me-3"></i>
                        {{ $complexeInfo['nom'] }}
                    </h1>
                    <p class="hero-subtitle animate-fade-in">
                        {{ $complexeInfo['description'] }}
                    </p>
                    <a href="#etablissements" class="btn btn-light btn-lg me-3">
                        <i class="fas fa-building me-2"></i>Nos Établissements
                    </a>
                    <a href="#secteurs" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-list me-2"></i>Nos Secteurs
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Établissements Section -->
    <section id="etablissements" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">
                    <i class="fas fa-building me-3"></i>Nos Établissements
                </h2>
            </div>

            <div class="row g-4">
                @forelse($etablissements as $etablissement)
                    <div class="col-lg-6 col-md-12">
                        <div class="card custom-card etablissement-card animate-fade-in">
                            <div class="card-header-custom">
                                <i class="fas fa-school me-2"></i>
                                {{ $etablissement->nom_efp }}
                            </div>
                            <div class="card-body p-4">
                                <div class="info-item">
                                    <i class="fas fa-code info-icon"></i>
                                    <div>
                                        <strong>Code EFP:</strong>
                                        <span class="ms-2">{{ $etablissement->code_efp }}</span>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt info-icon"></i>
                                    <div>
                                        <strong>Adresse:</strong>
                                        <span class="ms-2">Marrakech, Maroc</span>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <i class="fas fa-certificate info-icon"></i>
                                    <div>
                                        <strong>Spécialités:</strong>
                                        <span class="ms-2">Formation professionnelle multisectorielle</span>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <i class="fas fa-star info-icon"></i>
                                    <div>
                                        <strong>Points forts:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Équipements modernes et performants</li>
                                            <li>Formateurs qualifiés et expérimentés</li>
                                            <li>Formations certifiantes reconnues</li>
                                            <li>Accompagnement à l'insertion professionnelle</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <a href="https://www.myway.ac.ma/fr/etablissement/{{ $etablissement->code_efp }}" 
                                       target="_blank" 
                                       class="btn-myway">
                                        <i class="fas fa-external-link-alt me-2"></i>
                                        Plus d'infos sur MyWay
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            Aucun établissement disponible pour le moment.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Secteurs Section -->
    <section id="secteurs" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">
                    <i class="fas fa-layer-group me-3"></i>Nos Secteurs de Formation
                </h2>
            </div>

            <div class="row g-4">
                @forelse($secteurs as $secteur)
                    <div class="col-lg-6 col-md-12">
                        <div class="card custom-card secteur-card animate-fade-in">
                            <div class="card-header-custom" style="background: linear-gradient(135deg, var(--accent-color), #ea580c);">
                                <i class="fas fa-industry me-2"></i>
                                {{ $secteur->nom_secteur }}
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <strong class="d-block mb-3" style="font-size: 1.1rem;">
                                        <i class="fas fa-book-open me-2"></i>
                                        Filières disponibles:
                                    </strong>
                                    
                                    @if($secteur->filieres->count() > 0)
                                        <div class="d-flex flex-wrap">
                                            @foreach($secteur->filieres as $filiere)
                                                <a href="https://www.myway.ac.ma/fr/filiere/{{ $filiere->code_filiere }}" 
                                                   target="_blank" 
                                                   class="filiere-badge">
                                                    <i class="fas fa-graduation-cap me-1"></i>
                                                    {{ $filiere->nom_filiere }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">Aucune filière disponible</p>
                                    @endif
                                </div>

                                <div class="text-center mt-4">
                                    <a href="https://www.myway.ac.ma/fr/secteurs" 
                                       target="_blank" 
                                       class="btn-myway">
                                        <i class="fas fa-external-link-alt me-2"></i>
                                        Découvrir tous les secteurs
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            Aucun secteur disponible pour le moment.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">
                    <i class="fas fa-envelope me-3"></i>Contactez-nous
                </h2>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card custom-card">
                        <div class="card-body p-5">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="contact-item">
                                        <div class="contact-icon">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div>
                                            <strong>Téléphone</strong>
                                            <p class="mb-0">{{ $complexeInfo['telephone'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="contact-item">
                                        <div class="contact-icon">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div>
                                            <strong>Email</strong>
                                            <p class="mb-0">{{ $complexeInfo['email'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="contact-item">
                                        <div class="contact-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div>
                                            <strong>Adresse</strong>
                                            <p class="mb-0">{{ $complexeInfo['adresse'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p class="mb-2">
                <i class="fas fa-graduation-cap me-2"></i>
                {{ $complexeInfo['nom'] }}
            </p>
            <p class="mb-0">
                © {{ date('Y') }} - Tous droits réservés
            </p>
            <div class="mt-3">
                <a href="https://www.myway.ac.ma/fr" target="_blank" class="text-white text-decoration-none">
                    <i class="fas fa-globe me-2"></i>Plateforme MyWay
                </a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animation on scroll
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

        document.querySelectorAll('.custom-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease-out';
            observer.observe(card);
        });
    </script>
</body>
</html>