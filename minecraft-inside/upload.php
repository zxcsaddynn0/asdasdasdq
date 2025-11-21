<?php 
require_once 'includes/config.php';

// Проверка авторизации
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if($_POST) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $minecraft_version = $_POST['minecraft_version'];
    $file_type = $_POST['file_type'];
    
    // Валидация
    if(empty($title) || empty($description) || empty($category_id) || empty($minecraft_version)) {
        $error = "Все поля обязательны для заполнения";
    } else {
        // Загрузка файла
        $file_path = '';
        if(isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
            $upload_dir = 'uploads/' . $file_type . '/';
            
            // Создаем директорию если не существует
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $_FILES['file']['name']);
            $file_path = $upload_dir . $file_name;
            
            // Проверка типа файла
            $allowed_types = ['application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed'];
            $file_type_mime = mime_content_type($_FILES['file']['tmp_name']);
            
            if(in_array($file_type_mime, $allowed_types)) {
                if(move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
                    // Успешная загрузка
                } else {
                    $error = "Ошибка загрузки файла";
                }
            } else {
                $error = "Разрешены только файлы ZIP, RAR и 7Z";
            }
        } else {
            $error = "Файл обязателен для загрузки";
        }
        
        if(empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO files (title, description, author_id, category_id, minecraft_version, file_path, file_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
                $stmt->execute([$title, $description, $_SESSION['user_id'], $category_id, $minecraft_version, $file_path, $file_type]);
                $success = "Файл успешно отправлен на модерацию!";
                
                // Очищаем форму
                $_POST = [];
            } catch(PDOException $e) {
                $error = "Ошибка загрузки: " . $e->getMessage();
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <h1>Добавить контент</h1>
    
    <?php if($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="alert success"><?= $success ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="upload-form">
        <div class="form-group">
            <label for="file_type">Тип контента *</label>
            <select name="file_type" id="file_type" required onchange="updateCategories()">
                <option value="">Выберите тип</option>
                <option value="mods" <?= $_POST['file_type']=='mods'?'selected':'' ?>>Мод</option>
                <option value="maps" <?= $_POST['file_type']=='maps'?'selected':'' ?>>Карта</option>
                <option value="resourcepacks" <?= $_POST['file_type']=='resourcepacks'?'selected':'' ?>>Ресурспак</option>
                <option value="shaders" <?= $_POST['file_type']=='shaders'?'selected':'' ?>>Шейдеры</option>
                <option value="skins" <?= $_POST['file_type']=='skins'?'selected':'' ?>>Скин</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="title">Название *</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required maxlength="255" placeholder="Введите название файла">
        </div>
        
        <div class="form-group">
            <label for="description">Описание *</label>
            <textarea id="description" name="description" rows="6" required placeholder="Опишите ваш файл..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="category_id">Категория *</label>
                <select name="category_id" id="category_id" required>
                    <option value="">Сначала выберите тип контента</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="minecraft_version">Версия Minecraft *</label>
                <select name="minecraft_version" id="minecraft_version" required>
                    <option value="">Выберите версию</option>
                    <option value="1.20" <?= ($_POST['minecraft_version']??'')=='1.20'?'selected':'' ?>>1.20</option>
                    <option value="1.19" <?= ($_POST['minecraft_version']??'')=='1.19'?'selected':'' ?>>1.19</option>
                    <option value="1.18" <?= ($_POST['minecraft_version']??'')=='1.18'?'selected':'' ?>>1.18</option>
                    <option value="1.17" <?= ($_POST['minecraft_version']??'')=='1.17'?'selected':'' ?>>1.17</option>
                    <option value="1.16" <?= ($_POST['minecraft_version']??'')=='1.16'?'selected':'' ?>>1.16</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="file">Файл * (ZIP, RAR, 7Z)</label>
            <input type="file" id="file" name="file" accept=".zip,.rar,.7z" required>
            <small>Максимальный размер: 100MB</small>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" required>
                Я согласен с <a href="rules.php" target="_blank">правилами сайта</a>
            </label>
        </div>
        
        <button type="submit" class="btn-submit">Отправить на модерацию</button>
    </form>
    
    <div class="upload-info">
        <h3>Правила загрузки:</h3>
        <ul>
            <li>Файл должен быть в формате ZIP, RAR или 7Z</li>
            <li>Максимальный размер файла: 100MB</li>
            <li>Не загружайте контент, нарушающий авторские права</li>
            <li>Все файлы проходят проверку модератором</li>
            <li>Модерация занимает до 24 часов</li>
        </ul>
    </div>
</div>

<style>
.upload-form {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #2c3e50;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #3498db;
    outline: none;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
    font-family: inherit;
}

.form-group small {
    display: block;
    margin-top: 0.3rem;
    color: #6c757d;
    font-size: 0.8rem;
}

.form-group input[type="checkbox"] {
    width: auto;
    margin-right: 0.5rem;
}

.btn-submit {
    background: #3498db;
    color: white;
    padding: 1rem 3rem;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s;
}

.btn-submit:hover {
    background: #2980b9;
}

.upload-info {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.upload-info h3 {
    margin-bottom: 1rem;
    color: #2c3e50;
}

.upload-info ul {
    list-style-type: none;
    padding: 0;
}

.upload-info li {
    padding: 0.3rem 0;
    position: relative;
    padding-left: 1.5rem;
}

.upload-info li:before {
    content: "•";
    color: #3498db;
    position: absolute;
    left: 0;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-weight: bold;
}

.alert.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function updateCategories() {
    const fileType = document.getElementById('file_type').value;
    const categorySelect = document.getElementById('category_id');
    
    if(!fileType) {
        categorySelect.innerHTML = '<option value="">Сначала выберите тип контента</option>';
        return;
    }
    
    // В реальном проекте здесь был бы AJAX запрос
    const categories = {
        'mods': [
            {id: 1, name: 'Оптимизация'},
            {id: 2, name: 'Интерфейс'},
            {id: 3, name: 'Боевые'}
        ],
        'maps': [
            {id: 4, name: 'Приключения'},
            {id: 5, name: 'Выживание'},
            {id: 6, name: 'Паркур'}
        ],
        'resourcepacks': [
            {id: 7, name: 'Реалистичные'},
            {id: 8, name: 'Стилизованные'},
            {id: 9, name: 'Фэнтези'}
        ],
        'shaders': [
            {id: 10, name: 'Реализм'},
            {id: 11, name: 'Фэнтези'},
            {id: 12, name: 'Аниме'}
        ],
        'skins': [
            {id: 13, name: 'Персонажи'},
            {id: 14, name: 'Животные'},
            {id: 15, name: 'Фэнтези'}
        ]
    };
    
    categorySelect.innerHTML = '<option value="">Выберите категорию</option>';
    
    if(categories[fileType]) {
        categories[fileType].forEach(cat => {
            categorySelect.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
        });
    }
}

// Инициализация при загрузке
document.addEventListener('DOMContentLoaded', function() {
    updateCategories();
});
</script>

<?php include 'includes/footer.php'; ?>