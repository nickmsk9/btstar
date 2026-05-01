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
  
$r = @sql_query("SELECT u.*" . ($CURUSER ? ", (SELECT COUNT(*) FROM karma WHERE type='user' AND value = u.id AND user = $CURUSER[id]) AS canrate" : "") . " FROM users u WHERE id=$id") or sqlerr(__FILE__, __LINE__);
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
	if($rating > 1) $percent = 100;
	else $percent = $rating*100;
	$rate = $rating;
    $rating = "<font color=\"" . get_ratio_color($rating) . "\">" . number_format($rating, 2) . "</font>";
	$rbar = '<div style="position: relative; left: 30%; top: 0px;">'.$rate.'</div><div style="display: block;"><div style="background-color:rgb(218, 226, 232); float:left;height:16px;" width="'.$percent.'">&nbsp;</div><div style="background-color: white; float: left; height: 16px;" width="'.(100-$percent).'">&nbsp;</div></div>';
}
else {
    $rating = "N/A";
	$rbar = '<div width="100%" style="background: transparent;">'.$rating.'</div>';
}

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
    $avatar = "<img src=\"" . $DEFAULTBASEURL.'/avatars/'.$user['avatar']."\" style=\"border:3px double #ccc;\" title=\"\" alt=\"\" />";
else
    $avatar = "<img src=\"pic/default_avatar.gif\" style=\"width:100px;border:3px double #ccc;\" title=\"\" alt=\"\" />";

if ($user["gender"] == "1") $gender = "Мужской";
elseif ($user["gender"] == "2") $gender = "Женский";

if ($user['last_access'] > (get_date_time(gmtime() - 300)))
    $status = "<font color=\"lightgray\">(Онлайн)</font>";
else
    $status = "";

stdhead("Просмотр профиля " . $user["username"]);
$enabled = $user["enabled"] == 'yes';
if (!$CURUSER || $user["canrate"] > 0 || $CURUSER['id'] == $user['id'])
begin_frame("<div style=\"float: left;\">".$user["firstname"]."&nbsp;".$user["username"]."&nbsp;".$user["surname"]."&nbsp;".$status."</div><div style=\"float: right;\"><img src=\"pic/minus-dis.png\" title=\"Вы не можете голосовать\" alt=\"\" /> " . karma($user["karma"]) . " <img src=\"pic/plus-dis.png\" title=\"Вы не можете голосовать\" alt=\"\" /></div>");
else
begin_frame("<div style=\"float: left;\">".$user["firstname"]."&nbsp;".$user["username"]."&nbsp;".$user["surname"]."&nbsp;".$status."</div><div id=\"karma$id\" style=\"float: right;\" ><img src=\"pic/minus.png\" style=\"cursor:pointer;\" title=\"Уменьшить карму\" alt=\"\" onclick=\"javascript: karma('$id', 'user', 'minus');\" /> " . karma($user["karma"]) . " <img src=\"pic/plus.png\" style=\"cursor:pointer;\" onclick=\"javascript: karma('$id', 'user', 'plus');\" title=\"Увеличить карму\" alt=\"\" /></div>");
print("<table width=100% border=0 cellspacing=0 cellpadding=5><tr><td valign='top' align='left' width='100px' style='border:none'>");
print("".$avatar."");
//print('<br><div width="100%" style="padding: 2px; border: 1px solid gray; margin: 2px; margin-top: 4px; background-color: #ddd;">'.$rbar.'</div>');
if ($CURUSER['id'] != $id)
{
    print("<a href=\"pmto-$id\" class=\"menu\">Личное сообщение</a>");
    $res = sql_query("SELECT id FROM friends WHERE userid=" . sqlesc($CURUSER['id']) . " AND friendid = $id AND status = 'yes'") or sqlerr(__FILE__, __LINE__);
    if (mysql_num_rows($res) > 0)
        print("<a href=\"javascript:void(0);\" onclick=\"javascript:addtofriends('$id', 'delete');\" class=\"menu\">Удалить из друзей</a>\n");
    else
        print("<a href=\"javascript:void(0);\" onclick=\"javascript:addtofriends('$id', 'add');\" class=\"menu\">Добавить в друзья</a>\n");
		 if (get_user_class() >= UC_MODERATOR && $user["class"] < get_user_class())
        print("<a href=\"javascript:void(0);\" onclick=\"javascript:moderate('$id');\" class=\"menu\">Модерирование</a>\n");
}
print("<link rel=\"stylesheet\" href=\"css/user.css\" type=\"text/css\">\n");
print("<script language=\"JavaScript\" src=\"js/user.js\" type=\"text/javascript\"></script>\n");
?>
<td class='outer' valign='top' align='left' style='border:none'>
<div id="actions"></div><div id="tabs">
<span class="tab active" id="info">Общее</span>
<span class="tab" id="friends">Друзья</span>
<span class="tab" id="downloaded">Скачал</span>
<span class="tab" id="uploaded">Загрузил</span>
<span class="tab" id="notes">Записи</span>
<span id="loading"></span>
<div id="body" user="<?=$user["id"];?>">
<h4>Основная информация <?php if ($user['id']==$CURUSER['id']) { ?><a href="my.php?t=profile" style="color: gray;">[Редактировать]</a> <?php } ?></h4>
<div align="left"><b><font color="gray">Имя:</font></b> <b><?=($user['firstname'].' <i>'.$user['username'].'</i> '.$user['surname']);?></b></div>
<div align="left"><b><font color="gray">Класс:</font></b> <?=get_user_class_color($user['class'], get_user_class_name($user['class']))?></div>
<div align="left"><b><font color="gray">Пол:</font></b> <?=$gender;?></div>
<?php if($user['birthday']!='0000-00-00') { ?>
<div align="left"><b><font color="gray">Дата рождения:</font></b> <?=nicetime($user["birthday"]);?> года (<?=(date("Y")-date("Y",strtotime($user['birthday']))+((date("m")>date("m",strtotime($user['birthday']))||(date("m")==date("m",strtotime($user['birthday']))&&date("d")>=date("d",strtotime($user['birthday'])))) ? 1 : 0)-1);?>)</div><br>
<?php } ?>
<h4>Контактная информация <?php if ($user['id']==$CURUSER['id']) { ?><a href="my.php?t=contact" style="color: gray;">[Редактировать]</a> <?php } ?></h4>
<div align="left"><b><font color="gray">Страна:</font></b> <?=$arr["name"];?></div>
<div align="left"><b><font color="gray">Город:</font></b> <a href="users.php?city=<?=$user['city'];?>" title="Найди людей из этого города"><?=$city;?></a></div>
<?php if ($user["icq"]){?>
<div align="left"><b><font color="gray">Номер ICQ:</font></b> <img src="email_img.php?uid=<?=$user['id'];?>&type=2" alt="ICQ пользователя <?=$user['name'];?>"></div><?php } ?>
<?php if ($user['website']) { ?>
<div align="left"><b><font color="gray">Сайт:</font></b> <a href="goto.php?link=<?=urlencode(urlencode($user['website']));?>"><?=$user['website'];?></a></div><?php } ?><br>
<h4>Статистика на трекере</h4>
<div align="left"><b><font color="gray">Раздал:</font></b> <?=str_replace(" ", "&nbsp;", mksize($user['uploaded']))?></div>
<div align="left"><b><font color="gray">Скачал:</font></b> <?=str_replace(" ", "&nbsp;", mksize($user['downloaded']))?></div>
<div align="left"><b><font color="gray">Рейтинг:</font></b> <?=$rating;?></div><br>
<h4>Личная информация <?php if ($user['id']==$CURUSER['id']) { ?><a href="my.php?t=about" style="color: gray;">[Редактировать]</a> <?php } ?></h4>
<?php
foreach(explode(",", $user["lovemovies"]) as $lov)
$love .= "<a style=\"font-weight:normal;\" href=\"browse.php?search=".$lov."\">".$lov."</a>, ";
                if ($love)
                $love = substr($love, 0, -2);
				if ($user["lovemovies"]) {
?>
<div align="left"><b><font color="gray">Любимые фильмы:</font></b> <?=$love;?></div> <?php } ?>
<div align="left"><b><font color="gray">О себе:</font></b> <?=(!empty($user['info']) ? str_replace("\n","<br>",htmlspecialchars($user["info"])) : '<i>Пользователь не заполнил информации о себе</i>');?></div>
</div>
</div>
<?php
begin_frame("Стена");
	print("<div id=\"wall\">\n");
$count = get_row_count("wall", "WHERE owner = $id");
$limited = 25;
list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "userdetails.php?id=$id&", array(lastpagedefault => 1));
$res = sql_query("SELECT w.*, u.username, u.class, u.avatar, u.gender FROM wall AS w LEFT JOIN users AS u ON u.id = w.user WHERE w.owner = $id ORDER BY w.added $limit") or sqlerr(__FILE__,__LINE__);
if (mysql_num_rows($res) < 1)
    print("<p>Нет записей.</p>\n");
else
{
    print("<table border=\"0\" width=\"100%\">\n");
    while ($row = mysql_fetch_array($res))
    {
	if ($row["gender"] == "1") $genders = "написал";
elseif ($row["gender"] == "2") $genders = "написала";
        print("<tr class=\"zebra\" valign=\"top\">
            <td width=\"50\" style=\"border: none;\"><img src=\"" . ($row['avatar'] ? $DEFAULTBASEURL.'/avatars/small/'.$row['avatar'] : "pic/default_avatar.gif") . "\" style=\"border:1px solid #999;padding:5px;width:50px;\" title=\"\" alt=\"\" /></td>
            <td style=\"border: none;\">
			    <div style=\"border-top: 1px solid #516A88;\">
                <div style=\"float:left;\"><a href=\"id" . $row['user'] . "\">" . get_user_class_color($row['class'], $row['username']) . "</a><font color=\"#C0C0C0\">&nbsp;".$genders."<br></font></div>
                <div style=\"float:right;\"><font size=\"1\" color=\"#C0C0C0\">" . nicetime($row['added'], true) . "&nbsp;" . (($CURUSER['id'] == $row['owner'] || $CURUSER['id'] == $row['user'] || get_user_class() >= UC_MODERATOR) ? "<a href=\"javascript:void(0);\" onclick=\"javascript:wall_del('" . $row['id'] . "', '" . $row['owner'] . "');\"><img src=\"pic/warned2.gif\" border=\"0\" /></a>" : "") . "</font></div><br /><div style=\"border-bottom: 1px solid #DCDCDC;\"></div>" . format_comment($row['text']) . "</td>
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