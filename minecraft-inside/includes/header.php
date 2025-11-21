<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minecraft Inside - Моды, карты, текстуры, скины, шейдеры для Майнкрафт</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/minecraft-style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/images/favicon.ico">
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="header-container">
            <!-- Top Header -->
            <div class="header-top">
                <div class="header-left">
                    <div class="logo">
                        <a href="<?= BASE_URL ?>/" class="logo-link">
                            <span class="logo-text">Minecraft Inside</span>
                            <span class="logo-subtitle">Моды, карты, текстуры</span>
                        </a>
                    </div>
                </div>

                <div class="header-center">
                    <form action="search.php" method="GET" class="search-form">
                        <div class="search-box">
                            <input type="text" name="q" placeholder="Поиск модов, карт, скинов..." class="search-input">
                            <button type="submit" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="header-right">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="user-section">
                            <a href="<?= BASE_URL ?>/upload.php" class="upload-btn">
                                <i class="fas fa-plus"></i>
                                <span>Добавить файл</span>
                            </a>
                            <div class="user-menu">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span class="username"><?= escape($_SESSION['username']) ?></span>
                                <div class="user-dropdown">
                                    <a href="<?= BASE_URL ?>/profile.php" class="dropdown-item">
                                        <i class="fas fa-user-circle"></i>
                                        Мой профиль
                                    </a>
                                    <a href="<?= BASE_URL ?>/favorites.php" class="dropdown-item">
                                        <i class="fas fa-heart"></i>
                                        Избранное
                                    </a>
                                    <?php if($_SESSION['role'] === 'admin'): ?>
                                        <a href="<?= BASE_URL ?>/admin/" class="dropdown-item">
                                            <i class="fas fa-cog"></i>
                                            Админ-панель
                                        </a>
                                    <?php endif; ?>
                                    <div class="dropdown-divider"></div>
                                    <a href="<?= BASE_URL ?>/logout.php" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Выйти
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-section">
                            <a href="<?= BASE_URL ?>/login.php" class="auth-link">Войти</a>
                            <a href="<?= BASE_URL ?>/register.php" class="auth-btn">Регистрация</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Main Navigation -->
            <nav class="main-navigation">
                <div class="nav-container">
                    <a href="<?= BASE_URL ?>/mods.php" class="nav-item">
                        <i class="fas fa-cubes"></i>
                        <span>Моды</span>
                    </a>
                    <a href="<?= BASE_URL ?>/maps.php" class="nav-item">
                        <i class="fas fa-map"></i>
                        <span>Карты</span>
                    </a>
                    <a href="<?= BASE_URL ?>/resourcepacks.php" class="nav-item">
                        <i class="fas fa-palette"></i>
                        <span>Текстуры</span>
                    </a>
                    <a href="<?= BASE_URL ?>/shaders.php" class="nav-item">
                        <i class="fas fa-sun"></i>
                        <span>Шейдеры</span>
                    </a>
                    <a href="<?= BASE_URL ?>/skins.php" class="nav-item">
                        <i class="fas fa-user"></i>
                        <span>Скины</span>
                    </a>
                    <a href="<?= BASE_URL ?>/servers.php" class="nav-item">
                        <i class="fas fa-server"></i>
                        <span>Серверы</span>
                    </a>
                    <a href="<?= BASE_URL ?>/news.php" class="nav-item">
                        <i class="fas fa-newspaper"></i>
                        <span>Новости</span>
                    </a>
                    <a href="<?= BASE_URL ?>/forum.php" class="nav-item">
                        <i class="fas fa-comments"></i>
                        <span>Форум</span>
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <main class="main-content">