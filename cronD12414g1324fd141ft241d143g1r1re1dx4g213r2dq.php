<?php
$no_login = true;
require_once('include/bittorrent.php');
dbconn();
autoclean();

echo "Обновлено ",$_torrents_r,' торрентов. ',$_trackers_r,' запросов к удаленным трекерам.';

?>