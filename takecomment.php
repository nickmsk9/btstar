<?
require_once("include/bittorrent.php");
dbconn(true);
loggedinorreturn();
header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
if ($_POST["action"] == "add") {
$torrentid = intval($_POST["tid"]);
$comment = intval($_POST["cid"]);
$subres = sql_query("SELECT COUNT(*) FROM comments WHERE torrent = ".sqlesc($torrentid)."");
$subrow = mysql_fetch_array($subres);
$count = $subrow[0];
$limited = 10;
$res = sql_query("SELECT name FROM torrents WHERE id = ".sqlesc($torrentid)."") or sqlerr(__FILE__,__LINE__);
$arr = mysql_fetch_array($res);
$text = base64_decode($_POST["text"]);
if ((is_valid_id($torrentid)) && ($arr) && ($text))  {
		sql_query("INSERT INTO comments (user, torrent, added, text, ori_text, ip) VALUES (" . $CURUSER["id"] . ",".sqlesc($torrentid).", '".get_date_time()."', ".sqlesc($text).",".sqlesc($text).", ".sqlesc(getip()).")") or sqlerr(__FILE__,__LINE__);
		sql_query("UPDATE torrents SET comments = comments + 1 WHERE id = ".sqlesc($torrentid)."") or sqlerr(__FILE__,__LINE__);
		$count++;
	}
                list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "details.php?id=$id&", array(lastpagedefault => 1));

                $subres = sql_query("SELECT c.id, c.ip, c.text, c.user, c.added, c.editedby, c.editedat, u.avatar, u.warned, ".
                  "u.username, u.title, u.class, u.donor, u.downloaded, u.uploaded, u.gender, u.last_access, e.username AS editedbyname FROM comments AS c LEFT JOIN users AS u ON c.user = u.id LEFT JOIN users AS e ON c.editedby = e.id WHERE torrent = " .
                  "$torrentid ORDER BY c.id $limit") or sqlerr(__FILE__, __LINE__);
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
         print("</table></div>");



 /* print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
  print("<tr><td width=\"100%\" align=\"center\" >");
  print("<form name=comment method=\"post\" action=\"comment.php?action=add\">");
  print("<div align=\"center\">". textbbcode("comment","text","", 1) ."</div>");
  print("</td></tr><tr><td align=\"left\" colspan=\"2\">");
  print("<input type=\"hidden\" name=\"tid\" value=\"$id\"/>");
  print('<a id="'.$row['id'].'" class="add_comment" method="send" href="javascript:void(0)">Комментировать</a>');
  print("</td></tr></form></table></div>"); */
   }

  if($_POST["action"] == "edit") {
  $commentid = intval($_POST["cid"]);
  $res = sql_query("SELECT c.*, t.name, t.id AS tid FROM comments AS c LEFT JOIN torrents AS t ON c.torrent = t.id WHERE c.id=$commentid") or sqlerr(__FILE__,__LINE__);
  $arr = mysql_fetch_array($res);
  if (!$arr)
  stderr($tracker_lang['error'], $tracker_lang['invalid_id']);
  if ($arr["user"] != $CURUSER["id"] && get_user_class() < UC_MODERATOR)
  stderr($tracker_lang['error'], $tracker_lang['access_denied']);
   print("<table style=\"margin-top: 2px;\" cellpadding=\"5\" width=\"100%\">");
  print("<tr><td>");
  textbbcode("comment","text",$arr["text"]);
  print("</td></tr></table>");
  }
}
//$torrentid = intval($_POST["tid"]);
//if (empty($torrentid) || !is_valid_id($torrentid)) {
//stdmsg($tracker_lang['error'], "Ошибка!");
//die();
//}
//$id = intval($_POST["tid"]);
?>