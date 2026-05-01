<?
require_once("include/bittorrent.php");
function bark($msg) {
	stdmsg("Произошла ошибка", $msg, 'error');
}
dbconn();
loggedinorreturn();

if (get_user_class() < UC_USER){
    bark("У Вас нет прав для просмотра этой страницы.");
    }

stdhead("Активация кода бонусов");

if (isset($_POST['code']) && $_POST['code']){

if ($_POST['csub'] && $_POST['code']) {

$s=$_POST['code'];

$res = sql_query("SELECT * FROM bonusgen WHERE pid = '".$s."'");
$row = mysql_fetch_array($res);

if (!$row)
{
bark("Код не верный. <a href=\"bonuscode.php\">Повторить</a>");
}else{
if ($row['activated']=="no") {
if (!sql_query("UPDATE users SET bonus = bonus + ".$row['bonus']." WHERE id = ".sqlesc($CURUSER["id"]))) {
				stderr("Ошибка", "Не могу обновить бонус!");
				die();
			}elseif (!sql_query("UPDATE bonusgen SET owner='".$CURUSER['username']."',activated='yes' WHERE pid='".$s."'")) {
			stderr("Ошибка", "Не могу обновить бонус!");
			die();
			}
stdmsg($tracker_lang['success'], "Код успешно активирован!<p>Полученный бонус : <b>".$row['bonus']."</b>");

write_log($CURUSER[username]." активировал код на получение ".$row['bonus']." бонуса(ов)","FFAE00","tracker");

$sender_id = 0;
$clases = array(UC_ADMINISTRATOR,UC_SYSOP,UC_GOD);
$subject ="Бонусный код активирован";
$msg .= "Пользователь [url=userdetails.php?id=".$CURUSER['id']."]".$CURUSER['username']."[/url] активировал код пополнения бонусов :\n\n"
	."[b]Код пополнения:[/b] ".$s
	."\n[b]Полученный бонус :[/b] ".$row['bonus']
	."\n[b]Статус на момент активации : [/b]";
	
if ($row["activated"]=='yes'){
   $msg .= "[color=red]Активирован[/color]";
   }else{
   $msg .= "[color=green]Свободен[/color]";
   }
   $msg.="\n\n[i]PS. Обратите внимание, если статус на момент активации кода равен [b]Активирован[/b]. В этом случае скорее всего пользователь является читером и использует какой-либо баг в данном модуле.[/i]";

sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) SELECT $sender_id, id, NOW(), ".sqlesc($msg).", ".sqlesc($subject)." FROM users WHERE class IN (".implode(", ", array_map("sqlesc", $clases)).")") or sqlerr(__FILE__,__LINE__);

}else{
bark("Код уже активирован. Введите другой код. <a href=\"bonuscode.php\">Повторить</a>");
}

}
}


}else{
begin_frame("Активация бонусного кода");
echo "<form method=\"post\" action=\"bonuscode.php\">"
	."<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">"
	."<tr>"
	."<td><b>Код</b></td>"
	."<td><input name=\"code\" type=\"text\" SIZE=\"50\"></td>"
	."</tr>"		
	."<tr><td colspan=\"2\" align=\"left\"><input type=\"submit\" name=\"csub\" value=\"Получить бонус\"></td></tr>"
	."<tr><td colspan=\"2\" class=\"success\"><b><li>-&nbsp;Все коды детально отслеживаются</li><li>-&nbsp;Подбор ваучеров (перебором) будет рассматриватся как намеренное вредительство ресурсу.</li></b></td></tr>"
	."</table>"
	."</form><p><p><p>";
end_frame();
}
stdfoot();

?> 
<style type="text/css">
.agree {
  width: 60% 
}
</style>