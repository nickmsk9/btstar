<?php
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();
if (get_user_class() < UC_POWER_USER)
stderr("Ошибка", "Что ты тут забыл?");
function bark($msg) {
stdhead();
stdmsg("Ошибка!", $msg);
stdfoot();
exit;
}
$catid = $_POST["type"];
if (!$_POST["type"])
	bark("Вы должны выбрать категорию.");

if (!$_POST["trailer"])
bark("Введите адресс постера.");

$name = sqlesc($_POST["name"]);
if (!$_POST["name"])
bark("Введите название релиза.");

$realeasedate = $_POST["realeasedate"];
$trailer = sqlesc($_POST["trailer"]);
$descr = sqlesc($_POST["descr"]);
$added = sqlesc(get_date_time());
sql_query("INSERT INTO futurerls (name,userid,added,trailer,realeasedate,descr,cat) VALUES($name,$CURUSER[id],$added,$trailer,'$realeasedate',$descr,$catid)") or sqlerr(__FILE__, __LINE__);

header("Refresh: 0; url=futurerls.php");
?>
