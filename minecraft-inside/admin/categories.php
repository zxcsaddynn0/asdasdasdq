<?php
require_once '../includes/config.php';
requireAdmin();

// Обработка добавления категории
if(isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    
    if(!empty($name) && !empty($type)) {
        try {
            $pdo->prepare("INSERT INTO categories (name, type) VALUES (?, ?)")->execute([$name, $type]);
            $message = "Категория добавлена";
        } catch(PDOException $e) {
            $error = "Ошибка: " . $e->getMessage();
        }
    } else {
        $error = "Все поля обязательны";
    }
}

// Обработка удаления категории
if(isset($_POST['delete_category'])) {
    $category_id = (int)$_POST['category_id'];
    
    // Проверяем, нет ли файлов в этой категории
    $file_count = $pdo->prepare("SELECT COUNT(*) FROM files WHERE category_id = ?")->execute([$category_id])->fetchColumn();
    
    if($file_count > 0) {
        $error = "Нельзя удалить категорию, в которой есть файлы";
    } else {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$category_id]);
        $message = "Категория удалена";
    }
}

// Получаем все категории
$categories = $pdo->query("
    SELECT c.*, 
           COUNT(f.id) as files_count 
    FROM categories c 
    LEFT JOIN files f ON c.id = f.category_id 
    GROUP BY c.id 
    ORDER BY c.type, c.name
")->fetchAll();

// Группируем по типам
$categories_by_type = [];
foreach($categories as $category) {
    $categories_by_type[$category['type']][] = $category;
}

include 'header.php';
?>

<div class="page-header">
    <h1>Управление категориями</h1>
</div>

<?php if(isset($message)): ?>
    <div class="alert alert-success">✅ <?= $message ?></div>
<?php endif; ?>

<?php if(isset($error)): ?>
    <div class="alert alert-error">❌ <?= $error ?></div>
<?php endif; ?>

<div class="admin-grid">
    <div class="admin-card">
        <h2>Добавить категорию</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Название категории</label>
                <input type="text" id="name" name="name" class="form-control" required maxlength="100">
            </div>
            
            <div class="form-group">
                <label for="type">Тип контента</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="">Выберите тип</option>
                    <option value="mods">Моды</option>
                    <option value="maps">Карты</option>
                    <option value="resourcepacks">Ресурспаки</option>
                    <option value="shaders">Шейдеры</option>
                    <option value="skins">Скины</option>
                </select>
            </div>
            
            <button type="submit" name="add_category" class="btn btn-success">➕ Добавить категорию</button>
        </form>
    </div>

    <div class="admin-card">
        <h2>Статистика категорий</h2>
        <div class="categories-stats">
            <?php foreach($categories_by_type as $type => $type_categories): ?>
                <div class="type-section">
                    <h3>
                        <?= $type === 'mods' ? '🛠️ Моды' : 
                           ($type === 'maps' ? '🗺️ Карты' : 
                           ($type === 'resourcepacks' ? '🎨 Ресурспаки' : 
                           ($type === 'shaders' ? '🌈 Шейдеры' : '👤 Скины'))) ?>
                        <span class="type-count">(<?= count($type_categories) ?>)</span>
                    </h3>
                    
                    <?php foreach($type_categories as $category): ?>
                        <div class="category-item">
                            <span class="category-name"><?= escape($category['name']) ?></span>
                            <span class="category-files"><?= $category['files_count'] ?> файлов</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="admin-card">
    <h2>Все категории</h2>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Тип</th>
                    <th>Файлов</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($categories as $category): ?>
                <tr>
                    <td><?= $category['id'] ?></td>
                    <td>
                        <strong><?= escape($category['name']) ?></strong>
                    </td>
                    <td>
                        <span class="type-badge type-<?= $category['type'] ?>">
                            <?= $category['type'] ?>
                        </span>
                    </td>
                    <td>
                        <span class="files-count"><?= $category['files_count'] ?></span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                                <button type="submit" name="delete_category" class="btn btn-danger" 
                                        <?= $category['files_count'] > 0 ? 'disabled title="Нельзя удалить категорию с файлами"' : '' ?>>
                                    🗑️ Удалить
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.categories-stats {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.type-section h3 {
    margin-bottom: 1rem;
    color: #2c3e50;
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 0.5rem;
}

.type-count {
    color: #666;
    font-size: 0.9rem;
}

.category-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem;
    background: #f8f9fa;
    border-radius: 6px;
    margin-bottom: 0.5rem;
}

.category-name {
    font-weight: 500;
}

.category-files {
    background: #3498db;
    color: white;
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
}

.type-badge {
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
}

.type-mods { background: #fff3cd; color: #856404; }
.type-maps { background: #d4edda; color: #155724; }
.type-resourcepacks { background: #d1ecf1; color: #0c5460; }
.type-shaders { background: #e2e3e5; color: #383d41; }
.type-skins { background: #f8d7da; color: #721c24; }

.files-count {
    font-weight: bold;
    color: #3498db;
}

.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>

<?php include 'footer.php'; ?>