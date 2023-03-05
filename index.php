<?php
require_once(realpath('boot.php'));

$objects = getObjectsTree(getObjects());
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
    <div class="description">
        <h2>Описание объекта</h2>
        <div class="text"></div>
    </div>
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
                        <?php if ($object['deeper']) echo '<span class="showChilds">[+]</span>'; ?>
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
<script src="admin/assets/js/functions.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>