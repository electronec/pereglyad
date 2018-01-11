<?
header('Content-Type: text/html; charset=utf-8');
setlocale(LC_ALL, 'ru_RU.65001', 'rus_RUS.65001', 'Russian_Russia.65001', 'russian');


// Страница авторизации


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

# Удаляем логины, у которых истек срок просмотров (сравниваем с текущей датой)
        $query3 = mysqli_query($connection, "DELETE FROM users WHERE last < '".mktime(0,0,0,date("m")  ,date("d") ,date("Y"))."'");

//# Удаляем количества просматривавших собрание в предыдущие дни
//        $query4 = mysqli_query($connection, "DELETE FROM numbers WHERE date < '".mktime(0,0,0,date("m")  ,date("d") ,date("Y"))."' ");


# Ждем ввода данных в форме
	if (isset($_POST['submit'])) 
{

# Вытаскиваем из БД запись, у которой логин равняеться введенному
	$query = mysqli_query($connection, "SELECT user_id, user_password, user_hash, online, time_in, dubl FROM users WHERE user_login='".mysqli_real_escape_string($connection, $_POST['login'])."' LIMIT 1");
	$data = mysqli_fetch_assoc($query);


# Снимаем блокировку с данного логина, если она стоит, но которая на самом деле, возможно, устарела
	if ($data['time_in'] < (time()-30)) // Если метка времени на данном логине хотя бы на 30 с. младше текущего времени,
	{
        mysqli_query($connection, "UPDATE users SET online=0 WHERE user_id = '".$data['user_id']."'"); // то снимаем блокировку
	$data['online'] = "0";                                                                        // и обновляем массив текущего логина
//        mysqli_query($connection, "DELETE FROM numbers WHERE user_login = '".mysqli_real_escape_string($connection, $_POST['login'])."'");
	}

# Выясняем причины ошибок, сравниваем пароли, смотрим на наличие блокировки
		if ($data['user_password'] !== md5(md5($_POST['password'])))
			{
		$err1[] = "Для логіна<b> ".$_POST['login']."</b> або термін дії закінчився або логін і пароль введені невірно.<br>Попробуйте уважно ввести їх знову або зверніться до відповідального в своєму зборі.";
			}
		if ($data['online'] === "1")
			{
		$err1[] = "Під логіном<b> ".$_POST['login']."</b> вже хтось увійшов. Не розраховано переглядати трансляцію під одним логіном <br>на декільках пристроях.<br>Запросіть власний логін у відповідального.";
			}
//                if ($data['dubl'] === "20")
//                        {
//                $err1[] = "Вашим логіном<b> ".$_POST['login']."</b> хтось час від часу користується. <br>Це може привести до його блокування .";
//                        }



#Если пароль верный и никто не смотрит на этом логине (единый критерий "праведности" клиента - пароль и отсутствие заблокированности)
	if (($data['user_password'] === md5(md5($_POST['password']))) and ($data['online'] !== "1"))
	{

# Генерируем случайное число и шифруем его
        $hash = md5(generateCode(10));

# Если пользователя выбрал привязку к IP
        if (!@$_POST['not_attach_ip']) 
		{
# Переводим IP в строку
            $insip = ", user_ip=INET_ATON('".$_SERVER['REMOTE_ADDR']."')";
		}


# Записываем в БД количество смотрящих, новый хеш авторизации и IP
        mysqli_query($connection, "UPDATE users SET number='".mysqli_real_escape_string($connection, $_POST['number'])."' , user_hash='".$hash."' ".$insip." WHERE user_id='".$data['user_id']."'");

# Также в другую таблицу дату и время, логин пользователя и количество просматривающих на этом логине(используется для статистики)
//	mysqli_query($connection, "INSERT INTO numbers SET date='".mktime(date("H"), date("i"), 0, date("m")  ,date("d") ,date("Y"))."', user_login='".mysqli_real_escape_string($connection, $_POST['login'])."', number='".mysqli_real_escape_string($connection, $_POST['number'])."'");
	mysqli_query($connection, "INSERT INTO numbers SET date='".time()."', user_login='".mysqli_real_escape_string($connection, $_POST['login'])."', number='".mysqli_real_escape_string($connection, $_POST['number'])."'");

# Ставим куки
        setcookie("id", $data['user_id'], time() + 60 * 60 * 6);
        setcookie("hash", $hash, time() + 60 * 60 * 6);


# Переадресовываем браузер на страницу проверки нашего скрипта
        header("Location: play.php");
        exit();

        }  

#Якщо неудача
        else {
        echo '<h4 align="center">', "<b>Зверніть увагу, що</b>", '</h4>';
        foreach ($err1 AS $error1)
                {
            echo '<p style="text-align: center">'. $error1 . '</p>';
                }
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
    <title>Вхід на перегляд</title>
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
        <h2 class="form-signin-heading">Вхід для перегляду</h2>
        <label for="inputLogin" class="sr-only">Логін</label>
        <input type="text" id="inputLogin" name="login" class="form-control" placeholder="Логін" required autofocus>
        <label for="inputPassword" class="sr-only">Пароль</label>
        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Пароль" required>

        <label for="inputNumber" class="sr-only">Кількість</label>
        <input type="text" id="inputNumber" name="number" class="form-control" placeholder="Скількі осіб переглядають програму" required autofocus>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="not_attach_ip"> Не прикріплювати к IP (не є небезпечним)
            </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Увійти</button>
    </form>

</div>

</body>
</html>
