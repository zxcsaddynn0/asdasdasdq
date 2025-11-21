<?php 
require_once 'includes/config.php';

// Фильтры
$version_filter = $_GET['version'] ?? '';
$category_filter = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

// Пагинация
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Базовый запрос
$sql = "SELECT f.*, u.username, c.name as category_name 
        FROM files f 
        LEFT JOIN users u ON f.author_id = u.id 
        LEFT JOIN categories c ON f.category_id = c.id 
        WHERE f.status = 'approved' AND c.type = 'maps'";

$params = [];
$count_params = [];

// Применяем фильтры
if($version_filter) {
    $sql .= " AND f.minecraft_version = ?";
    $params[] = $version_filter;
    $count_params[] = $version_filter;
}

if($category_filter) {
    $sql .= " AND f.category_id = ?";
    $params[] = $category_filter;
    $count_params[] = $category_filter;
}

// Сортировка
switch($sort) {
    case 'popular': 
        $sql .= " ORDER BY f.downloads_count DESC"; 
        break;
    case 'rating': 
        $sql .= " ORDER BY f.rating DESC"; 
        break;
    default: 
        $sql .= " ORDER BY f.created_date DESC"; 
        break;
}

// Получаем общее количество
$count_sql = "SELECT COUNT(*) FROM files f 
              LEFT JOIN categories c ON f.category_id = c.id 
              WHERE f.status = 'approved' AND c.type = 'maps'";

if($version_filter) {
    $count_sql .= " AND f.minecraft_version = ?";
}
if($category_filter) {
    $count_sql .= " AND f.category_id = ?";
}

$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($count_params);
$total_files = $count_stmt->fetchColumn();
$total_pages = ceil($total_files / $per_page);

// Добавляем пагинацию
$sql .= " LIMIT $offset, $per_page";

// Выполняем запрос
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$files = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container">
    <h1>Карты для Minecraft</h1>
    
    <div class="filters">
        <form method="GET">
            <select name="version">
                <option value="">Все версии</option>
                <option value="1.20" <?= $version_filter=='1.20'?'selected':'' ?>>1.20</option>
                <option value="1.19" <?= $version_filter=='1.19'?'selected':'' ?>>1.19</option>
                <option value="1.18" <?= $version_filter=='1.18'?'selected':'' ?>>1.18</option>
            </select>
            
            <select name="category">
                <option value="">Все категории</option>
                <?php
                $cats = $pdo->query("SELECT * FROM categories WHERE type = 'maps'");
                while($cat = $cats->fetch()) {
                    $selected = $category_filter == $cat['id'] ? 'selected' : '';
                    echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
                }
                ?>
            </select>
            
            <select name="sort">
                <option value="newest" <?= $sort=='newest'?'selected':'' ?>>Новые</option>
                <option value="popular" <?= $sort=='popular'?'selected':'' ?>>Популярные</option>
                <option value="rating" <?= $sort=='rating'?'selected':'' ?>>По рейтингу</option>
            </select>
            
            <button type="submit">Применить</button>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="upload.php" class="btn-upload">+ Добавить карту</a>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="results-info">
        <p>Найдено карт: <?= $total_files ?></p>
        <?php if($version_filter || $category_filter): ?>
            <a href="maps.php" class="btn-clear">Сбросить фильтры</a>
        <?php endif; ?>
    </div>
    
    <div class="files-grid">
        <?php if(empty($files)): ?>
            <div class="no-results">
                <h3>Карты не найдены</h3>
                <p>Попробуйте изменить параметры фильтрации или <a href="upload.php">добавьте первую карту</a></p>
            </div>
        <?php else: ?>
            <?php foreach($files as $file): ?>
            <div class="file-card">
                <div class="file-image">
                    <img src="<?= $file['preview_image'] ?: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjE1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjVmNWY1Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIGltYWdlPC90ZXh0Pjwvc3ZnPg==' ?>" alt="<?= htmlspecialchars($file['title']) ?>">
                </div>
                <div class="file-info">
                    <h3><a href="file.php?id=<?= $file['id'] ?>"><?= htmlspecialchars($file['title']) ?></a></h3>
                    <p class="author">Автор: <?= htmlspecialchars($file['username']) ?></p>
                    <p class="version">Версия: <?= $file['minecraft_version'] ?></p>
                    <p class="downloads">📥 Скачиваний: <?= $file['downloads_count'] ?></p>
                    <p class="rating">⭐ Рейтинг: <?= number_format($file['rating'], 1) ?></p>
                    <p class="category">Категория: <?= $file['category_name'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <?php if($total_pages > 1): ?>
    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">← Назад</a>
        <?php endif; ?>
        
        <?php for($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
               class="<?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
        
        <?php if($page < $total_pages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Вперед →</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>