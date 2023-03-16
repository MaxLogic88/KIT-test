<?php
require_once (realpath('../boot.php'));

//Если пользователь уже авторизован, то перенаправляем на главную страницу админки
if(isset($_SESSION['user_id'])) {
    header('Location: main.php');
    die();
}

$tmpl = loadTemplate(realpath('../templates/admin/login.html'));

echo $tmpl;