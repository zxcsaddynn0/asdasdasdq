<?php 
require_once 'includes/config.php';

$file_id = (int)$_GET['id'];

// Исправить эту часть:
$stmt = $pdo->prepare("
    SELECT f.*, u.username, u.avatar, c.name as category_name, c.type as category_type
    FROM files f 
    LEFT JOIN users u ON f.author_id = u.id 
    LEFT JOIN categories c ON f.category_id = c.id 
    WHERE f.id = ? AND f.status = 'approved'
");
$stmt->execute([$file_id]);
$file = $stmt->fetch();

if(!$file) {
    die("Файл не найден или не одобрен");
}

// Обработка скачивания
if(isset($_GET['download']) && isset($_SESSION['user_id'])) {
    $pdo->prepare("UPDATE files SET downloads_count = downloads_count + 1 WHERE id = ?")->execute([$file_id]);
    
    // Логируем скачивание
    $pdo->prepare("INSERT INTO downloads (file_id, user_id, download_date) VALUES (?, ?, NOW())")->execute([$file_id, $_SESSION['user_id']]);
    
    header("Location: " . $file['file_path']);
    exit;
}

// Обработка комментария
if($_POST && isset($_POST['comment']) && isset($_SESSION['user_id'])) {
    $comment = trim($_POST['comment']);
    if(!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (file_id, user_id, text) VALUES (?, ?, ?)");
        $stmt->execute([$file_id, $_SESSION['user_id'], $comment]);
        header("Location: file.php?id=$file_id");
        exit;
    }
}

// Получаем комментарии
$comments_stmt = $pdo->prepare("
    SELECT c.*, u.username, u.avatar 
    FROM comments c 
    LEFT JOIN users u ON c.user_id = u.id 
    WHERE c.file_id = ? 
    ORDER BY c.created_date DESC
");
$comments_stmt->execute([$file_id]);
$comments = $comments_stmt->fetchAll();

try {
    $stmt = $pdo->prepare("
        SELECT f.*, u.username
        FROM files f
        LEFT JOIN users u ON f.author_id = u.id
        WHERE f.category_id = ? AND f.id != ? AND f.status = 'approved'
        ORDER BY f.downloads_count DESC
        LIMIT 4
    ");
    
    if (!$stmt) {
        throw new Exception("Ошибка prepare: " . json_encode($pdo->errorInfo()));
    }
    
    $stmt->execute([$file['category_id'], $file_id]);
    $similar_files = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("SQL Error: " . $e->getMessage());
    $similar_files = []; // Пустой массив при ошибке
}


include 'includes/header.php';
?>

<div class="container">
    <!-- Хлебные крошки -->
    <div class="breadcrumbs">
        <a href="index.php">Главная</a> > 
        <a href="<?= $file['category_type'] ?>.php"><?= ucfirst($file['category_type']) ?></a> > 
        <span><?= htmlspecialchars($file['title']) ?></span>
    </div>

    <!-- Основная информация -->
    <div class="file-header">
        <div class="file-main">
            <div class="file-image">
                <img src="<?= $file['preview_image'] ?: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjVmNWY1Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNiIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIGltYWdlPC90ZXh0Pjwvc3ZnPg==' ?>" alt="<?= htmlspecialchars($file['title']) ?>">
            </div>
            <div class="file-details">
                <h1><?= htmlspecialchars($file['title']) ?></h1>
                <div class="file-meta">
                    <span class="author">👤 Автор: <?= htmlspecialchars($file['username']) ?></span>
                    <span class="version">🎮 Версия: <?= $file['minecraft_version'] ?></span>
                    <span class="downloads">📥 Скачиваний: <?= $file['downloads_count'] ?></span>
                    <span class="rating">⭐ Рейтинг: <?= number_format($file['rating'], 1) ?>/5</span>
                    <span class="category">🏷️ Категория: <?= $file['category_name'] ?></span>
                    <span class="date">📅 Добавлен: <?= date('d.m.Y', strtotime($file['created_date'])) ?></span>
                </div>

                <!-- Кнопки действий -->
                <div class="file-actions">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="file.php?id=<?= $file_id ?>&download=1" class="btn-download">📥 Скачать</a>
                    <?php else: ?>
                        <a href="login.php" class="btn-download">🔒 Войдите чтобы скачать</a>
                    <?php endif; ?>
                    
                    <button class="btn-like" onclick="rateFile(5)">❤️ Нравится</button>
                    <button class="btn-favorite">⭐ В избранное</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Описание -->
    <div class="file-description">
        <h2>Описание</h2>
        <div class="description-content">
            <?= nl2br(htmlspecialchars($file['description'])) ?>
        </div>
    </div>

    <!-- Похожие файлы -->
    <?php if(!empty($similar_files)): ?>
    <div class="similar-files">
        <h2>Похожие файлы</h2>
        <div class="files-grid">
            <?php foreach($similar_files as $similar): ?>
            <div class="file-card">
                <h3><a href="file.php?id=<?= $similar['id'] ?>"><?= htmlspecialchars($similar['title']) ?></a></h3>
                <p class="author">Автор: <?= htmlspecialchars($similar['username']) ?></p>
                <p class="downloads">Скачиваний: <?= $similar['downloads_count'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Комментарии -->
    <div class="comments-section">
        <h2>Комментарии (<?= count($comments) ?>)</h2>
        
        <?php if(isset($_SESSION['user_id'])): ?>
        <form method="POST" class="comment-form">
            <textarea name="comment" placeholder="Оставьте ваш комментарий..." required maxlength="1000"></textarea>
            <button type="submit">Отправить комментарий</button>
        </form>
        <?php else: ?>
        <div class="login-prompt">
            <p><a href="login.php">Войдите</a>, чтобы оставить комментарий</p>
        </div>
        <?php endif; ?>

        <div class="comments-list">
            <?php if(empty($comments)): ?>
                <p class="no-comments">Пока нет комментариев. Будьте первым!</p>
            <?php else: ?>
                <?php foreach($comments as $comment): ?>
                <div class="comment">
                    <div class="comment-header">
                        <div class="comment-author">
                            <strong><?= htmlspecialchars($comment['username']) ?></strong>
                        </div>
                        <div class="comment-date">
                            <?= date('d.m.Y H:i', strtotime($comment['created_date'])) ?>
                        </div>
                    </div>
                    <div class="comment-text">
                        <?= nl2br(htmlspecialchars($comment['text'])) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.breadcrumbs {
    margin-bottom: 2rem;
    color: #666;
    font-size: 0.9rem;
}

.breadcrumbs a {
    color: #3498db;
    text-decoration: none;
}

.breadcrumbs a:hover {
    text-decoration: underline;
}

.file-header {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 2rem;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
}

.file-main {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
}

.file-image {
    height: 200px;
}

.file-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.file-details {
    padding: 2rem 2rem 2rem 0;
}

.file-details h1 {
    color: #2c3e50;
    margin-bottom: 1.5rem;
    font-size: 2rem;
}

.file-meta {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
    margin-bottom: 2rem;
}

.file-meta span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #555;
}

.file-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-download {
    background: #27ae60;
    color: white;
    padding: 1rem 2rem;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 1.1rem;
    transition: background-color 0.3s;
}

.btn-download:hover {
    background: #219653;
}

.btn-like, .btn-favorite {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.3s;
}

.btn-like:hover, .btn-favorite:hover {
    background: #e9ecef;
}

.file-description {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
}

.file-description h2 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.description-content {
    line-height: 1.7;
    color: #333;
}

.similar-files {
    margin-bottom: 3rem;
}

.similar-files h2 {
    color: #2c3e50;
    margin-bottom: 1.5rem;
}

.comments-section {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
}

.comments-section h2 {
    color: #2c3e50;
    margin-bottom: 1.5rem;
}

.comment-form textarea {
    width: 100%;
    padding: 1rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 1rem;
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
    font-size: 1rem;
}

.comment-form button {
    background: #3498db;
    color: white;
    padding: 0.8rem 2rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
}

.login-prompt {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 2rem;
}

.login-prompt a {
    color: #3498db;
    text-decoration: none;
    font-weight: bold;
}

.comments-list {
    margin-top: 2rem;
}

.comment {
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem 0;
}

.comment:last-child {
    border-bottom: none;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.8rem;
}

.comment-author {
    font-weight: bold;
    color: #2c3e50;
}

.comment-date {
    color: #6c757d;
    font-size: 0.9rem;
}

.comment-text {
    line-height: 1.6;
    color: #333;
}

.no-comments {
    text-align: center;
    color: #6c757d;
    font-style: italic;
    padding: 2rem;
}
</style>

<script>
function rateFile(rating) {
    if(!confirm('Поставить оценку ' + rating + ' звезд?')) return;
    
    fetch('rating.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'file_id=<?= $file_id ?>&rating=' + rating
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Спасибо за вашу оценку!');
            location.reload();
        } else {
            alert('Ошибка: ' + data.error);
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>