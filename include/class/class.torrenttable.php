<?php
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');
  
function torrenttable($res, $variant = "index", $returnto = '') {
                global $pic_base_url, $CURUSER, $use_wait, $use_ttl, $ttl_days, $tracker_lang;
				
	if ($use_wait)
  if (($CURUSER["class"] < UC_VIP) && $CURUSER) {
                  $gigs = $CURUSER["uploaded"] / (1024*1024*1024);
                  $ratio = (($CURUSER["downloaded"] > 0) ? ($CURUSER["uploaded"] / $CURUSER["downloaded"]) : 0);
                  if ($ratio < 0.5 || $gigs < 5) $wait = 48;
                  elseif ($ratio < 0.65 || $gigs < 6.5) $wait = 24;
                  elseif ($ratio < 0.8 || $gigs < 8) $wait = 12;
                  elseif ($ratio < 0.95 || $gigs < 9.5) $wait = 6;
                  else $wait = 0;
  }

print("<tr>\n");
$count_get = 0;

foreach ($_GET as $get_name => $get_value) {

$get_name = mysql_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_name)));

$get_value = mysql_escape_string(strip_tags(str_replace(array("\"","'"),array("",""),$get_value)));

if ($get_name != "sort" && $get_name != "type") {
if ($count_get > 0) {
$oldlink = $oldlink . "&" . $get_name . "=" . $get_value;
} else {
$oldlink = $oldlink . $get_name . "=" . $get_value;
}
$count_get++;
}

}

if ($count_get > 0) {
$oldlink = $oldlink . "&";
}


if ($_GET['sort'] == "1") {
if ($_GET['type'] == "desc") {
$link1 = "asc";
} else {
$link1 = "desc";
}
}

if ($_GET['sort'] == "2") {
if ($_GET['type'] == "desc") {
$link2 = "asc";
} else {
$link2 = "desc";
}
}

if ($_GET['sort'] == "3") {
if ($_GET['type'] == "desc") {
$link3 = "asc";
} else {
$link3 = "desc";
}
}

if ($_GET['sort'] == "4") {
if ($_GET['type'] == "desc") {
$link4 = "asc";
} else {
$link4 = "desc";
}
}

if ($_GET['sort'] == "5") {
if ($_GET['type'] == "desc") {
$link5 = "asc";
} else {
$link5 = "desc";
}
}

if ($_GET['sort'] == "7") {
if ($_GET['type'] == "desc") {
$link7 = "asc";
} else {
$link7 = "desc";
}
}

if ($_GET['sort'] == "8") {
if ($_GET['type'] == "desc") {
$link8 = "asc";
} else {
$link8 = "desc";
}
}

if ($_GET['sort'] == "9") {
if ($_GET['type'] == "desc") {
$link9 = "asc";
} else {
$link9 = "desc";
}
}

if ($_GET['sort'] == "10") {
if ($_GET['type'] == "desc") {
$link10 = "asc";
} else {
$link10 = "desc";
}
}

if ($link1 == "") { $link1 = "asc"; } // for torrent name
if ($link2 == "") { $link2 = "desc"; }
if ($link3 == "") { $link3 = "desc"; }
if ($link4 == "") { $link4 = "desc"; }
if ($link5 == "") { $link5 = "desc"; }
if ($link7 == "") { $link7 = "desc"; }
if ($link8 == "") { $link8 = "desc"; }
if ($link9 == "") { $link9 = "desc"; }
if ($link10 == "") { $link10 = "desc"; }

?>
<td class="colhead" align="center"><img src="pic/torrenttable/genre.gif" border="0"></td>
<td class="colhead" align="left"><a href="browse.php?<? print $oldlink; ?>sort=1&type=<? print $link1; ?>" class="altlink_white"><img src="pic/torrenttable/release.gif" border="0"></a></td>
<!--<td class="heading" align="left">DL</td>-->
<?
if ($wait)
        print("<td class=\"colhead\" align=\"center\">".$tracker_lang['wait']."</td>\n");

if ($variant == "mytorrents")
        print("<td class=\"colhead\" align=\"center\">".$tracker_lang['visible']."</td>\n");


?>
<td class="colhead" align="center"><a href="browse.php?<? print $oldlink; ?>sort=2&type=<? print $link2; ?>" class="altlink_white"><img src="pic/torrenttable/files.gif" border="0" alt="Файлов"></a></td>
<td class="colhead" align="center"><a href="browse.php?<? print $oldlink; ?>sort=3&type=<? print $link3; ?>" class="altlink_white"><img src="pic/torrenttable/comments.gif" border="0" alt="Комментариев"></a></td>
<? if ($use_ttl) {
?>
        <td class="colhead" align="center"><?=$tracker_lang['ttl'];?></td>
<?
}
?>
<td class="colhead" align="center"><a href="browse.php?<? print $oldlink; ?>sort=5&type=<? print $link5; ?>" class="altlink_white"><img src="pic/torrenttable/mb.gif" border="0" alt="Размер"></a></td>
<td class="colhead" align="center"><a href="browse.php?<? print $oldlink; ?>sort=7&type=<? print $link7; ?>" class="altlink_white"><img src="pic/torrenttable/seeders.gif" border="0" alt="Сидеров"></a></td>
<td class="colhead" align="center"><a href="browse.php?<? print $oldlink; ?>sort=8&type=<? print $link8; ?>" class="altlink_white"><img src="pic/torrenttable/leechers.gif" border="0" alt="Личеров"></a></td>
<?

if ($variant == "index" || $variant == "bookmarks")
        print("<td class=\"colhead\" align=\"center\"><a href=\"browse.php?{$oldlink}sort=9&type={$link9}\" class=\"altlink_white\"><img src=\"pic/torrenttable/upped.gif\" border=\"0\"></a></td>\n");

if ($variant == "bookmarks")
        print("<td class=\"colhead\" align=\"center\">".$tracker_lang['delete']."</td>\n");

print("</tr>\n");

print("<tbody id=\"highlighted\">");

//if ((get_user_class() >= UC_MODERATOR) && $variant == "index")
  //      print("<form method=\"post\" action=\"deltorrent.php?mode=delete\">");

        if ($variant == "bookmarks")
                print ("<form method=\"post\" action=\"takedelbookmark.php\">");

        while ($row = mysql_fetch_assoc($res)) {
                $id = $row["id"];
                print("<tr".($row["sticky"] == "yes" ? " class=\"highlight\"" : "").">\n");

                print("<tr style=\"background: #F5F8FA;\">");
				print("<td align=\"center\" rowspan=2 width=\"50\" style=\"padding:0;margin:0;border-right:0\">");
                if (isset($row["cat_name"])) {
                        print("<a href=\"browse.php?cat=" . $row["category"] . "\">");
                        if (isset($row["cat_pic"]) && $row["cat_pic"] != "")
                                print("<img border=\"0\" src=\"$pic_base_url/cats/" . $row["cat_pic"] . "\" alt=\"" . $row["cat_name"] . "\" width=\"90%\" />");
                        else
                                print($row["cat_name"]);
                        print("</a>");
                }
                else
                        print("-");
                print("</td>\n");

                $dispname = $row["name"];
                $thisisfree = ($row['free']=="yes" ? "<img src=\"pic/freedownload.gif\" title=\"".$tracker_lang['golden']."\" alt=\"".$tracker_lang['golden']."\">" : "");
                print("<td colspan=\"9\" align=\"left\">".($row["sticky"] == "yes" ? "Важный: " : "")."<a onmouseover=\"return overlib('<div style=\'padding: 5px;\'><table id=\'thumbs\'><tr style=\'background: #f0f0f0;\'><td colspan=\'2\'><img src=\'torrents/images/$row[image1]\' width=\'250\'></td></tr></table></div>');\" onmouseout=\"return nd();\" href=\"torrent-".$id."\" style=\"cursor:pointer;\"");
                print("\"><b>$dispname</b></a> $thisisfree\n");

                if ($CURUSER["id"] == $row["owner"] || get_user_class() >= UC_MODERATOR)
                        $owned = 1;
                else
                        $owned = 0;

                                if ($owned)
                        print("<a href=\"edit.php?id=$row[id]\"><img border=\"0\" src=\"pic/pen.gif\" alt=\"".$tracker_lang['edit']."\" title=\"".$tracker_lang['edit']."\" /></a>\n");
print("</td></tr><tr>");

							if(isset($row['multitracker'])&&$row['multitracker'])
							print('<td style="width:55%"><font size="1" color="#808080">Теги: '.addtags($row["tags"]).' | <font color="red" title="Раздача и скачивание не учитывается">Мультитрекерный</font></td>');				
							else
				            print('<td style="width:55%"><font size="1" color="#808080">Теги: '.addtags($row["tags"]).' | Скидка: <font color="red">'.$row["free"].'%</font></td>');				

                                                                if ($wait)
                                                                {
                                                                  $elapsed = floor((gmtime() - strtotime($row["added"])) / 3600);
                                if ($elapsed < $wait)
                                {
                                  $color = dechex(floor(127*($wait - $elapsed)/48 + 128)*65536);
                                  print("<td align=\"center\"><nobr><a href=\"faq.php#dl8\"><font color=\"$color\">" . number_format($wait - $elapsed) . " h</font></a></nobr></td>\n");
                                }
                                else
                                  print("<td align=\"center\"><nobr>".$tracker_lang['no']."</nobr></td>\n");
                }

        print("</td>\n");

                if ($variant == "mytorrents") {
                        print("<td align=\"right\">");
                        if ($row["visible"] == "no")
                                print("<font color=\"red\"><b>".$tracker_lang['no']."</b></font>");
                        else
                                print("<font color=\"green\">".$tracker_lang['yes']."</font>");
                        print("</td>\n");
                }

                if ($row["type"] == "single")
                        print("<td align=\"right\">" . $row["numfiles"] . "</td>\n");
                else {
                        if ($variant == "index")
                                print("<td align=\"right\"><b>" . $row["numfiles"] . "</b></td>\n");
                        else
                                print("<td align=\"right\"><b>" . $row["numfiles"] . "</b></td>\n");
                }

                if (!$row["comments"])
                        print("<td align=\"right\">" . $row["comments"] . "</td>\n");
                else {
                        if ($variant == "index")
                                print("<td align=\"right\"><b>" . $row["comments"] . "</b></td>\n");
                        else
                                print("<td align=\"right\"><b>" . $row["comments"] . "</b></td>\n");
                }

//                print("<td align=center><nobr>" . str_replace(" ", "<br />", $row["added"]) . "</nobr></td>\n");
                                $ttl = ($ttl_days*24) - floor((gmtime() - sql_timestamp_to_unix_timestamp($row["added"])) / 3600);
                                if ($ttl == 1) $ttl .= " час"; else $ttl .= "&nbsp;часов";
                if ($use_ttl)
                        print("<td align=\"center\">$ttl</td>\n");
                print("<td align=\"center\">" . str_replace(" ", "&nbsp;", mksize($row["size"])) . "</td>\n");
//                print("<td align=\"right\">" . $row["views"] . "</td>\n");
//                print("<td align=\"right\">" . $row["hits"] . "</td>\n");

                print("<td align=\"center\">");
				
		if(isset($row['multitracker'])&&$row['multitracker'])
		{$row['seeders']+=$row['f_seeders'];
		$row['leechers']+=$row['f_peers'];}

        if ($row["seeders"]) {
			if ($variant == "index")
			{
			   if ($row["leechers"]) $ratio = $row["seeders"] / $row["leechers"]; else $ratio = 1;
				print("<b><a href=\"details.php?id=$id&amp;hit=1&amp;toseeders=1\"><font color=" .
				  get_slr_color($ratio) . ">" . $row["seeders"] . "</font></a></b>\n");
			}
			else
				print("<b><a class=\"" . linkcolor($row["seeders"]) . "\" href=\"details.php?id=$id&amp;dllist=1#seeders\">" .
				  $row["seeders"] . "</a></b>\n");
		}
		else
			print("<span class=\"" . linkcolor($row["seeders"]) . "\">" . $row["seeders"] . "</span>");

		print("</td><td align=\"center\">");

		if ($row["leechers"]) {
			if ($variant == "index")
				print("<b><a href=\"details.php?id=$id&amp;hit=1&amp;todlers=1\">" .
				   number_format($row["leechers"]) . ($peerlink ? "</a>" : "") .
				   "</b>\n");
			else
				print("<b><a class=\"" . linkcolor($row["leechers"]) . "\" href=\"details.php?id=$id&amp;dllist=1#leechers\">" .
				  $row["leechers"] . "</a></b>\n");
		}
		else
			print("0\n");

                print("</td>");
				
				if ($row["cat_name"]=="XXX") {
				   print("<td align=\"center\"><b>Скрыто</b></td>\n");
				 }  elseif ($variant == "index") {
                        print("<td align=\"center\">" . (isset($row["username"]) ? ("<a href=\"id" . $row["owner"] . "\"><b>" . get_user_class_color($row["class"], htmlspecialchars_uni($row["username"])) . "</b></a>") : "<i>(unknown)</i>") . "</td>\n");
                 }
						
                if ($variant == "bookmarks")
                        print ("<td align=\"center\"><input type=\"checkbox\" name=\"delbookmark[]\" value=\"" . $row[bookmarkid] . "\" /></td>");
print("</tr>");  
        print("</tr> \n");		

$oldday = $day; // старая дата 
        }
        print("</tbody>");


        return $rows;
}

?>