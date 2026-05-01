<?php

require_once("include/bittorrent.php");
dbconn();
header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);
header ("Cache-control: no-store");
header ("Pragma: no-cache");

if(($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') == 'XMLHttpRequest' && ($_SERVER["REQUEST_METHOD"] ?? '') == 'POST')
{
    $id = (int)$_POST["user"];
    $act = (string)$_POST["act"];

    if (!is_valid_id($id) || empty($act))
    	die("Ошибка");

    print("<link rel=\"stylesheet\" href=\"css/user.css\" type=\"text/css\">\n");

    function maketable($res)
    {
        global $tracker_lang;
        $ret = "<table class=\"tt\">\n
            <tr><td class=\"tt\" style=\"padding:0px;margin:0px;width:45px;\" align=\"center\"><img src=\"pic/genre.gif\" title=\"Категория\" alt=\"\" /></td><td class=\"tt\"><img src=\"pic/release.gif\" title=\"Название\" alt=\"\" /></td><td class=\"tt\" align=\"center\"><img src=\"pic/mb.gif\" title=\"Размер\" alt=\"\" /></td><td class=\"tt\" width=\"30\" align=\"center\"><img src=\"pic/seeders.gif\" title=\"Раздают\" alt=\"\" /></td><td class=\"tt\" width=\"30\" align=\"center\"><img src=\"pic/leechers.gif\" title=\"Качают\" alt=\"\" /></td><td class=\"tt\" align=\"center\"><img src=\"pic/uploaded.gif\" title=\"Раздал\" alt=\"\" /></td>\n
            <td class=\"tt\" align=\"center\"><img src=\"pic/downloaded.gif\" title=\"Скачал\" alt=\"\" /></td><td class=\"tt\" align=\"center\"><img src=\"pic/ratio.gif\" title=\"Рейтинг\" alt=\"\" /></td></tr>\n";
        while ($arr = mysql_fetch_assoc($res))
        {
            if ($arr["downloaded"] > 0)
            {
                $ratio = number_format($arr["uploaded"] / $arr["downloaded"], 3);
                $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";
            }
            else
            {
                if ($arr["uploaded"] > 0)
                    $ratio = "Inf.";
                else
                    $ratio = "---";
            }
            $catid = $arr["catid"];
        	$catimage = htmlspecialchars($arr["image"]);
        	$catname = htmlspecialchars($arr["catname"]);
        	$size = str_replace(" ", "&nbsp;", mksize($arr["size"]));
        	$uploaded = str_replace(" ", "&nbsp;", mksize($arr["uploaded"]));
        	$downloaded = str_replace(" ", "&nbsp;", mksize($arr["downloaded"]));
        	$seeders = number_format($arr["seeders"]);
        	$leechers = number_format($arr["leechers"]);
            $ret .= "
                <tr>\n
                <td rowspan=\"2\" style=\"padding:0;margin:0;\"><a href=\"browse.php?cat=$catid\"><img src=\"pic/cats/$catimage\" title=\"$catname\" alt=\"\" border=\"0\"/></a></td>\n
                <td colspan=\"7\"><a href=details.php?id=$arr[torrent]&amp;hit=1><b>" . $arr["torrentname"] ."</b></a></td>\n
                </tr>\n
                <tr>\n
                <td align=\"left\"><font color=\"#808080\" size=\"1\">" . $arr["added"] . "</font></td>\n
                <td align=\"center\">$size</td>\n
                <td align=\"center\">$seeders</td>\n
                <td align=\"center\">$leechers</td>\n
                <td align=\"center\">$uploaded</td>\n
        		<td align=\"center\">$downloaded</td>\n
                <td align=\"center\">$ratio</td>\n
                </tr>\n";
        }
        $ret .= "</table>\n";
        return $ret;
    }

    $res = @sql_query("SELECT * FROM users WHERE id = $id") or sqlerr(__FILE__, __LINE__);
    $user = mysql_fetch_array($res) or die("Неверный идентификатор");

    print("<style>\n");
    print("table.main td {border:1px solid #cecece;margin:0;}\n");
    print("table.main a {color:#266C8A;font-family:tahoma;}\n");
    print("</style>\n");

    if ($act == "info")
    {
	$res = sql_query("SELECT name FROM countries WHERE id = $user[country] LIMIT 1") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) == 1)
{
  $arr = mysql_fetch_assoc($res);
}

$res = mysql_query("SELECT name FROM cities WHERE id=$user[city] LIMIT 1") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) == 1) 
{ 
 $art = mysql_fetch_assoc($res); 
 $city = "$art[name]"; 
}

if ($user["downloaded"] > 0)
{
    $rating = $user["uploaded"] / $user["downloaded"];
    $rating = floor($rating * 1000) / 1000;
    $rating = "<font color=\"" . get_ratio_color($rating) . "\">" . number_format($rating, 2) . "</font>";
}
else
    $rating = "N/A";

	if ($user["gender"] == "1") $gender = "Мужской";
elseif ($user["gender"] == "2") $gender = "Женский";
	?>
<div align="left"><b><font color="gray">Класс:</font></b> <?=get_user_class_color($user['class'], get_user_class_name($user['class']))?></div>
<div align="left"><b><font color="gray">Пол:</font></b> <?=$gender;?></div>
<?php if($user['birthday']!='0000-00-00') { ?>
<div align="left"><b><font color="gray">Дата рождения:</font></b> <?=nicetime($user["birthday"]);?> года (<?=(date("Y")-date("Y",strtotime($user['birthday']))+((date("m")>date("m",strtotime($user['birthday']))||(date("m")==date("m",strtotime($user['birthday']))&&date("d")>=date("d",strtotime($user['birthday'])))) ? 1 : 0)-1);?>)</div><br>
<?php } ?>
<h4>Контактная информация <div class="edit" ><?php if($user['id']==$CURUSER['id']) { ?><a href="my.php">[ редактировать ]</a><?php } ?></div></h4>
<div align="left"><b><font color="gray">Страна:</font></b> <?=$arr["name"];?></div>
<div align="left"><b><font color="gray">Город:</font></b> <?=$city;?></div>
<?php if ($user["icq"]) {?>
<div align="left"><b><font color="gray">Номер ICQ:</font></b>  <img src="email_img.php?uid=<?=$user['id'];?>&type=2" alt="ICQ пользователя <?=$user['name'];?>"></div> <?php } ?><br>
<h4>Статистика на трекере</h4>
<div align="left"><b><font color="gray">Раздал:</font></b> <?=str_replace(" ", "&nbsp;", mksize($user['uploaded']))?></div>
<div align="left"><b><font color="gray">Скачал:</font></b> <?=str_replace(" ", "&nbsp;", mksize($user['downloaded']))?></div>
<div align="left"><b><font color="gray">Рейтинг:</font></b> <?=$rating;?></div><br>
<h4>Личная информация <div class="edit" ><?php if($user['id']==$CURUSER['id']) { ?><a href="my.php">[ редактировать ]</a><?php } ?></div></h4>
<?php
foreach(explode(",", $user["lovemovies"]) as $lov)
$love .= "<a style=\"font-weight:normal;\" href=\"browse.php?search=".$lov."\">".$lov."</a>, ";
                if ($love)
                $love = substr($love, 0, -2);
				if ($user["lovemovies"]) {
?>
<div align="left"><b><font color="gray">Любимые фильмы:</font></b> <?=$love;?></div> <?php } ?>
<div align="left"><b><font color="gray">О себе:</font></b> <?=(!empty($user['info']) ? str_replace("\n","<br>",htmlspecialchars($user["info"])) : '<i>Пользователь не заполнил информацию о себе</i>')?></div>
<?php
        die();
    }
    elseif ($act == "friends")
    {
        $res = sql_query("SELECT f.friendid as id, u.username AS name, u.class, u.avatar, u.gender, u.title, u.donor, u.warned, u.enabled, u.last_access FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id WHERE f.userid=$id AND f.status = 'yes' ORDER BY name") or sqlerr(__FILE__, __LINE__);
        if (mysql_num_rows($res) > 0)
        {
            print("<div id=\"friends\">\n");
            while ($row = mysql_fetch_array($res))
            {
                if (empty($row['avatar']))
                    $avatar = "pic/default_avatar.gif";
                else
                    $avatar = $DEFAULTBASEURL.'/avatars/'.$row['avatar'];
                $dt = get_date_time(gmtime() - 300);
                if ($row['last_access'] > $dt)
                    $status = "<font color=\"#008000\">Онлайн</font>";
                else
                    $status = "<font color=\"#FF0000\">Оффлайн</font>";
                if ($row["gender"] == "1")
                    $gender = "<img src=\"pic/male.gif\" alt=\"Парень\" title=\"Парень\" />";
                else
                    $gender = "<img src=\"pic/female.gif\" alt=\"Девушка\" title=\"Девушка\" />";
                print("<div class=\"friend\">\n");
                print("<div class=\"avatar\"><a href=\"userdetails.php?id=" . $row['id'] . "\"><img src=\"$avatar\" alt=\"\" /></a></div>\n");
                print("<div class=\"finfo\">\n");
                print("<p><b>Имя:</b>&nbsp;<a href=\"userdetails.php?id=" . $row['id'] . "\">" . get_user_class_color($row['class'], $row['name']) . "</a></p>\n");
                print("<p><b>Пол:</b>&nbsp;$gender</p>\n");
                print("<p><b>Класс:</b>&nbsp;" . get_user_class_name($row['class']) . "</p>\n");
                print("<p><b>Статус:</b>&nbsp;$status</p>\n");
                print("</div>\n");
                print("<div class=\"actions\">\n");
                print("<p><a href=\"message.php?action=sendmessage&receiver=" . $row['id'] . "\">Отправить сообщение</a></p>\n");
                print("<p><a href=\"friends.php?id=" . $row['id'] . "\">Друзья " . get_user_class_color($row['class'], $row['name']) . "</a></p>\n");
                if ($CURUSER['id'] == $id)
                    print("<p><a href=\"friends.php?action=delete&type=friend&targetid=" . $row['id'] . "\">Убрать из друзей</a></p>\n");
                print("</div>\n");
                print("<div style=\"clear:both;\"></div>\n");
                print("</div>\n");
            }
            print("</div>\n");
        }
        else
            print("<div class=\"tab_error\">У пользователя нет друзей.</div>\n");
        die();
    }
    elseif ($act == "downloaded")
    {
        $res = sql_query("SELECT snatched.torrent AS id, snatched.uploaded, snatched.seeder, snatched.downloaded, snatched.startdat, snatched.completedat, snatched.last_action, categories.name AS catname, categories.image AS catimage, categories.id AS catid, torrents.name, torrents.seeders, torrents.leechers FROM snatched JOIN torrents ON torrents.id = snatched.torrent JOIN categories ON torrents.category = categories.id WHERE snatched.finished='yes' AND userid = $id ORDER BY torrent") or sqlerr(__FILE__,__LINE__);
        if (mysql_num_rows($res) > 0)
        {
            print "<table class=\"tt\" width=\"100%\">\n
            <tr>
            <td class=\"colhead\" style=\"padding:0;margin:0;width:45px;\" align=\"center\"><img src=\"pic/torrenttable/genre.gif\" title=\"Категория\" alt=\"\" /></td>
            <td class=\"colhead\"><img src=\"pic/torrenttable/release.gif\" title=\"Название\" alt=\"\" /></td>
            <td class=\"colhead\" width=\"30\" align=\"center\"><img src=\"pic/torrenttable/seeders.gif\" title=\"Раздают\" alt=\"\" /></td>
            <td class=\"colhead\" width=\"30\" align=\"center\"><img src=\"pic/torrenttable/leechers.gif\" title=\"Качают\" alt=\"\" /></td>
            <td class=\"colhead\" width=\"30\" align=\"center\"><img src=\"pic/torrenttable/uploaded.gif\" title=\"Раздал\" alt=\"\" /></td>
            <td class=\"colhead\" width=\"30\" align=\"center\"><img src=\"pic/torrenttable/downloaded.gif\" title=\"Скачал\" alt=\"\" /></td>
            <td class=\"colhead\" width=\"30\" align=\"center\"><img src=\"pic/torrenttable/ratio.gif\" title=\"Скачал\" alt=\"\" /></td>
            <td class=\"colhead\" width=\"30\" align=\"center\"><img src=\"pic/torrenttable/start.gif\" title=\"Начал\" alt=\"\" /></td>
            <td class=\"colhead\" width=\"30\" align=\"center\"><img src=\"pic/torrenttable/end.gif\" title=\"Закончил\" alt=\"\" /></td>
            <td class=\"colhead\" width=\"30\" align=\"center\"><img src=\"pic/torrenttable/seeded.gif\" title=\"Сид?\" alt=\"\" /></td>";
            while ($row = mysql_fetch_array($res))
            {
                if ($row["downloaded"] > 0)
                {
                    $ratio = number_format($row["uploaded"] / $row["downloaded"], 3);
                    $ratio = "<font color=\"" . get_ratio_color($ratio) . "\">$ratio</font>";
                }
                else
                {
            	    if ($row["uploaded"] > 0)
                        $ratio = "Inf.";
            	    else
            		    $ratio = "---";
                }
                $uploaded = mksize($row["uploaded"]);
                $downloaded = mksize($row["downloaded"]);
                if ($row["seeder"] == 'yes')
            	    $seeder = "<font color=\"green\">Да</font>";
                else
            	    $seeder = "<font color=\"red\">Нет</font>";
            	$cat = "<a href=\"browse.php?cat=$row[catid]\"><img src=\"pic/cats/$row[catimage]\" alt=\"$row[catname]\" border=\"0\" /></a>";
                print "<tr><td style=\"padding:0;margin:0;\" rowspan=\"2\">$cat</td><td colspan=\"9\"><a href=\"details.php?id=" . $row["id"] . "&amp;hit=1\"><b>" . $row["name"] . "</b></a></td></tr>" .
                  "<tr><td align=\"left\" width=500></td><td align=\"center\">$row[seeders]</td><td align=\"center\">$row[leechers]</td><td align=\"center\"><nobr>$uploaded</nobr></td><td align=\"center\"><nobr>$downloaded</nobr></td><td align=\"center\">$ratio</td><td align=\"center\"><nobr style=\"font-size:10px;\">$row[startdat]</nobr></td><td align=\"center\"><nobr style=\"font-size:10px;\">$row[completedat]</nobr></td><td align=\"center\">$seeder</td>\n";
            }
            print "</table>";
        }
        else
            print("<div class=\"tab_error\">Пользователь не скачивал торрентов.</div>");
        die();
    }
    elseif ($act == "uploaded")
    {
        $res = sql_query("SELECT t.id, t.name, t.seeders, t.added, t.leechers, t.f_seeders, t.f_peers, t.multitracker, t.category, c.name AS catname, c.image AS catimage, c.id AS catid FROM torrents AS t LEFT JOIN categories AS c ON t.category = c.id WHERE t.owner = $id ORDER BY t.name") or sqlerr(__FILE__, __LINE__);
        if (mysql_num_rows($res) > 0)
        {
            print("<table class=\"tt\">\n" .
            "<tr><td class=\"colhead\" style=\"padding:0;margin:0;width:50px;\" align=\"center\"><img src=\"pic/torrenttable/genre.gif\" title=\"Категория\" alt=\"\" /></td><td class=\"colhead\"><img src=\"pic/torrenttable/release.gif\" title=\"Название\" alt=\"\" /></td><td class=\"colhead\" width=\"50\" align=\"center\"><img src=\"pic/torrenttable/seeders.gif\" title=\"Раздают\" alt=\"\" /></td><td class=\"colhead\" width=\"50\" align=\"center\"><img src=\"pic/torrenttable/leechers.gif\" title=\"Качают\" alt=\"\" /></td></tr>\n");
            while ($row = mysql_fetch_assoc($res))
            {
		        $cat = "<a href=\"browse.php?cat=$row[catid]\"><img src=\"pic/cats/$row[catimage]\" alt=\"$row[catname]\" border=\"0\" /></a>";
                print("<tr><td rowspan=\"2\" style=\"padding:0;margin:0;\">$cat</td><td colspan=\"3\"><a href=\"details.php?id=" . $row["id"] . "&hit=1\"><b>" . $row["name"] . "</b></a></td></tr>\n");
                print("<tr><td><font color=\"#808080\" size=\"1\">" . $row["added"] .($row['multitracker']==1 ? ", Мультитрекерный" : "")."</font></td><td align=\"center\">".($row['seeders']+$row['f_seeders'])."</td><td align=\"center\">".($row['leechers']+$row['f_peers'])."</td></tr>\n");
            }
            print("</table>");
        }
        else
            print("<div class=\"tab_error\">Пользователь не загружал торрентов.</div>");
        die();
    }
    elseif ($act == "moderate")
    {
        if (get_user_class() >= UC_MODERATOR && $user["class"] < get_user_class())
        {
            print("<h2>Модерирование</h2>\n");
            print("<table width=\"100%\" cellpadding=\"5\">\n");
            print("<tr><td>\n");
            print("<form method=\"post\" action=\"modtask.php\">\n");
            print("<input type=\"hidden\" name=\"action\" value=\"edituser\">\n");
            print("<input type=\"hidden\" name=\"userid\" value=\"$id\">\n");
            print("<input type=\"hidden\" name=\"returnto\" value=\"userdetails.php?id=$id\">\n");
            print("<table width=\"100%\" cellpadding=\"5\" align=\"left\">\n");
            print("<tr><td class=\"rowhead\">Заголовок</td><td colspan=\"2\" align=\"left\"><input type=\"text\" size=\"60\" name=\"title\" value=\"" . htmlspecialchars($user[title]) . "\"></tr>\n");
          	$avatar = htmlspecialchars($user["avatar"]);
            print("<tr><td class=\"rowhead\">Удалить аватар</td><td colspan=\"2\" align=\"left\"><input type=\"checkbox\" name=\"avatar\" value=\"{$user['avatar']}\"></tr>\n");
          	if ($CURUSER["class"] < UC_ADMINISTRATOR)
          	    print("<input type=\"hidden\" name=\"donor\" value=\"$user[donor]\">\n");
          	else
          	    print("<tr><td class=\"rowhead\">Донор</td><td colspan=\"2\" align=\"left\"><input type=\"radio\" name=\"donor\" value=\"yes\"" .($user["donor"] == "yes" ? " checked" : "").">Да <input type=\"radio\" name=\"donor\" value=\"no\"" .($user["donor"] == "no" ? " checked" : "").">Нет</td></tr>\n");

          	if (get_user_class() == UC_MODERATOR && $user["class"] > UC_VIP)
          	    print("<input type=\"hidden\" name=\"class\" value=\"$user[class]\">\n");
          	else
          	{
                print("<tr><td class=\"rowhead\">Класс</td><td colspan=\"2\" align=\"left\"><select name=\"class\">\n");
                if (get_user_class() == UC_SYSOP)
                    $maxclass = UC_SYSOP;
                elseif (get_user_class() == UC_MODERATOR)
                    $maxclass = UC_VIP;
                else
                    $maxclass = get_user_class() - 1;
                for ($i = 0; $i <= $maxclass; ++$i)
                    print("<option value=\"$i\"" . ($user["class"] == $i ? " selected" : "") . ">$prefix" . get_user_class_name($i) . "\n");
                print("</select></td></tr>\n");
          	}
          	print("<tr><td class=\"rowhead\">Сбросить день рождения</td><td colspan=\"2\" align=\"left\"><input type=\"radio\" name=\"resetb\" value=\"yes\">Да<input type=\"radio\" name=\"resetb\" value=\"no\" checked>Нет</td></tr>\n");
          	$modcomment = htmlspecialchars($user["modcomment"]);
          	$supportfor = htmlspecialchars($user["supportfor"]);
          	print("<tr><td class=rowhead>Поддержка</td><td colspan=2 align=left><input type=radio name=support value=yes" .($user["support"] == "yes" ? " checked" : "").">Да <input type=radio name=support value=no" .($user["support"] == "no" ? " checked" : "").">Нет</td></tr>\n");
          	print("<tr><td class=rowhead>Поддержка для:</td><td colspan=2 align=left><textarea cols=60 rows=6 name=supportfor>$supportfor</textarea></td></tr>\n");
          	print("<tr><td class=rowhead>История пользователя</td><td colspan=2 align=left><textarea cols=60 rows=6".(get_user_class() < UC_SYSOP ? " readonly" : " name=modcomment").">$modcomment</textarea></td></tr>\n");
          	print("<tr><td class=rowhead>Добавить заметку</td><td colspan=2 align=left><textarea cols=60 rows=3 name=modcomm></textarea></td></tr>\n");
          	$warned = $user["warned"] == "yes";

           	print("<tr><td class=\"rowhead\"" . (!$warned ? " rowspan=\"2\"": "") . ">Предупреждение</td>
           	<td align=\"left\" width=\"20%\">" . ($warned ? "<input name=\"warned\" value=\"yes\" type=\"radio\" checked>Да<input name=\"warned\" value=\"no\" type=\"radio\">Нет" : "Нет" ) ."</td>");

          	if ($warned)
            {
          		$warneduntil = $user['warneduntil'];
          		if ($warneduntil == '0000-00-00 00:00:00')
              		print("<td align=\"center\">На неограниченый срок</td></tr>\n");
          		else
                {
              		print("<td align=\"center\">До $warneduntil");
          	    	print(" (" . mkprettytime(strtotime($warneduntil) - gmtime()) . " осталось)</td></tr>\n");
           	    }
            }
            else
            {
                print("<td>Предупредить на <select name=\"warnlength\">\n");
                print("<option value=\"0\">------</option>\n");
                print("<option value=\"1\">1 неделю</option>\n");
                print("<option value=\"2\">2 недели</option>\n");
                print("<option value=\"4\">4 недели</option>\n");
                print("<option value=\"8\">8 недель</option>\n");
                print("<option value=\"255\">Неограничено</option>\n");
                print("</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Комментарий в ЛС:</td></tr>\n");
                print("<tr><td colspan=\"2\" align=\"left\"><input type=\"text\" size=\"60\" name=\"warnpm\"></td></tr>");
            }
            print("<tr><td class=\"rowhead\" rowspan=\"2\">Включен</td><td colspan=\"2\" align=\"left\"><input name=\"enabled\" value=\"yes\" type=\"radio\"" . ($user["enabled"] == 'yes' ? " checked" : "") . ">Да <input name=\"enabled\" value=\"no\" type=\"radio\"" . ($user["enabled"] == 'no' ? " checked" : "") . ">Нет</td></tr>\n");
            if ($user["enabled"] == 'yes')
              	print("<tr><td colspan=\"2\" align=\"left\">Причина отключения:&nbsp;<input type=\"text\" name=\"disreason\" size=\"60\" /></td></tr>");
          	else
          		print("<tr><td colspan=\"2\" align=\"left\">Причина включения:&nbsp;<input type=\"text\" name=\"enareason\" size=\"60\" /></td></tr>");
            print("<tr><td class=\"rowhead\">Изменить раздачу</td><td align=\"left\"><img src=\"pic/plus.gif\" id=\"uppic\" onClick=\"togglepic('$DEFAULTBASEURL','uppic','upchange')\" style=\"cursor: pointer;\">&nbsp;<input type=\"text\" name=\"amountup\" size=\"10\" /><td>\n<select name=\"formatup\">\n<option value=\"mb\">MB</option>\n<option value=\"gb\">GB</option></select></td></tr>");
            print("<tr><td class=\"rowhead\">Изменить скачку</td><td align=\"left\"><img src=\"pic/plus.gif\" id=\"downpic\" onClick=\"togglepic('$DEFAULTBASEURL','downpic','downchange')\" style=\"cursor: pointer;\">&nbsp;<input type=\"text\" name=\"amountdown\" size=\"10\" /><td>\n<select name=\"formatdown\">\n<option value=\"mb\">MB</option>\n<option value=\"gb\">GB</option></select></td></tr>");
			print("<tr><td class=\"rowhead\">Изменить кредиты</td><td align=\"left\" colspan=\"2\"><img src=\"pic/plus.gif\" id=\"credpic\" onClick=\"togglepic('$DEFAULTBASEURL','credpic','credchange')\" style=\"cursor: pointer;\">&nbsp;<input type=\"text\" name=\"cred\" size=\"10\" /></tr>");
            print("<tr><td class=\"rowhead\">Сбросить passkey</td><td colspan=\"2\" align=\"left\"><input name=\"resetkey\" value=\"1\" type=\"checkbox\"></td></tr>\n");
            if ($CURUSER["class"] < UC_ADMINISTRATOR)
                print("<input type=\"hidden\" name=\"deluser\">");
            else
                print("<tr><td class=\"rowhead\">Удалить</td><td colspan=\"2\" align=\"left\"><input type=\"checkbox\" name=\"deluser\"></td></tr>");
            print("</td></tr>");
            print("<tr><td colspan=\"3\" align=\"center\"><input type=\"submit\" class=\"btn\" value=\"ОК\"></td></tr>\n");
            print("</table>\n");
            print("<input type=\"hidden\" id=\"upchange\" name=\"upchange\" value=\"plus\"><input type=\"hidden\" id=\"downchange\" name=\"downchange\" value=\"plus\"><input type=\"hidden\" id=\"warnchange\" name=\"warnchange\" value=\"plus\">\n"); 
			print("</form>\n");
            print("</td>\n</tr>\n</table>\n\n");
            die();
        }
        else
            die("У вас нет прав");
    }
    elseif ($act == "pm")
    {
        ?>
        <script language="JavaScript" type="text/javascript">
        function send_message(to, msg, subject)
        {
            var text = enBASE64(msg);
            var subj = enBASE64(subject);
            if (text == '' || subj == '')
            {
                alert('Вы не указали сообщение или тему.');
                return;
            }
            jQuery.post("user.php",{"user":to,"msg":text,"subject":subj,"act":"sendmessage"},function (response) {
                jQuery("#operation").empty();
                jQuery("#operation").append(response);
            });
            document.pm.msg.value = '';
            document.pm.subject.value = '';
        };
        </script>
        <?php
        print("<form name=\"pm\">\n");
        print("<h2>Личное сообщение</h2>\n");
        print("<table width=\"100%\" cellpadding=\"5\">\n");
        print("<tr><td>\n");
        print("<div id=\"operation\"></div>\n");
        print("<p>Тема: <input name=\"subject\" type=\"text\" style=\"width:370px;\" /></p>");
        textbbcode("pm", "msg", htmlspecialchars($text), $long);
        print("<p><input type=\"button\" value=\"Отправить\" onclick=\"javascript:send_message('$id', document.pm.msg.value, document.pm.subject.value);\"/>&nbsp;&nbsp;<input type=\"reset\" value=\"Отменить\" /></p>\n");
        print("</td></tr>\n");
        print("</table>\n");
        print("</form>\n");
        die();
    }

    elseif ($act == "sendmessage")
    {
        if (!empty($_POST['subject']) && !empty($_POST['msg']))
        {
            $dt = get_date_time();
            $text = sqlesc(base64_decode($_POST['msg']));
            $subject = sqlesc(base64_decode($_POST['subject']));
            sql_query("INSERT INTO messages (sender, receiver, subject, msg, added) VALUES (" . sqlesc($CURUSER['id']) . ", $id, $subject, $text, " . sqlesc($dt) . ")") or sqlerr(__FILE__,__LINE__);
            die("<div class=\"success\">Ваше сообщение отправлено.</div>");
        }
        else
            die("Вы не ввели тему или сообщение");
    }

    elseif ($act == "statistics")
    {
        $comments = get_row_count("comments", "WHERE user = $id");
        $seeder = get_row_count("peers", "WHERE userid = $id AND seeder = 'yes'");
        $leecher = get_row_count("peers", "WHERE userid = $id AND seeder = 'no'");
        $torrents = get_row_count("torrents", "WHERE owner = $id");
        $snatched = get_row_count("snatched", "WHERE userid = $id");
        $thanks = get_row_count("thanks", "WHERE userid = $id");
        $ratings = get_row_count("ratings", "WHERE user = $id");
        $bookmarks = get_row_count("bookmarks", "WHERE userid = $id");
        $friends = get_row_count("friends", "WHERE userid = $id");
        $invites = get_row_count("invites", "WHERE inviter = $id");
        print("<h2>Статистика</h2>\n");
        print("<table width=\"100%\" cellpadding=\"5\">\n");
        print("<tr>\n");
        print("<td><b>Комментариев:</b> $comments</td>\n");
        print("<td><b>Качает:</b> $leecher</td>\n");
        print("<td><b>Раздает:</b> $seeder</td>\n");
        print("<td><b>Загрузил:</b> $torrents</td>\n");
        print("<td><b>Скачал:</b> $snatched</td>\n");
        print("</tr>\n");
        print("<tr>\n");
        print("<td><b>Спасибо:</b> $thanks</td>\n");
        print("<td><b>Оценил:</b> $ratings</td>\n");
        print("<td><b>Закладок:</b> $bookmarks</td>\n");
        print("<td><b>Пригласил:</b> $bookmarks</td>\n");
        print("<td><b>Друзей:</b> $friends</td>\n");
        print("</tr>\n");
        print("</table>\n");
        die();
    }

    elseif ($act == "addtofriends")
    {
        $type = $_POST['type'];
        if (empty($type) || empty($CURUSER['id']))
            die("Прямой доступ закрыт");
        if ($type == "add")
        {
            $res = sql_query("SELECT id, status FROM friends WHERE userid=" . sqlesc($CURUSER['id']) . " AND friendid = $id") or sqlerr(__FILE__, __LINE__);
            $row = mysql_fetch_array($res);
            if ($row['status'] == 'yes')
                die("<div class=\"error\">Пользователь уже ваш друг.</div>");
            elseif ($row['status'] == 'pending')
                die("<div class=\"error\">Вы уже отправляли запрос. Дождитеcь решения пользователя.</div>");
            elseif ($row['status'] == 'no')
                die("<div class=\"error\">Пользователь отказал Вам в дружбе.</div>");
            else
            {
                sql_query("INSERT INTO friends (userid, friendid) VALUES (" . sqlesc($CURUSER['id']) . ", $id)") or sqlerr(__FILE__, __LINE__);
                $newid = mysql_insert_id();
                die("<div class=\"success\">Запрос на дружбу отправлен. Дождитесь ответа пользователя.</div>");
            }
        }
        if ($type = "delete")
        {
            sql_query("DELETE FROM friends WHERE userid = $id AND friendid = " . sqlesc($CURUSER['id']));
            sql_query("DELETE FROM friends WHERE friendid = $id AND userid = " . sqlesc($CURUSER['id']));
            die("<div class=\"success\">Пользователь удален из друзей.</div>");
        }
        die();
    }
	elseif($act=='notes')
	{
		if($id==$CURUSER['id']||$CURUSER['class']>=UC_MODERATOR) $friend=true;
		else {
		$friend = mysql_fetch_array(sql_query("SELECT status FROM friends WHERE friendid = ".$CURUSER['id']." AND userid = ".$id));
		if(empty($friend)||$friend['status']!='yes')
			$friend = false;
		else
			$friend = true;}
		$notes=sql_query("SELECT * FROM notes WHERE uid=".$id);
		if(mysql_num_rows($notes)==0)
			die( "<div class=\"tab_error\">У пользователя нет записей</div>");
		echo '<table width="100%" cellpadding="5" border="0">';
		$closed=0;
		while($note=mysql_fetch_array($notes))
		{
			if($note['access']==0&&!$friend)
				{$closed++; continue;}
		$text = htmlspecialchars($note['text']);
		$text = str_replace("\n", ' ',$text);
		$text=preg_replace("#\[.*\]#is","",$text);
		if(strlen($note['text']) > 240)
		$text=substr($text,0,200).'...';
			?><tr><td><span style="font-size: 12pt;"><a href="note.php?uid=<?=$id;?>&id=<?=$note['id'];?>"><?=$note['name'];?></a></span><br>Добавлена: <?=nicetime($note['timestamp'],true);?><br>
		<?php if(!empty($note['last_edit'])) { ?><small>Последняя правка: <?=nicetime($note['last_edit'],true);?><?php } ?>
		<hr>
		<?=$text;?>
		<hr>
		<?php if(!empty($note['tags'])) { ?>Теги: <?php
			$tags=explode(',',$note['tags']);
			$i=0;
			foreach ($tags as $tag)
		{echo ($i!=0 ? ', ' : '').'<a href="notetag.php?tag='.urlencode(trim($tag)).'" style="color:green;font-weight:normal;">'.trim($tag).'</a>';
		$i++;}echo "<br>";}
		?> Просмотров: <?=$note['views'];?>, комментариев: <?=$note['comments'];?></td></tr><?php		
		}
		echo "</table>";
		if($closed > 0)
		echo "<div class=\"success\">Не показано $closed скрытых записей</div>";
	
	}
    else
        die("Прямой доступ запрещен");
}
else
    die("Прямой доступ запрещен");
?>
