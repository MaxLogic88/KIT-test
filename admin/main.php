<?php
require_once(realpath('../boot.php'));

$objects = getObjectsTree(getObjects());

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
                        <option value="<?= $object['id'] ?>"><?= $object['title'] ?></option>
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
        <?php if (is_array($objects)): ?>
            <ul>
                <?php
                foreach ($objects as $object) {
                    $htmlClass = 'nav-object';
                    if ($object['deeper']) $htmlClass .= ' deeper';
                    if ($object['parent']) $htmlClass .= ' parent';

                    echo '<li class="' . $htmlClass . '">';
                    ?>
                    <div class="object" id="object-<?= $object['id'] ?>">
                        <span class="title"><?= $object['title'] ?></span>
                        ---
                        <span class="edit">[редактировать]</span>
                        <span class="delete">[удалить]</span>
                    </div>
                    <?php
                    if ($object['deeper']) {
                        echo '<ul>';
                    } elseif ($object['shallower']) {
                        echo '</li>';
                        echo str_repeat('</ul></li>', $object['level_diff']);
                    } else {
                        echo '</li>';
                    }
                }
                ?>
            </ul>
        <?php endif; ?>
    </div>
</main>
<script src="assets/js/functions.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
