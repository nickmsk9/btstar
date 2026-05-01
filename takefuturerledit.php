<?php
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();
function bark($msg) {
stdhead();
stdmsg("Ошибка!", $msg);
stdfoot();
exit;
}

$catid = $_POST["type"];
if (!$_POST["type"])
	bark("Вы должны выбрать категорию.");

if (!$_POST["id"])
bark("Что ты тут делаешь???");

$id = $_POST["id"];
$trailer = sqlesc($_POST["text"]);
$name = sqlesc($_POST["name"]);
$descr = sqlesc($_POST["descr"]);
$realeasedate = sqlesc($_POST["realeasedate"]);

mysql_query("UPDATE futurerls SET trailer=$trailer, realeasedate=$realeasedate, name=$name, descr=$descr, cat=$catid where id=$id");
$id = mysql_insert_id();
header("Refresh: 0; url=futurerls.php");
?>
