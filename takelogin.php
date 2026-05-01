<?
require_once("include/bittorrent.php");

if (!mkglobal("email:password"))
	die();

dbconn();

function bark($text = "Имя пользователя или пароль неверны")
{
  stderr("Ошибка входа", $text);
}

$res = sql_query("SELECT id, passhash, secret, enabled, status FROM users WHERE email = " . sqlesc($email));
$row = mysql_fetch_array($res);

if (!$row)
	bark("Вы не зарегистрированы в системе. Или ваш почтовый адрес или пароль не верны.");

if ($row["status"] == 'pending')
	bark("Вы еще не активировали свой почтовый ящик! Активируйте ваш почтовый ящик и попробуйте снова.");

if ($row["passhash"] != md5($row["secret"] . $password . $row["secret"]))
	bark();

if ($row["enabled"] == "no")
	bark("Ваша страница отключена.");
	
$peers = sql_query("SELECT COUNT(id) FROM peers WHERE userid = $row[id]");
$num = mysql_fetch_row($peers);
$ip = getip();
if ($num[0] > 0 && $row[ip] != $ip && $row[ip])
	bark("Этот пользователь на данный момент активен. Вход невозможен.");

logincookie($row["id"], $row["passhash"]);

if (!empty($_POST["returnto"]))
	header("Location: ".$DEFAULTBASEURL.$_POST['returnto']);
else
	header("Location: $DEFAULTBASEURL/");

?>