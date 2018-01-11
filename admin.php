<?
header('Content-Type: text/html; charset=utf-8');
setlocale(LC_ALL, 'ru_RU.65001', 'rus_RUS.65001', 'Russian_Russia.65001', 'russian');


// Страница авторизации администратора


# Функция для генерации случайной строки

function generateCode($length = 6)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0, $clen)];
    }
    return $code;
}


# Соединямся с БД
	$connection = mysqli_connect("localhost", "root", "701196");
	mysqli_select_db($connection, "zibr");


# Ждем ввода данных в форме
	if (isset($_POST['submit'])) 
{
# Проверяем, у кого истек срок просмотров
	$query1 = mysqli_query($connection, "SELECT user_id, e_mail, last FROM users WHERE user_login='".mysqli_real_escape_string($connection, $_POST['login'])."' LIMIT 1");
	$data1 = mysqli_fetch_assoc($query1);

# Сравниваем текущую дату с записаной как последняя для пользователя, который входит
	if ($data1['last'] < mktime(0,0,0,date("m")  ,date("d") ,date("Y")))
        {
//        $query2 = mysqli_query($connection, "DELETE FROM users WHERE user_id='".mysqli_real_escape_string($connection, $data1['user_id'])."' LIMIT 1");
        $query2 = mysqli_query($connection, "DELETE FROM users WHERE user_id='".mysqli_real_escape_string($connection, $data1['user_id'])."'");
        }

# Вытаскиваем из БД запись, у которой логин равняеться введенному
	$query = mysqli_query($connection, "SELECT user_id, user_password, admin FROM users WHERE user_login='".mysqli_real_escape_string($connection, $_POST['login'])."' LIMIT 1");
	$data = mysqli_fetch_assoc($query);

	
# Администратор ли это?
	if ($data['admin'] <> "1")
	{
//	print "Здається, ви не є адміністратор. Нажмите кнопку 'назад'";
        echo '<h4 align="center">', 'Здається, ви не є адміністратор. Нажмите кнопку <--', '</h4>';
	exit();
	}

# Сравниваем пароли

	if ($data['user_password'] === md5(md5($_POST['password']))) 
	{

# Генерируем случайное число и шифруем его

        $hash = md5(generateCode(10));

# Если пользователя выбрал привязку к IP
# Переводим IP в строку

# Записываем в БД новый хеш авторизации и IP
        mysqli_query($connection, "UPDATE users SET user_hash='".$hash."' ".$insip." WHERE user_id='".$data['user_id']."'");


# Ставим куки на 2 хвилини
	setcookie ("admin", $data['user_id'], time() + 120);
//        setcookie("id", $data['user_id'], time() + 60);
//        setcookie("hash", $hash, time() + 60);


# Переадресовываем браузер на страницу ввода нового пользователя
        header("Location: newuseradd.php");
        exit();

        }  

	else {
//        print "Ви ввели недійсний логін/пароль або строк дії логіна закінчився";
        echo '<h4 align="center">', 'Ви ввели недійсний логін/пароль або строк дії логіна закінчився', '</h4>';
             }

}

mysqli_close($connection);

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
    <title>Вхід</title>
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
        <h2 class="form-signin-heading">Вхід адміністратора</h2>
        <label for="inputLogin" class="sr-only">Логін</label>
        <input type="text" id="inputLogin" name="login" class="form-control" placeholder="Логін" required autofocus>
        <label for="inputPassword" class="sr-only">Пароль</label>
        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Пароль" required>
       <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Увійти</button>
    </form>


</div>

</body>
</html>
