<?php 
require_once 'includes/config.php';

// Если пользователь уже авторизован
if(isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}

$error = '';

if($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if(empty($username) || empty($password)) {
        $error = "Все поля обязательны для заполнения";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Перенаправляем на предыдущую страницу или профиль
            header("Location: " . ($_GET['redirect'] ?? 'profile.php'));
            exit;
        } else {
            $error = "Неверное имя пользователя или пароль";
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="auth-container">
        <div class="auth-form">
            <h1>Вход в аккаунт</h1>
            
            <?php if($error): ?>
                <div class="alert error"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if(isset($_GET['registered'])): ?>
                <div class="alert success">Регистрация успешна! Теперь вы можете войти.</div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Имя пользователя</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn-auth">Войти</button>
            </form>
            
            <div class="auth-links">
                <p>Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>
                <p><a href="recover.php">Забыли пароль?</a></p>
            </div>
        </div>
        
        <div class="auth-info">
            <h2>Преимущества аккаунта</h2>
            <ul>
                <li>📥 Скачивание файлов</li>
                <li>📤 Загрузка собственного контента</li>
                <li>⭐ Оценка и комментирование</li>
                <li>📊 Отслеживание статистики</li>
                <li>💾 Сохранение в избранное</li>
            </ul>
        </div>
    </div>
</div>

<style>
.auth-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    max-width: 1000px;
    margin: 0 auto;
    align-items: start;
}

.auth-form {
    background: white;
    padding: 2.5rem;
    border-radius: 15px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.1);
}

.auth-form h1 {
    text-align: center;
    margin-bottom: 2rem;
    color: #2c3e50;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #2c3e50;
}

.form-group input {
    width: 100%;
    padding: 1rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-group input:focus {
    border-color: #3498db;
    outline: none;
}

.btn-auth {
    width: 100%;
    background: #3498db;
    color: white;
    padding: 1rem;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    cursor: pointer;
    margin-bottom: 1.5rem;
    transition: background-color 0.3s;
}

.btn-auth:hover {
    background: #2980b9;
}

.auth-links {
    text-align: center;
}

.auth-links a {
    color: #3498db;
    text-decoration: none;
}

.auth-links a:hover {
    text-decoration: underline;
}

.auth-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2.5rem;
    border-radius: 15px;
}

.auth-info h2 {
    margin-bottom: 1.5rem;
    text-align: center;
}

.auth-info ul {
    list-style: none;
    padding: 0;
}

.auth-info li {
    padding: 0.8rem 0;
    font-size: 1.1rem;
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
    .auth-container {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>