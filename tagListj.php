<?
require_once("include/bittorrent.php");
dbconn();

header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'POST')
{
if(cache_check('tagsc',300))
$tagi = cache_read('tagsc');
else {
$tags = sql_query("SELECT name, SUM(howmuch) as howmuch FROM tags GROUP BY name ORDER BY howmuch DESC");
$tagi = '';
while($tag = mysql_fetch_array($tags))
	if(!empty($tag['name']))
		$tagi .= ($tagi ? "<br>\n" : '').'<a href="browse.php?tag='.urlencode(htmlspecialchars($tag['name'])).'">'.htmlspecialchars($tag['name']).'</a> ('.$tag['howmuch'].')';
cache_write('tagsc',$tagi);
}
setcookie('tagst','tags');
echo $tagi;
}
else die('Direct access denied');
?>