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
        WHERE f.status = 'approved' AND c.type = 'mods'";

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

// Получаем общее количество для пагинации
$count_sql = "SELECT COUNT(*) FROM files f 
              LEFT JOIN categories c ON f.category_id = c.id 
              WHERE f.status = 'approved' AND c.type = 'mods'";

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

// Добавляем пагинацию к основному запросу
$sql .= " LIMIT $offset, $per_page";

// Выполняем запрос
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$files = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container">
    <h1>Моды для Minecraft</h1>
    
    <!-- Фильтры -->
    <div class="filters">
        <form method="GET">
            <select name="version">
                <option value="">Все версии</option>
                <option value="1.20" <?= $version_filter=='1.20'?'selected':'' ?>>1.20</option>
                <option value="1.19" <?= $version_filter=='1.19'?'selected':'' ?>>1.19</option>
                <option value="1.18" <?= $version_filter=='1.18'?'selected':'' ?>>1.18</option>
                <option value="1.17" <?= $version_filter=='1.17'?'selected':'' ?>>1.17</option>
                <option value="1.16" <?= $version_filter=='1.16'?'selected':'' ?>>1.16</option>
            </select>
            
            <select name="category">
                <option value="">Все категории</option>
                <?php
                $cats = $pdo->query("SELECT * FROM categories WHERE type = 'mods'");
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
                <a href="upload.php" class="btn-upload">+ Добавить мод</a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Информация о результатах -->
    <div class="results-info">
        <p>Найдено модов: <?= $total_files ?></p>
        <?php if($version_filter || $category_filter): ?>
            <a href="mods.php" class="btn-clear">Сбросить фильтры</a>
        <?php endif; ?>
    </div>
    
    <!-- Список модов -->
    <div class="files-grid">
        <?php if(empty($files)): ?>
            <div class="no-results">
                <h3>Моды не найдены</h3>
                <p>Попробуйте изменить параметры фильтрации</p>
            </div>
        <?php else: ?>
            <?php foreach($files as $file): ?>
            <div class="file-card">
                <div class="file-image">
                    <img src="<?= $file['preview_image'] ?: '../images/no-image.png' ?>" alt="<?= htmlspecialchars($file['title']) ?>">
                </div>
                <div class="file-info">
                    <h3><a href="file.php?id=<?= $file['id'] ?>"><?= htmlspecialchars($file['title']) ?></a></h3>
                    <p class="author">Автор: <?= htmlspecialchars($file['username']) ?></p>
                    <p class="version">Версия: <?= $file['minecraft_version'] ?></p>
                    <p class="downloads">📥 Скачиваний: <?= $file['downloads_count'] ?></p>
                    <p class="rating">⭐ Рейтинг: <?= number_format($file['rating'], 1) ?></p>
                    <p class="category">Категория: <?= $file['category_name'] ?></p>
                    <p class="date">Добавлен: <?= date('d.m.Y', strtotime($file['created_date'])) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Пагинация -->
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

<style>
.filters {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.filters form {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.filters select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    min-width: 150px;
}

.btn-upload {
    background: #27ae60;
    color: white;
    padding: 0.5rem 1rem;
    text-decoration: none;
    border-radius: 4px;
    margin-left: auto;
}

.results-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0 0.5rem;
}

.btn-clear {
    background: #e74c3c;
    color: white;
    padding: 0.3rem 0.8rem;
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.9rem;
}

.file-image {
    height: 150px;
    overflow: hidden;
    border-radius: 8px 8px 0 0;
}

.file-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.file-info {
    padding: 1rem;
}

.no-results {
    grid-column: 1 / -1;
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 8px;
}

.no-results h3 {
    color: #666;
    margin-bottom: 1rem;
}
</style>

<?php include 'includes/footer.php'; ?>