<?
if (!defined('UC_GOD'))
	die('Direct access denied.');
?>
</td>
<td valign="top" width="190px" style="border:0;padding-right:0px;">
<?


$datum = getdate();

$datum[hours] = sprintf("%02.0f", $datum[hours]);

$datum[minutes] = sprintf("%02.0f", $datum[minutes]);

$datum[seconds] = sprintf("%02.0f", $datum[seconds]);

$uped = mksize($CURUSER['uploaded']);

$downed = mksize($CURUSER['downloaded']);

if ($CURUSER["downloaded"] > 0)

{

$ratio = $CURUSER['uploaded'] / $CURUSER['downloaded'];

$ratio = number_format($ratio, 3);

$color = get_ratio_color($ratio);

if ($color)

$ratio = "<font color=$color>$ratio</font>";

}

else

if ($CURUSER["uploaded"] > 0)

$ratio = "Inf.";

else

$ratio = "---";

if ($CURUSER['donor'] == "yes")
	$medaldon = "<img src=\"pic/star.gif\" alt=\"Донор\" title=\"Донор\">";
if ($CURUSER['warned'] == "yes")
	$warn = "<img src=\"pic/warned.gif\" alt=\"Предупрежден\" title=\"Предупрежден\">";


if ($CURUSER) {
if($unread) {
$messag = "<a class=\"menu\" href=\"message.php\">Мои сообщения (<b>$unread</b>)</a>";
}else{
$messag = "<a class=\"menu\" href=\"message.php\">Мои сообщения</a>";
}


	$userbar = "<a class=\"menu\" href=\"id".$CURUSER["id"]."\">Моя страница</a>
	<a class=\"menu\" href=\"friends.php\">Мои друзья";
$fetch=mysql_fetch_array(sql_query("SELECT COUNT(*) FROM friends WHERE friendid=".$CURUSER['id']." AND status = 'pending'"));
if($fetch&&$fetch[0]!=0) $userbar.=" (<b>".$fetch[0]."</b>)";
	$userbar.="</a><a class=\"menu\" href=\"subnet.php\">Мои соседи</a>";
	$userbar.="<a class=\"menu\" href=\"mynote.php\">Мои записи</a>";
	$userbar.=$messag."
	<a class=\"menu\" href=\"mybonus.php\">Мои бонусы (<b>$CURUSER[bonus]</b>)</a>
	<a class=\"menu\" href=\"mytorrents.php\">".$tracker_lang['my_torrents']."</a>
    <a class=\"menu\" href=\"my.php\">Мои настройки</a>";
} else {
	$userbar = '<table width="100%" border="0"><form method="post" action="takelogin.php">
<tr><td style="border:none;"><font class="small">E-Mail:</font>:</td><td style="border:none;"><input type="text" size=20 name="email"/></td></tr>
<tr><td style="border:none;"><font class="small">'.$tracker_lang['password'].'</font>:</td><td style="border:none;"><input type="password" size=20 name="password" /></td></tr>
<tr><td colspan="2" style="border:none;"><input type="submit" value="'.$tracker_lang['login'].'!" class=\"btn\"></td></tr></form>
<tr><td colspan="2" style="border:none;"><a class="menu" href="signup.php">'.$tracker_lang['signup'].'</a></td></tr></table>';
}

if ($CURUSER['override_class'] != 255) $usrclass = "&nbsp;<img src=\"pic/warning.gif\" title=".get_user_class_name($CURUSER['class'])." alt=".get_user_class_name($CURUSER['class']).">&nbsp;";

elseif(get_user_class() >= UC_MODERATOR) $usrclass = "&nbsp;<a href=\"setclass.php\"><img src=\"pic/warning.gif\" title=\"".get_user_class_name($CURUSER['class'])."\" alt=\"".get_user_class_name($CURUSER['class'])."\" border=\"0\"></a>&nbsp;";

	blok_menu("<div style=\"float:left;\">Моё меню</div><div style=\"float:right; margin-top: 4px;\"><a href=\"/rss.xml\" title=\"RSS-лента\"><img src=\"/pic/rss_boite.gif\" alt=\"RSS\" border=\"0\"></a></div>", $userbar, "userbar", yes, "155");

if ($CURUSER) {

	//$usermenu = "<a class=\"menu\" href=\"invite.php\">&nbsp;Пригласить</a>"
    $usermenu = "<a class=\"menu\" href=\"users.php\">&nbsp;Люди</a>"
           ."<a class=\"menu\" href=\"futurerls.php\">&nbsp;Скоро на трекере</a>"
		   ."<a class=\"menu\" href=\"pages.php\">&nbsp;Персоны кино</a>";
	if($CURUSER['class']>=UC_ADMINISTRATOR)
	$usermenu .= '<a class="menu" href="admin.php">&nbsp;Админпанель</a>';
		   
	blok_menu("Ваши функции", $usermenu , "usermenu", yes, "155");

}	
?> <script language="javascript" type="text/javascript" src="js/tags.js"></script><?
if((empty($_COOKIE['tagst'])||$_COOKIE['tagst']=='cloud')&&!is_bot())
{
begin_frame("<span id=\"tagcchead\">Облако <a href=\"javascript:tag_switch();\" title=\"Теги\"><img src=\"pic/r-arrow.gif\" alt=\"Теги\" title=\"Теги\" border=\"0\"></a></span>");
?> <div id="tagscc" style="overflow: auto; height: 190px;"></div>
<script language="javascript" type="text/javascript"> var tagNow = 'cloud'; tag_puts(); </script> <?
}
else
{
begin_frame("<span id=\"tagcchead\"><a href=\"javascript:tag_switch();\" title=\"Облако\"><img src=\"pic/l-arrow.gif\" alt=\"Облако\" title=\"Облако\" border=\"0\"></a> Теги</span>");
?> <div id="tagscc" style="overflow: auto; height: 190px;"></div>
<script language="javascript" type="text/javascript"> var tagNow = 'tags'; tag_puts(); </script> <?
}
end_frame();
//if($show_ad) {
begin_frame('Посетители');
?>
<div align="center"><img src="http://s03.flagcounter.com/count/tv7H/bg=FFFFFF/txt=000000/border=CCCCCC/columns=2/maxflags=10/viewers=0/labels=0/" alt="Страны посетителей" border="0"></div>
<? end_frame();// }
?>
</td>
<?
// Variables for End Time

//$phptime = 		$seconds - $querytime;
//$query_time = 	$querytime;
//$percentphp = 	number_format(($phptime/$seconds) * 100, 2);
//$percentsql = 	number_format(($querytime/$seconds) * 100, 2);
$seconds = 		number_format(substr(timer() - $tstart, 0, 8),3);
	print("</td></tr></table>\n");
	//print("<div id=\"footer\">\n");
	//print("<td width=\"49%\"><div align=\"center\"><br><a href=\"http://bt-star.ru\">bt-star.ru</a> &copy; 2008-".date("Y")." <br><b>Faris Grimm || ".sprintf($tracker_lang["page_generated"], $seconds, $queries, $percentphp, $percentsql)."<br>Хочу сказать огромное спасибо моей любимой Полине за вдохновение.</font></div></td>\n");
	print("<table width=\"50%\" style=\"margin-left: 35px;\"><tr>");
		?> <td style="border: 0;" align="right" valign="top" width="90px"><!--LiveInternet counter--><script type="text/javascript"><!--
document.write("<a href='http://www.liveinternet.ru/click' "+
"target=_blank><img src='http://counter.yadro.ru/hit?t23.1;r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";"+Math.random()+
"' alt='' title='LiveInternet: показано число посетителей за"+
" сегодня' "+
"border='0' width='88' height='15'><\/a>")
//--></script><!--/LiveInternet-->

</td>
<?
	print("<td align=\"left\" style=\"border: none;\"><a href=\"http://bt-star.ru\">bt-star.ru</a> &copy; 2008-".date("Y")." <br>Faris Grimm || ".sprintf($tracker_lang["page_generated"], $seconds, $queries)."<br>Хочу сказать огромное спасибо моей любимой Полине за вдохновение.</td>\n");
	print("</tr></table></body></html>\n");
?>
<!--GA-->
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-9010746-1");
pageTracker._trackPageview();
} catch(err) {}</script>
<!--/GA-->