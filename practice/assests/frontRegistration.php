<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link rel="stylesheet" href="styles/style.css">
    <link type="image/x-icon" href="images/logo.png" rel="shortcut icon">
    <link type="Image/x-icon" href="images/logo.png" rel="icon">
    <title>Hotel</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.toggle-button').click(function() {
            var parentDiv = $(this).next('.newdates');
            parentDiv.slideToggle('slow', function() {
                var buttonText = parentDiv.is(':visible') ? 'Закрыть' : 'Добавить запись';
                $(this).prev('.toggle-button').text(buttonText);
            });
        });
    });
    </script>
</head>
<body>
    <header>
        <div class="header-items">
            <a href="index.php" class="logo">
                <img src="images/logo.png" alt="logo" width="37" height="37">
                <h1>Гостиница</h1>
            </a>
            <nav>
                <ul>
                    <li><a href="Guests.php">Проживающие</a></li>
                    <li><a href="Employees.php">Сотрудники гостиницы</a></li>
                    <li><a href="Rooms.php">Номера</a></li>
                    <li><a class="active" href="#">Журнал регистрации проживающих</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <?php
            if (!empty($_COOKIE['messages'])) {
                echo '<div class="messages">';
                $messages = unserialize($_COOKIE['messages']);
                foreach ($messages as $message) {
                    echo $message . '</br>';
                }
                echo '</div>';
                setcookie('messages', '', time() + 24 * 60 * 60);
            }
            if (!empty($_COOKIE['errors'])) {
                echo '<div class="errors">';
                $errors = unserialize($_COOKIE['errors']);
                foreach ($errors as $error) {
                    echo $error . '</br>';
                }
                echo '</div>';
                setcookie('errors', '', time() + 24 * 60 * 60);
            }
        ?>
        <form action="" method="POST">
            <div class="main-content">
                <h2>Журнал регистрации проживающих</h2>
            </div>
            <div class="main-content">
                <button type="button" class="toggle-button" style="margin-bottom: 20px;">Добавить запись</button>
                <div class="newdates" style="display: none;">
                    <div class="newdates-item">
                        <label for="guest_id">Проживающий:</label>
                    </div>
                    <div class="newdates-item">
                            <?php
                                $stmt = $db->prepare("SELECT id, name FROM guests");
                                $stmt->execute();
                                $Guests = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                echo    '<select name="guest_id">';
                                print('<option disabled selected>выберите проживающего</option>');
                                foreach ($Guests as $guest) {
                                    printf('<option value="%d">%d. %s</option>', $guest['id'], $guest['id'], $guest['name']);
                                }
                echo            '</select>';
                            ?>
                        </select>
                    </div>
                    <div class="newdates-item">
                        <label for="checkin">Дата заезда:</label>
                    </div>
                    <div class="newdates-item">
                        <input type="date" name="checkin" placeholder="заезд">
                    </div>
                    <div class="newdates-item">
                        <label for="checkout">Дата выезда:</label>
                    </div>
                    <div class="newdates-item">
                        <input type="date" name="checkout" placeholder="выезд">
                    </div>
                    <div class="newdates-item">
                        <input type="submit" name="addnewdate" value="Добавить">
                    </div>
                </div>
            </div>
            <div class="main-content">
            <?php
                echo    '<table>
                            <tr>
                                <th>id</th>
                                <th>Проживающий</th>
                                <th>Дата заезда</th>
                                <th>Дата выезда</th>
                                <th colspan=2>&nbsp;</th>
                            <tr>';
                foreach ($values as $value) {
                    echo    '<tr>';
                    echo        '<td>'; print($value['id']); echo '</td>';
                    echo        '<td>';
                                    $stmt = $db->prepare("SELECT id, name FROM guests");
                                    $stmt->execute();
                                    $Guests = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo            '<select'; if(empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) print(" disabled ");
                                        else print(" "); echo 'name="guest_id'.$value['id'].'">';
                                    foreach ($Guests as $guest) {
                                        if ($guest['id'] == $value['guest_id']) {
                                            printf('<option selected value="%d">%d. %s</option>', $guest['id'], $guest['id'], $guest['name']);
                                        } else {
                                            printf('<option value="%d">d. %s</option>', $guest['id'], $guest['id'], $guest['name']);
                                        }
                                    }
                    echo            '</select>';
                    echo        '</td>';
                    echo        '<td>
                                    <input'; if(empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) print(" disabled ");
                                    else print(" "); echo 'type="date" name="checkin'.$value['id'].'" value="'.$value['checkin'].'">
                                </td>';
                    echo        '<td>
                                    <input'; if(empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) print(" disabled ");
                                    else print(" "); echo 'type="date" name="checkout'.$value['id'].'" value="'.$value['checkout'].'">
                                </td>';
                if (empty($_COOKIE['edit']) || ($_COOKIE['edit'] != $value['id'])) {
                    echo        '<td> <input name="edit'.$value['id'].'" type="image" src="https://static.thenounproject.com/png/2185844-200.png" width="20" height="20" alt="submit"/> </td>';
                    echo        '<td> <input name="clear'.$value['id'].'" type="image" src="https://cdn-icons-png.flaticon.com/512/860/860829.png" width="20" height="20" alt="submit"/> </td>';
                } else {
                    echo        '<td colspan=2> <input name="save'.$value['id'].'" type="image" src="https://cdn-icons-png.flaticon.com/512/84/84138.png" width="20" height="20" alt="submit"/> </td>';
                }
                    echo    '</tr>';
                }
                echo '</table>';
            ?>
            </div>
        </form>
    </main>
</body>
</html>
