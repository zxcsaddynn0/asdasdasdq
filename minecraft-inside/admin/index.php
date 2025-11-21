<?php
require_once '../includes/config.php';
requireAdmin();

// Статистика
$stats = [
    'total_files' => $pdo->query("SELECT COUNT(*) FROM files")->fetchColumn(),
    'pending_files' => $pdo->query("SELECT COUNT(*) FROM files WHERE status = 'pending'")->fetchColumn(),
    'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'total_downloads' => $pdo->query("SELECT SUM(downloads_count) FROM files")->fetchColumn() ?: 0,
    'total_comments' => $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn()
];

// Последние действия
$recent_actions = $pdo->query("
    SELECT f.title, u.username, f.status, f.created_date 
    FROM files f 
    LEFT JOIN users u ON f.author_id = u.id 
    ORDER BY f.created_date DESC 
    LIMIT 5
")->fetchAll();

include 'header.php';
?>

<div class="admin-container">
    <h1>Панель администратора</h1>
    <p class="admin-welcome">Добро пожаловать, <?= escape($_SESSION['username']) ?>!</p>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📁</div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['total_files'] ?></div>
                <div class="stat-label">Всего файлов</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">⏳</div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['pending_files'] ?></div>
                <div class="stat-label">На модерации</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['total_users'] ?></div>
                <div class="stat-label">Пользователей</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">📥</div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['total_downloads'] ?></div>
                <div class="stat-label">Скачиваний</div>
            </div>
        </div>
    </div>

    <div class="admin-grid">
        <div class="admin-card">
            <h2>Быстрые действия</h2>
            <div class="quick-actions">
                <a href="moderation.php" class="action-btn">
                    <span class="action-icon">✅</span>
                    <span class="action-text">Модерация файлов</span>
                    <span class="action-badge"><?= $stats['pending_files'] ?></span>
                </a>
                
                <a href="users.php" class="action-btn">
                    <span class="action-icon">👥</span>
                    <span class="action-text">Управление пользователями</span>
                </a>
                
                <a href="categories.php" class="action-btn">
                    <span class="action-icon">🏷️</span>
                    <span class="action-text">Категории и теги</span>
                </a>
            </div>
        </div>

        <div class="admin-card">
            <h2>Последние действия</h2>
            <div class="recent-actions">
                <?php if(empty($recent_actions)): ?>
                    <p class="no-actions">Нет recent действий</p>
                <?php else: ?>
                    <?php foreach($recent_actions as $action): ?>
                    <div class="action-item">
                        <div class="action-main">
                            <strong><?= escape($action['username']) ?></strong> загрузил файл
                        </div>
                        <div class="action-details">
                            <span class="action-file">"<?= escape($action['title']) ?>"</span>
                            <span class="action-status status-<?= $action['status'] ?>">
                                <?= $action['status'] === 'pending' ? '⏳ На модерации' : 
                                   ($action['status'] === 'approved' ? '✅ Одобрен' : '❌ Отклонен') ?>
                            </span>
                        </div>
                        <div class="action-time">
                            <?= date('d.m.Y H:i', strtotime($action['created_date'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.admin-welcome {
    color: #666;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    font-size: 2.5rem;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #2c3e50;
    line-height: 1;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

.admin-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.admin-card {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
}

.admin-card h2 {
    margin-bottom: 1.5rem;
    color: #2c3e50;
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 0.5rem;
}

.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s;
    position: relative;
}

.action-btn:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.action-icon {
    font-size: 1.5rem;
}

.action-text {
    flex: 1;
    font-weight: 500;
}

.action-badge {
    background: #e74c3c;
    color: white;
    padding: 0.3rem 0.6rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: bold;
}

.recent-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.action-item {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.action-main {
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.action-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.action-file {
    color: #666;
    font-style: italic;
}

.action-status {
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-approved { background: #d4edda; color: #155724; }
.status-rejected { background: #f8d7da; color: #721c24; }

.action-time {
    color: #999;
    font-size: 0.8rem;
}

.no-actions {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 2rem;
}
</style>

<?php include 'footer.php'; ?>