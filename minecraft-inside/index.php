<?php 
require_once 'includes/config.php';

// Получаем популярные моды
$popular_mods = $pdo->query("
    SELECT f.*, u.username, c.name as category_name 
    FROM files f 
    LEFT JOIN users u ON f.author_id = u.id 
    LEFT JOIN categories c ON f.category_id = c.id 
    WHERE f.status = 'approved' AND c.type = 'mods'
    ORDER BY f.downloads_count DESC 
    LIMIT 8
")->fetchAll();

// Получаем новые файлы
$new_files = $pdo->query("
    SELECT f.*, u.username, c.name as category_name, c.type as category_type
    FROM files f 
    LEFT JOIN users u ON f.author_id = u.id 
    LEFT JOIN categories c ON f.category_id = c.id 
    WHERE f.status = 'approved'
    ORDER BY f.created_date DESC 
    LIMIT 12
")->fetchAll();

include 'includes/header.php';
?>

<div class="container">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Minecraft Inside</h1>
            <p class="hero-subtitle">Крупнейшая коллекция модов, карт и ресурспаков для Minecraft</p>
            <div class="hero-search">
                <form action="search.php" method="GET" class="search-form">
                    <div class="search-box">
                        <input type="text" name="q" placeholder="Поиск модов, карт, текстур, скинов..." class="search-input">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="categories-section">
        <h2 class="section-title">Категории</h2>
        <div class="categories-grid">
            <a href="mods.php" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-cubes"></i>
                </div>
                <h3>Моды</h3>
                <p>Добавьте новые возможности в игру</p>
            </a>
            
            <a href="maps.php" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-map"></i>
                </div>
                <h3>Карты</h3>
                <p>Исследуйте удивительные миры</p>
            </a>
            
            <a href="resourcepacks.php" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-palette"></i>
                </div>
                <h3>Текстуры</h3>
                <p>Измените внешний вид игры</p>
            </a>
            
            <a href="shaders.php" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-sun"></i>
                </div>
                <h3>Шейдеры</h3>
                <p>Улучшите графику до невероятного уровня</p>
            </a>
            
            <a href="skins.php" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-user"></i>
                </div>
                <h3>Скины</h3>
                <p>Настройте внешний вид персонажа</p>
            </a>
            
            <a href="servers.php" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-server"></i>
                </div>
                <h3>Серверы</h3>
                <p>Найдите сервер для игры</p>
            </a>
        </div>
    </section>

    <!-- Popular Mods -->
    <section class="files-section">
        <div class="section-header">
            <h2 class="section-title">Популярные моды</h2>
            <a href="mods.php" class="view-all">Все моды →</a>
        </div>
        
        <div class="files-grid">
            <?php foreach($popular_mods as $file): ?>
            <div class="file-card">
                <div class="file-image">
                    <img src="<?= $file['preview_image'] ?: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjgwIiBoZWlnaHQ9IjE2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOGY4Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIGltYWdlPC90ZXh0Pjwvc3ZnPg==' ?>" alt="<?= htmlspecialchars($file['title']) ?>">
                    <div class="file-badge">Мод</div>
                </div>
                <div class="file-content">
                    <div class="file-title">
                        <a href="file.php?id=<?= $file['id'] ?>"><?= htmlspecialchars($file['title']) ?></a>
                    </div>
                    <div class="file-meta">
                        <span><i class="fas fa-user"></i> <?= htmlspecialchars($file['username']) ?></span>
                        <span><i class="fas fa-tag"></i> <?= $file['category_name'] ?></span>
                    </div>
                    <div class="file-stats">
                        <span class="file-downloads">
                            <i class="fas fa-download"></i> <?= $file['downloads_count'] ?>
                        </span>
                        <span class="file-rating">
                            <i class="fas fa-star"></i> <?= number_format($file['rating'], 1) ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- New Files -->
    <section class="files-section">
        <div class="section-header">
            <h2 class="section-title">Новые файлы</h2>
        </div>
        
        <div class="files-grid">
            <?php foreach($new_files as $file): ?>
            <div class="file-card">
                <div class="file-image">
                    <img src="<?= $file['preview_image'] ?: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjgwIiBoZWlnaHQ9IjE2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOGY4Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIGltYWdlPC90ZXh0Pjwvc3ZnPg==' ?>" alt="<?= htmlspecialchars($file['title']) ?>">
                    <div class="file-badge"><?= $file['category_type'] ?></div>
                </div>
                <div class="file-content">
                    <div class="file-title">
                        <a href="file.php?id=<?= $file['id'] ?>"><?= htmlspecialchars($file['title']) ?></a>
                    </div>
                    <div class="file-meta">
                        <span><i class="fas fa-user"></i> <?= htmlspecialchars($file['username']) ?></span>
                        <span><i class="fas fa-folder"></i> <?= $file['category_name'] ?></span>
                    </div>
                    <div class="file-stats">
                        <span class="file-downloads">
                            <i class="fas fa-download"></i> <?= $file['downloads_count'] ?>
                        </span>
                        <span class="file-date">
                            <i class="fas fa-calendar"></i> <?= date('d.m.Y', strtotime($file['created_date'])) ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>