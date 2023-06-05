<?php

include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $db->prepare("SELECT id, name, room_number FROM guests");
        $stmt->execute();
        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    $new = array();
    include('assets/frontGuests.php');
} else {
    $errors = array();
    $messages = array();
    if (!empty($_POST['addnewdate'])) {
        if (empty($_POST['name'])) {
            $errors['name'] = 'Заполните поле "Имя проживающего"';
        } 

        if (empty($_POST['room_number'])) {
            $errors['room_number'] = 'Заполните поле "Номер комнаты"';
        }
        
        if (empty($errors)) {
            $name = $_POST['name'];
            $room_number = $_POST['room_number'];
            $stmt = $db->prepare("INSERT INTO guests (name, room_number) VALUES (?, ?)");
            $stmt->execute([$name, $room_number]);
            $messages['added'] = 'Посетитель "'.$name.'" успешно добавлен';
        }
    } 
    foreach ($_POST as $key => $value) {
        if (preg_match('/^clear(\d+)_x$/', $key, $matches)) {
            $id = $matches[1]; 
            $stmt = $db->prepare("SELECT id FROM registration WHERE guest_id = ?");
            $stmt->execute([$id]);
            $empty = $stmt->rowCount() === 0;
            if (!$empty) {
                $errors['delete'] = 'Поле с <b>id = '.$id.'</b> невозможно удалить, т.к. оно связанно с журналом регистрации проживающих';
            } else {
                $stmt = $db->prepare("DELETE FROM guests WHERE id = ?");
                $stmt->execute([$id]);
                $messages['deleted'] = 'Проживающий с <b>id = '.$id.'</b> успешно удалён';
            }
        }
        if (preg_match('/^edit(\d+)_x$/', $key, $matches)) {
            $id = $matches[1];
            setcookie('edit', $id, time() + 24 * 60 * 60);
        }
        if (preg_match('/^save(\d+)_x$/', $key, $matches)) {
            setcookie('edit', '', time() + 24 * 60 * 60);
            $id = $matches[1];
            $stmt = $db->prepare("SELECT name, room_number FROM guests WHERE id = ?");
            $stmt->execute([$id]);
            $old_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dates['name'] = $_POST['name' . $id];
            $dates['room_number'] = $_POST['room_number' . $id];

            if (array_diff_assoc($dates, $old_dates[0])) {
                $stmt = $db->prepare("UPDATE guests SET name = ?, room_number = ? WHERE id = ?");
                $stmt->execute([$dates['name'], $dates['room_number'], $id]);
                $messages['edited'] = 'Проживающий с <b>id = '.$id.'</b> успешно обновлён';
            }
        }
    }
    if (!empty($messages)) {
        setcookie('messages', serialize($messages), time() + 24 * 60 * 60);
    }
    if (!empty($errors)) {
        setcookie('errors', serialize($errors), time() + 24 * 60 * 60);
    }
    header('Location: Guests.php');
}