<?php
require_once("include/bittorrent.php");
dbconn(false);
loggedinorreturn();
if(empty($_GET['link'])) die("Error");
stdhead('Переход по ссылке');
stdmsg($tracker_lang['error'], 'Вы уходите с bt-star.ru по внешней ссылке, если вы уверены, нажмите ссылку снизу:<br><a href="'.urldecode($_GET['link']).'">'.urldecode($_GET['link']).'</a>');
stdfoot();
?>