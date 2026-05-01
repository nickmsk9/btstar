<?php

require_once('benc.php');
global $_torrents_r, $_trackers_r;
$_torrents_r = 0;
$_trackers_r = 0;

$sql = sql_query("SELECT id, info_hash, announce_list FROM torrents WHERE multitracker = 1");
while($torrent = mysql_fetch_array($sql))
{
	$_torrents_r++;
	$tracker_cache = array();
	$f_peers = 0;
	$f_seeders = 0;
	$announce_list = explode("\n",$torrent['announce_list']);
	foreach($announce_list as $announce)
	{
		$response = get_remote_peers($announce, $torrent['info_hash'],true);
		if($response['state']=='ok')
		{
			$_trackers_r++;
			$tracker_cache[] = $response['tracker'].':'.($response['leechers'] ? $response['leechers'] : 0).':'.($response['seeders'] ? $response['seeders'] : 0);
			$f_peers += $response['leechers'];
			$f_seeders += $response['seeders'];
		}
		else
			$tracker_cache[] = $response['tracker'].':false';
	}
	$tracker_cache = implode("\n",$tracker_cache);
	mysql_try_reconnect();
	sql_query("UPDATE LOW_PRIORITY torrents SET f_peers = ".$f_peers.', f_seeders = '.$f_seeders.', tracker_cache = '.sqlesc($tracker_cache).' WHERE id = '.$torrent['id']) or die(mysql_error());
}

?>