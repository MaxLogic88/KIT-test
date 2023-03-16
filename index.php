<?php
require_once(realpath('boot.php'));

$objects = getTree(getObjects());
$objects = array_values($objects);

$tmplObjects = '';
if (is_array($objects)) {
    foreach ($objects as $key => $object) {
        $between = ['{SHOWCHILDS}', '{/SHOWCHILDS}'];
        $removeBetween = true;
        if ($objects[$key + 1]['deep'] > $object['deep']) {
            $removeBetween = false;
        }
        $tmplObjects .= loadTemplate("templates/partials/object.html", [
            "{ID}" => $object['id'],
            "{PARENT_ID}" => $object['parent_id'],
            "{ML}" => $object['margin-left'],
            "{TITLE}" => $object['title'],
        ], $between, $removeBetween);
    }
}

$tmpl = loadTemplate("templates/main.html", [
    "{OBJECTS}" => $tmplObjects
]);

echo $tmpl;
