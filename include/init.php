<?php
# IMPORTANT: Do not edit below unless you know what you are doing!
if(!defined('IN_TRACKER') && !defined('IN_ANNOUNCE') )
  die("Hacking attempt!");

if (!function_exists("htmlspecialchars_uni")) {
        function htmlspecialchars_uni($message) {
                $message = preg_replace("#&(?!\#[0-9]+;)#si", "&amp;", $message); // Fix & but allow unicode
                $message = str_replace("<","&lt;",$message);
                $message = str_replace(">","&gt;",$message);
                $message = str_replace("\"","&quot;",$message);
                $message = str_replace("  ", "&nbsp;&nbsp;", $message);
                return $message;
        }
}

// DEFINE IMPORTANT CONSTANTS
define ('TIMENOW', time());
/*$url = explode('/', htmlspecialchars_uni($_SERVER['PHP_SELF'])); 
array_pop($url);*/
$DEFAULTBASEURL = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$BASEURL = $DEFAULTBASEURL;
$announce_urls = array();
$announce_urls[] = "/announce.php";
$multi_announce = array();
$multi_announce[] = "/multitracker/announce";

define ('MULTIREHASH',600);

// DEFINE TRACKER GROUPS
define ("UC_BOT", -1);
define ("UC_USER", 0);
define ("UC_POWER_USER", 1);
define ("UC_VIP", 2);
define ("UC_UPLOADER", 3);
define ("UC_MODERATOR", 4);
define ("UC_ADMINISTRATOR", 5);
define ("UC_SYSOP", 6);
define ("UC_GOD", 7);

$botlist = array (
	"AdsBot-Google" => 'Google Ads Bot',
	"Mediapartners-Google" => 'Google Adsense Bot',
	"Google Desktop" => 'Google Desktop Bot',
	'Feedfetcher-Googe' => 'Google Feedfetcher Bot',
	'Googlebot' => 'Google Bot',
	'ibm.com/cs/crawler' => 'IBM Research Bot',
	'msnbot-NewsBlogs/' => 'MSN NewsBot',
	'msnbot/' => 'MSN Bot',
	'online link validator' => 'Online Link Validator',
	'Yahoo!' => 'Yahoo! Bot',
	'Yahoo-' => 'Yahoo! Bot',
	'Yandex' => 'Yandex Bot',
	'Rambler' => 'Rambler Bot',
	'Google' => 'Google Bot');

?>
