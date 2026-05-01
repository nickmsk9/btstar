<?php
$dir = opendir('languages');
        $lang = array();
        while ( $file = readdir($dir) ) {
                if (preg_match('#^lang_#i', $file) && !is_file($dir . '/' . $file) && !is_link($dir . '/' . $file)) {
                        $filename = trim(str_replace("lang_","", $file));
                        $displayname = preg_replace("/^(.*?)_(.*)$/", "\\1 [ \\2 ]", $filename);
                        $displayname = preg_replace("/\[(.*?)_(.*)\]/", "[ \\1 - \\2 ]", $displayname);
                        $lang[$displayname] = $filename;
                }
        }
        closedir($dir);
        @asort($lang);
        @reset($lang);

        $lang_select = '<select name="language">';
        while ( list($displayname, $filename) = @each($lang) ) {
                $selected = ((strtolower($CURUSER["language"]) == strtolower($filename) ) ? ' selected="selected"' : '');
                $lang_select .= '<option value="' . $filename . '"' . $selected . '>' . ucwords($displayname) . '</option>';
        }
        $lang_select .= '</select>';

function format_tz($a)
{
	$h = floor($a);
	$m = ($a - floor($a)) * 60;
	return ($a >= 0?"+":"-") . (strlen(abs($h)) > 1?"":"0") . abs($h) .
		":" . ($m==0?"00":$m);
}

tr("Ваше имя", "<input type=\"text\" size=\"40\" name=\"fname\" id=\"fname\" value=\"".$CURUSER["firstname"]."\">", 1);
tr("Ваша фамилия", "<input type=\"text\" size=\"40\" name=\"sname\" id=\"sname\" value=\"".$CURUSER["surname"]."\">", 1);
tr("Ваш ник", "<input type=\"text\" size=\"40\" name=\"username\" id=\"username\" value=\"".$CURUSER['username']."\">", 1);

tr($tracker_lang['my_allow_pm_from'],
"<input type=radio name=acceptpms" . ($CURUSER["acceptpms"] == "yes" ? " checked" : "") . " value=\"yes\">Все (исключая блокированных)
&nbsp;<input type=radio name=acceptpms" .  ($CURUSER["acceptpms"] == "friends" ? " checked" : "") . " value=\"friends\">Только друзей
&nbsp;<input type=radio name=acceptpms" .  ($CURUSER["acceptpms"] == "no" ? " checked" : "") . " value=\"no\">Только администрации"
,1);

tr($tracker_lang['my_parked'],
"<input type=\"radio\" name=\"parked\"" . ($CURUSER["parked"] == "yes" ? " checked" : "") . " value=\"yes\">".$tracker_lang['yes']."
<input type=\"radio\" name=\"parked\"" . ($CURUSER["parked"] == "no" ? " checked" : "") . " value=\"no\">".$tracker_lang['no']."
<br /><font class=\"small_text\">".$tracker_lang['my_you_can_park'].".</font>"
,1);

tr($tracker_lang['my_delete_after_reply'], "<input type=checkbox name=deletepms" . ($CURUSER["deletepms"] == "yes" ? " checked" : "") . ">",1);
tr($tracker_lang['my_sentbox'], "<input type=checkbox name=savepms" . ($CURUSER["savepms"] == "yes" ? " checked" : "") . ">",1);

$r = sql_query("SELECT id,name FROM categories ORDER BY sort") or sqlerr(__FILE__, __LINE__);
//$categories = "Default browsing categories:<br />\n";
if (mysql_num_rows($r) > 0)
{
	$categories .= "<table><tr>\n";
	$i = 0;
	while ($a = mysql_fetch_assoc($r))
	{
	  $categories .=  ($i && $i % 2 == 0) ? "</tr><tr>" : "";
	  $categories .= "<td class=bottom style='padding-right: 5px'><input name=cat$a[id] type=\"checkbox\" " . (strpos($CURUSER['notifs'], "[cat$a[id]]") !== false ? " checked" : "") . " value='yes'>&nbsp;" . htmlspecialchars($a["name"]) . "</td>\n";
	  ++$i;
	}
	$categories .= "</tr></table>\n";
}

tr($tracker_lang['my_default_browse'],$categories,1);
tr($tracker_lang['my_language'], $lang_select ,1);
//tr($tracker_lang['my_website'], "<input type=\"text\" name=\"website\" size=50 value=\"" . htmlspecialchars($CURUSER["website"]) . "\" /> ", 1);
tr($tracker_lang['my_torrents_per_page'], "<input type=text size=10 name=torrentsperpage value=$CURUSER[torrentsperpage]> (0 = установки по умолчанию)",1);
//
//tr($tracker_lang['my_messages_per_page'], "<input type=text size=10 name=postsperpage value=$CURUSER[postsperpage]> (0 = установки по умолчанию)",1);
tr($tracker_lang['my_show_avatars'], "<input type=checkbox name=avatars" . ($CURUSER["avatars"] == "yes" ? " checked" : "") . "> (Пользователи с маленькими каналами могут отключить эту опцию)",1);

tr($tracker_lang['my_userbar'], "<img src=\"torrentbar/bar.php/".$CURUSER["id"].".png\" border=\"0\"><br />".$tracker_lang['my_userbar_descr'].":<br /><input type=\"text\" size=65 value=\"[url=$DEFAULTBASEURL][img]$DEFAULTBASEURL/torrentbar/bar.php/".$CURUSER["id"].".png[/img][/url]\" readonly />",1);
tr("Сменить пасскей","<input type=checkbox name=resetpasskey value=1 /> (Вы должны перекачать все активные торренты после смены пасскея)", 1);

if (strlen($CURUSER['passkey']) != 32) {
	$CURUSER['passkey'] = md5($CURUSER['username'].get_date_time().$CURUSER['passhash']);
	sql_query("UPDATE users SET passkey='$CURUSER[passkey]' WHERE id=$CURUSER[id]");
}
tr("Ваш пасскей","<b>$CURUSER[passkey]</b>", 1);
tr("Привязать IP к пасскею", "<input type=checkbox name=passkey_ip" . ($CURUSER["passkey_ip"] != "" ? " checked" : "") . "> Включив эту опцию вы можете защитить себя от неавторизованной закакачки по вашему пасскею привязав его к IP. Если ваш IP динамический - не включайте эту опцию.<br />На данный момент ваш IP: <b>".getip()."</b>", 1);

?>