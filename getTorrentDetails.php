<?
require_once("include/bittorrent.php");
dbconn(false);
header("Content-Type: text/html; charset=" .$tracker_lang['language_charset']);
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

$tid = (int) $_POST["tid"];


$query = sql_query("SELECT name, descr, image1 FROM torrents WHERE id = '" .$tid. "'");
$row = mysql_fetch_array($query);


print("<table cellpadding='5' width='100%' border='0' align='center'><tr>");
print("<td valign='middle' align='left' width='185'>");

$img_tor = (preg_match("/http:/",$row["image1"]) ? $row["image1"] : "torrents/images/" .$row["image1"]); 
if ($row["image1"] != "")
   $img = "<img border='0' width='200' title='" .$row["name"]. "' src='" .$img_tor. "' />";
else
   $img = "[no image]";
print("<center>" .$img. "</center>");
print("<td valign='top' align='left'>");

if(!$row)
  die("Не известная ошибка ...");

$det = $row["descr"];
$pos_img_tag = strpos($det, "[img]");
if($pos_img_tag > 0)
{
    $det_cut = substr($det, 0, -(strlen($det) - $pos_img_tag));
    print(format_comment($det_cut));
}
else
   print(format_comment($det));

print("</td>");
print("<tr><td colspan='2'><div align='right'><a href='details.php?id=$tid'>[Подробней]</a></div></td></tr>");
print("</td></tr></table><br />");

?> 