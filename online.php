<?php
include_once("include/bittorrent.php");
dbconn();
header("Content-Type: text/html; charset=utf-8");
print "    <div id=\"wol\">";


$dt = gmtime() - 180;
$dt = sqlesc(get_date_time($dt));
$res = mysql_query("SELECT id, username, class, donor, warned, parked FROM users WHERE last_access >= $dt ORDER BY username") or print(mysql_error());
while ($arr = mysql_fetch_assoc($res)) {

    $username = $arr['username'];
    $id = $arr['id'];
    
    echo "<font size = 1><span onclick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='privat($username) '+parent.document.shoutform.shout.value;return false;\" style=\"cursor: pointer; color: red; font-weight: bold;\">P</span> <a href=userdetails.php?id=$id onclick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[$username] '+parent.document.shoutform.shout.value;return false;\" target=_blank>".get_user_class_color($arr["class"], $arr["username"]) . "</a></font></br>";
}
print "</div>";
?> 
