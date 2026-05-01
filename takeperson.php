<?php
require_once("include/bittorrent.php");
dbconn();
$name = $_POST["name"];
$year = $_POST["year"];
$month = $_POST["month"];
$day = $_POST["day"];
$poster = unesc($_POST["poster"]);
$descr = unesc($_POST["descr"]);
if ($year=='0000' || $month=='00' || $day=='00')
        stderr($tracker_lang['error'],"ѕохоже вы указали неверную дату рождени€");
        $birthday = date("$year.$month.$day");

$ret = sql_query("INSERT INTO akters (name, descr, ori_descr, birthday, owner, poster) VALUES (". implode(",", array_map("sqlesc", array($name, $descr, $descr, $birthday, $CURUSER["id"], $poster))) .")") or die(mysql_error());
$id = mysql_insert_id();

header("Location: persons.php");
?>