<?php
require_once 'includes/config.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Не авторизован']);
    exit;
}

if($_POST && isset($_POST['file_id']) && isset($_POST['rating'])) {
    $file_id = (int)$_POST['file_id'];
    $rating = (int)$_POST['rating'];
    $user_id = $_SESSION['user_id'];
    
    // Проверяем валидность оценки
    if($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'error' => 'Неверная оценка']);
        exit;
    }
    
    try {
        // Добавляем/обновляем рейтинг
        $stmt = $pdo->prepare("INSERT INTO ratings (file_id, user_id, rating) VALUES (?, ?, ?) 
                              ON DUPLICATE KEY UPDATE rating = ?");
        $stmt->execute([$file_id, $user_id, $rating, $rating]);
        
        // Пересчитываем средний рейтинг
        $avg_stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as votes FROM ratings WHERE file_id = ?");
        $avg_stmt->execute([$file_id]);
        $result = $avg_stmt->fetch();
        
        $pdo->prepare("UPDATE files SET rating = ? WHERE id = ?")->execute([$result['avg_rating'], $file_id]);
        
        echo json_encode([
            'success' => true, 
            'new_rating' => round($result['avg_rating'], 1),
            'votes' => $result['votes']
        ]);
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Неверные параметры']);
}
?>