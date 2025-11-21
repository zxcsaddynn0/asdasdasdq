<?php
require_once 'includes/config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Получаем файлы пользователя
$stmt = $pdo->prepare("
    SELECT f.*, c.name as category_name, c.type as category_type
    FROM files f 
    LEFT JOIN categories c ON f.category_id = c.id 
    WHERE f.author_id = ? 
    ORDER BY f.created_date DESC
");
$stmt->execute([$user_id]);
$user_files = $stmt->fetchAll();

// Статистика
$total_downloads = array_sum(array_column($user_files, 'downloads_count'));
$approved_files = array_filter($user_files, function($file) {
    return $file['status'] === 'approved';
});
$pending_files = array_filter($user_files, function($file) {
    return $file['status'] === 'pending';
});

include 'includes/header.php';
?>

<div class="container">
    <div class="profile-header">
        <div class="profile-avatar">
            <div class="avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
        </div>
        <div class="profile-info">
            <h1><?= htmlspecialchars($user['username']) ?></h1>
            <p class="profile-email">📧 <?= htmlspecialchars($user['email']) ?></p>
            <p class="profile-role">🎯 <?= $user['role'] === 'admin' ? 'Администратор' : 'Пользователь' ?></p>
            <p class="profile-date">📅 Зарегистрирован: <?= date('d.m.Y', strtotime($user['registration_date'])) ?></p>
        </div>
    </div>

    <div class="profile-stats">
        <div class="stat-card">
            <div class="stat-number"><?= count($user_files) ?></div>
            <div class="stat-label">Всего файлов</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= count($approved_files) ?></div>
            <div class="stat-label">Одобрено</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= count($pending_files) ?></div>
            <div class="stat-label">На модерации</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $total_downloads ?></div>
            <div class="stat-label">Всего скачиваний</div>
        </div>
    </div>

    <div class="profile-content">
        <div class="profile-actions">
            <a href="upload.php" class="btn-primary">📤 Загрузить файл</a>
            <?php if($user['role'] === 'admin'): ?>
                <a href="admin/" class="btn-secondary">⚙️ Панель администратора</a>
            <?php endif; ?>
        </div>

        <div class="user-files">
            <h2>Мои файлы</h2>
            
            <?php if(empty($user_files)): ?>
                <div class="no-files">
                    <h3>У вас пока нет файлов</h3>
                    <p>Начните с загрузки вашего первого контента!</p>
                    <a href="upload.php" class="btn-primary">Загрузить первый файл</a>
                </div>
            <?php else: ?>
                <div class="files-grid">
                    <?php foreach($user_files as $file): ?>
                    <div class="file-card">
                        <div class="file-status <?= $file['status'] ?>">
                            <?= $file['status'] == 'pending' ? '⏳ На модерации' : 
                                ($file['status'] == 'approved' ? '✅ Одобрен' : '❌ Отклонен') ?>
                        </div>
                        <h3><a href="file.php?id=<?= $file['id'] ?>"><?= htmlspecialchars($file['title']) ?></a></h3>
                        <p class="file-type">Тип: <?= $file['category_type'] ?></p>
                        <p class="category">Категория: <?= $file['category_name'] ?></p>
                        <p class="downloads">📥 Скачиваний: <?= $file['downloads_count'] ?></p>
                        <p class="rating">⭐ Рейтинг: <?= number_format($file['rating'], 1) ?></p>
                        <p class="date">📅 <?= date('d.m.Y', strtotime($file['created_date'])) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.profile-header {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 2rem;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
}

.profile-avatar .avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    font-weight: bold;
}

.profile-info h1 {
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.profile-email, .profile-role, .profile-date {
    margin: 0.3rem 0;
    color: #666;
}

.profile-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #3498db;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

.profile-content {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
}

.profile-actions {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.btn-primary {
    background: #3498db;
    color: white;
    padding: 0.8rem 1.5rem;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
}

.btn-secondary {
    background: #95a5a6;
    color: white;
    padding: 0.8rem 1.5rem;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
}

.user-files h2 {
    margin-bottom: 1.5rem;
    color: #2c3e50;
}

.no-files {
    text-align: center;
    padding: 3rem;
    background: #f8f9fa;
    border-radius: 10px;
}

.no-files h3 {
    margin-bottom: 1rem;
    color: #666;
}

.file-status {
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    margin-bottom: 0.5rem;
    display: inline-block;
    font-weight: bold;
}

.file-status.pending { background: #fff3cd; color: #856404; }
.file-status.approved { background: #d4edda; color: #155724; }
.file-status.rejected { background: #f8d7da; color: #721c24; }

.file-type {
    background: #3498db;
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 3px;
    font-size: 0.8rem;
    display: inline-block;
    margin-bottom: 0.5rem;
}
</style>

<?php include 'includes/footer.php'; ?>