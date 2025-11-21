// main.js - Основные функции сайта

class MinecraftInside {
    constructor() {
        this.init();
    }

    init() {
        this.initializeLoader();
        this.initializeBackToTop();
        this.initializeUserMenu();
        this.initializeMobileMenu();
        this.initializeTooltips();
        this.initializeAnimations();
        this.initializeNotifications();
    }

    // Инициализация загрузки
    initializeLoader() {
        // Скрываем loader когда страница полностью загружена
        window.addEventListener('load', () => {
            setTimeout(() => {
                const loader = document.querySelector('.page-loader');
                if (loader) {
                    loader.style.opacity = '0';
                    loader.style.visibility = 'hidden';
                    
                    // Удаляем через 500ms после скрытия
                    setTimeout(() => {
                        loader.remove();
                    }, 500);
                }
            }, 300); // Минимальная задержка для плавности
        });

        // На случай если load событие не сработает
        setTimeout(() => {
            const loader = document.querySelector('.page-loader');
            if (loader && loader.style.opacity !== '0') {
                loader.style.opacity = '0';
                loader.style.visibility = 'hidden';
                setTimeout(() => loader.remove(), 500);
            }
        }, 3000); // Максимальное время загрузки 3 секунды
    }

    // Кнопка "Наверх"
    initializeBackToTop() {
        const backToTop = document.querySelector('.back-to-top');
        if (!backToTop) return;

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });

        backToTop.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Меню пользователя
    initializeUserMenu() {
        const userMenu = document.querySelector('.user-menu');
        if (!userMenu) return;

        userMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            const dropdown = userMenu.querySelector('.user-dropdown');
            dropdown.classList.toggle('show');
        });

        // Закрытие при клике вне меню
        document.addEventListener('click', () => {
            const dropdowns = document.querySelectorAll('.user-dropdown');
            dropdowns.forEach(dropdown => dropdown.classList.remove('show'));
        });
    }

    // Мобильное меню
    initializeMobileMenu() {
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const mainNav = document.querySelector('.main-nav');
        
        if (!mobileToggle || !mainNav) return;

        mobileToggle.addEventListener('click', () => {
            mobileToggle.classList.toggle('active');
            mainNav.classList.toggle('show');
        });

        // Закрытие при клике на ссылку
        const navLinks = mainNav.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileToggle.classList.remove('active');
                mainNav.classList.remove('show');
            });
        });
    }

    // Всплывающие подсказки
    initializeTooltips() {
        const tooltips = document.querySelectorAll('[data-tooltip]');
        
        tooltips.forEach(tooltip => {
            tooltip.addEventListener('mouseenter', this.showTooltip);
            tooltip.addEventListener('mouseleave', this.hideTooltip);
        });
    }

    showTooltip(e) {
        const tooltipText = this.getAttribute('data-tooltip');
        const tooltip = document.createElement('div');
        tooltip.className = 'custom-tooltip';
        tooltip.textContent = tooltipText;
        document.body.appendChild(tooltip);

        const rect = this.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
    }

    hideTooltip() {
        const tooltip = document.querySelector('.custom-tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }

    // Анимации
    initializeAnimations() {
        // Анимация появления элементов при скролле
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, observerOptions);

        // Наблюдаем за элементами с анимацией
        const animatedElements = document.querySelectorAll('.file-card, .category-card, .text-reveal');
        animatedElements.forEach(el => observer.observe(el));
    }

    // Уведомления
    initializeNotifications() {
        window.showNotification = (message, type = 'success', duration = 5000) => {
            // Используем Toastify если доступен
            if (typeof Toastify !== 'undefined') {
                Toastify({
                    text: message,
                    duration: duration,
                    gravity: "top",
                    position: "right",
                    backgroundColor: type === 'success' ? "#44bd32" : 
                                   type === 'error' ? "#e84118" : "#fbc531",
                    stopOnFocus: true,
                }).showToast();
            } else {
                // Фолбэк уведомление
                this.showFallbackNotification(message, type);
            }
        };
    }

    showFallbackNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Показываем уведомление
        setTimeout(() => notification.classList.add('show'), 100);

        // Автоматическое скрытие
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
}

// Инициализация когда DOM готов
document.addEventListener('DOMContentLoaded', () => {
    new MinecraftInside();
});