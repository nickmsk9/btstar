<?php
require_once("include/bittorrent.php");
dbconn(false);
loggedinorreturn();

if (get_user_class() < UC_SYSOP)
stderr($tracker_lang['error'], $tracker_lang['access_denied']);
stdhead("Спамилка");
begin_frame("Спамилка");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="5">
<form method=post name=message action=takeemail.php>
<tr><td class="rowhead">Тема</td><td align="left"><input name="subject" type="text" size="70"></td></tr>
<tr><td class="rowhead">Описание</td><td align="left">
<?php textbbcode("message","msg",$body, 0); ?>
</td></tr>
<tr><td colspan="2" align="left"><input type=submit value="Отправить" class=btn>
</td></tr>
</form>
</table>
<?php
end_frame();
stdfoot();
?> 