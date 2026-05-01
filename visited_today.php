<?php

require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();
stdhead();

$dt = strtotime("now") - ((date("G")*60*60)+(date("i")*60));
$res = sql_query("SELECT id, gender, username, class, donor, warned FROM  users  WHERE UNIX_TIMESTAMP(last_access) >= $dt ORDER BY class DESC") or sqlerr(__FILE__, __LINE__);
while ($arr = mysql_fetch_assoc($res))
{

    if ($todayactive)
        $todayactive .= ", ";

    if ($CURUSER) {
        $todayactive .= "<a href=userdetails.php?id=" . $arr["id"] . ">".get_user_class_color($arr["class"], $arr["username"])."</a></a>";
    } else {
        $todayactive .= "<a href=userdetails.php?id=" . $arr["id"] . ">".get_user_class_color($arr["class"], $arr["username"])."</a></a>";
    }
    $donator = $arr["donor"] == "yes";
    if ($donator) {
        $todayactive .= "<img src=\"pic/star.gif\">";

    }
    $warned = $arr["warned"] == "yes";
    if ($warned) {
        $todayactive .= " <b>(<font color=red title=Предупрежден>П</font>)</b>";
    }
    $usersactivetoday++;
}
//end visited today

begin_frame("Статистика регистраций");
list($lastuser) = mysql_fetch_array(sql_query("SELECT COUNT(*) FROM users WHERE DATEDIFF(added, NOW()) = -1"));
list($newuser) = mysql_fetch_array(sql_query("SELECT COUNT(*) FROM users WHERE DATE(added) = DATE(NOW())"));
print("<table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\">");
print("<tr><td class=\"rowhead\">Зарегестрированно вчера:</td><td align=\"left\">".$lastuser." пользователей</td></tr>");
print("<tr><td class=\"rowhead\">Зарегестрированно сегодня:</td><td align=\"left\">".$newuser." пользователей</td></tr>");
print("</table>");
end_frame();
begin_frame("Сегодня нас посетили");
echo "<div><b><font color=red>".$usersactivetoday."</font> пользователей посетило трекер сегодня</b></div><hr>";
echo " " . $todayactive . " ";
end_frame();

stdfoot();
?> 