    </div>

    <script>
    // Подтверждение действий
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.btn-delete, .btn-danger');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if(!confirm('Вы уверены, что хотите выполнить это действие?')) {
                    e.preventDefault();
                }
            });
        });
        
        // Динамическое обновление статистики
        function updateStats() {
            // Здесь можно добавить AJAX обновление статистики
        }
        
        // Автообновление каждые 30 секунд
        setInterval(updateStats, 30000);
    });
    </script>
</body>
</html>