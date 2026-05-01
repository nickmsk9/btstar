<?php
require_once("include/bittorrent.php");
dbconn();
$id = (int)$_GET["id"];
if (!is_numeric($_GET['id'])) { header("HTTP/1.0 404 Not Found");
stderr($tracker_lang['error'], $tracker_lang['no_torrent_with_such_id']);	}
if($CURUSER)
$res = sql_query("SELECT torrents.*, karma.id AS canrate FROM torrents LEFT JOIN karma ON karma.type='torrent' AND karma.value = torrents.id AND user =".$CURUSER['id']." WHERE torrents.id = $id") or sqlerr(__FILE__, __LINE__);
else
$res = sql_query("SELECT * FROM torrents WHERE id = $id") or sqlerr(__FILE__, __LINE__);
$row = mysql_fetch_array($res);
if(empty($row))
{ header("HTTP/1.0 404 Not Found");
	stderr($tracker_lang['error'],$tracker_lang['no_torrent_with_such_id']);
}
stdhead("Обзор торрента ".$row["name"]."");
//begin_frame("Обзор торрента ".$row["name"]."");
print("<link rel=\"stylesheet\" href=\"css/user.css\" type=\"text/css\">\n");
print("<script language=\"JavaScript\" src=\"js/details.js?v=20260501\" type=\"text/javascript\"></script>\n");
print("<div id=\"tabs\">");
print("<span class=\"tab active\" id=\"info\">Описание</span>");
print("<span class=\"tab\" id=\"peers\">Пиры</span>");
print("<span class=\"tab\" id=\"thanks\">Поблагодарили</span>");
print("<span id=\"loading\"></span>");
print("<div id=\"body\" torrent=\"".$id."\">");
print("<table width=\"100%\" border=\"0\" cellpadding=\"5\">");
// Начало тегов
foreach(explode(",", $row["tags"]) as $tag)
$tags .= "<a style=\"font-weight:normal;color:green;\" href=\"browse.php?tag=".$tag."\">".$tag."</a>, ";
if ($tags)
$tags = substr($tags, 0, -2);
// Конец тегов
// Начала спасибок
if($CURUSER) {					
$torrentid = intval($_GET['id']);
list($count777) = mysql_fetch_row(sql_query("SELECT COUNT(*) FROM thanks WHERE torrentid = $torrentid AND userid = $CURUSER[id] LIMIT 1")) or sqlerr(__FILE__,__LINE__);

if ($row['owner'] == $CURUSER['id'] || $count777 != 0)
     $can_not_thanks = true;
          
//$thanksby .= "<input type=\"button\" name=\"send_thanks\" id=\"send_thanks\" value=\"Сказать спасибо\" ".($can_not_thanks == true ? " disabled" : "")." />&nbsp;";
$thanksby .= "";

//$thanksby .= "<div class=\"spoiler_head\" id=\"show_thanks\"><img border=\"0\" src=\"pic/plus.gif\" title=\"Показать\">&nbsp;&nbsp;Сказали спасибо за раздачу</div>";
//$thanksby .= "<div class=\"spoiler_body\" style=\"display:none;\" id=\"thanks_body\"></div>";
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
print("<input type=\"hidden\" name=\"torrentid\" id=\"torrentid\" value=\"{$id}\">");
print("<span id=\"thanks_msg\"></span>&nbsp;<img src=\"pic/thanks.png\" title=\"Сказать спасибо\" name=\"send_thanks\" id=\"send_thanks\" style=\"cursor: pointer;\">");
print("</td></tr>");
}
print("<tr><td colspan=\"3\" style=\"border-top: none;\"><div style=\"float: left;\">".$tags."</div>");
print("</td></tr>");
print("<tr><td valign=\"top\" colspan=\"3\">");
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
print("</div>");
print("</div>");

//end_frame();
					begin_frame("Комментарии");
		 print("<p><a name=\"startcomments\"></a></p>\n");

        $subres = sql_query("SELECT COUNT(*) FROM comments WHERE torrent = $id");
        $subrow = mysql_fetch_array($subres);
        $count = $subrow[0];

        $limited = 10;

if (!$count) {
  print("<table border=\"0\" style=\"margin-top: 2px;\" cellpadding=\"3\" width=\"100%\">");
//  print("<tr><td class=colhead align=\"left\" colspan=\"2\"> <a name=comments>&nbsp;</a><b>Комментарии</b></td></tr>");
  print("<tr><td align=\"center\" style=\"border: none;\">");
  print("<form name=comment method=\"post\" action=\"comment.php?action=add\">");
  textbbcode("comment","text","");
  print("</td></tr><tr><td align=\"left\" colspan=\"2\" style=\"border: none;\">");
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
  print("<tr><td width=\"100%\" align=\"center\" style=\"border:none\">");
  //print("Ваше имя: ");
  //print("".$CURUSER['username']."<p>");
  print("<form name=comment method=\"post\" action=\"comment.php?action=add\">");
 // print("<center><table border=\"0\"><tr><td class=\"clear\">");
  print("<div align=\"center\">". textbbcode("comment","text","") ."</div>");
 // print("</td></tr></table></center>");
  print("</td></tr><tr><td align=\"left\" colspan=\"2\" style=\"border:none;\">");
  print("<input type=\"hidden\" name=\"tid\" value=\"$id\"/>");
  print("<input type=\"submit\" class=btn value=\"Разместить комментарий\" />");
  print("</td></tr></form></table></div>");

        }

end_frame();
stdfoot();
?>
