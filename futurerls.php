<?php
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn(true);

if($action == addfuturerl){
if (get_user_class() < UC_POWER_USER)
stderr($tracker_lang['sorry'], "Только пользователи со статусом опытный пользователь и выше могут добавлять ожилаемые релизы.");
stdhead("Добавить ожидаемый релиз");
begin_frame("Добавить ожидаемый релиз");
print("<form name=hhhhh method=post action=takefuturerl.php>\n");
?>
<table align="center" class="embedded" width="550" border="1" cellspacing="0" cellpadding="5">
<tr><td class="rowhead">Название:</td><td align="left"><input type='text' name='name' size='80' /></td></tr>
<tr><td class="rowhead">Дата выхода:</td><td align="left"><input type='text' name='realeasedate' size='40' /></td></tr>
<tr><td class="rowhead">Постер:</td><td align="left"><input type='text' name='trailer' size='80' /></td></tr>
<tr><td class="rowhead">Описание:</td><td align="left">
<?
textbbcode("upload","descr","", 0);
?>
</td></tr>
<?
$s = "<select name=\"type\">\n<option value=\"0\">(".$tracker_lang['choose'].")</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$s .= "</select>\n";
print("<tr><td class='rowhead'>Категория:</td><td align='left'> ".$s." </td></tr>");
print("<tr><td colspan='2'><input type=submit class=btn value='Добавить'></td></tr>\n");
print("</form>\n");
echo "</table>";
end_frame();
stdfoot();
die();
}

stdhead("Ожидаемые релизы");
$res2 = sql_query("SELECT count(id) FROM futurerls") or die(mysql_error());
$row = mysql_fetch_array($res2);
$url = " .$_SERVER[PHP_SELF]?";
$count = $row[0];
$perpage = 15;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $url);
begin_frame("Ожидаемые релизы");
if (get_user_class() >= UC_POWER_USER){
?>
<center></br>
<a href=futurerls.php?action=addfuturerl>Добавить новый ожидаемый релиз</a>
</center></p>
<?
}
print("</br>");
if ($count == 0)
print("<p align=center><b>Извините тут ничего, нет :(</b></p>\n");
else
{
print("<table width=100% cellspacing=0 cellpadding=5>\n");
print("<tr><td class=colhead align=center>Тип</td><td class=colhead align=left> Название / Добавлен</td><td align=center class=colhead>Дата выхода</td><td align=center class=colhead>Скачать</td><td align=center class=colhead>Добавил</td></tr>\n");
$res = sql_query("SELECT futurerls.name, futurerls.id, futurerls.userid, futurerls.download, futurerls.added, futurerls.realeasedate, users.username, users.class, users.enabled, categories.image FROM futurerls left join users on users.id=futurerls.userid left join categories on categories.id=futurerls.cat ORDER BY futurerls.added DESC $limit") or sqlerr();
while ($arr = mysql_fetch_assoc($res))
{
if($arr[realeasedate] == '')
$realeasedate = '<i>Неизвестно</i>';
else
$realeasedate = $arr[realeasedate];

if($arr[download] == '0')
$download = '<i>Нет на трекере</i>';
else
$download = '<a href=details.php?id='.$arr["download"].'>Скачать</a>';

print("<tr><td align=center><img src=/pic/cats/$arr[image]></a><td><a href=futurerldetails.php?id=$arr[id]>$arr[name]</a></br>$arr[added]</td><td align=center>$realeasedate</td><td align=center>$download</td><td align=center><a href=userdetails.php?id=$arr[userid]><b>".get_user_class_color($arr['class'],$arr['username'])."</a></td></tr>\n");
}
print("</table>\n");
print("</br>");
echo $pagerbottom;
}
end_frame();
stdfoot();
?>
