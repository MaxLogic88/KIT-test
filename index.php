<?php
require_once(realpath('boot.php'));

//sd(testTree(getObjects()));
$objects = getTree(getObjects());
$objects = array_values($objects);
//updateLastElem($objects);
//sd($objects);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список объектов</title>
    <link href="assets/css/main.css" rel="stylesheet" />
</head>
<body>
<main>
    <a href="admin">Перейти в административную панель</a>
    <div class="description">
        <h2>Описание объекта</h2>
        <div class="text"></div>
    </div>
    <div class="container">
        <h1>Список объектов</h1>
        <?php if (is_array($objects)): ?>
            <?php foreach ($objects as $key => $object): ?>
                <div class="object" id="object-<?= $object['id'] ?>" data-parent="<?= $object['parent_id'] ?>" style="margin-left: <?= $object['margin-left'] ?>">
                    <span class="title"><?= $object['title'] ?></span>
                    <?php if ($objects[$key+1]['deep'] > $object['deep']): ?>
                        <span class="showChilds">[+]</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
<script src="admin/assets/js/functions.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>