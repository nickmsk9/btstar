<?php

### "Случайные раздачи" by merdox [AJAX-часть] --> ###

require_once("include/bittorrent.php");

dbconn();

header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'POST')
{
    ### Настройки ###
    $cats = "2,13"; //id категорий, учавствующих в поиске
    $pwidth = "120"; //ширина отображаемого постера

    $res = sql_query("SELECT * FROM torrents WHERE ontop='yes' LIMIT 3") or sqlerr(__FILE__, __LINE__);
    if (mysql_num_rows($res) > 0)
    {
        $row = mysql_fetch_array($res);
        print("<a href=\"details.php?id=" . $row["id"] . "\"><img src=\"torrents/images/" . $row["image1"] . "\" width=\"$pwidth\" border=\"0\" title=\"" . $row["name"] . "\" alt=\"Загрузка..\" /></a>");
    }
    else
        print("Нет торрентов");
}
else
    die("Прямой доступ запрещен");

### <-- "Случайные раздачи" by merdox [AJAX-часть] ###

?>