<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
parked();

function bark($msg) {
stderr("Ошибка", $msg);
}

$id = (int) $_GET["id"];
if (!isset($id) || !$id)
die();
$res = mysql_query("SELECT futurerls.id, futurerls.name, futurerls.userid, futurerls.comments, futurerls.trailer, futurerls.added, futurerls.realeasedate, futurerls.descr FROM futurerls WHERE futurerls.id = $id")or sqlerr();
$num = mysql_fetch_array($res);
mysql_free_result($res);
if (!$num) bark("Нет ожидаемого релиза с таким ID");

stdhead("Просмотр ожидаемого релиза");
begin_frame();

if($num[realeasedate] == '')
$num[realeasedate] = '<i>Неизвестно</i>';


$res2 = sql_query("SELECT id, username, class, enabled FROM users WHERE id=$num[userid]") or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_assoc($res2);

?>
<table cellspacing="1" cellpadding="5" border="0" class="tableinborder" style="width: 100%">
<?

if (get_user_class() >= UC_MODERATOR || $CURUSER["id"] == $num["userid"]){
$razd = '<td class=colhead align=center width=25%>Редактировать</td>';
$razd2 = '<form method=post action=futurerledit.php?id='.$id.'>';
$razd2 .= '<td align=center><input type=submit value=Редактировать style=height: 22px></form></br></td>';

$vipol = '<td class=colhead align=center width=20%>Выполнить</td>';
$vipolnit = '<form method=post action=futurerledit.php?act=take&id='.$id.'>';
$vipolnit .= '<td align=center><input type=submit value=Выполнить style=height: 22px></br></td>';
$perc = '20%';
}
else
{
$perc = '33%';
}
print("<tr><td class=\"colhead\" colspan=\"2\">".$num["name"]."</td></tr>");
print("<tr><td width=90% class=tablea align=left style=border:none;><a href=\"".$num['trailer']."\" rel=\"lightbox\"><img src=\"".$num['trailer']."\" border=\"0\" width=\"250\"></a></td></tr>");
print("<tr><td width=100% align=left style=border:none;>".format_comment($num['descr'])."</td></tr>");
print("<table width=100% cellpadding=5>");
print("<tr><td class=colhead align=center width=".$perc.">Добавил</td><td class=colhead align=center width=".$perc.">Добавлен</td><td class=colhead align=center width=".$perc.">Дата выхода</td>$razd $vipol</tr>");
print("<tr><td align=center><a href=userdetails.php?id=$user[id]>".get_user_class_color($user['class'], $user['username'])."</a></td><td align=center>$num[added]</td><td align=center>$num[realeasedate]</td>$razd2 $vipolnit</tr>");
print("</table>");

end_frame();
stdfoot();
die;
?>
