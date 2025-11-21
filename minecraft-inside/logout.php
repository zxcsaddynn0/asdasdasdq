<?php
require_once 'includes/config.php';

// Уничтожаем сессию
session_destroy();

// Перенаправляем на главную
header("Location: index.php");
exit;
?>