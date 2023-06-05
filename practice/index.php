<?php

include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        $stmt = $db->prepare("SELECT id, guest_id, checkin, checkout FROM registration");
        $stmt->execute();
        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    $new = array();
    include('assets/frontRegistration.php');
} else {
    $errors = array();
    $messages = array();
    if (!empty($_POST['addnewdate'])) {
        if (empty($_POST['guest_id'])) {
            $errors['guest_id'] = 'Заполните поле "Проживающий"';
        } 

        if (empty($_POST['checkin'])) {
            $errors['checkin'] = 'Заполните поле "Дата заезда"';
        }

        if (empty($_POST['checkout'])) {
            $errors['checkout'] = 'Заполните поле "Дата выезда"';
        }
        
        if (empty($errors)) {
            $guest_id = $_POST['guest_id'];
            $checkin = $_POST['checkin'];
            $checkout = $_POST['checkout'];
            $stmt = $db->prepare("INSERT INTO registration (guest_id, checkin, checkout) VALUES (?, ?, ?)");
            $stmt->execute([$guest_id, $checkin, $checkout]);
            $messages['added'] = 'Запись успешно добавлена';
        }
    } 
    foreach ($_POST as $key => $value) {
        if (preg_match('/^clear(\d+)_x$/', $key, $matches)) {
            $id = $matches[1]; 
            $stmt = $db->prepare("DELETE FROM registration WHERE id = ?");
            $stmt->execute([$id]);
            $messages['deleted'] = 'Запись с <b>id = '.$id.'</b> успешно удалена';
        }
        if (preg_match('/^edit(\d+)_x$/', $key, $matches)) {
            $id = $matches[1];
            setcookie('edit', $id, time() + 24 * 60 * 60);
        }
        if (preg_match('/^save(\d+)_x$/', $key, $matches)) {
            setcookie('edit', '', time() + 24 * 60 * 60);
            $id = $matches[1];
            $stmt = $db->prepare("SELECT guest_id, checkin, checkout FROM registration WHERE id = ?");
            $stmt->execute([$id]);
            $old_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dates['guest_id'] = $_POST['guest_id' . $id];
            $dates['checkin'] = $_POST['checkin' . $id];
            $dates['checkout'] = $_POST['checkout' . $id];

            if (array_diff_assoc($dates, $old_dates[0])) {
                $stmt = $db->prepare("UPDATE registration SET guest_id = ?, checkin = ?, checkout = ? WHERE id = ?");
                $stmt->execute([$dates['guest_id'], $dates['checkin'], $dates['checkout'], $id]);
                $messages['edited'] = 'Запись с <b>id = '.$id.'</b> успешно обновлена';
            }
        }
    }
    if (!empty($messages)) {
        setcookie('messages', serialize($messages), time() + 24 * 60 * 60);
    }
    if (!empty($errors)) {
        setcookie('errors', serialize($errors), time() + 24 * 60 * 60);
    }
    header('Location: index.php');
}