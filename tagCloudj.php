<?php
require_once("include/bittorrent.php");
dbconn();

header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);
//header ("Cache-control: no-store");
//header ("Pragma: no-cache");

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'POST')
{
require_once("include/cloud_func.php");
setcookie('tagst','cloud');
print(flash_cloud('100%','190','8','12'));
}
else
die('Direct access denied');
?>