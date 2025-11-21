    </main>

    <footer class="main-footer">
        <!-- Footer content -->
    </footer>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    
    <!-- Main JavaScript -->
    <script src="<?= BASE_URL ?>/js/main.js"></script>
    <script src="<?= BASE_URL ?>/js/animations.js"></script>

    <script>
    // Простая инициализация когда DOM готов
    document.addEventListener('DOMContentLoaded', function() {
        // Инициализация AOS если есть
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 800,
                once: true
            });
        }

        // Инициализация Select2
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.filter-select').select2({
                minimumResultsForSearch: 10,
                width: '100%'
            });
        }

        // Глобальная функция для уведомлений
        window.showNotification = function(message, type = 'success') {
            // Простая реализация уведомления
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => notification.classList.add('show'), 100);
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        };
    });
    </script>
</body>
</html>