<?php

require_once("include/bittorrent.php");
dbconn(false);
loggedinorreturn();

stdhead("Настройки аккаунта");
begin_frame("Настройки аккаунта");
if ($_GET["edited"]) {
	print("<h1>".$tracker_lang['my_updated']."</h1>\n");
	if ($_GET["mailsent"])
		print("<h2>".$tracker_lang['my_mail_sent']."</h2>\n");
}
elseif ($_GET["emailch"])
	print("<h1>".$tracker_lang['my_mail_updated']."</h1>\n");
print("<link rel=\"stylesheet\" href=\"css/pedit.css\" type=\"text/css\">\n");

switch($_GET['t'])
{
	case 'avatar':
	    $sect='avatar';
	break;
	case 'contact':
		$sect='contact';
	break;
	case 'about':
		$sect='about';
	break;
	case 'password':
		$sect='password';
	break;
	case 'profile':
	default:
		$sect='profile';
	break;

}
?>
<table width="100%" border="0px" cellspacing="0" cellpadding="5" align="center">
<tr>
<td colspan="3" border="0" style="padding: 0px; border: none;"><div id="tabs" border="0">
<?php if($sect=='profile') { ?><span class="tab active" id="profile">Профиль</span><?php } else { ?>
<a href="my.php?t=profile"><span class="tab" id="profile">Профиль</span></a><?php }
if($sect=='contact') { ?>
<span class="tab active" id="contact">Контактная информация</span> <?php } else { ?>
<a href="my.php?t=contact"><span class="tab" id="contact">Контактная информация</span></a> <?php }
if($sect=='about') { ?>
<span class="tab active" id="about">О себе</span> <?php }else { ?>
<a href="my.php?t=about"><span class="tab" id="about">О себе</span></a><?php }
if($sect=='avatar') { ?>
<span class="tab active" id="avatar">Фотография</span> <?php }else { ?>
<a href="my.php?t=avatar"><span class="tab" id="avatar">Фотография</span></a></div>
<?php } ?>
</td></tr>
<td colspan="3">
<?php if($sect!='avatar') { ?>
<form method="post" action="takeprofedit.php?t=<?=$sect;?>"> <?php } ?>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
<?php
require('include/profile/'.$sect.'.php');

function priv($name, $descr) {
	global $CURUSER;
	if ($CURUSER["privacy"] == $name)
		return "<input type=\"radio\" name=\"privacy\" value=\"$name\" checked=\"checked\" /> $descr";
	return "<input type=\"radio\" name=\"privacy\" value=\"$name\" /> $descr";
}


if($sect!='avatar') {
?>
<tr><td colspan="2" align="center"><input type="submit" value="Обновить профиль" style='height: 25px'> <input type="reset" value="Сбросить изменения" style='height: 25px'></td></tr>
<?php } ?>
</table>
</form>
</td>
</tr>
</table>
<?php
end_frame();
stdfoot();

?>