<?php
require_once (realpath('../boot.php'));

//Если пользователь уже авторизован, то перенаправляем на главную страницу админки
if(isset($_SESSION['user_id'])) {
    header('Location: main.php');
    die();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
</head>
<body>
<a href="../">Перейти на главную страницу сайта</a>
<form action="form-handler.php" method="post">
    <div>
        <label for="username">Логин</label>
        <input type="text" name="username" required>
    </div>
    <div>
        <label for="password">Пароль</label>
        <input type="password" name="password" required>
    </div>
    <input type="hidden" name="task" value="login" />
    <button type="submit">Войти</button>
</form>
</body>
</html>