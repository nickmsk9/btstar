<?php
header("HTTP/1.0 404 Not Found");
require "include/bittorrent.php";
dbconn(false);
stdhead("Ой, а страница не найдена", 'all');
?>
<div align="left"><font color="red" size="20">404</font><br>
Данной страницы нету на сервере ...</div>
<?
stdfoot();
?>