<?
require_once("include/benc.php");
require_once("include/bittorrent.php");

ini_set("upload_max_filesize",$max_torrent_size);

function bark($msg) {
	genbark($msg, $tracker_lang['error']);
}

dbconn(); 

loggedinorreturn();
parked();

foreach(explode(":","type:descr:name") as $v) {
	if (!isset($_POST[$v]))
		bark("missing form data");
}

$replace = array(", ", " , ", " ,");

$tags = trim(str_replace($replace, ",", mb_convert_case(unesc($_POST["tags"]), MB_CASE_TITLE, $mysql_charset)));


if (!isset($_FILES["tfile"]))
	bark("missing form data");

$f = $_FILES["tfile"];
$fname = unesc($f["name"]);
if (empty($fname))
	bark("Файл не загружен. Пустое имя файла!");

$descr = unesc($_POST["descr"]);
if (!$descr)
	bark("Вы должны ввести описание!");

$catid = (0 + $_POST["type"]);
if (!is_valid_id($catid))
	bark("Вы должны выбрать категорию, в которую поместить торрент!");
	
if (!validfilename($fname))
	bark("Неверное имя файла!");
if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
	bark("Неверное имя файла (не .torrent).");
$shortfname = $torrent = $matches[1];
if (!empty($_POST["name"]))
	$torrent = unesc($_POST["name"]);

$tmpname = $f["tmp_name"];
if (!is_uploaded_file($tmpname))
	bark("eek");
if (!filesize($tmpname))
	bark("Пустой файл!");

$dict = bdec_file($tmpname, $max_torrent_size);
if (!isset($dict))
	bark("Что за хрень ты загружаешь? Это не бинарно-кодированый файл!");

if ($CURUSER['class'] >= UC_MODERATOR)
{
$free = $_POST['free'];
$updateset[] = "free = " . sqlesc($free);
}


if ($_POST['sticky'] == 'yes' AND get_user_class() >= UC_ADMINISTRATOR)
    $sticky = "yes";
else
    $sticky = "no";

function dict_check($d, $s) {
	if ($d["type"] != "dictionary")
		bark("not a dictionary");
	$a = explode(":", $s);
	$dd = $d["value"];
	$ret = array();
	foreach ($a as $k) {
		unset($t);
		if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
			$k = $m[1];
			$t = $m[2];
		}
		if (!isset($dd[$k]))
			bark("dictionary is missing key(s)");
		if (isset($t)) {
			if ($dd[$k]["type"] != $t)
				bark("invalid entry in dictionary");
			$ret[] = $dd[$k]["value"];
		}
		else
			$ret[] = $dd[$k];
	}
	return $ret;
}

function dict_get($d, $k, $t) {
	if ($d["type"] != "dictionary")
		bark("not a dictionary");
	$dd = $d["value"];
	if (!isset($dd[$k]))
		return;
	$v = $dd[$k];
	if ($v["type"] != $t)
		bark("invalid dictionary entry type");
	return $v["value"];
}

list($info) = dict_check($dict, "info");
list($dname, $plen, $pieces) = dict_check($info, "name(string):piece length(integer):pieces(string)");

/*if (!in_array($ann, $announce_urls, 1))
	bark("Неверный Announce URL! Должен быть ".$announce_urls[0]);*/

if (strlen($pieces) % 20 != 0)
	bark("invalid pieces");

$filelist = array();
$totallen = dict_get($info, "length", "integer");
if (isset($totallen)) {
	$filelist[] = array($dname, $totallen);
	$type = "single";
} else {
	$flist = dict_get($info, "files", "list");
	if (!isset($flist))
		bark("missing both length and files");
	if (!count($flist))
		bark("no files");
	$totallen = 0;
	foreach ($flist as $fn) {
		list($ll, $ff) = dict_check($fn, "length(integer):path(list)");
		$totallen += $ll;
		$ffa = array();
		foreach ($ff as $ffe) {
			if ($ffe["type"] != "string")
				bark("filename error");
			$ffa[] = $ffe["value"];
		}
		if (!count($ffa))
			bark("filename error");
		$ffe = implode("/", $ffa);
		$filelist[] = array($ffe, $ll);
	if ($ffe == 'Thumbs.db')
        {
            stderr("Ошибка", "В торрентах запрещено держать файлы Thumbs.db!");
            die;
        }
	}
	$type = "multi";
}

$old_announce = $dict['value']['announce']['value'];
$dict['value']['announce']=bdec(benc_str($announce_urls[0]));  // change announce url to local

if($_POST['multi']==1) {
	$multitracker = 1;
	$free = 0;

$a_list = array();
if ($dict['value']['announce-list']) {
  foreach ($dict['value']['announce-list']['value'] as $urls) {
	if($urls['value'][0]['value']!=$multi_announce[0])
    $a_list[] = $urls['value'][0]['value'];
  }}
unset($dict['value']['announce-list']);

if(!count($a_list)&&!empty($old_announce))
$a_list[] = $old_announce;


  //$announces[0] = array('type' => 'list', 'value' => array(bdec(benc_str($multi_announce[0]))), 'strlen' => strlen("l".$multi_announce[0]."e"), 'string' => "l".$multi_announce[0]."e");
    //$liststring .= "l".$multi_announce[0]."e";
	$a_list = array_unique($a_list);
$announce_list = sqlesc(implode("\n",$a_list));

  if (count($a_list)) {
  //$a_list = array_merge($multi_announce,$a_list);
  foreach ($a_list as $announce) {
    $announces[] = array('type' => 'list', 'value' => array(bdec(benc_str($announce))), 'strlen' => strlen("l".$announce."e"), 'string' => "l".$announce."e");
    $liststring .= "l".$announce."e";
  }}else
  {	stderr('Должен быть указан хотябы один анонсер!');
	die(); }
  $dict['value']['announce-list']['type'] = 'list';
  $dict['value']['announce-list']['value'] = $announces;

$dict['value']['announce-list']['string'] = "l".$liststring."e";
$dict['value']['announce-list']['strlen'] = strlen($dict['value']['announce-list']['string']);
//unset($dict['value']['info']['value']['private']);
}
else {
$announce_list ='NULL';
$multitracker = 0;
unset($dict['value']['announce-list']); // remove multi-tracker capability
unset($dict['value']['info']['value']['crc32']); // remove crc32
unset($dict['value']['info']['value']['ed2k']); // remove ed2k
unset($dict['value']['info']['value']['md5sum']); // remove md5sum
unset($dict['value']['info']['value']['sha1']); // remove sha1
unset($dict['value']['info']['value']['tiger']); // remove tiger
$dict['value']['info']['value']['private']=bdec('i1e');  // add private tracker flag
$dict['value']['info']['value']['source']=bdec(benc_str( "[$DEFAULTBASEURL] $SITENAME")); // add link for bitcomet users
}
unset($dict['value']['nodes']); // remove cached peers (Bitcomet & Azareus)
unset($dict['value']['azureus_properties']); // remove azureus properties
$dict=bdec(benc($dict)); // double up on the becoding solves the occassional misgenerated infohash
$dict['value']['comment']=bdec(benc_str( "Торрент создан для '$SITENAME'")); // change torrent comment
$dict['value']['created by']=bdec(benc_str( "$CURUSER[username]")); // change created by
$dict['value']['publisher']=bdec(benc_str( "$CURUSER[username]")); // change publisher
$dict['value']['publisher.utf-8']=bdec(benc_str( "$CURUSER[username]")); // change publisher.utf-8
$dict['value']['publisher-url']=bdec(benc_str( "$DEFAULTBASEURL/userdetails.php?id=$CURUSER[id]")); // change publisher-url
$dict['value']['publisher-url.utf-8']=bdec(benc_str( "$DEFAULTBASEURL/userdetails.php?id=$CURUSER[id]")); // change publisher-url.utf-8
list($info) = dict_check($dict, "info");

$infohash = sha1($info["string"]);

if($multitracker == 1)
{
	$tracker_cache = array();
	$f_peers = 0;
	$f_seeders = 0;
	foreach($a_list as $ann)
	{
		$response = get_remote_peers($ann, $infohash);
		if($response['state']=='ok')
		{
			//print_r($response);
			$tracker_cache[] = $response['tracker'].':'.($response['leechers'] ? $response['leechers'] : 0).':'.($response['seeders'] ? $response['seeders'] : 0);
			$f_peers += $response['leechers'];
			$f_seeders += $response['seeders'];
		}
		else
			$tracker_cache[] = $response['tracker'].':false';
	}
	$tracker_cache = sqlesc(implode("\n",$tracker_cache));
}
else {
$f_peers=0;
$f_seeders=0;
$tracker_cache=''; }

mysql_try_reconnect();

//////////////////////////////////////////////
//////////////Take Image Uploads//////////////

$maxfilesize = 512000; // 500kb

$allowed_types = array(
"image/gif" => "gif",
"image/pjpeg" => "jpg",
"image/jpeg" => "jpg",
"image/jpg" => "jpg",
"image/png" => "png"
// Add more types here if you like
);

for ($x=0; $x < 2; $x++) {

if (!($_FILES[image.$x]['name'] == "")) {
	$y = $x + 1;

	// Is valid filetype?
	if (!array_key_exists($_FILES[image.$x]['type'], $allowed_types))
		bark("Invalid file type! Image $y");

	if (!preg_match('/^(.+)\.(jpg|jpeg|png|gif)$/si', $_FILES[image.$x]['name']))
		bark("Неверное имя файла (не картинка).");

	// Is within allowed filesize?
	if ($_FILES[image.$x]['size'] > $maxfilesize)
		bark("Invalid file size! Image $y - Must be less than 500kb");

	// Where to upload?
	// Update for your own server. Make sure the folder has chmod write permissions. Remember this director
	$uploaddir = "torrents/images/";

	// What is the temporary file name?
	$ifile = $_FILES[image.$x]['tmp_name'];

	// Calculate what the next torrent id will be
	$ret = sql_query("SHOW TABLE STATUS LIKE 'torrents'");
	$row = mysql_fetch_array($ret);
	$next_id = $row['Auto_increment'];

	// By what filename should the tracker associate the image with?
	$ifilename = $next_id . $x . '.'.$allowed_types[$_FILES[image.$x]['type']];

	// Upload the file
	$copy = copy($ifile, $uploaddir.$ifilename);

	if (!$copy)
	bark("Error occured uploading image! - Image $y");
	
	$margin = 7;

$ifn=$uploaddir.$ifilename;

//две картинки которые накладываем одна для темного фона другая для светлого
$watermark_image_light = 'pic/watermark_light.png';
$watermark_image_dark =  'pic/watermark_dark.png';


list($image_width, $image_height)
    = getimagesize($ifn);


list($watermark_width, $watermark_height)
    = getimagesize($watermark_image_light);

$watermark_x = $image_width - $margin - $watermark_width;
$watermark_y = $image_height - $margin - $watermark_height;

$watermark_x2 = $watermark_x + $watermark_width;
$watermark_y2 = $watermark_y + $watermark_height;

if ($watermark_x < 0 OR $watermark_y < 0 OR
    $watermark_x2 > $image_width OR $watermark_y2 > $image_height OR
    $image_width < $min_image OR $image_height < $min_image)
    {
       return;
    }


$test123 = imagecreatetruecolor(1, 1);
if ($_FILES[image.$x]['type']=="image/gif")
    $creimg=imagecreatefromgif($ifn);
elseif ($_FILES[image.$x]['type']=="image/png")
    $creimg=imagecreatefrompng($ifn);
elseif ($_FILES[image.$x]['type']=="image/jpg" or $_FILES[image.$x]['type']=="image/jpeg" or $_FILES[image.$x]['type']=="image/pjpeg")
    $creimg=imagecreatefromjpeg($ifn);

imagecopyresampled($test123, $creimg, 0, 0, $watermark_x, $watermark_y, 1, 1, $watermark_width, $watermark_height);
$rgb = imagecolorat($test123, 0, 0);

$r = ($rgb >> 16) & 0xFF;
$g = ($rgb >> 8) & 0xFF;
$b = $rgb & 0xFF;
    
$max = min($r, $g, $b);
$min = max($r, $g, $b);
$lightness = (double)(($max + $min) / 510.0);
imagedestroy($test123);

$watermark_image = ($lightness < 0.5) ? $watermark_image_light : $watermark_image_dark;
$watermark = imagecreatefrompng($watermark_image);
imagealphablending($creimg, TRUE);
imagealphablending($watermark, TRUE);
imagecopy($creimg, $watermark, $watermark_x, $watermark_y, 0, 0,$watermark_width, $watermark_height);

imagedestroy($watermark);

if ($_FILES[image.$x]['type']=="image/png")
    imagepng($creimg,$ifn,0);
elseif ($_FILES[image.$x]['type']=="image/jpg" or $_FILES[image.$x]['type']=="image/jpeg" or $_FILES[image.$x]['type']=="image/pjpeg")
    imagejpeg($creimg,$ifn,100);
elseif ($_FILES[image.$x]['type']=="image/gif")
	imagegif($creimg,$ifn);

    $inames[$x] = $ifilename;  

$inames[] = $ifilename;

}

}

//////////////////////////////////////////////

// Replace punctuation characters with spaces

$torrent = htmlspecialchars(str_replace("_", " ", $torrent));

$ret = sql_query("INSERT INTO torrents (search_text, filename, owner, visible, sticky, info_hash, name, size, numfiles, type, tags, descr, ori_descr, free, image1, image2, category, save_as, added, last_action, multitracker, announce_list, f_peers, f_seeders, tracker_cache) VALUES (" . implode(",", array_map("sqlesc", array(searchfield("$shortfname $dname $torrent"), $fname, $CURUSER["id"], "no", $sticky, $infohash, $torrent, $totallen, count($filelist), $type, $tags, $descr, $descr, $free, $inames[0], $inames[1], 0 + $_POST["type"], $dname))) . ", '" . get_date_time() . "', '" . get_date_time() . "', ".$multitracker.", ".$announce_list.", ".$f_peers.", ".$f_seeders.", ".$tracker_cache.")");
if (!$ret) {
	if (mysql_errno() == 1062)
		bark("torrent already uploaded!");
	bark("mysql puked: ".mysql_error());
}
$id = mysql_insert_id();
sql_query("INSERT INTO checkcomm (checkid, userid, torrent) VALUES ($id, $CURUSER[id], 1)") or sqlerr(__FILE__,__LINE__);

move_uploaded_file($tmpname, "$torrent_dir/$id.torrent");

$fp = fopen("$torrent_dir/$id.torrent", "w");
if ($fp)
{
    @fwrite($fp, benc($dict), strlen(benc($dict)));
    fclose($fp);
}

////// МОД ТЭГОВ [by merdox] //////
$ret = array();
$res = sql_query("SELECT name FROM tags WHERE category = ".sqlesc($_POST["type"]));
while ($row = mysql_fetch_array($res))
	$ret[] = $row["name"];

$union = array_intersect($ret, explode(",", $tags));
$ununion = array_diff(explode(",", $tags), $ret);

foreach ($union as $tag) {
		@sql_query("UPDATE tags SET howmuch=howmuch+1 WHERE name LIKE ".sqlesc($tag)) or sqlerr(__FILE__, __LINE__);
	}

foreach ($ununion as $tag) {
		@sql_query("INSERT INTO tags (category, name, howmuch) VALUES (".sqlesc($_POST["type"]).", ".sqlesc($tag).", 1)") or sqlerr(__FILE__, __LINE__);
	}
////// МОД ТЭГОВ [by merdox] //////


write_log("Торрент номер $id ($torrent) был залит пользователем " . $CURUSER["username"],"5DDB6E","torrent");

stdhead("Файл загружен", 'all');

$downlink = "<a title=\"Скачать\" href=\"download.php?id=$id&amp;name=$fname\"><span style=\"color: red; cursor: help;\" title=\"Скачать торрент-файл.\">СКАЧАТЬ ФАЙЛ</span></a>"; 

print ("<table style='width: 100%; border: 1px dashed #008000; padding: 10px; background-color: #D6F3CC'>
<b><font size=2px>Спасибо, Ваша раздача почти готова. Торрент-файл размещен на сервере.<hr>
Теперь нужно $downlink и начать раздачу в клиенте, с его помощью.</font></b></table>");
print ("<br>");

$create = "<a title=\"Создать описание релиза\" target=\"_blank\" href=\"indexadd.php\"><span style=\"color: #DA0000; cursor: help;\" title=\"Создать описание релиза...\">СОЗДАТЬ ОПИСАНИЕ</span></a>";

print ("<table style='width: 100%; border: 1px dashed #990000; padding: 10px; background-color: #FFF0F0'>
<b><font color='#990000' size=2px>Напоминаем, что Вам необходимо $create Вашего релиза, чтобы он стал виден на главной странице сайта, всем посетителям!</font></b></table>");
print ("<br>");

$detalistorr = "torrent_info.php?id=$id";
$url = "edit.php?id=$id";
$gettorrent = "details.php?id=$id";

$editlink = "<center><table class=my_table width=\"100%\" border='0' cellspacing='0' cellpadding='0'>
             <tr>
             <td class=bottom><center><form method=post action=\"$url\"><input type=submit value=\"Редактировать торрент\" style='height: 20px; width: 160px;'></center></form></td>
             <td class=bottom><center><form method=post action=\"$gettorrent\"><input type=submit value=\"Перейти к деталям\" style='height: 20px; width: 160px;'></center></form></td>
             <td class=bottom><center><form method=post action=\"$detalistorr\"><input type=submit value=\"Данные торрента\" style='height: 20px; width: 160px;'></center></form></td>
             </tr>
             </table></center>";

print ("<table style='width: 100%; border: 1px dashed #008000; padding: 10px; background-color: #D6F3CC'>
<b><font size=2px>Дополнительные действия:</font></b><hr>
$editlink</table>");

stdfoot();  

?>