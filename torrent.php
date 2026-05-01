<?php

require_once("include/bittorrent.php");
dbconn();
header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);
header ("Cache-control: no-store");
header ("Pragma: no-cache");

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'POST')
{
    $id = (int)$_POST["torrent"];
    $act = (string)$_POST["act"];
	
	    if (!is_valid_id($id) || empty($act))
    	die("Ошибка");
		
function getagent($httpagent, $peer_id = "") {
        if (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]\_B([0-9][0-9|*])(.+)$)/", $httpagent, $matches))
        return "Azureus/$matches[1]";
        elseif (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]\_CVS)/", $httpagent, $matches))
        return "Azureus/$matches[1]";
        elseif (preg_match("/^Java\/([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
        return "Azureus/<2.0.7.0";
        elseif (preg_match("/^Azureus ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
        return "Azureus/$matches[1]";
        elseif (preg_match("/BitTorrent\/S-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
        return "Shadow's/$matches[1]";
        elseif (preg_match("/BitTorrent\/U-([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
        return "UPnP/$matches[1]";
        elseif (preg_match("/^BitTor(rent|nado)\\/T-(.+)$/", $httpagent, $matches))
        return "BitTornado/$matches[2]";
        elseif (preg_match("/^BitTornado\\/T-(.+)$/", $httpagent, $matches))
        return "BitTornado/$matches[1]";
        elseif (preg_match("/^BitTorrent\/ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
        return "ABC/$matches[1]";
        elseif (preg_match("/^ABC ([0-9]+\.[0-9]+(\.[0-9]+)*)\/ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
        return "ABC/$matches[1]";
        elseif (preg_match("/^Python-urllib\/.+?, BitTorrent\/([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
        return "BitTorrent/$matches[1]";
        elseif (preg_match("/^BitTorrent\/brst(.+)/", $httpagent, $matches))
        return "Burst";
        elseif (preg_match("/^RAZA (.+)$/", $httpagent, $matches))
        return "Shareaza/$matches[1]";
        elseif (preg_match("/Rufus\/([0-9]+\.[0-9]+\.[0-9]+)/", $httpagent, $matches))
        return "Rufus/$matches[1]";
        elseif (preg_match("/^Python-urllib\\/([0-9]+\\.[0-9]+(\\.[0-9]+)*)/", $httpagent, $matches))
        return "G3 Torrent";
        elseif (preg_match("/MLDonkey\/([0-9]+).([0-9]+).([0-9]+)*/", $httpagent, $matches))
        return "MLDonkey/$matches[1].$matches[2].$matches[3]";
        elseif (preg_match("/ed2k_plugin v([0-9]+\\.[0-9]+).*/", $httpagent, $matches))
        return "eDonkey/$matches[1]";
        elseif (preg_match("/uTorrent\/([0-9]+)([0-9]+)([0-9]+)([0-9A-Z]+)/", $httpagent, $matches))
        return "µTorrent/$matches[1].$matches[2].$matches[3].$matches[4]";
        elseif (preg_match("/CT([0-9]+)([0-9]+)([0-9]+)([0-9]+)/", $peer_id, $matches))
        return "cTorrent/$matches[1].$matches[2].$matches[3].$matches[4]";
        elseif (preg_match("/Transmission\/([0-9]+).([0-9]+)/", $httpagent, $matches))
        return "Transmission/$matches[1].$matches[2]";
        elseif (preg_match("/KT([0-9]+)([0-9]+)([0-9]+)([0-9]+)/", $peer_id, $matches))
        return "KTorrent/$matches[1].$matches[2].$matches[3].$matches[4]";
        elseif (preg_match("/rtorrent\/([0-9]+\\.[0-9]+(\\.[0-9]+)*)/", $httpagent, $matches))
        return "rTorrent/$matches[1]";
        elseif (preg_match("/^ABC\/Tribler_ABC-([0-9]+\.[0-9]+(\.[0-9]+)*)/", $httpagent, $matches))
        return "Tribler/$matches[1]";
        elseif (preg_match("/^BitsOnWheels( |\/)([0-9]+\\.[0-9]+).*/", $httpagent, $matches))
        return "BitsOnWheels/$matches[2]";
        elseif (preg_match("/BitTorrentPlus\/(.+)$/", $httpagent, $matches))
        return "BitTorrent Plus!/$matches[1]";
        elseif (ereg("^Deadman Walking", $httpagent))
        return "Deadman Walking";
        elseif (preg_match("/^eXeem( |\/)([0-9]+\\.[0-9]+).*/", $httpagent, $matches))
        return "eXeem$matches[1]$matches[2]";
        elseif (preg_match("/^libtorrent\/(.+)$/", $httpagent, $matches))
        return "libtorrent/$matches[1]";
        elseif (substr($peer_id, 0, 12) == "d0c")
        return "Mainline";
        elseif (substr($peer_id, 0, 1) == "M")
        return "Mainline/Decoded";
        elseif (substr($peer_id, 0, 3) == "-BB")
        return "BitBuddy";
        elseif (substr($peer_id, 0, 8) == "-AR1001-")
        return "Arctic Torrent/1.2.3";
        elseif (substr($peer_id, 0, 6) == "exbc\08")
        return "BitComet/0.56";
        elseif (substr($peer_id, 0, 6) == "exbc\09")
        return "BitComet/0.57";
        elseif (substr($peer_id, 0, 6) == "exbc\0:")
        return "BitComet/0.58";
        elseif (substr($peer_id, 0, 4) == "-BC0")
        return "BitComet/0.".substr($peer_id, 5, 2);
        elseif (substr($peer_id, 0, 7) == "exbc.\0L")
        return "BitLord/1.0";
        elseif (substr($peer_id, 0, 7) == "exbc..L")
        return "BitLord/1.1";
        elseif (substr($peer_id, 0, 3) == "346")
        return "TorrenTopia";
        elseif (substr($peer_id, 0, 8) == "-MP130n-")
        return "MooPolice";
        elseif (substr($peer_id, 0, 8) == "-SZ2210-")
        return "Shareaza/2.2.1.0";
        elseif (ereg("^0P3R4H", $httpagent))
        return "Opera BT Client";
        elseif (substr($peer_id, 0, 6) == "A310--")
        return "ABC/3.1";
        elseif (ereg("^XBT Client", $httpagent))
        return "XBT Client";
        elseif (ereg("^BitTorrent\/BitSpirit$", $httpagent))
        return "BitSpirit";
        elseif (ereg("^DansClient", $httpagent))
        return "XanTorrent";
        else
        return "Unknown";
}		
		
function dltable($name, $arr, $torrent)
{

        global $CURUSER, $tracker_lang;
		 $s = "<table width=100% class=main border=1 cellspacing=0 cellpadding=5>\n";
	     $s .= "<tr><td colspan=\"12\"><b>" . count($arr) . " $name</b></td></tr>\n";
        if (!count($arr))
                return $s."</table>\n";

        $s .= "<tr><td class=colhead>".$tracker_lang['user']."</td>" .
          "<td class=colhead align=center>".$tracker_lang['port_open']."</td>".
          "<td class=colhead align=right>".$tracker_lang['uploaded']."</td>".
          "<td class=colhead align=right>".$tracker_lang['ul_speed']."</td>".
          "<td class=colhead align=right>".$tracker_lang['downloaded']."</td>" .
          "<td class=colhead align=right>".$tracker_lang['dl_speed']."</td>" .
          "<td class=colhead align=right>".$tracker_lang['ratio']."</td>" .
          "<td class=colhead align=right>".$tracker_lang['completed']."</td>" .
          "<td class=colhead align=right>".$tracker_lang['connected']."</td>" .
          "<td class=colhead align=right>".$tracker_lang['idle']."</td>" .
          "<td class=colhead align=left>".$tracker_lang['client']."</td></tr>\n";
        $now = time();
        $moderator = (isset($CURUSER) && get_user_class() >= UC_MODERATOR);
		$mod = get_user_class() >= UC_MODERATOR;
        foreach ($arr as $e) {
                // user/ip/port
                // check if anyone has this ip
                $s .= "<tr>\n";
                if ($e["username"])
                  $s .= "<td><a href=\"userdetails.php?id=$e[userid]\"><b>".get_user_class_color($e["class"], $e["username"])."</b></a>".($mod ? "&nbsp;[<span title=\"{$e["ip"]}\" style=\"cursor: pointer\">IP</span>]" : "")."</td>\n";
                else
                  $s .= "<td>" . ($mod ? $e["ip"] : preg_replace('/\.\d+$/', ".xxx", $e["ip"])) . "</td>\n";
                $secs = max(10, ($e["la"]) - $e["pa"]);
                $revived = $e["revived"] == "yes";
        		$s .= "<td align=\"center\">" . ($e[connectable] == "yes" ? "<span style=\"color: green; cursor: help;\" title=\"Порт открыт. Этот пир может подключатся к любому пиру.\">".$tracker_lang['yes']."</span>" : "<span style=\"color: red; cursor: help;\" title=\"Порт закрыт. Рекомендовано проверить настройки Firwewall'а.\">".$tracker_lang['no']."</span>") . "</td>\n";
                $s .= "<td align=\"right\"><nobr>" . mksize($e["uploaded"]) . "</nobr></td>\n";
                $s .= "<td align=\"right\"><nobr>" . mksize($e["uploadoffset"] / $secs) . "/s</nobr></td>\n";
                $s .= "<td align=\"right\"><nobr>" . mksize($e["downloaded"]) . "</nobr></td>\n";
                //if ($e["seeder"] == "no")
                        $s .= "<td align=\"right\"><nobr>" . mksize($e["downloadoffset"] / $secs) . "/s</nobr></td>\n";
                /*else
                        $s .= "<td align=\"right\"><nobr>" . mksize($e["downloadoffset"] / max(1, $e["finishedat"] - $e["st"])) . "/s</nobr></td>\n";*/
                if ($e["downloaded"]) {
                  $ratio = floor(($e["uploaded"] / $e["downloaded"]) * 1000) / 1000;
                    $s .= "<td align=\"right\"><font color=" . get_ratio_color($ratio) . ">" . number_format($ratio, 3) . "</font></td>\n";
                } else
					if ($e["uploaded"])
	                  	$s .= "<td align=\"right\">Inf.</td>\n";
					else
	                  	$s .= "<td align=\"right\">---</td>\n";
                $s .= "<td align=\"right\">" . sprintf("%.2f%%", 100 * (1 - ($e["to_go"] / $torrent["size"]))) . "</td>\n";
                $s .= "<td align=\"right\">" . mkprettytime($now - $e["st"]) . "</td>\n";
                $s .= "<td align=\"right\">" . mkprettytime($now - $e["la"]) . "</td>\n";
                $s .= "<td align=\"left\">" . htmlspecialchars(getagent($e["agent"], $e["peer_id"])) . "</td>\n";
                $s .= "</tr>\n";
        }
        $s .= "</table>\n";
        return $s;
}
		
if ($act == "info")
{
if($CURUSER)
$res = sql_query("SELECT torrents.*, karma.id AS canrate FROM torrents LEFT JOIN karma ON karma.type='torrent' AND karma.value = torrents.id AND user =".$CURUSER['id']." WHERE torrents.id = $id") or sqlerr(__FILE__, __LINE__);
else
$res = sql_query("SELECT * FROM torrents WHERE id = $id") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
print("<table width=\"100%\" border=\"0\" cellpadding=\"5\">");
// Начало тегов
foreach(explode(",", $row["tags"]) as $tag)
$tags .= "<a style=\"font-weight:normal;color:green;\" href=\"browse.php?tag=".$tag."\">".$tag."</a>, ";
if ($tags)
$tags = substr($tags, 0, -2);
// Конец тегов
// Начала спасибок
if($CURUSER) {					
list($count777) = mysql_fetch_row(sql_query("SELECT COUNT(*) FROM thanks WHERE torrentid = $id AND userid = $CURUSER[id] LIMIT 1")) or sqlerr(__FILE__,__LINE__);

if ($row['owner'] == $CURUSER['id'] || $count777 != 0)
     $can_not_thanks = true;
          
}
// Конец спасибок
print("<tr><td width=\"40%\" style=\"background-color: #EEEEEE; border-bottom: none; border-right: none;\" align=\"left\"><a href=\"download.php?id=".$id."&name=".$row["name"]."\" style=\"color: black;\"><font style=\"font-size:14pt;\">Скачать</font></a>&nbsp;<sup><font style=\"font-size:8pt;\">".mksize($row["size"])."</font></sup></td>");
print("<td width=\"20%\" style=\"background-color: #EEEEEE; border-bottom: none; border-left: none; border-right: none;\" align=\"center\">");
if (!$CURUSER || $row["canrate"] > 0 || $CURUSER['id'] == $row['owner'])
print("<div><img src=\"pic/minus-dis.png\" title=\"Вы не можете голосовать\" alt=\"\" /> " . karma($row["karma"]) . " <img src=\"pic/plus-dis.png\" title=\"Вы не можете голосовать\" alt=\"\" /></div>");
else
print("<div id=\"karma$id\"><img src=\"pic/minus.png\" style=\"cursor:pointer;\" title=\"Уменьшить карму\" alt=\"\" onclick=\"javascript: karma('$id', 'torrent', 'minus');\" /> " . karma($row["karma"]) . " <img src=\"pic/plus.png\" style=\"cursor:pointer;\" onclick=\"javascript: karma('$id', 'torrent', 'plus');\" title=\"Увеличить карму\" alt=\"\" /></div>");

print("</td>");
print("<td width=\"40%\" style=\"background-color: #EEEEEE; border-left: none; border-bottom: none;\" align=\"right\">");
if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR) {
print("<a href=\"edit.php?id=".$id."\"><img src=\"pic/edit.png\" border=\"0\" title=\"Редактировать раздачу\"></a>");
}
if($can_not_thanks == true) {
print("</td></tr>");
} else {
print("<input type=\"hidden\" name=\"torrentid\" id=\"torrentid\" value=\"{$torrentid}\">");
print("<span id=\"thanks_msg\"></span>&nbsp;<img src=\"pic/thanks.png\" title=\"Сказать спасибо\" name=\"send_thanks\" id=\"send_thanks\" style=\"cursor: pointer;\">");
print("</td></tr>");
}
print("</div>");
print("<tr><td colspan=\"3\" style=\"border-top: none;\"><div style=\"float: left;\">".$tags."</div>");
print("</td></tr>");
print("<tr><td valign=\"top\" colspan=\"3\" ");
if($row['multitracker']==0)
print("<b><font color=\"#BB0000\">Скидка:</font> <font color=\"red\">".$row["free"]."%</font></b>");
else
print("<b><font color=\"#BB0000\">Данный торрент является мультитрекерным - скачивание полностью не учитывается.</font></b>");
print("</td></tr>");
print("<tr><td valign=\"top\" style=\"border-top: none; border-bottom: none; border-right: none\" colspan=\"2\"><span style=\"font-size: 14;\"><b>".$row["name"]."</b></span>");
print("<br><br><span align=\"justify\">".format_comment($row["descr"])."</span>");
print("</td><td valign=\"top\" align=\"right\" style=\"border-top: none; border-bottom: none; border-left: none;\" >");
if($row['image1'])
print("<img src=\"torrents/images/".$row["image1"]."\" width=\"250\">");
print("</td></tr>");
print("</table>");

}
if ($act == "peers")
{
$res = sql_query("SELECT torrents.seeders, torrents.tags, torrents.numratings, torrents.free, torrents.comment_lock, torrents.points, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(torrents.last_action) AS lastseed, torrents.name, IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.numfiles, torrents.image1, torrents.image2, torrents.multitracker, torrents.tracker_cache, torrents.f_peers, torrents.f_seeders, categories.name AS cat_name, categories.id AS cat_id, users.username FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id")
        or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
if(empty($row)) die('Ошибка');
					    $downloaders = array();
                        $seeders = array();
                        $subres = sql_query("SELECT seeder, finishedat, downloadoffset, uploadoffset, peers.ip, port, peers.uploaded, peers.downloaded, to_go, UNIX_TIMESTAMP(started) AS st, connectable, agent, peer_id, UNIX_TIMESTAMP(last_action) AS la, UNIX_TIMESTAMP(prev_action) AS pa, userid, users.username, users.class FROM peers INNER JOIN users ON peers.userid = users.id WHERE torrent = $id") or sqlerr(__FILE__, __LINE__);
                        while ($subrow = mysql_fetch_array($subres)) {
                                if ($subrow["seeder"] == "yes")
                                        $seeders[] = $subrow;
                                else
                                        $downloaders[] = $subrow;
                        }

                        function leech_sort($a,$b) {
                                if ( isset( $_GET["usort"] ) ) return seed_sort($a,$b);
                                $x = $a["to_go"];
                                $y = $b["to_go"];
                                if ($x == $y)
                                        return 0;
                                if ($x < $y)
                                        return -1;
                                return 1;
                        }
                        function seed_sort($a,$b) {
                                $x = $a["uploaded"];
                                $y = $b["uploaded"];
                                if ($x == $y)
                                        return 0;
                                if ($x < $y)
                                        return 1;
                                return -1;
                        }

                        usort($seeders, "seed_sort");
                        usort($downloaders, "leech_sort");

                        print(dltable("Раздающие", $seeders, $row));
                        if($row["leechers"]) {
                        print(dltable("Качающие", $downloaders, $row));
                        }
if($row['multitracker']==1)
{
	?><br>
<table cellspacing="0" cellpadding="5">
<tr><td colspan="3"><b>На других трекерах</b></td></tr>
<tr><td><b>Трекер</b></td><td><b>Раздающие</b></td><td><b>Качающие</b></td></tr>
<?php
	$list = explode("\n",$row['tracker_cache']);
	foreach($list as $tracker)
	{
		$stat = explode(':',$tracker);
		if($stat[1]!=='false')
		print("<tr><td>".$stat[0]."</td><td>".$stat[2]."</td><td>".$stat[1]."</td></tr>");	
	}
	print("<tr><td><b>Всего</b></td><td><b>".$row['f_seeders']."</b></td><td><b>".$row['f_peers'].'</b></td></tr>');
	print("</table>");
}
}
if($act == "thanks") {
		  if($id)
		  {
			  $count_sql = sql_query("SELECT COUNT(*) FROM thanks WHERE torrentid = $id");
	          $count_row = mysql_fetch_row($count_sql);
			  if($count_row===false) die('Ошибка');
	          $count = intval($count_row['0']);

	          if ($count == 0) {
		        $thanksby = "Никто не поставил спасибо этому торренту.";
				
				echo $thanksby;
	          } else {
			  $thanked_sql = sql_query("SELECT thanks.userid, thanks.added, users.username, users.class FROM thanks INNER JOIN users ON thanks.userid = users.id WHERE thanks.torrentid = $id ORDER BY thanks.id ASC");
		      while ($thanked_row = mysql_fetch_assoc($thanked_sql))
		      {
				  if(($thanked_row["userid"] == $CURUSER["id"]) || ($thanked_row["userid"] == $row["owner"]))
			        $can_not_thanks = true;
			
			        $userid   = intval($thanked_row['userid']);
			        $username = htmlspecialchars($thanked_row['username']);
			        $class    = intval($thanked_row['class']);
			        $thanksby .= "<a href=\"userdetails.php?id={$userid}\">".get_user_class_color($class, $username)."</a>&nbsp;<i>(".nicetime($thanked_row['added']).")</i>, ";
			  }
			  
		if ($thanksby)
			$thanksby = substr($thanksby, 0, -2);
			
			echo $thanksby;
		  }
		   }
}
}
?>