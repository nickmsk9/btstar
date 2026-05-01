<?php
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();

/// Mod by dokty - tbdev.net

$id = 0 + $_GET["id"];
$points = 0 + $_GET["points"];
if (!is_valid_id($id) || !is_valid_id($points))
die();

$pointscangive = array("10","20","50","100");
if (!in_array($points, $pointscangive))
stderr("Error","You can't give that amount of points!!!");

$sdsa = sql_query("SELECT 1 FROM coins WHERE torrentid=".sqlesc($id)." AND userid =" .sqlesc($CURUSER["id"])) or die();
$asdd = mysql_fetch_array($sdsa);
if ($asdd)
stderr("Ошибка","Вы уже подарили монеты этому торренту.");

$res = sql_query("SELECT owner FROM torrents WHERE id = ".sqlesc($id)) or die();
$row = mysql_fetch_assoc($res) or stderr("Error","Torrent was not found");
$userid = $row["owner"];

if ($userid == $CURUSER["id"])
stderr("Ошибка","Вы не можете подарить монеты сами себе!");

if ($CURUSER["bonus"] < $points)
stderr("Ошибка","У вас нет столько монет.");

sql_query("INSERT INTO coins (userid, torrentid, points) VALUES (".sqlesc($CURUSER["id"]).", ".sqlesc($id).", ".sqlesc($points).")") or sqlerr(__FILE__,__LINE__);
sql_query("UPDATE users SET bonus=bonus+".$points." WHERE id=".sqlesc($userid)) or sqlerr(__FILE__,__LINE__);
sql_query("UPDATE users SET bonus=bonus-".$points." WHERE id=".sqlesc($CURUSER["id"])) or sqlerr(__FILE__,__LINE__);
sql_query("UPDATE torrents SET points=points+".$points." WHERE id=".sqlesc($id)) or sqlerr(__FILE__,__LINE__);

stderr("Великолепно","Вы добавили $points торренту.");

?>