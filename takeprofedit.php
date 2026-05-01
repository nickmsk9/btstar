<?php
require_once("include/bittorrent.php");

function bark($msg) {
        stderr("Произошла ошибка", $msg);
}

dbconn();

loggedinorreturn();

mkglobal("email:oldpassword:chpassword:passagain");

switch($_GET['t'])
{
	case 'contact':
		$sect='contact';
	break;
	case 'about':
		$sect='about';
	break;
	case 'password':
		$sect='password';
	break;
	case 'profile':
		$sect='profile';
	break;
	case 'avatar':
		$sect='avatar';
	break;
	default:
		bark("Пустой запрос");
	break;
}
  

// $set = array();

$updateset = array();
$changedemail = 0;

if($sect=='password') {
if ($chpassword != "") {
        if (strlen($chpassword) > 40)
                bark("Извините, ваш пароль слишком длинный (максимум 40 символов)");
        if ($chpassword != $passagain)
                bark("Пароли не совпадают. Попробуйте еще раз.");

    if ($CURUSER["passhash"] != md5($CURUSER["secret"] . $oldpassword . $CURUSER["secret"]))
            bark("Вы ввели неправильный старый пароль.");

        $sec = mksecret();

                $sec = mksecret();
                $passhash = md5($sec . $chpassword . $sec);
                $updateset[] = "secret = " . sqlesc($sec);
                $updateset[] = "passhash = " . sqlesc($passhash);

                logincookie($CURUSER["id"], $passhash);
                $passupdated = 1;
} }

if($sect=='contact') {
if ($email != $CURUSER["email"] && !empty($email)) {
        if (!validemail($email))
                bark("Это не похоже на настоящий E-Mail.");
  $r = sql_query("SELECT id FROM users WHERE email=" . sqlesc($email)) or sqlerr(__FILE__, __LINE__);
        if (mysql_num_rows($r) > 0)
                bark("Этот e-mail адрес уже используется одним из пользователей трекера. (<b>$email</b>)");
        $changedemail = 1;
		$updateset[] = "email = ".sqlesc($email);
}}

if($sect=='profile') {
$updateset[] = "acceptpms = ".($_POST['acceptpms'] != '' ? "'yes'" : "'no'");
$updateset[] = "deletepms = ".($_POST["deletepms"] != "" ? "'yes'" : "'no'");
$updateset[] = "savepms = ".($_POST["savepms"] != "" ? "'yes'" : "'no'");
$notifs = "notifs = '".($_POST["pmnotif"] == 'yes' ? "[pm]" : "").($_POST["emailnotif"] == 'yes' ? "[email]" : "");
$r = sql_query("SELECT id FROM categories") or sqlerr(__FILE__, __LINE__);
$rows = mysql_num_rows($r);
for ($i = 0; $i < $rows; ++$i)
{
        $a = mysql_fetch_assoc($r);
        if ($_POST["cat$a[id]"] == 'yes')
          $notifs .= "[cat$a[id]]";
}
$updateset[]=$notifs."'";

if(!ereg("^[a-zA-Zа-яА-Я \.-]+$", $_POST["fname"]))
bark("Ваше имя содержит неверные символы.");
$updateset[] = "firstname = ".sqlesc($_POST["fname"]);

if(!ereg("^[a-zA-Zа-яА-Я \.-]+$", $_POST["sname"]))
bark("Ваша фамилия содержит неверные символы.");
$updateset[] = "surname = ".sqlesc($_POST["sname"]);

if(!ereg("^[a-zA-Zа-яА-Я0-9 _-]+$",$_POST['username']))
bark('Ваш ник содержит недопустимые символы');
$updateset[] = "username = ".sqlesc($_POST['username']);
// Мы убрали это
/*
$avatar = $_POST["avatar"];
// Check remote avatar size
        if ($avatar) {
                if (!preg_match('#^((http)|(ftp):\/\/[a-zA-Z0-9\-]+?\.([a-zA-Z0-9\-]+\.)+[a-zA-Z]+(:[0-9]+)*\/.*?\.(gif|jpg|jpeg|png)$)#is', $avatar))
                        stderr($tracker_lang['error'], $tracker_lang['avatar_adress_invalid']);
                if(!(list($width, $height) = getimagesize($avatar)))
                        stderr($tracker_lang['error'], $tracker_lang['avatar_adress_invalid']);
                if ($width > $avatar_max_width || $height > $avatar_max_height)
                        stderr($tracker_lang['error'], sprintf($tracker_lang['avatar_is_too_big'], $avatar_max_width, $avatar_max_height));
        }
// Check remote avatar size
$avatars = ($_POST["avatars"] != "" ? "yes" : "no"); */

$updateset[] = "parked = " . sqlesc($_POST["parked"]);
if ($_POST['resetpasskey'])
        $updateset[] = "passkey=''";

if($_POST['passkey_ip'])
$updateset[] = "passkey_ip = '".getip()."'";

// We have only one style now...
// $updateset[] = sqlesc($_POST["stylesheet"]);

$updateset[] = "language = " .sqlesc($_POST["language"]);

$updateset[] = "torrentsperpage = " . min(100, 0 + $_POST["torrentsperpage"]);
$updateset[] = "topicsperpage = " . min(100, 0 + $_POST["topicsperpage"]);
$updateset[] = "postsperpage = " . min(100, 0 + $_POST["postsperpage"]);
}


if($sect=='about') {
if($_POST['gender'] == 1 || $_POST['gender'] == 2)
$updateset[] = "gender =  ".$_POST["gender"];

///////////////// BIRTHDAY MOD /////////////////////
if(!empty($_POST['year'])&&is_numeric((int)$_POST['year']))
$year = (int)$_POST["year"];
if(!empty($_POST['month'])&&is_numeric((int)$_POST['month']))
$month = (int)$_POST["month"];
if(!empty($_POST['day'])&&is_numeric((int)$_POST['day']))
$day = (int)$_POST["day"];
if(!empty($year)&&!empty($month)&&!empty($day))
$updateset[] = "birthday = " . sqlesc(date("$year.$month.$day"));

if (is_valid_id($_POST["country"]))
  $updateset[] = "country = ".$_POST["country"];
if(!empty($_POST['newcity'])&&is_valid_id($_POST["country"]))
{	if(!ereg("^[a-zA-Zа-яА-Я0-9\. -]+$",$_POST['newcity']))
		bark("Это не похоже на реальное название города!");
	if(!mysql_fetch_array(sql_query("SELECT id FROM countries WHERE id = ".$_POST['country'])))
		bark("Этой страны нет в базе!");
	$arr=mysql_fetch_array(sql_query("SELECT id FROM cities WHERE lower(name)= lower(".sqlesc($_POST['newcity']).") AND country_id = ".$_POST['country']));
	if(!empty($arr['id']))
		$updateset[] = "city = ".$arr['id'];
	else{
	if(!sql_query("INSERT INTO cities (`name`,`country_id`) VALUES (".sqlesc($_POST['newcity']).", ".$_POST['country'].")"))
		bark("Неизвестная ошибка!");
	$updateset[] = "city = ".mysql_insert_id();}
}
elseif (is_valid_id($_POST["city"]))
  $updateset[] = "city = ".$_POST["city"];
$updateset[] = "info = " . sqlesc($_POST["info"]);
$updateset[] = "lovemovies = " . sqlesc($_POST["lovemovie"]);

}


if($sect=='contact') {

//$timezone = 0 + $_POST["timezone"];
//$dst = ($_POST["dst"] != "" ? "yes" : "no");

$icq = (int)$_POST["icq"];
if (strlen($icq) > 10)
    bark("Номер icq слишком длинный  (Макс - 10)");
$updateset[] = "icq = " . sqlesc($icq);

/* We haven't that now...
$msn = unesc($_POST["msn"]);
if (strlen($msn) > 30)
    bark("Ваш msn слишком длинный  (Макс - 30)");
$updateset[] = "msn = " . sqlesc(htmlspecialchars($msn));

$aim = unesc($_POST["aim"]);
if (strlen($aim) > 30)
    bark("Ваш aim слишком длинный  (Макс - 30)");
$updateset[] = "aim = " . sqlesc(htmlspecialchars($aim));

$yahoo = unesc($_POST["yahoo"]);
if (strlen($yahoo) > 30)
    bark("Ваш yahoo слишком длинный  (Макс - 30)");
$updateset[] = "yahoo = " . sqlesc(htmlspecialchars($yahoo));

$mirc = unesc($_POST["mirc"]);
if (strlen($mirc) > 30)
    bark("Ваш mirc слишком длинный  (Макс - 30)");
$updateset[] = "mirc = " . sqlesc(htmlspecialchars($mirc));

$skype = unesc($_POST["skype"]);
if (strlen($skype) > 20)
    bark("Ваш skype слишком длинный  (Макс - 20)");
$updateset[] = "skype = " . sqlesc(htmlspecialchars($skype));

/*
if ($privacy != "normal" && $privacy != "low" && $privacy != "strong")
        bark("whoops");

$updateset[] = "privacy = '$privacy'";
*/  

if(!empty($_POST['website'])&&ereg("^(http|https|ftp|ftps|steam)://[^<>]",$_POST['website']))
$updateset[] = "website = " . sqlesc(htmlspecialchars(unesc($_POST["website"])));

}

//$updateset[] = "timezone = $timezone";
//$updateset[] = "dst = '$dst'";

//$updateset[] = "avatar = " . sqlesc($avatar);

/* ****** */

if($sect=='avatar')
{

$avatar_max_width=200;
$avatar_max_height=400;
$maxfilesize = 5242880; // Допустимый размер в байтах (100кб)
$path=GetCWD()."/avatars";

// Разрешенные типы
$allowed_types = array(
"image/gif" => "gif",
"image/pjpeg" => "jpg",
"image/jpeg" => "jpg",
"image/jpg" => "jpg",
"image/png" => "png"
);

if($_POST['delete'])
{
	@unlink($path.'/'.$CURUSER['avatar']);
	@unlink($path.'/small/'.$CURUSER['avatar']);
	$updateset[]="avatar = ''";
}
elseif(empty($_FILES['avatar']['tmp_name'])) {
	bark("Аватар не загружен"); }
else
{
	if ($_FILES['avatar']['size'] > $maxfilesize)
	bark("Слишком большой размер файла (<font color=\"red\">".round($_FILES['avatar']['size']/1024,2)." кб.</font>)!<br>
      Аватар должен быть <font color=\"red\">менее ".($maxfilesize/1024)."kb</font>.");

	$ifile = $_FILES['avatar']['tmp_name'];
	// Проверяем на попытку взлома
	if(!is_uploaded_file($ifile))
	bark("Файл не загружен!");

	// Получаем НАСТОЯЩИЕ данные об аватаре
	if(!$av = @getimagesize($ifile))
		bark("Загружаемый вами файл не является картинкой или данный формат не поддерживается!");
	$width=$av[0];
	$height=$av[1];
	$mime=$av['mime'];

	// Проверка на допустимые форматы
	if (!array_key_exists($mime,$allowed_types))
		bark("Неверный тип файла для аватара!");
		
	// Берем ид
	$id = $CURUSER['id'];
	
	$ifilename = "avatar".$id.'.'.$allowed_types[$mime];
	if(!image_resize($mime, $ifile,$path.'/'.$ifilename,$avatar_max_width,$avatar_max_height))
	bark("<b><font color=\"red\">Ошибка загрузки аватара на сервер!</font><br>
	Свяжитесь с <a href=\"contact.php\" target=\"_blank\"'>Администрацией</a> сайта.</b>");
	// Создаем превьюшку для стены и комментариев
	if(!image_resize($mime, $ifile,$path.'/small/'.$ifilename,50,100)) 
	bark("<b><font color=\"red\">Ошибка загрузки аватара на сервер!</font><br>
	Свяжитесь с <a href=\"contact.php\" target=\"_blank\"'>Администрацией</a> сайта.</b>");
	if(!image_resize($mime, $ifile,$path.'/65/'.$ifilename,65,130)) 
	bark("<b><font color=\"red\">Ошибка загрузки аватара на сервер!</font><br>
	Свяжитесь с <a href=\"contact.php\" target=\"_blank\"'>Администрацией</a> сайта.</b>");

	$updateset[]="avatar= '".$ifilename."'";
}}


$urladd = "";

if ($changedemail) {
        $sec = mksecret();
        $hash = md5($sec . $email . $sec);
        $obemail = urlencode($email);
        $updateset[] = "editsecret = " . sqlesc($sec);
        $thishost = $_SERVER["HTTP_HOST"];
        $thisdomain = preg_replace('/^www\./is', "", $thishost);
        $body = <<<EOD
You have requested that your user profile (username {$CURUSER["username"]})
on $thisdomain should be updated with this email address ($email) as
user contact.

If you did not do this, please ignore this email. The person who entered your
email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.

To complete the update of your user profile, please follow this link:

http://$thishost/confirmemail.php/{$CURUSER["id"]}/$hash/$obemail

Your new email address will appear in your profile after you do this. Otherwise
your profile will remain unchanged.
EOD;

        mail($email, "$thisdomain profile change confirmation", $body, "From: $SITEEMAIL");

        $urladd .= "&mailsent=1";
}

sql_query("UPDATE users SET " . implode(",", $updateset) . " WHERE id = " . $CURUSER["id"]) or bark("Произошла ошибка при изменении профиля!");

header("Location: $DEFAULTBASEURL/my.php?t=".$sect."&edited=1" . $urladd);

?>