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
    $stmt = pdo()->prepare("SELECT * FROM objects ORDER BY lft");
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

//Узнаем максимальное значение rgt
function getMaxRgt()
{
    $sql = "SELECT MAX(rgt) FROM objects";
    $stmt = pdo()->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
}

//Создание объекта
function addObject($request)
{
    $parentObject = ($request['parent_id'] > 0) ? getObject($request['parent_id']) : false;

    $rgt = getMaxRgt();

    if (!$parentObject) {
        $request['level'] = 0;
        $request['rgt'] = 1;
        if ($rgt >= 2) $request['rgt'] = $rgt + 1;
    } else {
        $request['level'] = $parentObject['level'];
        $request['rgt'] = $parentObject['rgt'];

        //Обновляем дерево объектов
        $sql = "UPDATE objects SET rgt = rgt + 2, lft = IF(lft > :rgt, lft + 2, lft) WHERE rgt >= :rgt";
        $stmt = pdo()->prepare($sql);
        $stmt->bindValue(':rgt', $request['rgt'], PDO::PARAM_INT);
        $stmt->execute();
    }

    $sql = "INSERT INTO objects (title, description, parent_id, level, lft, rgt) VALUES (:title, :description, :parent_id, :level + 1, :rgt, :rgt + 1)";
    $stmt = pdo()->prepare($sql);
    $stmt->execute($request);
}

//Удаление объекта
function deleteObject($objectId)
{
    $object = getObject($objectId);
    $stmt = pdo()->prepare("DELETE FROM objects WHERE lft >= ? AND rgt <= ?");
    $stmt->execute([$object['lft'], $object['rgt']]);

    //Обновляем дерево объектов
    $sql = "UPDATE objects SET rgt = rgt - (:rgt - :lft + 1), lft = IF(lft > :lft, lft - (:rgt - :lft + 1), lft) WHERE rgt > :rgt";
    $stmt = pdo()->prepare($sql);
    $stmt->bindValue(':lft', $object['lft'], PDO::PARAM_INT);
    $stmt->bindValue(':rgt', $object['rgt'], PDO::PARAM_INT);
    $stmt->execute();

    return true;
}

//Редактирование объекта
function editObject($request)
{
    $object = getObject($request['id']);

    $stmt = pdo()->prepare("SELECT level FROM objects WHERE id = ?");
    $stmt->execute([$request['parent_id']]);
    $newParentLevel = $stmt->fetchColumn() ?: 0;

    //Если был изменен родитель
    if ($object['parent_id'] !== $request['parent_id']) {
        //Выбираем правый ключ в зависимости от того, куда перемещаем объект
        $rgtNear = 0;
        if ($newParentLevel === 0) {
            $rgtNear = getMaxRgt();
        } else {
            $stmt = pdo()->prepare("SELECT (rgt - 1) FROM objects WHERE id = ?");
            $stmt->execute([$request['parent_id']]);
            $rgtNear = $stmt->fetchColumn();
        }

        //Определяем смещение
        $skewLevel = $newParentLevel - $object['level'] + 1;
        $skewTree = $object['rgt'] - $object['lft'] + 1;

        $data = [
            'lft' => $object['lft'],
            'skewTree' => $skewTree,
            'skewLevel' => $skewLevel,
            'rgtNear' => $rgtNear,
            'rgt' => $object['rgt']
        ];

        //У нас 2 разных случая расчета lft и rgt, в зависимости от перемещения вверх или вниз по дереву
        if ($object['rgt'] > $rgtNear) {
            echo 'up';
            $data['skewEdit'] = $rgtNear - $object['lft'] + 1;

            $sql = "UPDATE objects SET rgt = IF(lft >= :lft, rgt + :skewEdit, IF(rgt < :lft, rgt + :skewTree, rgt)), 
                   level = IF(lft >= :lft, level + :skewLevel, level), 
                   lft = IF(lft >= :lft, lft + :skewEdit, IF(lft > :rgtNear, lft + :skewTree, lft)) WHERE rgt > :rgtNear AND lft < :rgt";
            $stmt = pdo()->prepare($sql);
            $stmt->execute($data);
        } else {
            echo 'down';
            $data['skewEdit'] = $rgtNear - $object['lft'] + 1 - $skewTree;

            $sql = "UPDATE objects SET lft = IF(rgt <= :rgt, lft + :skewEdit, IF(lft > :rgt, lft - :skewTree, lft)), 
                   level = IF(rgt <= :rgt, level + :skewLevel, level), 
                   rgt = IF(rgt <= :rgt, rgt + :skewEdit, IF(rgt <= :rgtNear, rgt - :skewTree, rgt)) WHERE rgt > :lft AND lft <= :rgtNear";
            $stmt = pdo()->prepare($sql);
            $stmt->execute($data);
        }
    }

    updateObject($request);
}

/**
 * Список допустимых объектов для перемещения узла
 * @param array $data lft, rgt
 * @return array
 */
function getObjectsForTreeChanges($data)
{
    $stmt = pdo()->prepare("SELECT id, title FROM `objects` WHERE id NOT IN (SELECT id FROM objects WHERE lft >= ? AND rgt <= ?) ORDER BY lft");
    $stmt->execute($data);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//Обновляем данные объекта без изменений в дереве
function updateObject($data)
{
    $sql = "UPDATE objects SET title = :title, description = :description, parent_id = :parent_id WHERE id = :id";
    $stmt = pdo()->prepare($sql);
    $stmt->execute($data);
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