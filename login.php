<?php
require_once("include/bittorrent.php");

dbconn();

if ($CURUSER)
	stderr($tracker_lang['error'], "Вы уже вошли на $SITENAME!");

stdhead("Вход");
begin_frame("Вход на сайт");
unset($returnto);
if (!empty($_GET["returnto"])) {
	$returnto = $_GET["returnto"];
	if (!$_GET["nowarn"]) {
		$error = "<tr><td colspan=\"2\"><div class=\"error\">К сожалению страница, которую вы пытаетесь посмотреть <b>доступна только вошедшим в систему</b>.<br />После успешного входа вы будете переадресованы на запрошеную страницу.</div></td></tr>";
		//print("<h1>Не авторизированы!</h1>\n");
		//print("<p><b>Ошибка:</b> Страница, которую вы пытаетесь посмотреть, доступна только зарегистрированым.</p>\n");
	}
}

?>
<form method="post" action="takelogin.php">
<table border="0" cellpadding="5" width="100%">
<?php
if (isset($error)) {
	echo $error;
}
?>
<tr><td class="rowhead">E-Mail:</td><td align="left"><input type="text" size="40" name="email" style="width: 200px; border: 1px solid gray" /></td></tr>
<tr><td class="rowhead">Пароль:</td><td align="left"><input type="password" size="40" name="password" style="width: 200px; border: 1px solid gray" /></td></tr>
<tr><td colspan="2" align="left"><input type="submit" value="Войти" class="btn"></td></tr>
</table>
<?php

if (isset($returnto))
	print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars_uni($returnto) . "\" />\n");

?>
</form>
<div class="success" align="left">Если Вы забыли пароль или Вы не можете зайти - попытайтесь воспользоваться формой <a href="recover.php">восстановления паролей</a><br>
Ещё не зарегистрированы ? Вы можете <a href="signup.php">зарегистрироваться</a> прямо сейчас!</div>

<?php
end_frame();
stdfoot();

?>