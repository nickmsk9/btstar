<?php
// Файл: blog.php
// Здесь у нас начинается система блогов :)
require_once("include/bittorrent.php");
dbconn(false);
loggedinorreturn(true);
function bark($msg) {
	global $tracker_lang;
	stdhead("Записи || Ошибка");
	stdmsg($tracker_lang['error'], $msg);
	exit;
}

if(empty($_GET['uid'])||!is_numeric($_GET['uid']))
bark('Неверный ID');
$uid=(int)$_GET['uid'];

if(!empty($_GET['id']))
{	if(!is_numeric($_GET['id']))
		bark("Неверный ID");
	$id=(int)$_GET['id'];}

$sql="SELECT notes.*, users.username, users.last_access, users.class, users.avatar, users.country, users.firstname, users.surname, friends.id AS fid FROM notes LEFT JOIN users ON users.id=".$uid." LEFT JOIN friends ON friends.userid = ".$uid." AND friends.friendid = ".($CURUSER['id'] ? $CURUSER['id'] : $refid)." AND friends.status='yes' WHERE notes.uid = ".$uid;
if(empty($id))
$sql.=" AND notes.id=(SELECT id FROM notes WHERE uid = ".$uid." ORDER BY id LIMIT 1)";
else
$sql.=" AND notes.id = ".$id;
$note = @sql_query($sql) or bark("Неизвестная ошибка!");
if(!$note = mysql_fetch_assoc($note))
bark("Такой записи не существует!");
stdhead("Записи || ".$note['firstname']." ".$note['username']." ".$note['surname']." || ".htmlspecialchars($note['name']));
?> <script type="text/javascript" language="javascript" src="js/note.js"></script> <?php
begin_frame('Записи &rarr; <a href="userdetails.php?id='.$note['uid'].'">'.$note['firstname']." ".$note['username']."</a> &rarr; ".htmlspecialchars($note['name']));
if($note['access']==0 and (!$CURUSER['id']==$uid||!$note['fid']) and $CURUSER['class'] < UC_MODERATOR)
bark("Эту запись можно просматривать только друзьям ".$note['firstname']." ".$note['username']." ".$note['surname']."!");
?>
<table width="100%" border="0" cellpadding="5"><tr><td width="100px" valign="top" rowspan="2">
<?php if (!empty($note['avatar']))
    echo "<img src=\"" . $DEFAULTBASEURL.'/avatars/'.$note['avatar']."\" style=\"border:3px double #ccc;\" title=\"\" alt=\"Аватар\" align=\"left\">";
else
    echo "<img src=\"pic/default_avatar.gif\" style=\"width:100px;border:3px double #ccc;\" title=\"\" alt=\"Аватар\" align=\"left\">";
?>
</td><td valign="top">
<span style="font-size: 18pt; font-weight: bold;"><?=htmlspecialchars($note['name']);?></span><br>Запись <a href="userdetails.php?id=<?=$uid;?>"><?=$note['firstname'];?> <?=$note['username'];?> <?=$note['surname'];?></a> от <b><?=nicetime($note['timestamp']);?>
<?php if(!empty($note['last_edit'])) { ?><br><small>Последняя правка: <?=nicetime($note['last_edit'],true);?><?php } ?>
<br>
<span style="color: gray;font-weight: normal;"><?php if($note['access']==1) { ?>Это открытая запись, её может просматривать любой пользователь<?php } else { ?>Это закрытая запись, только для друзей автора <?php } ?></span>
<br>
<?php if(!empty($note['torrents'])) {
	$torrents = explode(',',$note['torrents']);
	$tors=array();
	foreach($torrents as $torrent)
		if(is_numeric(trim($torrent)))
			$tors[]=$torrent;
	if(count($tors))
	{	$torrents = '';
		$sql=sql_query("SELECT id, name FROM torrents WHERE id IN (".implode(', ',$tors).")");
		while($torrent = mysql_fetch_array($sql))
			$torrents .= (!empty($torrents) ? ', ' : '').'<a href="torrent-'.$torrent['id'].'" style="color:blue;font-weight:normal;" class="tag-torrent">'.$torrent['name'].'</a>';
	}
	if(!empty($torrents))
	echo "<div>Торренты этой записи: ".$torrents."</div>";
}
if(!empty($note['tags'])) { ?><div style="float: left;">Теги: <?php
$tags=explode(',',$note['tags']);
$i=0;
foreach ($tags as $tag)
{echo ($i!=0 ? ', ' : '').'<a href="notetag.php?tag='.urlencode(trim($tag)).'" style="color:green;font-weight:normal;">'.trim($tag).'</a>';
$i++;}
echo "</div>";
}
if($CURUSER['id']==$uid||$COURUSER['class']>=UC_MODERATOR)
{
?>
<div style="float:right">
<a href="noteedit.php?uid=<?=$uid;?>&id=<?=$note['id'];?>&act=edit">[Редактировать]</a> <a href="noteedit.php?uid=<?=$uid;?>&id=<?=$note['id'];?>&act=delete">[Удалить]</a> 
</div>
<?php } ?>
</td></tr><tr><td valign="top">
<br>
<span align="justify" style="font-weight: normal;">
<?=format_comment($note['text']);?>
</span>
</td></tr></table>
<?php
end_frame();
sql_query("UPDATE IGNORE `notes` SET `views` = `views` + 1 WHERE `uid` = ".$uid." AND `id` = ".$id);
begin_frame("Комментраии");
	print("<div id=\"wall\">\n");
$count = get_row_count("noteswall", "WHERE owner = $uid AND nid= $id");
$limited = 25;
list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "note".$uid."-".$id.",", array(lastpagedefault => 1));
$res = sql_query("SELECT w.*, u.username, u.class, u.avatar, u.gender FROM noteswall AS w LEFT JOIN users AS u ON u.id = w.user WHERE w.owner = $uid AND w.nid = $id ORDER BY w.added $limit") or sqlerr(__FILE__,__LINE__);
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
                <div style=\"float:right;\"><font size=\"1\" color=\"#C0C0C0\">" . nicetime($row['added'], true) . "&nbsp;" . (($CURUSER['id'] == $row['owner'] || $CURUSER['id'] == $row['user'] || get_user_class() >= UC_MODERATOR) ? "<a href=\"javascript:void(0);\" onclick=\"javascript:wall_del('" . $row['id'] . "');\"><img src=\"pic/warned2.gif\" border=\"0\" /></a>" : "") . "</font></div><br /><div style=\"border-bottom: 1px solid #DCDCDC;\"></div>" . format_comment($row['text']) . "</td>
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
print("<p><input type=\"button\" value=\"Отправить\" onclick=\"javascript:wall_send('$uid','$id', document.wall.text.value);\"/>&nbsp;&nbsp;<input type=\"reset\" value=\"Отменить\" /></p>\n");
print("</form>\n");
end_frame();
stdfoot();
?>
