<?php

include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $db->prepare("SELECT id, name, position FROM employees");
        $stmt->execute();
        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    include('assets/frontEmployees.php');
} else {
    $errors = array();
    $messages = array();
    if (!empty($_POST['addnewdate'])) {
        if (empty($_POST['name'])) {
            $errors['name'] = 'Заполните поле "Имя сотрудника"';
        } 

        if (empty($_POST['position'])) {
            $errors['position'] = 'Заполните поле "Должность"';
        }
        
        if (empty($errors)) {
            $name = $_POST['name'];
            $position = $_POST['position'];
            $stmt = $db->prepare("INSERT INTO employees (name, position) VALUES (?, ?)");
            $stmt->execute([$name, $position]);
            $messages['added'] = 'Сотрудник "'.$name.'" успешно добавлен';
        }
    } 
    foreach ($_POST as $key => $value) {
        if (preg_match('/^clear(\d+)_x$/', $key, $matches)) {
            $id = $matches[1]; 
            $stmt = $db->prepare("DELETE FROM employees WHERE id = ?");
            $stmt->execute([$id]);
            $messages['deleted'] = 'Сотрудник с <b>id = '.$id.'</b> успешно удалён';
        }
        if (preg_match('/^edit(\d+)_x$/', $key, $matches)) {
            $id = $matches[1];
            setcookie('edit', $id, time() + 24 * 60 * 60);
        }
        if (preg_match('/^save(\d+)_x$/', $key, $matches)) {
            setcookie('edit', '', time() + 24 * 60 * 60);
            $id = $matches[1];
            $stmt = $db->prepare("SELECT name, position FROM employees WHERE id = ?");
            $stmt->execute([$id]);
            $old_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dates['name'] = $_POST['name' . $id];
            $dates['position'] = $_POST['position' . $id];

            if (array_diff_assoc($dates, $old_dates[0])) {
                $stmt = $db->prepare("UPDATE employees SET name = ?, position = ? WHERE id = ?");
                $stmt->execute([$dates['name'], $dates['position'], $id]);
                $messages['edited'] = 'Сотрудник с <b>id = '.$id.'</b> успешно обновлён';
            }
        }
    }
    if (!empty($messages)) {
        setcookie('messages', serialize($messages), time() + 24 * 60 * 60);
    }
    if (!empty($errors)) {
        setcookie('errors', serialize($errors), time() + 24 * 60 * 60);
    }
    header('Location: Employees.php');
}