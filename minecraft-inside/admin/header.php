<?php
require_once '../includes/config.php';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Minecraft Inside</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
    .admin-header {
        background: #2c3e50;
        color: white;
        padding: 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .admin-nav {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 70px;
    }
    
    .admin-brand a {
        color: white;
        text-decoration: none;
        font-size: 1.5rem;
        font-weight: bold;
    }
    
    .admin-links {
        display: flex;
        gap: 2rem;
        align-items: center;
    }
    
    .admin-links a {
        color: white;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        transition: background-color 0.3s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .admin-links a:hover,
    .admin-links a.active {
        background: rgba(255,255,255,0.1);
    }
    
    .admin-user {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: white;
    }
    
    .admin-user a {
        color: white;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        transition: background-color 0.3s;
    }
    
    .admin-user a:hover {
        background: rgba(255,255,255,0.1);
    }
    
    .admin-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
        min-height: calc(100vh - 70px);
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f8f9fa;
    }
    
    .page-header h1 {
        color: #2c3e50;
        margin: 0;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-primary {
        background: #3498db;
        color: white;
    }
    
    .btn-primary:hover {
        background: #2980b9;
    }
    
    .btn-success {
        background: #27ae60;
        color: white;
    }
    
    .btn-success:hover {
        background: #219653;
    }
    
    .btn-danger {
        background: #e74c3c;
        color: white;
    }
    
    .btn-danger:hover {
        background: #c0392b;
    }
    
    .btn-warning {
        background: #f39c12;
        color: white;
    }
    
    .btn-warning:hover {
        background: #d35400;
    }
    
    .table-container {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table th,
    .data-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #e9ecef;
    }
    
    .data-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .data-table tr:hover {
        background: #f8f9fa;
    }
    
    .status-badge {
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d4edda; color: #155724; }
    .status-rejected { background: #f8d7da; color: #721c24; }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border: 1px solid transparent;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }
    
    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .form-control {
        width: 100%;
        padding: 0.8rem;
        border: 2px solid #e9ecef;
        border-radius: 6px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }
    
    .form-control:focus {
        border-color: #3498db;
        outline: none;
    }
    
    .search-box {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .search-box input {
        flex: 1;
        padding: 0.8rem;
        border: 2px solid #e9ecef;
        border-radius: 6px;
        font-size: 1rem;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 2rem;
        padding: 1rem;
    }
    
    .pagination a,
    .pagination span {
        padding: 0.5rem 1rem;
        border: 1px solid #dee2e6;
        text-decoration: none;
        color: #3498db;
        border-radius: 4px;
    }
    
    .pagination .active {
        background: #3498db;
        color: white;
        border-color: #3498db;
    }
    </style>
</head>
<body>
    <header class="admin-header">
        <nav class="admin-nav">
            <div class="admin-brand">
                <a href="index.php">⚙️ Админ-панель</a>
            </div>
            
            <div class="admin-links">
                <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                    📊 Статистика
                </a>
                <a href="moderation.php" class="<?= basename($_SERVER['PHP_SELF']) == 'moderation.php' ? 'active' : '' ?>">
                    ✅ Модерация
                </a>
                <a href="users.php" class="<?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
                    👥 Пользователи
                </a>
                <a href="categories.php" class="<?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">
                    🏷️ Категории
                </a>
            </div>
            
            <div class="admin-user">
                <span><?= escape($_SESSION['username']) ?></span>
                <a href="../">🌐 Сайт</a>
                <a href="../logout.php">🚪 Выйти</a>
            </div>
        </nav>
    </header>

    <div class="admin-container">