<?php
require_once '../includes/config.php';
requireAdmin();

// Обработка действий модерации
if(isset($_POST['action']) && isset($_POST['file_id'])) {
    $file_id = (int)$_POST['file_id'];
    $action = $_POST['action'];
    
    if($action === 'approve') {
        $pdo->prepare("UPDATE files SET status = 'approved' WHERE id = ?")->execute([$file_id]);
        $message = "Файл одобрен";
    } elseif($action === 'reject') {
        $pdo->prepare("UPDATE files SET status = 'rejected' WHERE id = ?")->execute([$file_id]);
        $message = "Файл отклонен";
    } elseif($action === 'delete') {
        // Получаем путь к файлу для удаления
        $file = $pdo->prepare("SELECT file_path FROM files WHERE id = ?")->execute([$file_id])->fetch();
        if($file && file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }
        $pdo->prepare("DELETE FROM files WHERE id = ?")->execute([$file_id]);
        $message = "Файл удален";
    }
    
    header("Location: moderation.php?message=" . urlencode($message));
    exit;
}

// Получаем файлы на модерации
$stmt = $pdo->query("
    SELECT f.*, u.username, u.email, c.name as category_name, c.type as category_type
    FROM files f 
    LEFT JOIN users u ON f.author_id = u.id 
    LEFT JOIN categories c ON f.category_id = c.id 
    WHERE f.status = 'pending' 
    ORDER BY f.created_date DESC
");
$pending_files = $stmt->fetchAll();

include 'header.php';
?>

<div class="page-header">
    <h1>Модерация контента</h1>
    <div class="header-stats">
        <span class="stat-badge">Файлов на модерации: <?= count($pending_files) ?></span>
    </div>
</div>

<?php if(isset($_GET['message'])): ?>
    <div class="alert alert-success">
        ✅ <?= escape($_GET['message']) ?>
    </div>
<?php endif; ?>

<div class="table-container">
    <?php if(empty($pending_files)): ?>
        <div class="no-data">
            <h3>🎉 Отлично!</h3>
            <p>Нет файлов для модерации. Все чисто!</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Файл</th>
                    <th>Автор</th>
                    <th>Категория</th>
                    <th>Версия</th>
                    <th>Дата</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($pending_files as $file): ?>
                <tr>
                    <td>
                        <div class="file-info">
                            <strong><?= escape($file['title']) ?></strong>
                            <p class="file-description"><?= mb_substr(strip_tags($file['description']), 0, 100) ?>...</p>
                        </div>
                    </td>
                    <td>
                        <div class="user-info">
                            <strong><?= escape($file['username']) ?></strong>
                            <small><?= escape($file['email']) ?></small>
                        </div>
                    </td>
                    <td>
                        <span class="category-badge"><?= $file['category_name'] ?></span>
                        <small><?= $file['category_type'] ?></small>
                    </td>
                    <td><?= $file['minecraft_version'] ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($file['created_date'])) ?></td>
                    <td>
                        <div class="action-buttons">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-success" title="Одобрить">✅</button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-warning" title="Отклонить">❌</button>
                            </form>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-danger" title="Удалить" onclick="return confirm('Удалить файл безвозвратно?')">🗑️</button>
                            </form>
                            
                            <a href="../file.php?id=<?= $file['id'] ?>" class="btn btn-primary" target="_blank" title="Просмотреть">👁️</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<style>
.header-stats {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.stat-badge {
    background: #3498db;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
}

.file-info strong {
    display: block;
    margin-bottom: 0.3rem;
}

.file-description {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
    line-height: 1.4;
}

.user-info strong {
    display: block;
    margin-bottom: 0.2rem;
}

.user-info small {
    color: #666;
    font-size: 0.8rem;
}

.category-badge {
    display: block;
    background: #e9ecef;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
}

.no-data {
    text-align: center;
    padding: 3rem;
    color: #666;
}

.no-data h3 {
    margin-bottom: 1rem;
    color: #27ae60;
}

.action-buttons {
    display: flex;
    gap: 0.3rem;
    flex-wrap: wrap;
}

.action-buttons .btn {
    padding: 0.5rem;
    font-size: 0.9rem;
    min-width: auto;
}
</style>

<?php include 'footer.php'; ?>