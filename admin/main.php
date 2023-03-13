<?php
require_once(realpath('../boot.php'));

$objects = getTree(getObjects());
$objects = array_values($objects);

//Если пользователь не авторизован, то перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    die();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель Администратора</title>
    <link href="assets/css/main.css" rel="stylesheet"/>
</head>
<body>
<a href="../">Перейти на главную страницу</a>
<aside class="left-container">
    <form action="form-handler.php" method="post">
        <input type="hidden" name="task" value="logout"/>
        <button type="submit">Выйти (закрыть сессию)</button>
    </form>
    <h2>Создание объекта</h2>
    <form action="form-handler.php" method="post">
        <fieldset>
            <label for="parent">Родительский объект</label>
            <select name="parent_id" id="parent">
                <option value="0" selected>Не выбран</option>
                <?php if (is_array($objects)): ?>
                    <?php foreach ($objects as $object): ?>
                        <option value="<?= $object['id'] ?>">
                            <?php for ($i = 0; $i < $object['deep']; $i++): ?>
                            -
                            <?php endfor; ?>
                            <?= $object['title'] ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </fieldset>
        <fieldset>
            <label for="title">Название объекта</label>
            <input type="text" name="title" id="title"/>
        </fieldset>
        <fieldset>
            <label for="desc">Описание объекта</label>
            <textarea name="description" id="desc"></textarea>
        </fieldset>
        <input type="hidden" name="task" value="saveObject"/>
        <button type="submit">Создать</button>
    </form>
    <div class="edit-object">
        <h2>Редактирование объекта</h2>
        <form action="form-handler.php" method="post">
            <fieldset>
                <label>Родительский объект</label>
                <select name="parent_id"></select>
            </fieldset>
            <fieldset>
                <label>Название объекта</label>
                <input type="text" name="title"/>
            </fieldset>
            <fieldset>
                <label>Описание объекта</label>
                <textarea name="description"></textarea>
            </fieldset>
            <input type="hidden" name="id">
            <input type="hidden" name="task" value="editObject"/>
            <button type="submit">Обновить</button>
        </form>
    </div>
</aside>
<main>
    <div class="container">
        <h1>Список объектов</h1>
        <div id="objects">
            <?php if (is_array($objects)): ?>
                <?php foreach ($objects as $key => $object): ?>
                    <div class="object" id="object-<?= $object['id'] ?>" style="margin-left: <?= $object['margin-left'] ?>">
                        <span class="title"><?= $object['title'] ?></span>
                        ---
                        <span class="edit">[редактировать]</span>
                        <span class="delete">[удалить]</span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>
<script src="assets/js/functions.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
