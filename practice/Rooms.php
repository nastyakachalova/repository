<?php

include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $db->prepare("SELECT id, room_number, capacity, price FROM rooms");
        $stmt->execute();
        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    include('assets/frontRooms.php');
} else {
    $errors = array();
    $messages = array();
    if (!empty($_POST['addnewdate'])) {
        if (empty($_POST['room_number'])) {
            $errors['room_number'] = 'Заполните поле "Номер комнаты"';
        } else if (!is_numeric($_POST['room_number'])) {
            $errors['room_number1'] = 'Поле "Номер комнаты" должно быть числом';
        }

        if (empty($_POST['capacity'])) {
            $errors['capacity'] = 'Заполните поле "Вместимость"';
        } else if (!is_numeric($_POST['room_number'])) {
            $errors['capacity1'] = 'Поле "Вместимость" должно быть числом';
        }

        if (empty($_POST['price'])) {
            $errors['price'] = 'Заполните поле "Цена"';
        } else if (!is_numeric($_POST['room_number'])) {
            $errors['price1'] = 'Поле "Цена" должно быть числом';
        }
        
        if (empty($errors)) {
            $room_number = intval($_POST['room_number']);
            $capacity = intval($_POST['capacity']);
            $price = intval($_POST['price']);
            $stmt = $db->prepare("INSERT INTO rooms (room_number, capacity, price) VALUES (?, ?, ?)");
            $stmt->execute([$room_number, $capacity, $price]);
            $messages['added'] = 'Комната "'.$room_number.'" успешно добавлена';
        }
    } 
    foreach ($_POST as $key => $value) {
        if (preg_match('/^clear(\d+)_x$/', $key, $matches)) {
            $id = $matches[1]; 
            $stmt = $db->prepare("SELECT id FROM guests WHERE room_number = ?");
            $stmt->execute([$id]);
            $empty = $stmt->rowCount() === 0;
            if (!$empty) {
                $errors['delete'] = 'Поле с <b>id = '.$id.'</b> невозможно удалить, т.к. оно связанно с журналом проживающих';
            } else {
                $stmt = $db->prepare("DELETE FROM rooms WHERE id = ?");
                $stmt->execute([$id]);
                $messages['deleted'] = 'Номер с <b>id = '.$id.'</b> успешно удалён';
            }
        }
        if (preg_match('/^edit(\d+)_x$/', $key, $matches)) {
            $id = $matches[1];
            setcookie('edit', $id, time() + 24 * 60 * 60);
        }
        if (preg_match('/^save(\d+)_x$/', $key, $matches)) {
            setcookie('edit', '', time() + 24 * 60 * 60);
            $id = $matches[1];
            $stmt = $db->prepare("SELECT room_number, capacity, price FROM rooms WHERE id = ?");
            $stmt->execute([$id]);
            $old_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dates['room_number'] = $_POST['room_number' . $id];
            $dates['capacity'] = $_POST['capacity' . $id];
            $dates['price'] = $_POST['price' . $id];

            if (array_diff_assoc($dates, $old_dates[0])) {
                $stmt = $db->prepare("UPDATE rooms SET room_number = ?, capacity = ?, price = ? WHERE id = ?");
                $stmt->execute([$dates['room_number'], $dates['capacity'], $dates['price'], $id]);
                $messages['edited'] = 'Номер с <b>id = '.$id.'</b> успешно обновлён';
            }
        }
    }
    if (!empty($messages)) {
        setcookie('messages', serialize($messages), time() + 24 * 60 * 60);
    }
    if (!empty($errors)) {
        setcookie('errors', serialize($errors), time() + 24 * 60 * 60);
    }
    header('Location: Rooms.php');
}
