<?
require_once("include/bittorrent.php");
dbconn(false);
loggedinorreturn();

if(empty($_SERVER['HTTP_REFERER'])) {
header("HTTP/1.0 503 Server Error");
die('Error response');}

$uid=(int) $_GET['uid'];
$t=(int) $_GET['t'];

if(empty($uid)) {
header("HTTP/1.0 503 Server Error");
die('Error response');}

$SQL0070=sql_query("SELECT email, icq, id FROM `users` WHERE `id`=".$uid);
if(!$e_mail=mysql_fetch_array($SQL0070)) {
header("HTTP/1.0 503 Server Error");
die('Error response');}

if(empty($t)) $t=2;
switch($t)
{
	case 1: // Email
	$email= $e_mail['email'];
	break;
	case 2:
	if(!empty($e_mail['icq']))
	$email=$e_mail['icq'];
	else
	{header("HTTP/1.0 503 Server Error");
	die('Error response');}
	break;
	default:
	header("HTTP/1.0 503 Server Error");
	die('Error response');
	break;
}

header("Content-type: image/jpeg");
header("Last-Modified: " . date("D, d M Y H:i:s",time()) . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$img=imagecreate((strlen($email)+1)*7,12);
$color=ImagecolorAllocate($img,255,255,255);
$trans = ImageColorTransparent($img, $color);
ImageFill($img, 0, 0, $color);
$text=ImagecolorAllocate($img,0,0,0);
//$line=ImagecolorAllocate($img,mt_rand(100,240),mt_rand(70,200),mt_rand(60,180));
//$line2=ImagecolorAllocate($img,mt_rand(200,240),mt_rand(200,240),mt_rand(200,255));
//imageline($img, mt_rand(0,7), mt_rand(7,12), mt_rand((strlen($email)+1)*6-10,(strlen($email)+1)*6), mt_rand(0,6), $line2);
ImageString($img,3,3,0,$email,$text);
//imageline($img, mt_rand(0,7), mt_rand(0,10), mt_rand((strlen($email)+1)*6-20,(strlen($email)+1)*6), mt_rand(8,12), $line);
ImageTTFText($img, 0, 0, 0, 0, $text, "include/fonts/verdana.ttf", "$text");
imagejpeg($img, null, 80);
ImageDestroy($img);
?>