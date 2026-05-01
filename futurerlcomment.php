<?
require_once("include/bittorrent.php");
$valid_actions = array('add', 'edit', 'delete', 'vieworiginal', 'quote');
$action = in_array($_GET["action"], $valid_actions) ? $_GET['action'] : '';
dbconn(false);
loggedinorreturn();
if ($action == "add")
{
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
$futurerlid = 0 + $_POST["tid"];
if (!is_valid_id($futurerlid))
stderr("Ошибка", "Неверный ID $futurerlid.");
$res = mysql_query("SELECT id FROM futurerls WHERE id = $futurerlid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if (!$arr)
stderr("Ошибка", "Нет анонса с таким ID $futurerlid.");
$text = trim($_POST["msg"]);
if (!$text)
stderr("Ошибка", "А писать то будем то?");
mysql_query("INSERT INTO comments (user, trailer, added, text, ori_text) VALUES (" .
$CURUSER["id"] . ",$futurerlid, '" . get_date_time() . "', " . sqlesc($text) .
"," . sqlesc($text) . ")");
$newid = mysql_insert_id();
mysql_query("UPDATE futurerls SET comments = comments + 1 WHERE id = $futurerlid");
header("Refresh: 0; url=futurerldetails.php?id=$futurerlid&viewcomm=$newid#comm$newid");
die;
}
$futurerlid = 0 + $_GET["tid"];
if (!is_valid_id($futurerlid))
stderr("Ошибка", "Неверный ID $futurerlid.");
$res = mysql_query("SELECT id FROM futurerls WHERE id=$futurerlid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if (!$arr)
stderr("Ошибка", "Неверный ID $futurerlid.");
stdhead("Добавление комментария для ожидаемого релиза");
begin_frame("Добавление комментария для ожидаемого релиза");
print("<p><form name=\"nrg\" method=\"post\" action=\"futurerlcomment.php?action=add\">\n");
print("<input type=\"hidden\" name=\"tid\" value=\"$futurerlid\"/>\n");
?>
<center>
<?
textbbcode("nrg","msg","$body",0);

print("<center><p><input type=submit class=btn value='Отправить'> <input type=reset class=btn value='Сбросить'></p></center></form></br>\n");
end_frame();
stdfoot();
die;
}
elseif ($action == "edit")
{
$commentid = 0 + $_GET["cid"];
if (!is_valid_id($commentid))
stderr("Ошибка", "Неверный ID $commentid.");
$res = mysql_query("SELECT c.*, o.id FROM comments AS c LEFT JOIN futurerls AS o ON c.trailer = o.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
//$res = mysql_query("SELECT c.*, o.coming FROM comments AS c LEFT JOIN coming AS o ON c.coming = o.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if (!$arr)
stderr("Ошибка", "Неверный ID $commentid.");
if ($arr["user"] != $CURUSER["id"] && get_user_class() < UC_MODERATOR)
stderr("Ошибка", "Доступа нет.");
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
$text = $_POST["msg"];
$returnto = $_POST["returnto"];
if ($text == "")
stderr("Ошибка", "А писать будем то?");
$text = sqlesc($text);
$editedat = sqlesc(get_date_time());
mysql_query("UPDATE comments SET text=$text, editedat=$editedat, editedby=$CURUSER[id] WHERE id=$commentid") or sqlerr(__FILE__, __LINE__);
if ($returnto)
header("Location: $returnto");
else
header("Location: $BASEURL/");      // change later ----------------------
die;
}
stdhead("Редактирование комментария");
begin_frame("Редактирование комментария");
print("<form name=nrg method=\"post\" action=\"futurerlcomment.php?action=edit&amp;cid=$commentid\">\n");
print("<input type=\"hidden\" name=\"returnto\" value=\"" . $_SERVER["HTTP_REFERER"] . "\" size=\"20\" />\n");
print("<input type=\"hidden\" name=\"cid\" value=\"$commentid\" size=\"20\" />\n");
?>
<center>
<?
textbbcode("nrg","msg","" . htmlspecialchars(stripslashes($arr["text"])) . "",0);
?>

<?
print("<center><p><input type=submit class=btn value='Отправить'> <input type=reset class=btn value='Сбросить'></p></center></form></br>\n");
end_frame();
stdfoot();
die;
}
elseif ($action == "delete")
{
if (get_user_class() < UC_MODERATOR)
stderr("Ошибка", "Доступа нет.");
$commentid = 0 + $_GET["cid"];
if (!is_valid_id($commentid))
stderr("Ошибка", "Неверный ID $commentid.");
$sure = $_GET["sure"];
if (!$sure)
{
$referer = $_SERVER["HTTP_REFERER"];
stderr("Удаление комментария", "Если вы уверены нажмите\n" .
"<a href=?action=delete&cid=$commentid&sure=1" .
($referer ? "&returnto=" . urlencode($referer) : "") .
">тут</a>.");
}
$res = mysql_query("SELECT trailer FROM comments WHERE id=$commentid")  or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if ($arr)
$futurerlid = $arr["trailer"];
mysql_query("DELETE FROM comments WHERE id=$commentid") or sqlerr(__FILE__,__LINE__);
if ($futurerlid && mysql_affected_rows() > 0)
mysql_query("UPDATE futurerls SET comments = comments - 1 WHERE id = $futurerlid");
$returnto = $_GET["returnto"];
if ($returnto)
header("Location: $returnto");
else
header("Location: $BASEURL/");      // change later ----------------------
die;
}
elseif ($action == "vieworiginal")
{
if (get_user_class() < UC_MODERATOR)
stderr("Ошибка", "Доступа нет.");
$commentid = 0 + $_GET["cid"];
if (!is_valid_id($commentid))
stderr("Ошибка", "Неверный ID $commentid.");
$res = mysql_query("SELECT c.*, t.id FROM comments AS c LEFT JOIN futurerls AS t ON c.trailer = t.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
if (!$arr)
stderr("Ошибка", "Неверный ID $commentid.");
stdhead("Оригинальный ответ");
begin_frame("Оригинальный ответ");
echo htmlspecialchars($arr["ori_text"]);
$returnto = $_SERVER["HTTP_REFERER"];
if ($returnto)
print("<p><font size=small>(<a href=$returnto>Назад</a>)</font></p>\n");
end_frame();
stdfoot();
die;
}
elseif ($action == "quote") 
{
        $commentid = 0 + $_GET["cid"];
        if (!is_valid_id($commentid))
                stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
        $res = sql_query("SELECT c.*, o.name, o.id AS oid, u.username FROM comments AS c JOIN futurerels AS o ON c.trailer = o.id JOIN users AS u ON c.user = u.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
        $arr = mysql_fetch_array($res);
        if (!$arr)
                stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
        $text = "[quote=$arr[username]]" . $arr["text"] . "[/quote]\n";
        $relid = $arr["id"];
        stdhead("Добавить комментарий к \"" . $arr["name"] . "\"");
        $name = (strlen($arr["name"])>40?substr($arr["name"],0,40)."...":$arr["name"]);
        print("<form name=form method=\"post\" action=\"futurerelcomment.php?action=add\">\n");
        print("<input type=\"hidden\" name=\"tid\" value=\"$relid\" />\n");
        print("<table border=1 cellspacing=\"0\" cellpadding=\"5\">\n");
        echo ("<tr><td class=colhead align=left>Редактировать комментарий к \"" . htmlspecialchars($name) . "\"</td><tr>\n");
        print("<tr><td align=center>\n");
        textbbcode("form","msg",htmlspecialchars_uni($text));
        print("<div align=center><a href=tags.php target=_blank>Все теги</a></div>\n");
        print("</td></tr>\n");
        print("<tr><td align=center colspan=2><input type=submit value=\"Добавить\"></td></tr></form></table>\n");
        stdfoot();
	  die;
}
else
stdhead();
stdmsg("Извините", "В данный момент это действие отключено в этом ражделе.");
stdfoot();
die;
?>
