<?php
require_once("include/bittorrent.php");
gzip();
dbconn(true);
loggedinorreturn(true);
stdhead($tracker_lang['homepage']);
begin_frame('Последние новости трекера');
// Настройки "главной" записи
$noteuid = 1705; // UID
$noteid = 1; // ID
if (cache_check("blognews", 60))
$notes = cache_read("blognews");
else {
$notes = sql_query("SELECT n.text, n.last_edit, n.timestamp, n.uid, n.views, n.comments, u.username, u.firstname, u.surname, u.class FROM notes AS n LEFT JOIN users AS u ON u.id = n.uid WHERE n.uid = ".$noteuid." AND n.id = ".$noteid) or sqlerr(__FILE__, __LINE__);
    $blog_cache = array();
    while ($cache_data = mysql_fetch_assoc($notes))
        $blog_cache[] = $cache_data;

    cache_write("blognews", $blog_cache);
    $notes = $blog_cache;
    }
?> <table width="100%" cellpadding="5" border="0"> <?php
foreach($notes as $note) {
		$text = format_comment($note['text']);
		/*$text = str_replace("\n", ' ',$text);
		$text=preg_replace("#\[.*\]#is","",$text);
		if(strlen($note['text']) > 240)
		$text=substr($text,0,200).'...';*/
		/* <span style="font-size: 12pt;"><a href="note<?=$note['uid'];?>-<?=$note['id'];?>"><?=$note['name'];?></a></span> */
		?><tr><td><?=nicetime((empty($note['last_edit']) ? $note['timestamp'] : $note['last_edit']),true);?><br>
		Автор: <a href="id<?=$note['uid'];?>"><?=$note['firstname'];?> <i><?=get_user_class_color($note['class'],$note['username']);?></i> <?=$note['surname'];?></a>
		<hr>
		<?=$text;?>
		<hr>
		<div style="float: left;">Просмотров: <?=$note['views'];?>, комментариев: <?=$note['comments'];?></div>
		<div style="float: right;">[<a href="note<?=$noteuid;?>-<?=$noteid;?>">Перейти</a>]
		<?php if($CURUSER['id']==$noteuid||get_user_class()>=UC_MODERATOR) { ?>
		 [<a href="noteedit.php?uid=<?=$noteuid;?>&id=<?=$noteid;?>&act=edit">Редактировать</a>]
		<?php } ?>
		</div></td></tr>
	</table> <?php
}
end_frame();
/*
if (get_user_class() >= UC_SYSOP) {
begin_frame("".$tracker_lang['news']." - <a href=\"news.php\">Добавить</a>");
} else {
begin_frame($tracker_lang['news']);
}
//Новости//
if (cache_check("news", 300))
    $resource = cache_read("news");
else {
$resource = sql_query("SELECT news.* , COUNT(newscomments.id) AS numcomm FROM news LEFT JOIN newscomments ON newscomments.news = news.id WHERE ADDDATE(news.added, INTERVAL 45 DAY) > NOW() GROUP BY news.id ORDER BY news.added DESC LIMIT 3") or sqlerr(__FILE__, __LINE__);
    $news_cache = array();
    while ($cache_data = mysql_fetch_array($resource))
        $news_cache[] = $cache_data;

    cache_write("news", $news_cache);
    $resource = $news_cache;
    }
print("<script language=\"javascript\" type=\"text/javascript\" src=\"js/show_hide.js\"></script>");
if ($resource) {
print("<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"10\"><tr><td class=\"text\" style=\"border:none;\">\n");
foreach($resource as $array) {
if ($news_flag == 0) {
print("<span style=\"cursor: pointer;\" onclick=\"javascript: show_hide('s".$array["id"]."')\"><img border=\"0\" src=\"pic/minus.gif\" id=\"pics".$array["id"]."\" title=\"Скрыть\"></span>&nbsp;");
print("<span style=\"cursor: pointer;\" onclick=\"javascript: show_hide('s".$array["id"]."')\">".date("d.m.Y",strtotime($array['added']))." - \n");
print("<b>".$array['subject']."</b></span>\n");
print("<span id=\"ss".$array["id"]."\" style=\"display: block;\">".format_comment($array['body'])."</span>");
print("<div align=\"right\">Комментариев: ".$array['numcomm']." [<a href=\"newsoverview.php?id=".$array['id']."\">Комментировать</a>]</div>");
if (get_user_class() >= UC_ADMINISTRATOR) {
print("<font size=\"-2\">[<a class=\"altlink\" href=\"news.php?action=edit&newsid=" . $array['id'] . "&returnto=" . urlencode($_SERVER['PHP_SELF']) . "\"><b>Ред.</b></a>]</font>");
print("<font size=\"-2\">[<a class=\"altlink\" href=\"news.php?action=delete&newsid=" . $array['id'] . "&returnto=" . urlencode($_SERVER['PHP_SELF']) . "\"><b>Удал.</b></a>]</font>");
}
print("<br />");

$news_flag = 1;
} else {
print("<span style=\"cursor: pointer;\" onclick=\"javascript: show_hide('s".$array["id"]."')\"><img border=\"0\" src=\"pic/plus.gif\" id=\"pics".$array["id"]."\" title=\"Показать\"></span>&nbsp;");
print("<span style=\"cursor: pointer;\" onclick=\"javascript: show_hide('s".$array["id"]."')\">".date("d.m.Y",strtotime($array['added']))." - \n");
print("<b>".$array['subject']."</b></span>\n");
print("<span id=\"ss".$array["id"]."\" style=\"display: none;\">".format_comment($array['body'])."</span>");
if (get_user_class() >= UC_ADMINISTRATOR) {
print("<font size=\"-2\">[<a class=\"altlink\" href=\"news.php?action=edit&newsid=" . $array['id'] . "&returnto=" . urlencode($_SERVER['PHP_SELF']) . "\"><b>Ред.</b></a>]</font>");
print("<font size=\"-2\">[<a class=\"altlink\" href=\"news.php?action=delete&newsid=" . $array['id'] . "&returnto=" . urlencode($_SERVER['PHP_SELF']) . "\"><b>Удал.</b></a>]</font>");
}
print("<div align=\"right\">Комментариев: ".$array['numcomm']." [<a href=\"newsoverview.php?id=".$array['id']."\">Комментировать</a>]</div>");
print("<br />");
    	}
	}
print("</ul></td></tr></table>\n");	
} else {
print("<table class=\"main\" align=\"center\" border=\"1\" cellspacing=\"0\" cellpadding=\"10\"><tr><td class=\"text\">");
print("<div align=\"center\"><h3>".$tracker_lang['no_news']."</h3></div>\n");
print("</td></tr></table>");
}
end_frame();
//Новости Конец//
*/

//Блок релизов - Начало
/*begin_frame("Релизы");
$pwidth = "120"; //ширина отображаемого постера
if (cache_check("release", 300))
    $res = cache_read("release");
else {
$res = sql_query("SELECT * FROM torrents WHERE ontop='yes' ORDER BY id DESC LIMIT 5") or sqlerr(__FILE__, __LINE__);
    $release_cache = array();
    while ($cache_data = mysql_fetch_assoc($res))
        $release_cache[] = $cache_data;

    cache_write("release", $release_cache);
    $res = $release_cache;
}
//print('<div style="overflow-x: scroll; width: 950;">');
     print("<table align=\"center\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">");
    print("<tr valign=\"middle\">");
    foreach($res as $row) {
	print("<td class=\"embedded\" width=\"$pwidth\"><div id=\"screenshots\"><a href=\"torrent-" . $row["id"] . "\"><img src=\"torrents/images/" . $row["image1"] . "\" width=\"$pwidth\" border=\"0\" title=\"" . $row["name"] . "\" alt=\"Загрузка..\" /></a></div></td>");
	}
    print("</tr>");
    print("</table>");
//print('</div>');
end_frame();
//Блок релизов - Конец
//Блок опросов - Начало */

begin_frame("Опрос");
if($CURUSER) {
?>
<script type="text/javascript" src="js/poll.core.js"></script>
<link href="css/poll.core.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">$(document).ready(function(){loadpoll();});</script>
<?php
print("<table width=\"100%\" class=\"main\" align=\"center\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\">");
print("<tr><td class=\"text\" align=\"center\" style=\"border:none\" ><div id=\"poll_container\"><div id=\"loading_poll\" style=\"display:none\"></div>");
print("<noscript><b>Включите JavaScript в браузере</noscript></div></td></tr></table>");
}
else
echo "Опрос доступен только зарегистрированным пользователям";
end_frame();
//Блок опросов - Конец
//Чат Начало
if($CURUSER) {
begin_frame("Живое общение");
print("<link rel=\"stylesheet\" href=\"css/user.css\" type=\"text/css\">\n");
print("<script language=\"JavaScript\" src=\"js/user.js\" type=\"text/javascript\"></script>\n");
print("<form action=\"shoutbox.php\" method=\"post\" name=\"shoutform\" onsubmit=\"return sendShout(this);\">");
?>
<table cellspacing="0" cellpadding="5" width="100%"  >
<tr>
<td style="white-space: nowrap;">
<input type="text" name="shout" style="width: 100%;"  MAXLENGTH="200px">
</td><td style="white-space: nowrap;" width="5%">
<input type="submit" value="Отправить">
<INPUT TYPE="button" VALUE="Смайлы" onClick="javascript:winop()">
</td>
</tr>
<tr>
<td colspan="2">
    <div id="shoutbox" style="overflow: auto; height: 350px; width: 100%; padding-top: 0cm">
          Загрузка... 
    </div>
    </td>
</tr>
</table></form>
<div id="loading-layer">

</div>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript">

function winop()
{
windop = window.open("moresmiles.php?form=shoutform&text=shout","mywin","height=500,width=600,resizable=no,scrollbars=yes");
}

function sendShout(formObj) {

    /*if (postingShout) {
        alert('Отправка сообщения...')
        return false
    }*/

    Shout = formObj.shout.value

    if (Shout.replace(/ /g, '') == '') {
        alert('Вы должны вести сообщение!')
        return false
    }

    sb_Clear();

    var ajax = new tbdev_ajax();
    ajax.onShow ('');
    ajax.onShow = function() { };
    var varsString = "";
    ajax.requestFile = "shoutbox.php";
    ajax.setVar("do", "shout");
    ajax.setVar("shout", Shout);
    ajax.method = 'POST';
    ajax.element = 'shoutbox';
    ajax.sendAJAX(varsString);
	//jQuery.Post('shoutbox.php',{"do":"shout","shout":Shout}, function(response) {
	//jQuery("#shoutbox").empty();
	//jQuery("#shoutbox").append(response); });

    return false;
}

function getShouts() {

    var ajax = new tbdev_ajax();
    ajax.onShow = function() { };
    var varsString = "";
    ajax.requestFile = "shoutbox.php";
    ajax.method = 'POST';
    ajax.element = 'shoutbox';
    ajax.sendAJAX(varsString);
    setTimeout("getShouts();", 10000);

    return false

}


function sb_Clear() {
    document.forms["shoutform"].shout.value = ''
    return true;
}



function deleteShout(id) {

    if (confirm("Вы точно хотите удалить это сообщение?")) {
        var ajax = new tbdev_ajax();
        ajax.onShow = function() { };
        var varsString = "";
        ajax.requestFile = "shoutbox.php";
        ajax.setVar("do", "delete");
        ajax.setVar("id", id);
        ajax.method = 'POST';
        ajax.element = 'shoutbox';
        ajax.sendAJAX(varsString);
    }
    
    return false

}
getShouts();

-->
</script>
<?php
end_frame();
}
//Чат Конец 

//Статистика 
begin_frame("Статистика");
if (!cache_check("stats", 600)) {
$registered = number_format(get_row_count("users"));
$torrents = number_format(get_row_count("torrents"));
$seeders = get_row_count("peers", "WHERE seeder='yes'");
list($f_seeders) = mysql_fetch_array(sql_query("SELECT sum(f_seeders) FROM torrents"));
$leechers = get_row_count("peers", "WHERE seeder='no'");
list($f_leechers) = mysql_fetch_array(sql_query("SELECT sum(f_peers) FROM torrents"));
//$warned_users = number_format(get_row_count("users", "WHERE warned = 'yes'"));
//$disabled = number_format(get_row_count("users", "WHERE enabled = 'no'"));
$uploaders = number_format(get_row_count("users", "WHERE class = ".UC_UPLOADER));
//$vip = number_format(get_row_count("users", "WHERE class = ".UC_VIP));
if ($leechers+$f_leechers == 0)
  $ratio = 0;
else
  $ratio = round(($seeders+$f_seeders) / ($leechers+$f_leechers) * 100);
$peers = number_format($seeders + $leechers);
$seeders = number_format($seeders);
$leechers = number_format($leechers);
$res = mysql_query("SELECT SUM(size)FROM torrents;") or sqlerr(__FILE__, __LINE__);
$arr = mysql_fetch_assoc($res) or die("ошибка доступа к БД ");
$result = mysql_query("SELECT SUM(downloaded) AS totaldl, SUM(uploaded) AS totalul FROM users") or sqlerr(__FILE__, __LINE__); 
$row = mysql_fetch_assoc($result); 
$stats['totaldownloaded'] = $row["totaldl"]; 
$stats['totaluploaded'] = $row["totalul"]; 
//$test = mksize($stats['totaluploaded'] + $stats['totaldownloaded']+1024*1024*1024*50);
	$stats = array(
		"registered" => $registered,
		"torrents" => $torrents,
		//"warned_users" => $warned_users,
		//"disabled" => $disabled,
		"uploaders" => $uploaders,
		//"vip" => $vip,
		"ratio" => $ratio,
		"peers" => $peers,
		"seeders" => $seeders,
		"leechers" => $leechers,
		"arr" => $arr,
		//"test" => $test,
		"f_leechers" => $f_leechers,
		"f_seeders" => $f_seeders
	);
	cache_write("stats", $stats);
} else {
	$stats = cache_read("stats");
	$registered = $stats["registered"];
	$torrents = $stats["torrents"];
	//$warned_users = $stats["warned_users"];
	//$disabled = $stats["disabled"];
	$uploaders = $stats["uploaders"];
	//$vip = $stats["vip"];
	$ratio = $stats["ratio"];
	$peers = $stats["peers"];
	$seeders = $stats["seeders"];
	$leechers = $stats["leechers"];
	$arr = $stats["arr"];
	//$test = $stats["test"];
	$f_leechers = $stats['f_leechers'];
	$f_seeders = $stats['f_seeders'];
}

print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">
		<tbody id=\"collapseobj_showstats\" style=\"\">	
	<tr>	
	<td class=\"rowhead\"><div align=\"right\">Пользователей</div></td>
	<td class=\"rowhead\" ><div align=\"right\"><b>".$registered."</b></div></td>
	<td class=\"rowhead\"><div align=\"right\">Торрентов</div></td>
	<td class=\"rowhead\"><div align=\"right\"><b>".$torrents."</b></div></td>
	<td class=\"rowhead\"><div align=\"right\" >Раздающих </div></td>

	<td class=\"rowhead\"><div align=\"right\"><b><font color=\"green\" title=\"Вместе с другими трекерами\">".($seeders+$f_seeders)."</font></b></div></td>
	<td class=\"rowhead\"><div align=\"right\">Качающих </div></td>
	<td class=\"rowhead\"><div align=\"right\"><b><font color=\"red\" title=\"Вместе с другими трекерами\">".($leechers+$f_leechers)."</font></b></div></td>
	</tr>
	<tr>
	<td class=\"rowhead\"><div align=\"right\"><font color=\"orange\">".$tracker_lang['users_uploaders']."</font></div></td>
	<td class=\"rowhead\"><div align=\"right\"><b>".$uploaders."</b></div></td>
	<td class=\"rowhead\"><div align=\"right\">Общий размер раздач</div></td>
	<td class=\"rowhead\"><div align=\"right\">".mksize($arr['SUM(size)'])."</div></td>
	
	<td class=\"rowhead\"><div align=\"right\">Подключений </div></td>
	<td class=\"rowhead\"><div align=\"right\">".$peers."</div></td>
	<td class=\"rowhead\"><div align=\"right\">Рейтинг</div></td>
	<td class=\"rowhead\"><div align=\"right\">".$ratio."%</div></td>

	</tr>
		<tr>
			<td colspan=\"8\" height=\"10\" align=\"center\" class=\"subheader\" onmouseover=\"this.style.backgroundColor='#E8E8FF';\" onmouseout=\"this.style.backgroundColor='#FFFFFF';\">
			<p align=center><font class=small><b>Статистика обновляется каждые 10 минут.</b></font></p>
			</td>
		</tr>");
print("</td></tr></tbody></table>");	
end_frame();
//Статистика - конец

//Кто онлайн ?
if (cache_check("online", 30) && cache_check("latest", 30) && cache_check("how", 30)) {
	$result = cache_read("online");
	$a = cache_read("latest");
	$how = cache_read("how");
} else {
$a = mysql_fetch_array(sql_query("SELECT id, username FROM users WHERE status='confirmed' ORDER BY id DESC LIMIT 1"));

$how = mysql_fetch_assoc(sql_query("SELECT * FROM much_on WHERE main = 'yes' ORDER BY date DESC"));

$title_who = array();

$dt = sqlesc(time() - 300);

if ($use_sessions)
    $result = sql_query("SELECT DISTINCT s.uid, s.username, s.class, s.ip FROM sessions AS s WHERE s.time > $dt ORDER BY s.class DESC");
else
	$result = sql_query("SELECT u.id, u.username, u.class FROM users AS u WHERE u.last_access > ".sqlesc(get_date_time(time() - 300))." GROUP BY u.username ORDER BY u.class DESC");

		$online_cache = array();
	while ($cache_data = mysql_fetch_array($result))
		$online_cache[] = $cache_data;

	cache_write("online", $online_cache);
	cache_write("latest", $a);
	cache_write("how", $how);
	$result = $online_cache;
}

foreach ($result as $arr) {
	list($uid, $uname, $class) = $arr;

    if (!empty($uname)) {
    	$title_who[] = "<a href=\"userdetails.php?id=".$uid."\" class=\"online\">".get_user_class_color($class, $uname)."</a>";
    }

    if ($class >= UC_USER) {
    	$users++;
	} elseif (empty($uname)) {
    	$guests++;
    }

    $total++;

	if (empty($uname))
		continue;
	else
		$who_online .= $title_who;

}

if ($staff == "")  $staff = 0;
if ($guests == "") $guests = 0;
if ($users == "")  $users = 0;
if ($total == "")  $total = 0;
begin_frame("Сейчас на сайте " . $users . " пользователей");
if (count($title_who)){
print("<table border=\"0\" width=\"100%\"><tr valign=\"top\"><td width=40 style='border: none'><img src=\"pic/whosonline.gif\" border=0 align=absmiddle></td><td align=\"left\" style=\"padding-top: 7px;\" class=\"embedded\"> ".@implode(", ", $title_who)."</td></tr></table>\n");
if($how[amount] < $total){ sql_query("UPDATE much_on SET amount = $total, date = NOW() WHERE main = 'yes'"); }
print("<hr>Рекорд одновременного посещения трекера: <b>" .$how[amount]. "</b> <br>Зафиксирован: $how[date]");  
}
else
print("<table border=\"0\" width=\"100%\"><tr valign=\"top\"><td width=40 style='border: none'><img src=\"pic/whosonline.gif\" border=0 align=absmiddle></td><td align=\"left\" style=\"padding-top: 7px;\" class=\"embedded\"> Нет активных пользователей</td></tr></table>\n");
end_frame();
//Конец Кто онлайн?
// Нагрузка на сервер
/*
$con = sql_query("SELECT userid FROM peers GROUP by userid");
$connected = mysql_num_rows($con);
$blocktitle = $tracker_lang['server_load'];
$avgload = get_server_load();
if (strtolower(substr(PHP_OS, 0, 3)) != 'win')
	$percent = $avgload * 4;
else
	$percent = $avgload;
if ($percent <= 50) $pic = "loadbargreen.gif";
elseif ($percent <= 70) $pic = "loadbaryellow.gif";
else $pic = "loadbarred.gif";
	$width = $percent * 4;
print("<center>
<table class=\"main\" border=\"0\" width=\"402\"><tr><td style=\"padding: 0px; background-repeat: repeat-x\" title=\"Нагрузка: $percent%, Средняя (LA): $avgload\">"
."<img height=\"15\" width=\"$width\" src=\"pic/$pic\" alt=\"Нагрузка: $percent%, Средняя (LA): $avgload\" title=\"Нагрузка: $percent%, Средняя (LA): $avgload\">"
."</td></tr></table>"
."<b>Всего к трекеру подключено уникальных $connected пользователей.</b></center>");
*/
// Конец Нагрузка на сервер

stdfoot();
?>