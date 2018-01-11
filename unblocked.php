#!/usr/bin/php5 -f
<?php
# Соединямся с БД
        $connection = mysqli_connect("localhost", "root", "701196");
        mysqli_select_db($connection, "zibr");

        $query = mysqli_query($connection, "SELECT user_id, user_login FROM users WHERE online=1 AND time_in < '".(time()-30)."'");

# Записываем в БД разблокировку текущего логина на время просмотра
	while($data5 = mysqli_fetch_assoc($query))
		{
        mysqli_query($connection, "UPDATE users SET online=0 WHERE user_id = '".$data5['user_id']."'");
        mysqli_query($connection, "DELETE FROM numbers WHERE user_login = '".$data5['user_login']."'");
		}

mysqli_close($connection);

//header("Refresh: 21; url=unblocked.php");
?>

