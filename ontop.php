<?
require_once("include/bittorrent.php");

if (!mkglobal("id"))
    die();

$id = 0 + $id;
if (!$id)
    die();

dbconn();

loggedinorreturn();

$updateset = array();

if (get_user_class() < UC_MODERATOR)
    stderr($tracker_lang['error'], "Отказано в доступе.");

$res = sql_query("SELECT ontop FROM torrents WHERE id=$id");
$row = mysql_fetch_array($res);

if ($row["ontop"] == "no")
$updateset[] = "ontop = 'yes'";
else
$updateset[] = "ontop = 'no'";

sql_query("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $id");

header("Refresh: 0; url=$DEFAULTBASEURL");
?>