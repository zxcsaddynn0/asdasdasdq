// animations.js - Анимации и эффекты

class Animations {
    constructor() {
        this.initSmoothScroll();
        this.initHoverEffects();
        this.initCounterAnimation();
        this.initParallax();
    }

    // Плавная прокрутка для якорей
    initSmoothScroll() {
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        
        anchorLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                
                if (href !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    }

    // Эффекты при наведении
    initHoverEffects() {
        // Эффект для карточек
        const cards = document.querySelectorAll('.file-card, .category-card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
            });
        });

        // Эффект для кнопок
        const buttons = document.querySelectorAll('.btn-glow');
        
        buttons.forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                btn.style.transform = 'translateY(-2px)';
            });
            
            btn.addEventListener('mouseleave', () => {
                btn.style.transform = 'translateY(0)';
            });
        });
    }

    // Анимация счетчиков
    initCounterAnimation() {
        const counters = document.querySelectorAll('.counter');
        const observerOptions = {
            threshold: 0.5
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        counters.forEach(counter => observer.observe(counter));
    }

    animateCounter(counter) {
        const target = parseInt(counter.textContent);
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current).toLocaleString();
        }, 16);
    }

    // Параллакс эффект
    initParallax() {
        const parallaxElements = document.querySelectorAll('.parallax');
        
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            
            parallaxElements.forEach(element => {
                const speed = element.dataset.speed || 0.5;
                const yPos = -(scrolled * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
        });
    }

    // Анимация появления текста
    animateTextReveal() {
        const textElements = document.querySelectorAll('.text-reveal');
        
        textElements.forEach((element, index) => {
            setTimeout(() => {
                element.classList.add('revealed');
            }, index * 200);
        });
    }
}

// Инициализация анимаций
document.addEventListener('DOMContentLoaded', () => {
    new Animations();
});

// GSAP анимации если библиотека доступна
if (typeof gsap !== 'undefined') {
    gsap.registerPlugin(ScrollTrigger);
    
    // Анимация появления элементов при скролле
    gsap.utils.toArray('.animate-on-scroll').forEach(element => {
        gsap.fromTo(element, {
            y: 50,
            opacity: 0
        }, {
            y: 0,
            opacity: 1,
            duration: 1,
            scrollTrigger: {
                trigger: element,
                start: "top 80%",
                end: "bottom 20%",
                toggleActions: "play none none reverse"
            }
        });
    });

    // Анимация героя
    gsap.timeline()
        .from('.hero-content h1', { 
            y: 50, 
            opacity: 0, 
            duration: 1 
        })
        .from('.hero-content p', { 
            y: 30, 
            opacity: 0, 
            duration: 0.8 
        }, '-=0.5')
        .from('.search-box', { 
            y: 30, 
            opacity: 0, 
            duration: 0.8 
        }, '-=0.3');
}