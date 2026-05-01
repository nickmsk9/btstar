<?
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

function cache_check($file, $time) {
	global $rootpath, $no_cache;
	if($no_cache) return false;
	return file_exists("cache/$file.cache") && is_readable("cache/$file.cache") && (TIMENOW - $time < filemtime("cache/$file.cache")) && filesize("cache/$file.cache") > 0 && $_GET["no_cache"] != 1;
}

function cache_read($file) {
	global $rootpath;
	return unserialize(@file_get_contents("cache/$file.cache"));
}

function cache_write($file, $data) {
	global $rootpath, $no_cache;
	if($no_cache) return false;
	if (file_exists("cache/$file.cache")) {
	if (is_writable("cache/$file.cache"))
		@file_put_contents("cache/$file.cache", serialize($data));}
	else
	{
		$fh = fopen("cache/$file.cache",'w+');
		fwrite($fh,serialize($data));
		fclose($fh);
	}
}

function cache_left($file, $time) {
	global $rootpath;
	return $time - (TIMENOW - filemtime($rootpath . "cache/$file.cache"));
}
?>