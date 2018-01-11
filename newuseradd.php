<?

header('Content-Type: text/html; charset=utf-8');
setlocale(LC_ALL, 'ru_RU.65001', 'rus_RUS.65001', 'Russian_Russia.65001', 'russian');

if (isset($_COOKIE['admin']))
{

// Страница регситрации нового пользователя

# Соединямся с БД
	$connection = mysqli_connect("localhost", "root", "701196");
	mysqli_select_db($connection, "zibr");

# Ждем ввода данных из формы
	if (isset($_POST['submit'])) {
	    $err = array();

# проверям логин
	if (!preg_match("/^[a-zA-Z0-9]+$/", $_POST['login'])) {
	 $err[] = "Логін може складатися лише із англійських літер і цифр";
    }
	if (strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30) {
        $err[] = "Логін повинен бути не меньш 3-х символів і не больш 30";
    }


# проверяем, не сущестует ли пользователя с таким именем
	$result = mysqli_query($connection, "SELECT COUNT(user_id) FROM users WHERE user_login='".mysqli_real_escape_string($connection, $_POST['login'])."'");
	mysqli_data_seek($result, 0);
	$row = mysqli_fetch_array($result);
	$count = $row[0];
	if ($count > 0) {
        $err[] = "Користувач з таким логіном вже існує в базі даних";
    }


# Если нет ошибок, то добавляем в БД нового пользователя

	if (count($err) == 0) 
	{
        $login = $_POST['login'];

# Кількість днів на перегляд
	$nd = $_POST['nd'];
# Обчіслюємо дату останнєго дня перегляду
	$last_day_transl = mktime(0,0,0,date("m")  ,date("d")+$nd,date("Y"));
# e-mail користувача
	$usermail = $_POST['umail'];


# Убираем лишние пробелы и делаем двойное шифрование

        $password = md5(md5(trim($_POST['password'])));

	$address = md5($stream);
        mysqli_query($connection, "INSERT INTO users SET user_login='".$login."', user_password='".$password."', last='".$last_day_transl."', e_mail= '".$usermail."'");


      if (isset($_COOKIE['admin']))
      {
        setcookie ("admin", "", 1);
      }



        header("Location: index.php");
        exit();

    } 
	else {
	echo '<h4 align="center">', "<b>При реєстрації відбулися наступні помилки:</b><br>", '</h4><br>';
//        print "<b>При реєстрації відбулися наступні помилки:</b><br>";



        foreach ($err AS $error) 
		{
//            print $error."<br>";
            echo '<p style="text-align: center">'. $error . '</p>';
        	}
	     }
}

mysqli_close($connection);

}
        else
        {
//        print "<b>Тут можуть бути тільки адміністратори</b><br>";
//        echo '<h4 align="center">', 'Тут можуть бути тільки адміністратори', '</h4>';
echo '<h4 align="center">', 'Тут можуть бути тільки адміністратори.<br>Якщо ви є адміністратором, то зайдіть <a href="admin.php">сюди</a> і введіть логін і пароль адміністратора', '</h4>';
        exit();
        }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <title></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/signin.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<div class="container">

    <form class="form-signin" method="post">
        <h2 class="form-signin-heading">Реєстрація користувача</h2>
        <label for="inputLogin" class="sr-only">Логін</label>
        <input type="text" id="inputLogin" name="login" class="form-control" placeholder="Логін" required autofocus>

        <label for="inputPassword" class="sr-only">Пароль</label>
        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Пароль" required>

        <label for="inputnd" class="sr-only">Кількість днів</label>
        <input type="text" id="inputnd" name="nd" class="form-control" placeholder="Кількість днів" required autofocus>

        <label for="inputmail" class="sr-only">e-mail</label>
        <input type="text" id="inputmail" name="umail" class="form-control" placeholder="e-mail" autofocus>


        <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Зареєструватися</button>
    </form>

</div>

</body>
</html>
