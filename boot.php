<?php
session_start();

//Создаем глобальную точку для подключения к БД
function pdo()
{
    static $pdo;

    if (!$pdo) {
        $config = include __DIR__ . '/config.php';

        $dsn = 'mysql:dbname=' . $config['db_name'] . ';host=' . $config['db_host'];
        $pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return $pdo;
}

//Получаем всё дерево объектов
function getObjects()
{
    $stmt = pdo()->prepare("SELECT * FROM objects");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//Получаем один объект
function getObject($objectId)
{
    $stmt = pdo()->prepare("SELECT * FROM objects WHERE id = ?");
    $stmt->execute([$objectId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

//Создание объекта
function addObject($request)
{
    $sql = "INSERT INTO objects (title, description, parent_id) VALUES (:title, :description, :parent_id)";
    $stmt = pdo()->prepare($sql);
    $stmt->execute($request);
}

//Удаление объекта
function deleteObject($objectId)
{
    //Удаление всех дочерних элементов может происходить рекурсивно
    //...но тогда придется делать много запросов: поиск дочерних элементов каждого нового уровня вложенности.
    //Кроме того, если при работе с большим объемом данных на сервер произойдет сбой, то может случиться так,
    //что часть наших данных удалится, а часть останется, что приведет к потере целостности записей БД.
    //Поэтому мы пойдем другим путем. Выберем все записи, и используем рекурсивную функцию, чтобы
    //уже в ней собрать все необходимые идентификаторы для удаления, а потом удалим все объекты одним запросом.
    $objects[] = $objectId;
    removeChilds(getObjects(), $objectId, $objects);

    $stmt = pdo()->prepare("DELETE FROM objects WHERE id IN (" . implode(',', $objects) . ")");
    $stmt->execute();

    return $objects;
}

//Рекурсивный сбор ID для удаления
function removeChilds($objects, $removeId, &$result)
{
    foreach ($objects as $object) {
        if ($removeId == $object['parent_id']) {
            $result[] = $object['id'];
            removeChilds($objects, $object['id'], $result);
        }
    }
}

//Редактирование объекта
function editObject($request)
{
    $sql = "UPDATE objects SET title = :title, description = :description, parent_id = :parent_id WHERE id = :id";
    $stmt = pdo()->prepare($sql);
    $stmt->execute($request);
}

//Рекурсивное построение древовидной структуры
function getTree($objects, $getOptionsObjectId = false, &$result = [], $parent_id = 0, $deep = 0)
{
    foreach ($objects as $key => $object) {
        if ($object['id'] == $getOptionsObjectId) continue; //Не включаем дочерние элементы в варианты выбора родителя
        if ($object['parent_id'] == $parent_id) {
            $result[$object['id']]['id'] = $object['id'];
            $result[$object['id']]['title'] = $object['title'];
            $result[$object['id']]['parent_id'] = $object['parent_id'];
            $result[$object['id']]['deep'] = $deep;
            $result[$object['id']]['margin-left'] = $deep * 20 . 'px';
            getTree($objects, $getOptionsObjectId, $result, $object['id'], $deep + 1);
        }
    }
    return $result;
}

//Чтобы не копипастить, возьмем готовую функцию построения дерева и добавим в нее условия возврата всех элементов кроме вложенных
function getOptionsForEditObject($objectId)
{
    return array_values(getTree(getObjects(), $objectId));
}

//Функция для получения подстроки между заданными параметрами $between
function getStrBetween($string, $between)
{
    $string = ' ' . $string;
    $ini = mb_strpos($string, $between[0]);
    $ini += mb_strlen($between[0]);
    $len = mb_strpos($string, $between[1], $ini) - $ini;
    return mb_substr($string, $ini, $len);
}

//Загружаем template из html файла и подставляем в него данные (шаблонизатор)
function loadTemplate($template, $tags = [], $between = false, $removeBetween = false)
{
    $html = file_get_contents($template);
    if (is_array($between)) {
        $tags[$between[0]] = '';
        $tags[$between[1]] = '';

        if ($removeBetween) {
            $html = str_replace(getStrBetween($html, $between), '', $html);
        }
    }
    return strtr($html, $tags);
}

//For simple debug
function sd($data = '')
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    die();
}