<?
# Соединямся с БД
        $connection = mysqli_connect("localhost", "root", "701196");
        mysqli_select_db($connection, "zibr");


# Записываем в БД последнее время активности
//        mysqli_query($connection, "UPDATE users SET time_in='".mktime(date("H"), date("i"), date("s"), date("m")  ,date("d") ,date("Y"))."' WHERE user_id == '".$_COOKIE['id']."'");
        mysqli_query($connection, "UPDATE users SET online = 1, time_in='".time()."' WHERE user_id = '".$_COOKIE['id']."'");
mysqli_close($connection);

?>

