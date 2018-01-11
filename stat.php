<?
//header('Content-Type: text/html; charset=utf-8');
//setlocale(LC_ALL, 'ru_RU.65001', 'rus_RUS.65001', 'Russian_Russia.65001', 'russian');


# Функция для пересчета времени
	function new_time($a) 	// преобразовываем время в нормальный вид
	{ 
	 date_default_timezone_set('Ukraine');
	 $ndate = date('d.m.Y', $a);
	 $ndate_time = date('H:i', $a);
	 $ndate_exp = explode('.', $ndate);
	 $nmonth = array(
	  1 => 'січ',
	  2 => 'лют',
	  3 => 'бер',
	  4 => 'квіт',
	  5 => 'трав',
	  6 => 'чер',
	  7 => 'лип',
	  8 => 'сер',
	  9 => 'вер',
	  10 => 'жов',
	  11 => 'лист',
	  12 => 'гру'
	 );

	 foreach ($nmonth as $key => $value) {
	  if($key == intval($ndate_exp[1])) $nmonth_name = $value;
	 }

	 if($ndate == date('d.m.Y')) return 'сьогодні в '.$ndate_time;
	 elseif($ndate == date('d.m.Y', strtotime('-1 day'))) return 'вчора в '.$ndate_time;
	 else return $ndate_exp[0].' '.$nmonth_name.' '.$ndate_exp[2].' в '.$ndate_time;
	}


# Соединямся с БД
        $connection = mysqli_connect("localhost", "root", "701196");
        mysqli_select_db($connection, "zibr");


# Вытаскиваем из БД записи, где записанная дата меньше текущей с точностью до минуты(секунды не учитываем)
//        $query = mysqli_query($connection, "SELECT * FROM numbers WHERE date > '".mktime(0,0,0,date("m")  ,date("d") ,date("Y"))."' ORDER BY date");
//        $query = mysqli_query($connection, "SELECT * FROM numbers WHERE online > 0 ORDER BY date");
//	$query = mysqli_query($connection, "SELECT * FROM numbers WHERE date < '".mktime(date("H"), date("i"), 0, date("m"), date("d"), date("Y"))."' ORDER BY date");
	$query = mysqli_query($connection, "SELECT * FROM users WHERE online = 1 ORDER BY user_login");


  // выводим на страницу сайта заголовки HTML-таблицы
  echo '<table align="center" border="1">';
  echo '<thead>';
  echo '<tr>';
  echo '<th width="45%" bgcolor="#305062">Перевірено в</th>';
  echo '<th width="25%" bgcolor="#305062"> Користувач </th>';
  echo '<th width="30%" bgcolor="#305062"> Присутні </th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';


   // выводим в HTML-таблицу все данные клиентов из таблицы MySQL 
	while ($userdata = mysqli_fetch_assoc($query))
  { 
    echo '<tr>';
    echo '<td align="left" width="40%" bgcolor="#48566c">' . new_time($userdata['time_in']) . '</td>';
//    echo '<td align="left" width="40%" bgcolor="#f0f0f0">' . new_time($userdata['date']) . '</td>';
    echo '<td align="center" width="20%" bgcolor="#48566c">' . $userdata['user_login'] . '</td>';
    echo '<td align="center" bgcolor="#48566c">' . $userdata['number'] . '</td>';
    echo '</tr>';

  }
  
    echo '</tbody>';
  echo '</table>';

    // закрываем соединение с сервером  базы данных
mysql_close($connect_to_db);

?>
<!DOCTYPE html>
<HTML>
<HEAD>
	<title>Статистика під’єднань</title>
	<meta charset="utf-8">
	<meta http-equiv="refresh" content="10; URL=stat.php" />
	<style>
		body {background: #37455b; color: lightgrey; }
		table {border-collapse: collapse;}
		th {border: 3px solid #536aa6; padding: 10px;}
		td {border: 3px solid #536aa6; padding: 10px; text-align: center;}
	</style>
<body>
</body>
</HEAD>
</HTML>
