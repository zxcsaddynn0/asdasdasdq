<?php
require_once 'includes/config.php';

$query = trim($_GET['q'] ?? '');
$category_type = $_GET['type'] ?? '';
$version = $_GET['version'] ?? '';
$sort = $_GET['sort'] ?? 'relevance';

$results = [];
$total_results = 0;

if(!empty($query)) {
    // Базовый запрос
    $sql = "SELECT f.*, u.username, c.name as category_name, c.type as category_type 
            FROM files f 
            LEFT JOIN users u ON f.author_id = u.id 
            LEFT JOIN categories c ON f.category_id = c.id 
            WHERE f.status = 'approved' AND 
                  (f.title LIKE ? OR f.description LIKE ?)";
    
    $params = ["%$query%", "%$query%"];
    
    // Фильтры
    if($category_type) {
        $sql .= " AND c.type = ?";
        $params[] = $category_type;
    }
    
    if($version) {
        $sql .= " AND f.minecraft_version = ?";
        $params[] = $version;
    }
    
    // Сортировка
    switch($sort) {
        case 'newest':
            $sql .= " ORDER BY f.created_date DESC";
            break;
        case 'popular':
            $sql .= " ORDER BY f.downloads_count DESC";
            break;
        case 'rating':
            $sql .= " ORDER BY f.rating DESC";
            break;
        default: // relevance
            $sql .= " ORDER BY 
                (f.title LIKE ?) DESC,
                f.downloads_count DESC";
            $params[] = "$query%";
            break;
    }
    
    // Пагинация
    $page = max(1, (int)($_GET['page'] ?? 1));
    $per_page = 10;
    $offset = ($page - 1) * $per_page;
    
    // Получаем результаты
    $stmt = $pdo->prepare($sql . " LIMIT $offset, $per_page");
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    
    // Общее количество
    $count_sql = "SELECT COUNT(*) FROM files f 
                  LEFT JOIN categories c ON f.category_id = c.id 
                  WHERE f.status = 'approved' AND 
                        (f.title LIKE ? OR f.description LIKE ?)";
    
    $count_params = ["%$query%", "%$query%"];
    
    if($category_type) {
        $count_sql .= " AND c.type = ?";
        $count_params[] = $category_type;
    }
    
    $total_stmt = $pdo->prepare($count_sql);
    $total_stmt->execute($count_params);
    $total_results = $total_stmt->fetchColumn();
    $total_pages = ceil($total_results / $per_page);
}

include 'includes/header.php';
?>

<div class="container">
    <h1>Поиск по сайту</h1>
    
    <form method="GET" class="search-form">
        <div class="search-box">
            <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Введите название мода, карты..." required>
            <button type="submit">Найти</button>
        </div>
        
        <div class="search-filters">
            <select name="type">
                <option value="">Все типы</option>
                <option value="mods" <?= $category_type=='mods'?'selected':'' ?>>Моды</option>
                <option value="maps" <?= $category_type=='maps'?'selected':'' ?>>Карты</option>
                <option value="resourcepacks" <?= $category_type=='resourcepacks'?'selected':'' ?>>Ресурспаки</option>
                <option value="shaders" <?= $category_type=='shaders'?'selected':'' ?>>Шейдеры</option>
                <option value="skins" <?= $category_type=='skins'?'selected':'' ?>>Скины</option>
            </select>
            
            <select name="version">
                <option value="">Все версии</option>
                <option value="1.20" <?= $version=='1.20'?'selected':'' ?>>1.20</option>
                <option value="1.19" <?= $version=='1.19'?'selected':'' ?>>1.19</option>
                <option value="1.18" <?= $version=='1.18'?'selected':'' ?>>1.18</option>
            </select>
            
            <select name="sort">
                <option value="relevance" <?= $sort=='relevance'?'selected':'' ?>>По релевантности</option>
                <option value="newest" <?= $sort=='newest'?'selected':'' ?>>Новые</option>
                <option value="popular" <?= $sort=='popular'?'selected':'' ?>>Популярные</option>
                <option value="rating" <?= $sort=='rating'?'selected':'' ?>>По рейтингу</option>
            </select>
        </div>
    </form>

    <?php if(!empty($query)): ?>
        <div class="search-results">
            <h2>Результаты поиска: "<?= htmlspecialchars($query) ?>"</h2>
            <p>Найдено: <?= $total_results ?> файлов</p>
            
            <?php if(empty($results)): ?>
                <p>Ничего не найдено. Попробуйте изменить запрос.</p>
            <?php else: ?>
                <div class="files-grid">
                    <?php foreach($results as $file): ?>
                    <div class="file-card">
                        <h3><a href="file.php?id=<?= $file['id'] ?>"><?= htmlspecialchars($file['title']) ?></a></h3>
                        <p class="file-type">Тип: <?= $file['category_type'] ?></p>
                        <p class="author">Автор: <?= htmlspecialchars($file['username']) ?></p>
                        <p class="version">Версия: <?= $file['minecraft_version'] ?></p>
                        <p class="downloads">Скачиваний: <?= $file['downloads_count'] ?></p>
                        <p class="description"><?= mb_substr(strip_tags($file['description']), 0, 100) ?>...</p>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Пагинация -->
                <?php if($total_pages > 1): ?>
                <div class="pagination">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?q=<?= urlencode($query) ?>&type=<?= $category_type ?>&version=<?= $version ?>&sort=<?= $sort ?>&page=<?= $i ?>" 
                           class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.search-form {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.search-box {
    display: flex;
    margin-bottom: 1rem;
}

.search-box input {
    flex: 1;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px 0 0 4px;
    font-size: 1.1rem;
}

.search-box button {
    background: #3498db;
    color: white;
    border: none;
    padding: 0 2rem;
    border-radius: 0 4px 4px 0;
    cursor: pointer;
    font-size: 1.1rem;
}

.search-filters {
    display: flex;
    gap: 1rem;
}

.search-filters select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.pagination {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
    gap: 0.5rem;
}

.pagination a {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    text-decoration: none;
    color: #333;
    border-radius: 4px;
}

.pagination a.active {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

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