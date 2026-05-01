<?
require_once("include/bittorrent.php");
dbconn();

header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'GET')
{
//if ($CURUSER)
//print("<div class=\"error\" align=\"left\"><b>Вы уже вошли на BT-Star.</b></div>");
print('<form method="post" action="takelogin.php">');
print('<br><table border="0" cellpadding="5" width="100%">');
//print('<tr><td colspan="2"><a href="#" style="font-weight: normal;" onclick="javascript:this.style.display=\'none\';document.getElementById(\'ss4\').innerHTML='';">Свернуть</a></td></tr>');
print('<tr><td class="rowhead">E-Mail:</td><td align="left"><input type="text" size="40" name="email" style="width: 200px; border: 1px solid gray" /></td></tr>');
print('<tr><td class="rowhead">Пароль:</td><td align="left"><input type="password" size="40" name="password" style="width: 200px; border: 1px solid gray" /></td></tr>');
if(!empty($_COOKIE['return']))
print('<input type="hidden" value="'.urldecode($_COOKIE['return']).'" name="returnto">');
print('<tr><td colspan="2" align="center"><input type="submit" value="Войти" class="btn"></td></tr>');
print('</table>');
}

?>