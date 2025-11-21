<?php
require_once 'includes/config.php';
include 'includes/header.php';
?>

<div class="container">
    <h1>Контакты</h1>
    
    <div class="content-page">
        <div class="contact-info">
            <div class="contact-methods">
                <div class="contact-method">
                    <h3>📧 Электронная почта</h3>
                    <p>Для общих вопросов: <strong>info@minecraft-inside.ru</strong></p>
                    <p>Для модерации: <strong>moderation@minecraft-inside.ru</strong></p>
                    <p>Техническая поддержка: <strong>support@minecraft-inside.ru</strong></p>
                </div>
                
                <div class="contact-method">
                    <h3>💬 Социальные сети</h3>
                    <p>ВКонтакте: <a href="https://vk.com/minecraft_inside" target="_blank">vk.com/minecraft_inside</a></p>
                    <p>Telegram: <a href="https://t.me/minecraft_inside" target="_blank">@minecraft_inside</a></p>
                    <p>Discord: <a href="#" target="_blank">Minecraft Inside Community</a></p>
                </div>
                
                <div class="contact-method">
                    <h3>⏰ Время работы поддержки</h3>
                    <p>Понедельник - Пятница: 10:00 - 18:00 (МСК)</p>
                    <p>Суббота: 12:00 - 16:00 (МСК)</p>
                    <p>Воскресенье: выходной</p>
                </div>
            </div>

            <div class="contact-form">
                <h2>Форма обратной связи</h2>
                <form method="POST" id="feedback-form">
                    <div class="form-group">
                        <label for="name">Ваше имя *</label>
                        <input type="text" id="name" name="name" required 
                               value="<?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Тема *</label>
                        <select id="subject" name="subject" required>
                            <option value="">Выберите тему</option>
                            <option value="technical">Техническая проблема</option>
                            <option value="moderation">Вопрос по модерации</option>
                            <option value="suggestion">Предложение по сайту</option>
                            <option value="partnership">Сотрудничество</option>
                            <option value="other">Другое</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Сообщение *</label>
                        <textarea id="message" name="message" rows="6" required 
                                  placeholder="Опишите ваш вопрос или предложение..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">Отправить сообщение</button>
                </form>
            </div>
        </div>

        <div class="faq-section">
            <h2>Часто задаваемые вопросы</h2>
            
            <div class="faq-item">
                <h3>Как долго длится модерация файла?</h3>
                <p>Обычно модерация занимает от 1 до 24 часов. В редких случаях при большой загрузке может потребоваться до 48 часов.</p>
            </div>
            
            <div class="faq-item">
                <h3>Почему мой файл был отклонен?</h3>
                <p>Основные причины: нарушение авторских прав, нерабочий файл, неподходящее содержание, неправильное описание.</p>
            </div>
            
            <div class="faq-item">
                <h3>Можно ли загружать файлы с других сайтов?</h3>
                <p>Только если у вас есть разрешение автора или файл распространяется свободно. Копирование без разрешения запрещено.</p>
            </div>
            
            <div class="faq-item">
                <h3>Как стать модератором?</h3>
                <p>Мы ищем активных пользователей с хорошей репутацией. Напишите нам на почту с темой "Модератор".</p>
            </div>
        </div>
    </div>
</div>

<style>
.contact-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-bottom: 3rem;
}

.contact-methods {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.contact-method {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.contact-method h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.contact-form {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #2c3e50;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.btn-submit {
    background: #3498db;
    color: white;
    padding: 1rem 2rem;
    border: none;
    border-radius: 5px;
    font-size: 1.1rem;
    cursor: pointer;
    width: 100%;
}

.faq-section {
    background: white;
    padding: 2rem;
    border-radius: 8px;
}

.faq-item {
    border-bottom: 1px solid #eee;
    padding: 1.5rem 0;
}

.faq-item:last-child {
    border-bottom: none;
}

.faq-item h3 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .contact-info {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.getElementById('feedback-form').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Спасибо за ваше сообщение! Мы ответим вам в ближайшее время.');
    this.reset();
});
</script>

<?php include 'includes/footer.php'; ?>