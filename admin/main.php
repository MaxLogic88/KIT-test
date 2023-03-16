<?php
require_once(realpath('../boot.php'));

$objects = getTree(getObjects());
$objects = array_values($objects);

//Если пользователь не авторизован, то перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    die();
}

$tmplOptions = '';
$tmplObjects = '';
if (is_array($objects)) {
    foreach ($objects as $key => $object) {
        $prefix = '';
        for ($i = 0; $i < $object['deep']; $i++) {
            $prefix .=  '-';
        }

        $tmplOptions .= loadTemplate(realpath('../templates/admin/partials/option.html'), [
            '{ID}' => $object['id'],
            '{TITLE}' => $prefix . $object['title'],
        ]);

        $tmplObjects .= loadTemplate(realpath('../templates/admin/partials/object.html'), [
            '{ID}' => $object['id'],
            '{TITLE}' => $object['title'],
            '{ML}' => $object['margin-left'],
        ]);
    }
}

$tmpl = loadTemplate(realpath('../templates/admin/main.html'), [
    '{OPTIONS}' => $tmplOptions,
    '{OBJECTS}' => $tmplObjects
]);

echo $tmpl;
