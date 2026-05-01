<?
require_once("include/bittorrent.php");

gzip();

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
        $s = "<b>" . count($arr) . " $name</b>\n";
        if (!count($arr))
                return $s;
        $s .= "\n";
        $s .= "<table width=100% class=main border=1 cellspacing=0 cellpadding=5>\n";
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

dbconn(false);

$id = (int)$_GET["id"];

if (!is_numeric($_GET['id'])) die ("Пошел нахуй отсюда");  

if ($_GET['lock_comments'] && get_user_class() >= UC_MODERATOR) {
    if ($_GET['lock_comments'] == 'yes' || $_GET['lock_comments'] == 'no')
        sql_query("UPDATE torrents SET comment_lock = ".sqlesc($_GET['lock_comments'])." WHERE id = $id");
        }  
		
$res = sql_query("SELECT torrents.seeders, torrents.tags, torrents.numratings, torrents.free, torrents.comment_lock, torrents.points, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(torrents.last_action) AS lastseed, torrents.name, IF(torrents.numratings < $minvotes, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.numfiles, torrents.image1, torrents.image2, categories.name AS cat_name, categories.id AS cat_id, users.username FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id")
        or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);

$owned = $moderator = 0;
        if (get_user_class() >= UC_MODERATOR)
                $owned = $moderator = 1;
        elseif ($CURUSER["id"] == $row["owner"])
                $owned = 1;
//}

///// 18 only mod
    if ($row["cat_name"]=="XXX") {
        $birthday = $CURUSER["birthday"];
        $birthday = date("U", strtotime($birthday));
        if($CURUSER[birthday] != "0000-00-00" && (date("U") - $birthday) > 567648000) {
        //do nothing, allowed
        } else {
        stderr($tracker_lang['error'], "Вам запрещено просматривать эту категорию, так как вам нету 18 лет.");
        }
    } 
///// 18 only mod end  	

if (!$row)
        stderr($tracker_lang['error'], $tracker_lang['no_torrent_with_such_id']);	
else {
        if ($_GET["hit"]) {
                sql_query("UPDATE torrents SET views = views + 1 WHERE id = $id");
                if ($_GET["tocomm"])
                        header("Location: $DEFAULTBASEURL/details.php?id=$id&page=0#startcomments");
                elseif ($_GET["filelist"])
                        header("Location: $DEFAULTBASEURL/details.php?id=$id&filelist=1#filelist");
                elseif ($_GET["toseeders"])
                        header("Location: $DEFAULTBASEURL/details.php?id=$id&dllist=1#seeders");
                elseif ($_GET["todlers"])
                        header("Location: $DEFAULTBASEURL/details.php?id=$id&dllist=1#leechers");
                else
                        header("Location: $DEFAULTBASEURL/details.php?id=$id");
                exit();
        }

        if (!isset($_GET["page"])) {
                stdhead($tracker_lang['torrent_details']." \"" . $row["name"] . "\"", 'all');
                                ?> 
<link rel="stylesheet" href="highslide/highslide.css" type="text/css" media="screen" /> 
<link rel="stylesheet" href="css/starbox.css" type="text/css" media="screen" />
<script type="text/javascript" src="highslide/highslide.js"></script> 
<script language="javascript" type="text/javascript" src="js/prototype.js"></script>
<script language="javascript" type="text/javascript" src="js/scriptaculous.js?load=effects,builder,controls"></script>
<script language="javascript" type="text/javascript" src="js/starbox.js"></script>
<?
                begin_frame($row["name"]);

                if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
                        $owned = 1;
                else
                        $owned = 0;

                $spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

                $prive = "";
                $s=$row["name"];

                print("<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\" style=\"border:none;\">\n");


                function hex_esc($matches) {
                        return sprintf("%02x", ord($matches[0]));
                }
				
				
				        $s = "";
				        
        if (!$owned || $moderator) {
        if($CURUSER) {
            $xres = sql_query("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $CURUSER["id"]);
            $xrow = mysql_fetch_array($xres);
            $s .= "<div id=\"ajax_vote\" style=\"font-family:Arial,Helvetica,sans-serif;font-size:0.8em;color:#9d9d9d;\"></div>";
            $s .= "<script language=\"javascript\" type=\"text/javascript\">";
            $s .= "new Starbox('ajax_vote', " . (float) $row["rating"] . ", {";
            $s .= "    onRate:function(element, memo) {";
            $s .= "        var ajax = new tbdev_ajax();";
            $s .= "        ajax.method = 'POST';";
            $s .= "        ajax.requestFile = 'takerate.php';";
            $s .= "        ajax.setVar('torrentid', " . $id . ");";
            $s .= "        ajax.setVar('rating', memo['rated']);";
            $s .= "        ajax.setVar('ajax', 'yes');";
            $s .= "        ajax.sendAJAX();";
            $s .= "    },";
            $s .= "    className:'pointy',";
            $s .= "    indicator:'" . $tracker_lang["rating"] . " #{average} " . $tracker_lang['from'] . " #{max} (#{total} " . $tracker_lang[(total == 1 ?'voted_one':'voted_more')] . ")',";
            $s .= "    max:5,";
            $s .= "    buttons:10,";
            $s .= "    total:" . (int) $row["numratings"] . ",";
            $s .= "    rated:" . ($xrow||!isset($CURUSER)?"true":"false") . ",";
            $s .= "    rerate:false});";
            $s .= "</script><br>";
            }
        }
		

		                if ($row["image1"] != "" OR $row["image2"] != "") {
                  if ($row["image1"] != "")
                    $img1 = "<a href=\"torrents/images/$row[image1]\" class=\"highslide\" onclick=\"return hs.expand(this)\"><img border='0' width=250 src='torrents/images/$row[image1]' /></a>";
                  if ($row["image2"] != "")
                    $img2 = "<a href='viewimage.php?pic=$row[image2]'><img border='0' src='torrents/images/$row[image1]' /></a>";
}

						foreach(explode(",", $row["tags"]) as $tag)
                $tags .= "<a style=\"font-weight:normal;color:green;\" href=\"browse.php?tag=".$tag."\">".$tag."</a>, ";

                if ($tags)
                $tags = substr($tags, 0, -2);

//                print('<td style="border:none;"><div style="float:right">'.$img1.'<br><br><center>'.$s.'</center></div><div align="left" style="margin:8px; width:900px;">'.format_comment($row["descr"]).'</div>');
			    print("<td valign='middle' align='left' width='185'>".$img1."<br><br><center>".$s."</center></td>");
				if ($row["free"] == 100) {
				print("<td class='outer' valign='top' align='left'><div class=\"success\"><b>Скачивание данной раздачи не учитывается.</b></div>".format_comment($row["descr"])."</td>");
				} else {
				print("<td class='outer' valign='top' align='left'>".format_comment($row["descr"])."</td>");
				}
									                   $url = "edit.php?id=" . $row["id"];
                $main = "ontop.php?id=" . $row["id"];
				$new = "new_movie.php?id=" .$row["id"];
                if (isset($_GET["returnto"])) {
                        $addthis = "&amp;returnto=" . urlencode($_GET["returnto"]);
                        $url .= $addthis;
                        $keepget .= $addthis;
                }
                $editlink = "a href=\"$url\" class=\"sublink\"";
                $mainlink = "a href=\"$main\" class=\"sublink\"";
                 
				                $uprow = (isset($row["username"]) ? ("<a href=userdetails.php?id=" . $row["owner"] . ">" . htmlspecialchars($row["username"]) . "</a>") : "<i>Аноним</i>");
     if(!$CURUSER)
        $s = "<font color=\"red\"><b>Что-бы скачать этот торрент <a href=\"signup.php\">зарегестрируйтесь</a>.</b></font>";
				                				                                if($CURUSER)
                $s = "".($row["banned"] == "no" ? "<a class=\"index\" href=\"download.php?id=$id&amp;name=" . rawurlencode($row["filename"]) . "\"><b>Скачать торрент</b></a> | ". $row["seeders"] . " ".$tracker_lang['seeders_l'].", " . $row["leechers"] . " ".$tracker_lang['leechers_l']." = " . ($row["seeders"] + $row["leechers"]) . " ".$tracker_lang['peers_l']."" : "Раздача приостановлена")."";
                if ($owned)
                $s .= " $spacer<$editlink>[".$tracker_lang['edit']."]</a>&nbsp;<$mainlink>[На главную]</a>";
					   tr ("<nobr>{$row["cat_name"]}</nobr>", $s, 1, 1, "10%");
               print("</table>");

					end_frame();
					begin_frame("Информация");
if($CURUSER) {					
$torrentid = ( is_numeric($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 );
list($count777) = mysql_fetch_row(sql_query("SELECT COUNT(*) FROM thanks WHERE torrentid = $torrentid AND userid = $CURUSER[id] LIMIT 1")) or sqlerr(__FILE__,__LINE__);

if ($row['owner'] == $CURUSER['id'] || $count777 != 0)
     $can_not_thanks = true;
          
$thanksby .= "<input type=\"button\" name=\"send_thanks\" id=\"send_thanks\" value=\"Сказать спасибо\" ".($can_not_thanks == true ? " disabled" : "")." />&nbsp;<span id=\"thanks_msg\"></span>";
$thanksby .= "<input type=\"hidden\" name=\"torrentid\" id=\"torrentid\" value=\"{$torrentid}\">";

$thanksby .= "<div style=\"margin-top:7px;\">";
$thanksby .= "<div class=\"spoiler_head\" id=\"show_thanks\"><img border=\"0\" src=\"pic/plus.gif\" title=\"Показать\">&nbsp;&nbsp;Последние поблагодарившие</div>";
$thanksby .= "<div class=\"spoiler_body\" style=\"display:none;\" id=\"thanks_body\"></div></div>";
?>
<script type="text/javascript">
   
   jQuery(document).ready(function(){
     var t_load   = '<img src="pic/upload.gif" title="Загрузка" />';
     var t_img_p  = '<img src="pic/plus.gif" />';
     var t_img_m  = '<img src="pic/minus.gif" />';
      
                 jQuery('#show_thanks').toggle(
                        function() {
                            jQuery('#thanks_body').slideDown( 'fast' ); 
                            jQuery('#thanks_body').empty();
                            jQuery('#thanks_body').append(t_load + '&nbsp;'); 
        jQuery.post('thanks_new.php',{'tid':jQuery('#torrentid').val(),'do':'show_thanks'},
                        function(data,status)
                        {
                            if(status != 'error')
                            {
                                jQuery('#thanks_body').empty();
                                jQuery('#thanks_body').append(data);
                            }
                            else
                            {
                                jQuery('#thanks_body').empty();
                                alert( 'Произошла ошибка' );
                                  return false;
                            }
                            
                        },'html');
                            jQuery(this).html(t_img_m + '&nbsp;&nbsp;Последние поблагодарившие');
                        },
                        function() {
                            jQuery('#thanks_body').slideUp( 'fast' );
                            jQuery(this).html(t_img_p + '&nbsp;&nbsp;Последние поблагодарившие');
                        }
                    );
             
     function SendThanks()
     {
         jQuery('#send_thanks').get(0).disabled = 'disabled';
         jQuery.post('thanks_new.php',{'tid':jQuery('#torrentid').val(),'do':'send_thanks'},
                         function(data,status)
                         {
                             if(status != 'error')
                             {
                                 jQuery('#thanks_msg').html( '&nbsp; <b>Ваша благодарность добавлена</b>' ).fadeOut(3000);
                             }
                             else
                             {
                                alert( 'Произошла ошибка. Попробуйте поже' );
                                  return false;
                             }
                         },'html');
        
     }
     
     jQuery('#send_thanks').click(SendThanks);
   
  });
</script>
<?
}
					print('<table width="100%" cellspacing="0" cellpadding="5">
<tr><td width="33%"><b>Забанен: </b>'.($row["banned"] == 'no' ? $tracker_lang['no'] : $tracker_lang['yes']).'</td><td width="33%"><b>Размер: </b> '.mksize($row["size"]).'</td><td width="33%"><b>Активность: </b>'.$row["seeders"].' раздающих '.$row["leechers"].' качающих</td></tr>
<tr><td width="33%"><b>Скидка: </b><b>'.$row["free"].'%</b></td>
<td colspan="2"><b>Тэги: </b>'.$tags.'</a></td>
</tr>
<tr><td colspan="3">'.$thanksby.'</td></tr>
</table>
');
					end_frame();
					begin_frame("Пиры");
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

					end_frame();
					begin_frame("Комментарии");
		?>
		
		<?
		 print("<p><a name=\"startcomments\"></a></p>\n");

        $subres = sql_query("SELECT COUNT(*) FROM comments WHERE torrent = $id");
        $subrow = mysql_fetch_array($subres);
        $count = $subrow[0];

        $limited = 10;

if (!$count) {
  print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
  print("<tr><td class=colhead align=\"left\" colspan=\"2\"> <a name=comments>&nbsp;</a><b>Комментарии</b></td></tr>");
  print("<tr><td align=\"center\" >");
  print("<form name=comment method=\"post\" action=\"comment.php?action=add\">");
  print("<div>");
  textbbcode("comment","text","");
  print("</div>");
  print("</td></tr><tr><td align=\"left\" colspan=\"2\">");
  print("<input type=\"hidden\" name=\"tid\" value=\"$id\"/>");
  print("<input type=\"submit\" class=btn value=\"Разместить комментарий\" />");
  print("</td></tr></form></table>");

        }
        else {
                list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "details.php?id=$id&", array(lastpagedefault => 1));

                $subres = sql_query("SELECT c.id, c.ip, c.text, c.user, c.added, c.editedby, c.editedat, u.avatar, u.warned, ".
                  "u.username, u.title, u.class, u.donor, u.downloaded, u.uploaded, u.gender, u.last_access, e.username AS editedbyname FROM comments AS c LEFT JOIN users AS u ON c.user = u.id LEFT JOIN users AS e ON c.editedby = e.id WHERE torrent = " .
                  "$id ORDER BY c.id $limit") or sqlerr(__FILE__, __LINE__);
                $allrows = array();
                while ($subrow = mysql_fetch_array($subres))
                        $allrows[] = $subrow;


         print("<div id=\"takecomment\"><table class=main cellspacing=\"0\" cellPadding=\"5\" width=\"100%\" >");
         print("<tr><td style=\"border:none;\">");
         commenttable($allrows);
         print("</td></tr>");
         print("<tr><td style=\"border:none;\">");
         print($pagerbottom);
         print("</td></tr>");
         print("</table>");



  print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
//  print("<tr><td class=colhead align=\"left\" colspan=\"2\">  <a name=comments>&nbsp;</a><b>:: Добавить комментарий к торренту</b></td></tr>");
  print("<tr><td width=\"100%\" align=\"center\" >");
  //print("Ваше имя: ");
  //print("".$CURUSER['username']."<p>");
  print("<form name=comment method=\"post\" action=\"comment.php?action=add\">");
 // print("<center><table border=\"0\"><tr><td class=\"clear\">");
  print("<div align=\"center\">". textbbcode("comment","text","", 1) ."</div>");
 // print("</td></tr></table></center>");
  print("</td></tr><tr><td  align=\"left\" colspan=\"2\">");
  print("<input type=\"hidden\" name=\"tid\" value=\"$id\"/>");
 // print("<input type=\"submit\" class=btn value=\"Разместить комментарий\" />");
  print('<a id="'.$row['id'].'" class="add_comment" method="send" href="javascript:void(0)">Комментировать</a>');
  print("</td></tr></form></table></div>");

        }

}
}
end_frame();
stdfoot();

?>

