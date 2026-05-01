<?
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
stdhead("Администрация");
begin_main_frame('');
?>
<?
$act = $_GET["act"];
if (!$act) {
// Get current datetime
$dt = gmtime() - 300;
$dt = sqlesc(get_date_time($dt));
// Search User Database for Moderators and above and display in alphabetical order
$res = sql_query("SELECT * FROM users WHERE class>=".UC_UPLOADER." AND status='confirmed' ORDER BY username" ) or sqlerr(__FILE__, __LINE__);
while ($arr = mysql_fetch_assoc($res))
{

 $res1 = mysql_query("SELECT name,flagpic FROM countries WHERE id=$arr[country] LIMIT 1") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res1) == 1) 
{
  $arr1 = mysql_fetch_assoc($res1); 
  $country = "<img src=/pic/flag/$arr1[flagpic] alt=\"$arr1[name]\" style='margin-left: 8pt'>"; 
  $country_city = "<img src=/pic/flag/$arr1[flagpic] alt=\"$arr1[name] - $art[name]\" style='margin-left: 8pt'>"; 
  $countryy = "$arr1[name]"; 
   }

$staff_table[$arr['class']]=$staff_table[$arr['class']].
"<tr><td width=\"70%\" align=\"left\"><a class=altlink href=userdetails.php?id=".$arr['id']."><b>".
get_user_class_color($arr['class'],$arr['username'])."</b></a></td><td width=\"10%\" align=\"center\">".$country."</td><td width=\"10%\" align=\"center\"> ".("'".$arr['last_access']."'">$dt?"<img src=".$pic_base_url."button_online.gif border=0 alt=\"online\">":"<img src=".$pic_base_url."button_offline.gif border=0 alt=\"offline\">" )."</td>".
"<td width=\"10%\" align=\"center\"><a href=message.php?action=sendmessage&amp;receiver=".$arr['id'].">".
"<img src=".$pic_base_url."button_pm.gif border=0></a></td></tr>".
" ";

 

}
}
begin_frame("SYSOp");
print("<table width=100% cellspacing=\"0\" cellpadding=\"5\">");
?>
<?=$staff_table[UC_GOD]?>
<?
print("</table>");
end_frame();
begin_frame("Владельцы");
print("<table width=100% cellspacing=\"0\" cellpadding=\"5\">");
?>
<?=$staff_table[UC_SYSOP]?>
<?
print("</table>");
end_frame();
begin_frame("Администраторы");
print("<table width=100% cellspacing=\"0\" cellpadding=\"5\">");
?>
<?=$staff_table[UC_ADMINISTRATOR]?>
<?
print("</table>");
end_frame();
begin_frame("Модераторы");
print("<table width=100% cellspacing=\"0\" cellpadding=\"5\">");
?>
<?=$staff_table[UC_MODERATOR]?>
<?
print("</table>");
end_frame();
begin_frame("Релизеры");
print("<table width=100% cellspacing=\"0\" cellpadding=\"5\">");
?>
<?=$staff_table[UC_UPLOADER]?>
<?
print("</table>");
end_frame();
end_main_frame();
stdfoot();
?>



