<?php

require_once("include/bittorrent.php");
dbconn();
header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);

if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'POST')
{
    $id = (int)$_POST['id'];
    $user = (int)$CURUSER['id'];
    $act = (string)$_POST["act"];
    $type = (string)$_POST["type"];

    if (empty($id) || empty($user) || empty($act) || empty($type))
        die("Прямой доступ закрыт");

    if (!in_array($type, array("torrent", "comment", "user")))
        die("Прямой доступ закрыт");

    $canrate = get_row_count("karma", "WHERE type = " . sqlesc($type) . " AND value = $id AND user = $user");

    if ($canrate > 0)
        die("Вы уже голосовали");

    if ($type == "torrent")
        $table = "torrents";
    elseif ($type == "comment")
        $table = "comments";
    else
        $table = "users";

    if ($act == 'plus')
    {
        sql_query("UPDATE $table SET karma = karma + 1 WHERE id = $id");
        sql_query("INSERT INTO karma (type, value, user, added) VALUES (" . sqlesc($type) . ", $id, $user, " . time() . ")");
        $show = true;
    }
    elseif ($act == 'minus')
    {
        sql_query("UPDATE $table SET karma = karma - 1 WHERE id = $id");
        sql_query("INSERT INTO karma (type, value, user, added) VALUES (" . sqlesc($type) . ", $id, $user, " . time() . ")");
        $show = true;
    }
    else
        die("Прямой доступ закрыт");

    if ($show)
    {
        $res = sql_query("SELECT karma FROM $table WHERE id = $id");
        $row = mysql_fetch_array($res);
        die("<img src=\"pic/minus-dis.png\" title=\"Вы не можете голосовать\" alt=\"\" />&nbsp;" . karma($row["karma"]) . "&nbsp;<img src=\"pic/plus-dis.png\" title=\"Вы не можете голосовать\" alt=\"\" />");
    }
}
else
    die("Прямой доступ закрыт");

?>