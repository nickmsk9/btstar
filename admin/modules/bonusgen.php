<?php
if (!defined("ADMIN_FILE")) die("Illegal File Access");

if (get_user_class() < UC_SYSOP)
	stderr($tracker_lang['error'], "Доступ закрыт!");
	
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();

function bark($msg) {
	stderr("Произошла ошибка", $msg);
}

function swd_rand($length = 10, $letters = true, $numbers = true, $case = 'i')
{
    $chars = array();
    
    if ($numbers)
    {
        $chars = array_merge($chars, range(48, 57));
    }

    if ($letters OR !$numbers)
    {
        $chars = array_merge($chars, range(65, 90), range(97, 122));
    }
    
    for ($string = ''; strlen($string) < $length; $string .= chr($chars[array_rand($chars)]))
	{
	if ((strlen($string)==4) || (strlen($string)==10) || (strlen($string)==16))	$string .= "-";	
	}	
	
    switch ($case)
    {
        case 'i': default: return $string;
        case 'u': return strtoupper($string);
        case 'l': return strtolower($string);
    }
}

function UniqueSerial($s){
$res = sql_query("SELECT pid FROM bonusgen WHERE pid = '".$s."'");
$row = mysql_fetch_array($res);

if (!$row)
{
return $s;
}else{
UniqueSerial(swd_rand(21));
}

}

function BonusGenerate() {
global $admin_file;

$count = get_row_count("bonusgen");
if (!$count) {
$empty = 0;
} else {
$empty = 1;
}
echo "<form method=\"post\" action=\"".$admin_file.".php?op=BonusGen\">"
		."<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\">"
		."<tr><td class=\"colhead\" colspan=\"2\">Генерация бонуса</td></tr>"
		."<tr>"
		."<td><b>Кол-во бонусов</b></td>"
		."<td><input name=\"bonus\" type=\"text\"></td>"
		."</tr>"		
		."<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"bsub\" value=\"Генерировать\"></td></tr>"
		."</table>"
		//."<input type=\"hidden\" name=\"op\" value=\"BonusGen\" />"
		."</form>";

echo "<form action=\"".$admin_file.".php?op=BonusGen\" method=\"post\" name=\"form1\">"
."<p><p><table border=\"0\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">"
		."<tr><td class=\"colhead\"><center>Код бонуса</center></td><td class=\"colhead\"><center>Бонус</center></td><td class=\"colhead\"><center>Владелец</center></td><td class=\"colhead\"><center>Статус</center></td><td class=\"colhead\"><center><INPUT type=\"checkbox\" title=\"Выбрать все\" value=\"Выбрать все\" onClick=\"this.value=check(document.form1.elements);\"></center></td></tr>";

if ($empty){

   $res = sql_query("SELECT * FROM bonusgen ORDER BY activated DESC") or sqlerr(__FILE__, __LINE__);
   while ($row = mysql_fetch_array($res)) {

   $id = $row["id"];
   $pid = $row["pid"];
   $bonus = $row["bonus"];
   
   if ($row["activated"]=='yes'){
   $activated = "<b><font color=red>Активирован</font></b>";
   }else{
   $activated = "<b><font color=green>Свободен</font></b>";
   }
   
   $owner = $row["owner"]; 
   
   echo "<tr><td align='center'>".$pid."</td>"
   ."<td align='center'>".$bonus."</td>"
   ."<td align='center'>".$owner."</td>"
   ."<td align='center'>".$activated."</td>"
   ."<td align='center'><INPUT type=\"checkbox\" name=\"bonusid[]\" title=\"Выбрать\" value=\"".$id."\" id=\"checkbox_tbl_".$id."\"></td></tr>";
   } 

}else{
echo "<tr><td align='center' colspan='5'>Список кодов пуст...</td></tr>";
}		
		
echo "<td class=\"colhead\" colspan=\"5\"><div align=right><input type=\"submit\" name=\"delete\" value=\"Удалить выбранное\" onClick=\"return confirm('Вы уверены?')\"></div></td>"
		."</table></form>";

if ($_POST['delete'] && $_POST['bonusid']) {
   $bonusid = $_POST['bonusid'];
   foreach ($bonusid as $id) {
   sql_query("DELETE FROM bonusgen WHERE id=" . sqlesc((int) $id)) or sqlerr(__FILE__,__LINE__);
   }
header("Location: ".$admin_file.".php?op=BonusGen");   
}

if ($_POST['bsub']) {
 if (!$_POST['bonus'])
 {
bark("Введите желаемое колличество бонусов");
 }else{
   $bonus = $_POST['bonus'];
   if (!preg_match("/^[0-9.]+$/", $bonus)) bark("Можно использовать только чистовое значение");
   }
   $string = UniqueSerial(swd_rand(21));
   sql_query("INSERT INTO bonusgen (pid,bonus) VALUES ('".$string."',".$bonus.")")  or sqlerr(__FILE__,__LINE__);  
   
header("Location: ".$admin_file.".php?op=BonusGen");   
}
	
}

switch ($op) {
	case "BonusGen":
	BonusGenerate();
	break;
}

?>
<script language="Javascript" type="text/javascript">
        <!-- Begin
        var checkflag = "false";
        var marked_row = new Array;
        function check(field) {
                if (checkflag == "false") {
                        for (i = 0; i < field.length; i++) {
                                field[i].checked = true;}
                                checkflag = "true";
                        }
                else {
                        for (i = 0; i < field.length; i++) {
                                field[i].checked = false; }
                                checkflag = "false";
                        }
                }
                //  End -->
        </script>