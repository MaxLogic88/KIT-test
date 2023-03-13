<?php
require_once (realpath('../boot.php'));

//Общий обработчик
if (isset($_POST['task'])) {
    switch ($_POST['task']) {
        // Валидируем имя пользователя
        case 'login':
            $stmt = pdo()->prepare("SELECT * FROM `users` WHERE `username` = :username");
            $stmt->execute(['username' => $_POST['username']]);
            if (!$stmt->rowCount()) {
                header('Location: index.php');
            }
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // проверяем пароль
            if (password_verify($_POST['password'], $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: main.php');
            }

            header('Location: index.php');
            die();

        case 'getObject':
            //В нашей реализации актуально только для Ajax
            if ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                $response['DataObject'] = getObject($_POST['id']);
                $response['options'] = getOptionsForEditObject($_POST['id']);
                echo json_encode($response);
                die();
            }
            break;
    }

    //Если пользователь не авторизован, то перенаправляем на страницу авторизации
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        die();
    }

    //Общий обработчик действий для авторизованного пользователя
    switch ($_POST['task']) {
        case 'logout':
            unset($_SESSION['user_id']);
            header('Location: index.php');
            break;

        case 'saveObject':
            unset($_POST['task']); //Убираем task перед записью данных в БД
            addObject($_POST);
            header('Location: main.php');
            break;

        case 'editObject':
            unset($_POST['task']); //Убираем task перед записью данных в БД
            editObject($_POST);
            header('Location: main.php');
            break;

        case 'deleteObject':
            $response = deleteObject($_POST['id']);

            //Для Ajax удаления
            if ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode($response);
                die();
            }
            header('Location: main.php');
            break;
    }
    die();
}

header('Location: /');
die();
