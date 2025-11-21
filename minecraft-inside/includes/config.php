<?php
session_start();

// Настройки для XAMPP
define('DB_HOST', 'localhost');
define('DB_NAME', 'minesklad_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', 'http://localhost/minecraft-inside');

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Создание таблиц если не существуют
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            avatar VARCHAR(255) DEFAULT 'default.png',
            registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            role ENUM('user','admin') DEFAULT 'user',
            is_banned BOOLEAN DEFAULT FALSE
        );
        
        CREATE TABLE IF NOT EXISTS categories (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            type ENUM('mods','maps','resourcepacks','shaders','skins') NOT NULL
        );
        
        CREATE TABLE IF NOT EXISTS files (
            id INT PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            author_id INT NOT NULL,
            category_id INT NOT NULL,
            minecraft_version VARCHAR(20) NOT NULL,
            file_path VARCHAR(255) NOT NULL,
            preview_image VARCHAR(255),
            downloads_count INT DEFAULT 0,
            rating FLOAT DEFAULT 0,
            status ENUM('pending','approved','rejected') DEFAULT 'pending',
            created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            file_type ENUM('mods','maps','resourcepacks','shaders','skins') NOT NULL,
            FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        );
        
        CREATE TABLE IF NOT EXISTS comments (
            id INT PRIMARY KEY AUTO_INCREMENT,
            file_id INT NOT NULL,
            user_id INT NOT NULL,
            text TEXT NOT NULL,
            created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
        
        CREATE TABLE IF NOT EXISTS ratings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            file_id INT NOT NULL,
            user_id INT NOT NULL,
            rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_rating (file_id, user_id),
            FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
        
        CREATE TABLE IF NOT EXISTS favorites (
            id INT PRIMARY KEY AUTO_INCREMENT,
            file_id INT NOT NULL,
            user_id INT NOT NULL,
            created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_favorite (file_id, user_id),
            FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
        
        CREATE TABLE IF NOT EXISTS downloads (
            id INT PRIMARY KEY AUTO_INCREMENT,
            file_id INT NOT NULL,
            user_id INT NOT NULL,
            download_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
        
        INSERT IGNORE INTO categories (name, type) VALUES
        ('Оптимизация', 'mods'),
        ('Интерфейс', 'mods'),
        ('Боевые', 'mods'),
        ('Приключения', 'maps'),
        ('Выживание', 'maps'),
        ('Паркур', 'maps'),
        ('Реалистичные', 'resourcepacks'),
        ('Стилизованные', 'resourcepacks'),
        ('Фэнтези', 'resourcepacks'),
        ('Реализм', 'shaders'),
        ('Фэнтези', 'shaders'),
        ('Аниме', 'shaders'),
        ('Персонажи', 'skins'),
        ('Животные', 'skins'),
        ('Фэнтези', 'skins');
        
        -- Создаем администратора по умолчанию
        INSERT IGNORE INTO users (username, email, password_hash, role) 
        VALUES ('admin', 'admin@minecraft-inside.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
    ");
    
} catch(PDOException $e) {
    // Создаем БД если не существует
    if($e->getCode() == 1049) {
        try {
            $pdo = new PDO("mysql:host=".DB_HOST, DB_USER, DB_PASS);
            $pdo->exec("CREATE DATABASE ".DB_NAME);
            $pdo->exec("USE ".DB_NAME);
            // Перезагружаем страницу для создания таблиц
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        } catch(PDOException $e2) {
            die("Ошибка создания базы данных: " . $e2->getMessage());
        }
    } else {
        die("Ошибка подключения к базе данных: " . $e->getMessage());
    }
}

// Функция для защиты от XSS
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Функция проверки авторизации
function requireAuth() {
    if(!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Функция проверки прав администратора
function requireAdmin() {
    if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../login.php");
        exit;
    }
}
?>