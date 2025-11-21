<?php
require_once 'includes/config.php';
include 'includes/header.php';
?>

<div class="container">
    <h1>О сайте Minecraft Inside</h1>
    
    <div class="content-page">
        <div class="about-hero">
            <h2>Добро пожаловать в сообщество Minecraft Inside!</h2>
            <p>Крупнейшая коллекция модов, карт, ресурспаков и скинов для Minecraft</p>
        </div>

        <div class="about-stats">
            <div class="stat">
                <h3>10,000+</h3>
                <p>Файлов</p>
            </div>
            <div class="stat">
                <h3>50,000+</h3>
                <p>Пользователей</p>
            </div>
            <div class="stat">
                <h3>1,000,000+</h3>
                <p>Скачиваний</p>
            </div>
        </div>

        <div class="about-content">
            <h2>Наша миссия</h2>
            <p>Minecraft Inside создан для объединения творцов и игроков Minecraft. Мы предоставляем платформу, где авторы могут делиться своими работами, а игроки - находить лучший контент для своей игры.</p>

            <h2>Что мы предлагаем</h2>
            <ul>
                <li><strong>Моды</strong> - Изменяйте геймплей, добавляйте новые возможности</li>
                <li><strong>Карты</strong> - Исследуйте удивительные приключения и паркур</li>
                <li><strong>Ресурспаки</strong> - Изменяйте текстуры и звуки игры</li>
                <li><strong>Шейдеры</strong> - Улучшайте графику до невероятного уровня</li>
                <li><strong>Скины</strong> - Настройте внешний вид своего персонажа</li>
            </ul>

            <h2>Наша команда</h2>
            <p>Мы - группа энтузиастов Minecraft, которые хотят сделать игру еще лучше. Наша команда модераторов следит за качеством контента и помогает сообществу развиваться.</p>

            <h2>Присоединяйтесь к нам!</h2>
            <p>Станьте частью нашего сообщества - зарегистрируйтесь, загружайте свои работы, оставляйте комментарии и оценки.</p>
            
            <div class="cta-buttons">
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn-primary">Зарегистрироваться</a>
                    <a href="upload.php" class="btn-secondary">Загрузить контент</a>
                <?php else: ?>
                    <a href="upload.php" class="btn-primary">Загрузить контент</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.about-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 2rem;
}

.about-hero h2 {
    margin-bottom: 1rem;
    font-size: 2.5rem;
}

.about-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin: 3rem 0;
}

.about-stats .stat {
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.about-stats h3 {
    font-size: 3rem;
    color: #3498db;
    margin-bottom: 0.5rem;
}

.about-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    line-height: 1.6;
}

.about-content h2 {
    color: #2c3e50;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.about-content h2:first-child {
    margin-top: 0;
}

.about-content ul {
    margin: 1rem 0;
    padding-left: 2rem;
}

.about-content li {
    margin-bottom: 0.5rem;
}

.cta-buttons {
    margin-top: 2rem;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-primary {
    background: #3498db;
    color: white;
    padding: 1rem 2rem;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
}

.btn-secondary {
    background: #95a5a6;
    color: white;
    padding: 1rem 2rem;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
}
</style>

<?php include 'includes/footer.php'; ?>