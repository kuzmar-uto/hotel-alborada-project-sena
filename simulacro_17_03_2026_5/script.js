// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.querySelector('nav ul');
    const overlay = document.querySelector('.overlay');
    
    // Toggle mobile menu
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            navMenu.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Close mobile menu when clicking overlay
    if (overlay) {
        overlay.addEventListener('click', function() {
            navMenu.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }
    
    // Close mobile menu when clicking on a link
    document.querySelectorAll('nav ul li a').forEach(link => {
        link.addEventListener('click', function() {
            navMenu.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    });
    
    // Google Profile Icon Click Handler
    const googleProfileBtn = document.getElementById('google-profile-btn');
    if (googleProfileBtn) {
        googleProfileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Verificar si el usuario está autenticado llamando al backend
            fetch('php/auth/get_user_profile.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirigir al perfil de Google
                        window.location.href = 'php/auth/google_profile.php';
                    } else {
                        // Usuario no autenticado, ir a login
                        window.location.href = 'cuenta.html';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // En caso de error, ir a cuenta.html
                    window.location.href = 'cuenta.html';
                });
        });
    }
    
    // Close menu when pressing escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            navMenu.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if(targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if(targetElement) {
                const headerOffset = 100;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Se eliminó cambio dinámico del header en scroll para mantener consistencia visual
    
    // Add loading animation to elements when they come into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    document.querySelectorAll('.room-card, .service-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
    
    // Improve touch feedback on mobile
    document.querySelectorAll('button, a, .btn').forEach(element => {
        element.addEventListener('touchstart', function() {
            this.style.opacity = '0.8';
        });
        
        element.addEventListener('touchend', function() {
            this.style.opacity = '1';
        });
    });

    // Carousel Functionality
    const carouselItems = document.querySelectorAll('.carousel-item');
    const indicators = document.querySelectorAll('.indicator');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    let currentIndex = 0;
    let autoPlayInterval;

    function showSlide(index) {
        if (index >= carouselItems.length) {
            currentIndex = 0;
        } else if (index < 0) {
            currentIndex = carouselItems.length - 1;
        } else {
            currentIndex = index;
        }

        // Remove active class from all items and indicators
        carouselItems.forEach(item => item.classList.remove('active'));
        indicators.forEach(indicator => indicator.classList.remove('active'));

        // Add active class to current item and indicator
        carouselItems[currentIndex].classList.add('active');
        indicators[currentIndex].classList.add('active');
    }

    function nextSlide() {
        showSlide(currentIndex + 1);
        resetAutoPlay();
    }

    function prevSlide() {
        showSlide(currentIndex - 1);
        resetAutoPlay();
    }

    function autoPlay() {
        autoPlayInterval = setInterval(() => {
            showSlide(currentIndex + 1);
        }, 5000); // Cambiar imagen cada 5 segundos
    }

    function resetAutoPlay() {
        clearInterval(autoPlayInterval);
        autoPlay();
    }

    // Event listeners for buttons
    if (prevBtn) {
        prevBtn.addEventListener('click', prevSlide);
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', nextSlide);
    }

    // Event listeners for indicators
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            showSlide(index);
            resetAutoPlay();
        });
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
        }
    });

    // Start auto play
    autoPlay();

    // Pause auto play on hover
    const carouselContainer = document.querySelector('.carousel-container');
    if (carouselContainer) {
        carouselContainer.addEventListener('mouseenter', () => {
            clearInterval(autoPlayInterval);
        });

        carouselContainer.addEventListener('mouseleave', () => {
            autoPlay();
        });
    }

    // Hero Background Carousel
    const heroSlides = document.querySelectorAll('.hero-slide');
    let heroCurrentIndex = 0;
    let heroAutoPlayInterval;

    function showHeroSlide(index) {
        if (index >= heroSlides.length) {
            heroCurrentIndex = 0;
        } else if (index < 0) {
            heroCurrentIndex = heroSlides.length - 1;
        } else {
            heroCurrentIndex = index;
        }

        // Remove active class from all slides
        heroSlides.forEach(slide => slide.classList.remove('active'));

        // Add active class to current slide
        heroSlides[heroCurrentIndex].classList.add('active');
    }

    function heroAutoPlay() {
        heroAutoPlayInterval = setInterval(() => {
            showHeroSlide(heroCurrentIndex + 1);
        }, 6000); // Cambiar imagen cada 6 segundos
    }

    // Initialize first hero slide
    if (heroSlides.length > 0) {
        showHeroSlide(0);
        heroAutoPlay();
    }

    // Pause hero carousel on hover
    const heroBackground = document.querySelector('.hero-background');
    if (heroBackground) {
        heroBackground.addEventListener('mouseenter', () => {
            clearInterval(heroAutoPlayInterval);
        });

        heroBackground.addEventListener('mouseleave', () => {
            heroAutoPlay();
        });
    }
});