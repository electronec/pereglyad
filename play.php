<?
header('Content-Type: text/html; charset=utf-8');
setlocale(LC_ALL, 'ru_RU.65001', 'rus_RUS.65001', 'Russian_Russia.65001', 'russian');


// Скрипт проверки


# Соединямся с БД

	$connection = mysqli_connect("localhost", "root", "701196");
	mysqli_select_db($connection, "zibr");


# Эта строка защищает страницу от входа на play.php без куки, которые установлены в предынущем скрипте на index.php
	if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) 
{
# Если время куков еще не истекло, то продолжаем
	$query = mysqli_query($connection, "SELECT *, INET_NTOA(user_ip) FROM users WHERE user_id = '".intval($_COOKIE['id'])."' LIMIT 1");
	$userdata = mysqli_fetch_assoc($query);

    if (($userdata['user_hash'] !== $_COOKIE['hash']) or
        ($userdata['user_id'] !== $_COOKIE['id']) or
        (($userdata['INET_NTOA(user_ip)'] !== $_SERVER['REMOTE_ADDR']) and ($userdata['user_ip'] !== "0"))) 
		{
        setcookie("id", "", time() - 3600 * 24 * 30 * 12, "/");
        setcookie("hash", "", time() - 3600 * 24 * 30 * 12, "/");

        echo '<h4 align="center">', 'Можливо, ви увійшли на цю сторінку <br>не вводячи логін і пароль.<br>Треба <a href="index.php">їх ввести</a>', '</h4>';
	header("Refresh:10; url=index.php");
		} 
	else 
	{

# Записываем в БД блокировку текущего логина на время просмотра (с точностью до секунд)
//        mysqli_query($connection, "UPDATE users SET online=1, time_in='".mktime(date("H"), date("i"), 0, date("m")  ,date("d") ,date("Y"))."' WHERE user_login='".$userdata['user_login']."'");
        mysqli_query($connection, "UPDATE users SET online=1, time_in='".time()."' WHERE user_login='".$userdata['user_login']."'");


# Также в другую таблицу дату и время, логин пользователя и количество просматривающих на этом логине для просмотра в статистике
# Здесь дата без секунд
//        mysqli_query($connection, "INSERT INTO numbers SET date='".mktime(date("H"), date("i"), 0, date("m")  ,date("d") ,date("Y"))."', user_login='".$userdata['user_login']."', number='".$userdata['number']."'");
# Однако, попробуем просто штамп времени
//        mysqli_query($connection, "INSERT INTO numbers SET date='".time()."', user_login='".$userdata['user_login']."', number='".$userdata['number']."'");


# Определяем Андроид/не Андроид

	$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
	if((stripos($ua,'android') !== false) or ($userdata['user_login'] === "romanyk"))  // && stripos($ua,'mobile') !== false) {
	{

# Для Андроидов
        readfile('universal_player_obfu2.html');
//       readfile('html5_player_fine.html');
	}
        else    {
# Для не Андроидов
        readfile('universal_player_obfu2.html');
//        readfile('rtmp_player_fine.html');
		}

    }

} 
	else {
# Если время куков вышло, то пусть снова авторизуется
	header("Location: index.php");
}

mysqli_close($connection);

?>

