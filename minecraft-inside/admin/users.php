<?php
require_once '../includes/config.php';
requireAdmin();

// Обработка действий с пользователями
if(isset($_POST['action']) && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    
    if($_POST['action'] === 'make_admin') {
        $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?")->execute([$user_id]);
        $message = "Пользователь назначен администратором";
    } elseif($_POST['action'] === 'remove_admin') {
        $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ?")->execute([$user_id]);
        $message = "Права администратора сняты";
    } elseif($_POST['action'] === 'ban') {
        $pdo->prepare("UPDATE users SET is_banned = TRUE WHERE id = ?")->execute([$user_id]);
        $message = "Пользователь забанен";
    } elseif($_POST['action'] === 'unban') {
        $pdo->prepare("UPDATE users SET is_banned = FALSE WHERE id = ?")->execute([$user_id]);
        $message = "Пользователь разбанен";
    }
    
    header("Location: users.php?message=" . urlencode($message));
    exit;
}

// Поиск и фильтрация
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Базовый запрос
$sql = "SELECT u.*, 
               COUNT(DISTINCT f.id) as files_count,
               COUNT(DISTINCT c.id) as comments_count,
               COALESCE(SUM(f.downloads_count), 0) as total_downloads
        FROM users u 
        LEFT JOIN files f ON u.id = f.author_id 
        LEFT JOIN comments c ON u.id = c.user_id 
        WHERE 1=1";
$params = [];

if($search) {
    $sql .= " AND (u.username LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if($role_filter) {
    $sql .= " AND u.role = ?";
    $params[] = $role_filter;
}

if($status_filter === 'banned') {
    $sql .= " AND u.is_banned = TRUE";
} elseif($status_filter === 'active') {
    $sql .= " AND u.is_banned = FALSE";
}

$sql .= " GROUP BY u.id ORDER BY u.registration_date DESC";

// Выполняем запрос
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

include 'header.php';
?>

<div class="page-header">
    <h1>Управление пользователями</h1>
    <div class="header-stats">
        <span class="stat-badge">Всего пользователей: <?= count($users) ?></span>
    </div>
</div>

<?php if(isset($_GET['message'])): ?>
    <div class="alert alert-success">
        ✅ <?= escape($_GET['message']) ?>
    </div>
<?php endif; ?>

<div class="search-box">
    <form method="GET" class="search-form">
        <input type="text" name="search" value="<?= escape($search) ?>" placeholder="Поиск по имени или email...">
        
        <select name="role">
            <option value="">Все роли</option>
            <option value="user" <?= $role_filter=='user'?'selected':'' ?>>Пользователь</option>
            <option value="admin" <?= $role_filter=='admin'?'selected':'' ?>>Администратор</option>
        </select>
        
        <select name="status">
            <option value="">Все статусы</option>
            <option value="active" <?= $status_filter=='active'?'selected':'' ?>>Активные</option>
            <option value="banned" <?= $status_filter=='banned'?'selected':'' ?>>Забаненные</option>
        </select>
        
        <button type="submit" class="btn btn-primary">🔍 Поиск</button>
        <a href="users.php" class="btn btn-warning">🔄 Сбросить</a>
    </form>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Пользователь</th>
                <th>Статистика</th>
                <th>Роль</th>
                <th>Статус</th>
                <th>Дата регистрации</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user): ?>
            <tr>
                <td>
                    <div class="user-info">
                        <strong><?= escape($user['username']) ?></strong>
                        <small><?= escape($user['email']) ?></small>
                        <div class="user-id">ID: <?= $user['id'] ?></div>
                    </div>
                </td>
                <td>
                    <div class="user-stats">
                        <div class="stat-item">
                            <span class="stat-number"><?= $user['files_count'] ?></span>
                            <span class="stat-label">файлов</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?= $user['comments_count'] ?></span>
                            <span class="stat-label">комментариев</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?= $user['total_downloads'] ?></span>
                            <span class="stat-label">скачиваний</span>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="role-badge role-<?= $user['role'] ?>">
                        <?= $user['role'] === 'admin' ? '👑 Админ' : '👤 Пользователь' ?>
                    </span>
                </td>
                <td>
                    <?php if($user['is_banned']): ?>
                        <span class="status-badge status-banned">🚫 Забанен</span>
                    <?php else: ?>
                        <span class="status-badge status-active">✅ Активен</span>
                    <?php endif; ?>
                </td>
                <td><?= date('d.m.Y H:i', strtotime($user['registration_date'])) ?></td>
                <td>
                    <div class="action-buttons">
                        <?php if($user['role'] === 'user'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <input type="hidden" name="action" value="make_admin">
                                <button type="submit" class="btn btn-success" title="Сделать админом">👑</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <input type="hidden" name="action" value="remove_admin">
                                <button type="submit" class="btn btn-warning" title="Убрать админа">👤</button>
                            </form>
                        <?php endif; ?>
                        
                        <?php if(!$user['is_banned']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <input type="hidden" name="action" value="ban">
                                <button type="submit" class="btn btn-danger" title="Забанить">🚫</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <input type="hidden" name="action" value="unban">
                                <button type="submit" class="btn btn-success" title="Разбанить">✅</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.search-form {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.search-form input,
.search-form select {
    padding: 0.8rem;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    font-size: 1rem;
}

.user-info strong {
    display: block;
    margin-bottom: 0.2rem;
}

.user-info small {
    color: #666;
    font-size: 0.9rem;
}

.user-id {
    font-size: 0.8rem;
    color: #999;
    margin-top: 0.2rem;
}

.user-stats {
    display: flex;
    gap: 1rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-weight: bold;
    color: #3498db;
    font-size: 1.1rem;
}

.stat-label {
    font-size: 0.8rem;
    color: #666;
}

.role-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
}

.role-admin { background: #fff3cd; color: #856404; }
.role-user { background: #d4edda; color: #155724; }

.status-banned { background: #f8d7da; color: #721c24; }
.status-active { background: #d4edda; color: #155724; }
</style>

<?php include 'footer.php'; ?>