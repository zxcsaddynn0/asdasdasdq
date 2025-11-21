<?php 
require_once 'includes/config.php';

// Если пользователь уже авторизован
if(isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}

$error = '';
$success = '';

if($_POST) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    // Валидация
    if(empty($username) || empty($email) || empty($password)) {
        $error = "Все поля обязательны для заполнения";
    } elseif($password !== $password_confirm) {
        $error = "Пароли не совпадают";
    } elseif(strlen($password) < 6) {
        $error = "Пароль должен быть не менее 6 символов";
    } elseif(strlen($username) < 3) {
        $error = "Имя пользователя должно быть не менее 3 символов";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Некорректный email адрес";
    } else {
        // Проверяем уникальность
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $existing = $stmt->fetch();
        
        if($existing) {
            $error = "Пользователь с таким именем или email уже существует";
        } else {
            // Создаем пользователя
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $password_hash]);
                $success = "Регистрация успешна! Теперь вы можете войти.";
                
                // Очищаем форму
                $_POST = [];
            } catch(PDOException $e) {
                $error = "Ошибка регистрации: " . $e->getMessage();
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="auth-container">
        <div class="auth-form">
            <h1>Регистрация</h1>
            
            <?php if($error): ?>
                <div class="alert error"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert success"><?= $success ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Имя пользователя *</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required minlength="3" maxlength="50">
                    <small>От 3 до 50 символов</small>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль *</label>
                    <input type="password" id="password" name="password" required minlength="6">
                    <small>Не менее 6 символов</small>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">Подтверждение пароля *</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" required>
                        Я согласен с <a href="rules.php" target="_blank">правилами сайта</a>
                    </label>
                </div>
                
                <button type="submit" class="btn-auth">Зарегистрироваться</button>
            </form>
            
            <div class="auth-links">
                <p>Уже есть аккаунт? <a href="login.php">Войдите</a></p>
            </div>
        </div>
        
        <div class="auth-info">
            <h2>Присоединяйтесь к сообществу!</h2>
            <p>Станьте частью крупнейшего сообщества Minecraft в рунете</p>
            
            <div class="features">
                <div class="feature">
                    <h3>🚀 Быстрый старт</h3>
                    <p>Начните загружать контент сразу после регистрации</p>
                </div>
                
                <div class="feature">
                    <h3>👥 Сообщество</h3>
                    <p>Общайтесь с другими энтузиастами Minecraft</p>
                </div>
                
                <div class="feature">
                    <h3>📈 Статистика</h3>
                    <p>Отслеживайте популярность ваших файлов</p>
                </div>
                
                <div class="feature">
                    <h3>🏆 Репутация</h3>
                    <p>Зарабатывайте репутацию за качественный контент</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.features {
    margin-top: 2rem;
}

.feature {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: rgba(255,255,255,0.1);
    border-radius: 8px;
}

.feature h3 {
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.feature p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

.form-group input[type="checkbox"] {
    width: auto;
    margin-right: 0.5rem;
}

.form-group small {
    display: block;
    margin-top: 0.3rem;
    color: #6c757d;
    font-size: 0.8rem;
}
</style>

<script>
document.getElementById('password_confirm').addEventListener('input', function() {
    const password = document.getElementById('password');
    const confirm = this;
    
    if(password.value !== confirm.value) {
        confirm.style.borderColor = '#e74c3c';
    } else {
        confirm.style.borderColor = '#27ae60';
    }
});
</script>

<?php include 'includes/footer.php'; ?>