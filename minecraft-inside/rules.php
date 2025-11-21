<?php
require_once 'includes/config.php';
include 'includes/header.php';
?>

<div class="container">
    <h1>Правила сайта</h1>
    
    <div class="content-page">
        <h2>1. Общие положения</h2>
        <p>1.1. Настоящие правила регулируют использование сайта Minecraft Inside.</p>
        
        <h2>2. Загрузка контента</h2>
        <p>2.1. Запрещена загрузка контента, нарушающего авторские права.</p>
        <p>2.2. Все файлы проходят модерацию перед публикацией.</p>
        
        <h2>3. Поведение пользователей</h2>
        <p>3.1. Запрещены оскорбления и нецензурная лексика.</p>
        <p>3.2. Спам и реклама запрещены.</p>
        
        <h2>4. Контактная информация</h2>
        <p>По вопросам модерации: admin@minecraft-inside.ru</p>
    </div>
</div>

<style>
.content-page {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    line-height: 1.6;
}

.content-page h2 {
    color: #2c3e50;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.content-page h2:first-child {
    margin-top: 0;
}
</style>

<?php include 'includes/footer.php'; ?>