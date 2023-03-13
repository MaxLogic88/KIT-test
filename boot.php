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

    return true;
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
function getTree($objects, &$result = [], $parent_id = 0, $deep = 0)
{
    foreach ($objects as $key => $object) {
        if ($object['parent_id'] == $parent_id) {
            $result[$object['id']]['id'] = $object['id'];
            $result[$object['id']]['title'] = $object['title'];
            $result[$object['id']]['parent_id'] = $object['parent_id'];
//            $result[$object['id']]['key'] = $key;
            $result[$object['id']]['deep'] = $deep;
            $result[$object['id']]['margin-left'] = $deep * 20 . 'px';
//            $result[$object['id']]['prev'] = prev($result);
//            $tempResult = $result;
            getTree($objects, $result, $object['id'], $deep + 1);
//            if (($tempResult == $result) && (1)) {
//                $result[$object['id']]['last'] = true;
//            }
        }
    }
    return $result;
}

//Для того, чтобы определить когда надо закрывать вложенные <ul>
function updateLastElem(&$objects)
{
    foreach ($objects as $key => &$object) {
        if ($object[$key+1]['deep'] != $object['deep']) {
            $last = true;
            for ($i = $key + 1; $i < count($objects); $i++) {
                if ($objects[$i]['parent_id'] == $object['parent_id']) $last = false;
            }
            if ($last) {
                $object['last'] = true;
                $last = false;
            }
        }
        if ((isset($objects[$key-1]) && ($objects[$key-1]['deep'] == $object['deep']) && ($objects[$key+1]['deep'] != $object['deep']))) {
            $object['last'] = true;
        }
    }
}

function testTree($objects, $parent_id = 0)
{
    $result = [];
    foreach ($objects as $key => $object) {
        if ($object['parent_id'] == $parent_id) {
            $result[$object['id']]['id'] = $object['id'];
            $result[$object['id']]['title'] = $object['title'];
            $result[$object['id']]['parent_id'] = $object['parent_id'];
            $result[$object['id']]['key'] = $key;
            $result[$object['id']]['childs'] = testTree($objects, $object['id']);
        }
    }
    return $result;
}

//Строим дерево объектов
function getObjectsTree($objects)
{
    $lastObject = 0;
    $start = 1;
    $end = 0;
    foreach ($objects as $key => $object) {
        $object['parent'] = false;

        if (isset($objects[$lastObject]) && $objects[$lastObject]['id'] == $object['parent_id']) {
            $objects[$lastObject]['parent'] = true;
        }

        $object['deeper'] = false;
        $object['shallower'] = false;
        $object['level_diff'] = 0;

        if (isset($objects[$lastObject])) {
            $objects[$lastObject]['deeper'] = ($object['level'] > $objects[$lastObject]['level']);
            $objects[$lastObject]['shallower'] = ($object['level'] < $objects[$lastObject]['level']);
            $objects[$lastObject]['level_diff'] = ($objects[$lastObject]['level'] - $object['level']);
        }

        $lastObject = $key;
    }

    if (isset($objects[$lastObject])) {
        $objects[$lastObject]['deeper'] = (($start ?: 1) > $objects[$lastObject]['level']);
        $objects[$lastObject]['shallower'] = (($start ?: 1) < $objects[$lastObject]['level']);
        $objects[$lastObject]['level_diff'] = ($objects[$lastObject]['level'] - ($start ?: 1));
    }

    return $objects;
}

//For simple debug
function sd($data = '')
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    die();
}