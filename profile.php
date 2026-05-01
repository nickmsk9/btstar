<?php
require_once("include/bittorrent.php");
dbconn(false);
loggedinorreturn();

function bark($msg) {
	global $tracker_lang;
	stdhead($tracker_lang['error']);
	stdmsg($tracker_lang['error'], $msg);
	stdfoot();
	exit;
}

$id = (int)$_GET["id"];

if (!is_valid_id($id))
  bark($tracker_lang['invalid_id']);
  
  $r = @sql_query("SELECT * FROM users WHERE id=$id") or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_array($r) or bark("Нет пользователя с таким ID $id.");

$it = sql_query("SELECT u.id, u.username, u.class, i.id AS invitedid, i.username AS invitedname, i.class AS invitedclass FROM users AS u LEFT JOIN users AS i ON i.id = u.invitedby WHERE u.invitedroot = $id OR u.invitedby = $id ORDER BY u.invitedby");
if (mysql_num_rows($it) >= 1) {
	$invitetree = "<table class=\"main\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\"><tr>".
		"<td class=\"colhead\">Пользователь</td><td class=\"colhead\">Пригласил</td>";
	while ($inviter = mysql_fetch_array($it))
		$invitetree .= "<tr><td><a href=\"userdetails.php?id=$inviter[id]\">".get_user_class_color($inviter["class"], $inviter["username"])."</a></td><td><a href=\"userdetails.php?id=$inviter[invitedid]\">".get_user_class_color($inviter["invitedclass"], $inviter["invitedname"])."</a></td></tr>";
	$invitetree .= "</table>";
}

if ($user["ip"] && (get_user_class() >= UC_MODERATOR || $user["id"] == $CURUSER["id"])) {
  $ip = $user["ip"];
  $dom = @gethostbyaddr($user["ip"]);
  if ($dom == $user["ip"] || @gethostbyname($dom) != $user["ip"])
    $addr = $ip;
  else
  {
    $dom = strtoupper($dom);
    $domparts = explode(".", $dom);
    $domain = $domparts[count($domparts) - 2];
    if ($domain == "COM" || $domain == "CO" || $domain == "NET" || $domain == "NE" || $domain == "ORG" || $domain == "OR" )
      $l = 2;
    else
      $l = 1;
    $addr = "$ip ($dom)";
  }
}

if ($user["downloaded"] > 0)
{
    $rating = $user["uploaded"] / $user["downloaded"];
    $rating = floor($rating * 1000) / 1000;
    $rating = "<font color=\"" . get_ratio_color($rating) . "\">" . number_format($rating, 2) . "</font>";
}
else
    $rating = "N/A";

$comments_count = get_row_count("comments", "WHERE user = $id");
if ($comments_count && (($user["class"] >= UC_POWER_USER && $user["id"] == $CURUSER["id"]) || get_user_class() >= UC_MODERATOR))
	$comments = "<a href=\"userhistory.php?action=viewcomments&id=$id\">$comments_count</a>\n";
else
	$comments = $comments_count;

$res = sql_query("SELECT name FROM countries WHERE id = $user[country] LIMIT 1") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) == 1)
{
  $arr = mysql_fetch_assoc($res);
}

$res = mysql_query("SELECT name FROM cities WHERE id=$user[city] LIMIT 1") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) == 1) 
{ 
 $art = mysql_fetch_assoc($res); 
 $city = "$art[name]"; 
}

if (!empty($user['avatar']))
    $avatar = "<img src=\"" . $user['avatar'] . "\" style=\"width:100px;border:3px double #ccc;\" title=\"\" alt=\"\" />";
else
    $avatar = "<img src=\"pic/default_avatar.gif\" style=\"width:100px;border:3px double #ccc;\" title=\"\" alt=\"\" />";

if ($user["gender"] == "1") $gender = "Мужской";
elseif ($user["gender"] == "2") $gender = "Женский";

stdhead("Просмотр профиля " . $user["username"]);
$enabled = $user["enabled"] == 'yes';
begin_frame($user["username"]);
print("<table width=100% border=0 cellspacing=0 cellpadding=5><tr><td valign='top' align='left' width='110' style='border:none'>");
print("".$avatar."");
if ($CURUSER['id'] != $id)
{
    $res = sql_query("SELECT id FROM friends WHERE userid=" . sqlesc($CURUSER['id']) . " AND friendid = $id AND status = 'yes'") or sqlerr(__FILE__, __LINE__);
    if (mysql_num_rows($res) > 0)
        print("<a href=\"javascript:void(0);\" onclick=\"javascript:addtofriends('$id', 'delete');\" class=\"menu\">Удалить из друзей</a>\n");
    else
        print("<a href=\"javascript:void(0);\" onclick=\"javascript:addtofriends('$id', 'add');\" class=\"menu\">Добавить в друзья</a>\n");
		 if (get_user_class() >= UC_MODERATOR && $user["class"] < get_user_class())
        print("<a href=\"javascript:void(0);\" onclick=\"javascript:moderate('$id');\" class=\"menu\">Модерирование</a>\n");
}
?>

<script language="JavaScript" src="js/user.js" type="text/javascript"></script>
<td class='outer' valign='top' align='left' style='border:none'>
<div id="actions"></div><div id="tabs">
<span class="tab active" id="info">Общее</span>
<span class="tab" id="friends">Друзья</span>
<span class="tab" id="downloaded">Скачал</span>
<span class="tab" id="uploaded">Загрузил</span>
<span id="loading"></span>
<div id="body" user="<?=$user["id"];?>">
<h4>Оснавная информация</h4>
<?php if($user["username"] == "Полина") { ?>
<div align="left"><b><font color="gray">Помолвлена с <a href="id1" title="Faris">Ваней</a></font></b></div>
<?php } ?>
<?php if($user["icq"] == "6227714") { ?>
<div align="left"><b><font color="gray">Помолвлен с <a href="id1" title="Faris">Полиной</a></font></b></div>
<?php } ?>
<div align="left"><b><font color="gray">Класс:</font></b> <?=get_user_class_color($user['class'], get_user_class_name($user['class']))?></div>
<div align="left"><b><font color="gray">Пол:</font></b> <?=$gender;?></div>
<div align="left"><b><font color="gray">Дата рождения:</font></b> <?=nicetime($user["birthday"]);?> года</div><br>
<h4>Контактная информация </h4>
<div align="left"><b><font color="gray">Страна:</font></b> <?=$arr["name"];?></div>
<div align="left"><b><font color="gray">Город:</font></b> <?=$city;?></div>
<?php if ($user["icq"])?>
<div align="left"><b><font color="gray">Номер ICQ:</font></b> <?=$user["icq"];?></div><br>
<h4>Статистика на трекере</h4>
<div align="left"><b><font color="gray">Раздал:</font></b> <?=str_replace(" ", "&nbsp;", mksize($user['uploaded']))?></div>
<div align="left"><b><font color="gray">Скачал:</font></b> <?=str_replace(" ", "&nbsp;", mksize($user['downloaded']))?></div>
<div align="left"><b><font color="gray">Рейтинг:</font></b> <?=$rating;?></div><br>
<h4>Личная информация </h4>
<?php
foreach(explode(",", $user["lovemovies"]) as $lov)
$love .= "<a style=\"font-weight:normal;\" href=\"browse.php?search=".$lov."\">".$lov."</a>, ";
                if ($love)
                $love = substr($love, 0, -2);
				if (isset($user["lovemovies"]))
?>
<div align="left"><b><font color="gray">Любимые фильмы:</font></b> <?=$love;?></div>
<div align="left"><b><font color="gray">О себе:</font></b> <?=htmlspecialchars($user["info"]);?></div>
</div>
</div>
<?php
begin_frame("Стена");
	print("<div id=\"wall\">\n");
$count = get_row_count("wall", "WHERE owner = $id");
$limited = 5;
list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "userdetails.php?id=$id&", array(lastpagedefault => 1));
$res = sql_query("SELECT w.*, u.username, u.class, u.avatar FROM wall AS w LEFT JOIN users AS u ON u.id = w.user WHERE w.owner = $id ORDER BY w.added $limit") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) < 1)
    print("<p>Нет записей.</p>\n");
else
{
    print("<table border=\"0\" width=\"100%\">\n");
    while ($row = mysql_fetch_array($res))
    {
        print("<tr class=\"zebra\" valign=\"top\">
            <td width=\"50\"><img src=\"" . ($row['avatar'] ? $row['avatar'] : "pic/default_avatar.gif") . "\" style=\"border:1px solid #999;padding:5px;width:50px;\" title=\"\" alt=\"\" /></td>
            <td>
                <div style=\"float:left;\"><a href=\"userdetails.php?id=" . $row['user'] . "\">" . get_user_class_color($row['class'], $row['username']) . "</a></div>
                <div style=\"float:right;\"><font size=\"1\" color=\"#C0C0C0\">" . nicetime($row['added'], true) . "&nbsp;" . (($CURUSER['id'] == $row['owner'] || $CURUSER['id'] == $row['user'] || get_user_class() >= UC_MODERATOR) ? "<a href=\"javascript:void(0);\" onclick=\"javascript:wall_del('" . $row['id'] . "', '" . $row['owner'] . "');\"><img src=\"pic/warned2.gif\" border=\"0\" /></a>" : "") . "</font></div><br />" . format_comment($row['text']) . "</td>
        </tr>\n");

    }
    print("</table>\n");
    print("<table border=\"0\">\n");
    print("<tr><td style=\"border:none;\">");
    print($pagertop);
    print("</td></tr>");
    print("</table>\n");
}
print("</div>");
print("<br>");
print("<form name=\"wall\">\n");
textbbcode("wall", "text",htmlspecialchars($text),$long);
print("<p><input type=\"button\" value=\"Отправить\" onclick=\"javascript:wall_send('$id', document.wall.text.value);\"/>&nbsp;&nbsp;<input type=\"reset\" value=\"Отменить\" /></p>\n");
print("</form>\n");
end_frame();
?>
</td></tr></table>
<?php
end_frame();
stdfoot();