<?php 
require_once 'includes/config.php';

$stmt = $pdo->query("
    SELECT f.*, u.username, c.name as category_name 
    FROM files f 
    LEFT JOIN users u ON f.author_id = u.id 
    LEFT JOIN categories c ON f.category_id = c.id 
    WHERE f.status = 'approved' AND c.type = 'skins'
    ORDER BY f.created_date DESC 
    LIMIT 12
");
$files = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container">
    <h1>Скины для Minecraft</h1>
    <p>Измените внешний вид вашего персонажа с помощью уникальных скинов</p>
    
    <div class="files-grid">
        <?php if(empty($files)): ?>
            <div class="no-results">
                <h3>Скинов пока нет</h3>
                <p>Будьте первым, кто <a href="upload.php">добавит скин</a></p>
            </div>
        <?php else: ?>
            <?php foreach($files as $file): ?>
            <div class="file-card">
                <h3><a href="file.php?id=<?= $file['id'] ?>"><?= htmlspecialchars($file['title']) ?></a></h3>
                <p class="author">Автор: <?= htmlspecialchars($file['username']) ?></p>
                <p class="version">Версия: <?= $file['minecraft_version'] ?></p>
                <p class="downloads">📥 Скачиваний: <?= $file['downloads_count'] ?></p>
                <p class="category">Категория: <?= $file['category_name'] ?></p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>