<?php
    try {
        $user='u52864';
        $password='3567354';
        $db=new PDO('mysql:host=localhost;dbname=u52864', $user, $password, 
        [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        if (empty($_POST['user_name']) || is_numeric($_POST['user_name']) 
        || !preg_match('/^([А-ЯЁ]{1}[а-яё])|([A-Z]{1}[a-z])+$/u', $_POST['user_name'])) 
        exit("Проверьте поле имя!");

        if (empty($_POST['user_email']) || is_numeric($_POST['user_email']) 
        || !preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-])*@[a-z0-9-]+(\.[a-z0-9-])*(\.[a-z]{2,4})$/', $_POST['user_email']))
         exit("Проверьте поле почта!");

        if ($_POST['user_date'] == "1900-01-01") exit("Проверьте поле дата!");

        if ($_POST['user_gender'] != "Male" && $_POST['user_gender'] != "Female")
         exit ("Выберите пол!");

        if ($_POST['user_limb'] != 3 && $_POST['user_limb'] != 4 && $_POST['user_limb'] != 5)
         exit ("Выберите кол-во конечностей!");

        $abilities = (int) $_POST['user_abilities'];
        if ($abilities < 1 || $abilities > 3)
        {
            $abilitiesErr = "Выберите способности!";
        }
        if ($abilities == null) exit("Выберите способности!");

        if (empty($_POST['user_biography']) || is_numeric($_POST['user_biography'])
         || !preg_match('/^[a-zA-Zа-яёА-ЯЁ0-9]/', $_POST['user_biography']))
          exit("Проверьте поле биография!");
          
        if ($_POST['user_checkbox'] == null) exit ("Нажмите согласие с контрактом!");

        $stmt=$db->prepare("INSERT INTO User (NAME,EMAIL,DATE,GENDER,LIMB,BIOGRAPHY,CONTRACT)
         VALUES (:NAME,:EMAIL,:DATE,:GENDER,:LIMB,:BIOGRAPHY,:CONTRACT)");
        $stmt->execute(['NAME'=>$_POST['user_name'],'EMAIL'=>$_POST['user_email'],'DATE'=>$_POST['user_date'],
        'GENDER'=>$_POST['user_gender'],'LIMB'=>$_POST['user_limb'],'BIOGRAPHY'=>$_POST['user_biography'],
        'CONTRACT'=>$_POST['user_checkbox']]);
        $id=$db->lastInsertId();
        $stmt=$db->prepare("INSERT INTO Link (ID_USER,ID_ABILITY) VALUES (:ID_USER,:ID_ABILITY)");
        foreach ($_POST['user_abilities'] as $ability) {
            if ($ability != false) {
                $stmt->execute(['ID_USER'=>$id,'ID_ABILITY'=>$ability]);
            }
        }
    }

    catch (PDOException $e) {
        print('Error: ' .$e -> getMessage());
        exit();
    }
?>