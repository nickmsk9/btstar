<?php
require_once("include/bittorrent.php");
dbconn(false);
loggedinorreturn();
header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);
header ("Cache-control: no-store");
header ("Pragma: no-cache");
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'POST')
{
	$id = eval($_GET['id']);
	if($id == 0) die();
	
	$tpl = mysql_fetch_array(sql_query('SELCET template FROM templates WHERE id = '.$id));
	if(empty($tpl)) die();
	else echo $tpl['template'];

}
else die('Direct access denied');
?>