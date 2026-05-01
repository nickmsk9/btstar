<?php
require ('include/bittorrent.php');
gzip();
dbconn();
loggedinorreturn();

$lim = 100;


if(isset($_GET['ref'])){
    $ref = str($_GET['ref']);
    stdhead("Последние переходы сайта ".$ref);
    begin_frame("Последние переходы сайта ".$ref);

    begin_table();
    echo "<tr><td class=row2><b>URL</b></td><td class=row2><b>Дата</b></td><td class=row2><b>IP</b></td></tr>\n";

    $a = sql_query("SELECT url, added,ip FROM `referrers` WHERE refsite='".$ref."' ORDER BY id DESC LIMIT 200");
    while($S = mysql_fetch_assoc($a)){
        $url = $S['url'];
        //$url = iconv ( 'CP1251', 'UTF-8', $url);
        echo "<tr><td class=row1><b><a href='".$S['url']."' target='_blank'>".urldecode(substr($url,0,100))."</a></b></td><td class=row1>".$S['added']."</td><td class=row1>".$S['ip']."</td></tr>\n";
    }
    
    end_table();
    end_frame();
    stdfoot();
exit;
}




stdhead("ТОП ".$lim." сайтов, от которых мы получаем посетителей");
begin_frame("ТОП ".$lim." сайтов, от которых мы получаем посетителей");

$a = sql_query("SELECT added FROM `referrers` ORDER BY id LIMIT 1");
$a = mysql_fetch_row($a);
$time = $a[0];

$a = sql_query("SELECT COUNT(*) FROM `referrers`");
$a = mysql_fetch_row($a);
$count = $a[0];

echo "<br>Статистика ведётся <b>".$time."<br>".$count." переходов.</b><br><br>";

begin_table();

echo "<tr><td class=row2><b>№</b></td><td class=row2><b>Сайт</b></td><td class=row2><b>Переходов</b></td></tr>\n";

$a = sql_query("SELECT refsite, COUNT( * ) AS cnt FROM `referrers` GROUP BY refsite ORDER BY cnt DESC LIMIT 0 , ".$lim);
$c=1;
while($S = mysql_fetch_assoc($a)){
    echo "<tr><td class=row2><b>".$c.".</b></td><td class=row1><b><a href='http://".$S['refsite']."/' target='_blank'>".$S['refsite']."</a></b></td><td class=row1><a href='".$_SERVER['PHP_SELF']."?ref=".$S['refsite']."'>".$S['cnt']."</a></td></tr>\n";
    $c++;
}

end_table();
end_frame();
stdfoot();
?> 