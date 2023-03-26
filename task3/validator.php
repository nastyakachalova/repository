<?php
    try {
        //подключаемся к базе данных
        $user='u52849';
        $password='2277004';
        $db=new PDO('mysql:host=localhost;dbname=u52849', $user, $password, 
        [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        //валидация полей
        //имя
        function validate_name($name) {
            $Err = "";
            if (strlen($name)<2 || strlen($name)>50)
            $Err = "Длина имени должна быть от 2 до 50 символов!";
            if (preg_match('/[^(\w)|(\x7F-\xFF)|(\s)]/', $name))
            $Err = "В написании имени допустимы только буквы латинского и русского алфавита, 
            цифры, символ подчеркивания и пробел!";
            if (!empty($Err))
            return ($Err);
        }
        $Name = validate_name($_POST['user_name']);
        if (empty($_POST['user_name'])) {
            exit ("Введите имя");
        }
        //почта
        function validate_email($data) {
            $err ="";
            if(strlen($data)<3 || strlen($data)>50)
                $err = "Email должен быть от 3 до 50 символов";
             if(!preg_match('/^([\w]+\.?)+(?<!\.)@(?!\.)[a-zа-я0-9ё\.-]+\.?[a-zа-яё]{2,}$ui/', $data))
               $err = $err . "Недопустимый формат email-адреса!"; 
            if(!empty($err)) 
                return($err);
        }
        $Email = validate_email($_POST['user_email']);
        if (empty($_POST['user_email'])) {
            exit ("Введите почту");
        }
        //дата
        function validateDate($date, $format = 'Y-m-d') {
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d -> format($format) == $date;
        }
        if (var_dump(validateDate($_POST['user_date'])) == false) {
            $dateErr = "Введите дату";
        }
        //пол
        if ($_POST['user_gender'] == 'Male'||'Female') {
            $gender = ($_POST['user_gender']);
        }
        else
        if ($_POST['user_gender'] == null) {
            $genderErr = "Выберите пол";
        }
        //конечности
        if ($_POST['user_limb'] == '3'||'4'||'5') {
            $limb = ($_POST['user_limb']);
        }
        else
        if ($_POST['user_limb'] == null) {
            $limbErr = "Выберите количество конечностей";
        }
        //суперспособности
        $ability = (int) $_POST['user_abilities'];
        if ($ability < 1 || $ability > 3) {
            $abilityErr = "Вы не выбрали суперспособность!";
        }
        if ($ability == null) {
            exit("Вы не выбрали суперспособность!");
        }
        //биография
        if (empty($_POST['user_biography'])) {
            exit ("Напишите биографию");
        }
        //контракт
        if ($_POST['user_checkbox'] == null) {
            exit ("Нажмите кнопку согласия с контрактом");
        }

        //отправка данных в базу
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

    //проверяем наличие ошибок
    catch (PDOException $e) {
        print('Error: ' .$e -> getMessage());
        exit();
    }
?>