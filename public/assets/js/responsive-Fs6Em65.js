/**
 * SWITCH GAME - JAVASCRIPT RESPONSIVE & INTERACTIONS
 * Optimisé pour les performances et l'accessibilité
 */

class ResponsiveManager {
    constructor() {
        this.breakpoints = {
            mobile: 320,
            mobileLarge: 480,
            tablet: 768,
            tabletLarge: 1024,
            desktop: 1200,
            desktopLarge: 1440
        };
        
        this.currentBreakpoint = this.getCurrentBreakpoint();
        this.isReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        this.init();
    }

    init() {
        this.setupResponsiveHandlers();
        this.setupScrollAnimations();
        this.setupIntersectionObserver();
        this.setupTouchGestures();
        this.setupKeyboardNavigation();
        this.setupPerformanceOptimizations();
        
        // Écouter les changements de taille d'écran
        window.addEventListener('resize', this.debounce(() => {
            this.handleResize();
        }, 250));
        
        // Écouter les changements de préférences d'accessibilité
        window.matchMedia('(prefers-reduced-motion: reduce)').addEventListener('change', (e) => {
            this.isReducedMotion = e.matches;
            this.updateAnimations();
        });
    }

    getCurrentBreakpoint() {
        const width = window.innerWidth;
        if (width < this.breakpoints.mobileLarge) return 'mobile';
        if (width < this.breakpoints.tablet) return 'mobileLarge';
        if (width < this.breakpoints.tabletLarge) return 'tablet';
        if (width < this.breakpoints.desktop) return 'tabletLarge';
        if (width < this.breakpoints.desktopLarge) return 'desktop';
        return 'desktopLarge';
    }

    setupResponsiveHandlers() {
        // Gestion des menus mobiles
        this.setupMobileMenu();
        
        // Gestion des grilles responsives
        this.setupResponsiveGrids();
        
        // Gestion des images responsives
        this.setupResponsiveImages();
    }

    setupMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navMenu = document.querySelector('.nav-menu');
        
        if (menuToggle && navMenu) {
            menuToggle.addEventListener('click', () => {
                navMenu.classList.toggle('active');
                menuToggle.classList.toggle('active');
                document.body.classList.toggle('menu-open');
            });

            // Fermer le menu en cliquant à l'extérieur
            document.addEventListener('click', (e) => {
                if (!navMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                    navMenu.classList.remove('active');
                    menuToggle.classList.remove('active');
                    document.body.classList.remove('menu-open');
                }
            });
        }
    }

    setupResponsiveGrids() {
        const grids = document.querySelectorAll('.responsive-grid');
        
        grids.forEach(grid => {
            this.updateGridLayout(grid);
        });
    }

    updateGridLayout(grid) {
        const items = grid.querySelectorAll('.grid-item');
        const containerWidth = grid.offsetWidth;
        
        // Calculer le nombre de colonnes optimal
        let columns = 1;
        if (this.currentBreakpoint === 'mobileLarge') columns = 2;
        else if (this.currentBreakpoint === 'tablet') columns = 2;
        else if (this.currentBreakpoint === 'tabletLarge') columns = 3;
        else if (this.currentBreakpoint === 'desktop') columns = 4;
        else if (this.currentBreakpoint === 'desktopLarge') columns = 5;
        
        // Appliquer les styles
        grid.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
        
        // Animation des éléments
        items.forEach((item, index) => {
            if (!this.isReducedMotion) {
                item.style.animationDelay = `${index * 0.1}s`;
                item.classList.add('animate-fadeInUp');
            }
        });
    }

    setupScrollAnimations() {
        // Scroll reveal animations
        const revealElements = document.querySelectorAll('.scroll-reveal, .scroll-reveal-left, .scroll-reveal-right, .scroll-reveal-scale');
        
        if (revealElements.length > 0) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            revealElements.forEach(el => observer.observe(el));
        }
    }

    setupIntersectionObserver() {
        // Lazy loading des images
        const images = document.querySelectorAll('img[data-src]');
        
        if (images.length > 0) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        }
    }

    setupTouchGestures() {
        // Swipe gestures pour les carrousels
        const carousels = document.querySelectorAll('.carousel');
        
        carousels.forEach(carousel => {
            let startX = 0;
            let currentX = 0;
            let isDragging = false;

            carousel.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                isDragging = true;
            });

            carousel.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                currentX = e.touches[0].clientX;
                const diffX = startX - currentX;
                
                if (Math.abs(diffX) > 50) {
                    if (diffX > 0) {
                        this.nextSlide(carousel);
                    } else {
                        this.prevSlide(carousel);
                    }
                    isDragging = false;
                }
            });

            carousel.addEventListener('touchend', () => {
                isDragging = false;
            });
        });
    }

    setupKeyboardNavigation() {
        // Navigation au clavier pour les modales
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });

        // Focus trap pour les modales
        this.setupFocusTrap();
    }

    setupFocusTrap() {
        const modals = document.querySelectorAll('.modal');
        
        modals.forEach(modal => {
            const focusableElements = modal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            modal.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        if (document.activeElement === firstElement) {
                            lastElement.focus();
                            e.preventDefault();
                        }
                    } else {
                        if (document.activeElement === lastElement) {
                            firstElement.focus();
                            e.preventDefault();
                        }
                    }
                }
            });
        });
    }

    setupPerformanceOptimizations() {
        // Debounce pour les événements de scroll
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                this.updateScrollProgress();
            }, 10);
        });

        // Optimisation des animations
        this.optimizeAnimations();
    }

    updateScrollProgress() {
        const scrollProgress = document.querySelector('.scroll-progress');
        if (scrollProgress) {
            const scrollTop = window.pageYOffset;
            const docHeight = document.body.scrollHeight - window.innerHeight;
            const scrollPercent = (scrollTop / docHeight) * 100;
            scrollProgress.style.width = `${scrollPercent}%`;
        }
    }

    optimizeAnimations() {
        // Utiliser requestAnimationFrame pour les animations fluides
        const animatedElements = document.querySelectorAll('.animate-fadeInUp, .animate-scaleIn');
        
        animatedElements.forEach(el => {
            el.style.willChange = 'transform, opacity';
        });
    }

    handleResize() {
        const newBreakpoint = this.getCurrentBreakpoint();
        
        if (newBreakpoint !== this.currentBreakpoint) {
            this.currentBreakpoint = newBreakpoint;
            this.updateResponsiveElements();
        }
    }

    updateResponsiveElements() {
        // Mettre à jour les grilles
        this.setupResponsiveGrids();
        
        // Mettre à jour les menus
        this.updateMenuForBreakpoint();
        
        // Mettre à jour les tailles de police
        this.updateFontSizes();
    }

    updateMenuForBreakpoint() {
        const navMenu = document.querySelector('.nav-menu');
        
        if (this.currentBreakpoint === 'mobile' || this.currentBreakpoint === 'mobileLarge') {
            navMenu?.classList.add('mobile-menu');
        } else {
            navMenu?.classList.remove('mobile-menu');
        }
    }

    updateFontSizes() {
        // Ajuster les tailles de police selon la taille d'écran
        const root = document.documentElement;
        const baseFontSize = this.getBaseFontSize();
        root.style.fontSize = `${baseFontSize}px`;
    }

    getBaseFontSize() {
        const width = window.innerWidth;
        if (width < 480) return 14;
        if (width < 768) return 15;
        if (width < 1024) return 16;
        return 18;
    }

    updateAnimations() {
        const animatedElements = document.querySelectorAll('[class*="animate-"]');
        
        animatedElements.forEach(el => {
            if (this.isReducedMotion) {
                el.style.animation = 'none';
            } else {
                el.style.animation = '';
            }
        });
    }

    // Utilitaires
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Méthodes pour les carrousels
    nextSlide(carousel) {
        const slides = carousel.querySelectorAll('.slide');
        const currentSlide = carousel.querySelector('.slide.active');
        const currentIndex = Array.from(slides).indexOf(currentSlide);
        const nextIndex = (currentIndex + 1) % slides.length;
        
        currentSlide?.classList.remove('active');
        slides[nextIndex]?.classList.add('active');
    }

    prevSlide(carousel) {
        const slides = carousel.querySelectorAll('.slide');
        const currentSlide = carousel.querySelector('.slide.active');
        const currentIndex = Array.from(slides).indexOf(currentSlide);
        const prevIndex = currentIndex === 0 ? slides.length - 1 : currentIndex - 1;
        
        currentSlide?.classList.remove('active');
        slides[prevIndex]?.classList.add('active');
    }

    closeAllModals() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
        });
    }
}

// Classe pour les interactions avancées
class InteractiveManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupRippleEffect();
        this.setupHoverEffects();
        this.setupClickAnimations();
        this.setupFormInteractions();
    }

    setupRippleEffect() {
        const rippleElements = document.querySelectorAll('.click-ripple');
        
        rippleElements.forEach(element => {
            element.addEventListener('click', (e) => {
                const ripple = document.createElement('span');
                const rect = element.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                element.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    }

    setupHoverEffects() {
        const hoverElements = document.querySelectorAll('.hover-lift, .hover-scale, .hover-glow');
        
        hoverElements.forEach(element => {
            element.addEventListener('mouseenter', () => {
                element.style.transition = 'all 0.3s ease';
            });
            
            element.addEventListener('mouseleave', () => {
                element.style.transition = 'all 0.3s ease';
            });
        });
    }

    setupClickAnimations() {
        const clickElements = document.querySelectorAll('.button-press');
        
        clickElements.forEach(element => {
            element.addEventListener('mousedown', () => {
                element.classList.add('pressed');
            });
            
            element.addEventListener('mouseup', () => {
                element.classList.remove('pressed');
            });
            
            element.addEventListener('mouseleave', () => {
                element.classList.remove('pressed');
            });
        });
    }

    setupFormInteractions() {
        const formInputs = document.querySelectorAll('.form-input, .form-select, .form-textarea');
        
        formInputs.forEach(input => {
            // Focus effects
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('focused');
            });
            
            // Validation feedback
            input.addEventListener('input', () => {
                this.validateInput(input);
            });
        });
    }

    validateInput(input) {
        const value = input.value.trim();
        const isValid = value.length > 0;
        
        input.classList.toggle('valid', isValid);
        input.classList.toggle('invalid', !isValid && value.length > 0);
    }
}

// Classe pour les performances
class PerformanceManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupLazyLoading();
        this.setupImageOptimization();
        this.setupResourceHints();
    }

    setupLazyLoading() {
        const lazyImages = document.querySelectorAll('img[data-src]');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            lazyImages.forEach(img => imageObserver.observe(img));
        }
    }

    setupImageOptimization() {
        const images = document.querySelectorAll('img');
        
        images.forEach(img => {
            // Optimiser les images selon la densité d'écran
            if (window.devicePixelRatio > 1) {
                const src = img.src;
                const highResSrc = src.replace(/\.(jpg|jpeg|png)$/, '@2x.$1');
                img.src = highResSrc;
            }
        });
    }

    setupResourceHints() {
        // Preload des ressources critiques
        const criticalResources = [
            '/assets/styles/responsive.css',
            '/assets/styles/components.css'
        ];
        
        criticalResources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.href = resource;
            link.as = 'style';
            document.head.appendChild(link);
        });
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    new ResponsiveManager();
    new InteractiveManager();
    new PerformanceManager();
});

// Export pour utilisation dans d'autres modules
window.SwitchGame = {
    ResponsiveManager,
    InteractiveManager,
    PerformanceManager
};
