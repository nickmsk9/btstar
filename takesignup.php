<?
require_once("include/bittorrent.php");

dbconn();

if ($deny_signup && !$allow_invite_signup)
        stderr($tracker_lang['error'], "Извините, но регистрация отключена администрацией.");

if ($CURUSER)
        stderr($tracker_lang['error'], sprintf($tracker_lang['signup_already_registered'], $SITENAME));

$users = get_row_count("users");
if ($users >= $maxusers)
        stderr($tracker_lang['error'], sprintf($tracker_lang['signup_users_limit'], number_format($maxusers)));

if (!mkglobal("wantusername:wantpassword:passagain:email"))
        stderr($tracker_lang['error'], "Прямой доступ к этому файлу не разрешен.");

function bark($msg) {
        global $tracker_lang;
        stdhead();
        stdmsg($tracker_lang['error'], $msg, 'error');
        stdfoot();
        exit;
}

function validusername($username)
{
        if(ereg("^[a-zA-Zа-яА-Я0-9 _-]+$",$username))
			return true;
		return false;
}
$updateset = array();

// Нам не нужны инвайты. Да.
/*
	if($_POST["invite"]) {
	if (strlen($_POST["invite"]) != 32)
	stderr("Ошибка", "Вы ввели не правильный код приглашения.");
	list($inviter) = mysql_fetch_row(sql_query("SELECT inviter FROM invites WHERE invite = ".sqlesc($_POST["invite"])));
    if (!$inviter)
	stderr("Ошибка", "Код приглашения введенный вами не рабочий.");
	list($invitedroot) = mysql_fetch_row(sql_query("SELECT invitedroot FROM users WHERE id = $inviter")); 
	} */

if($_POST['gender']==1||$_POST['gender']==2)
$updateset[] = 'gender = '.$_POST["gender"];
else
	bark('Выберите ваш пол');
	
if(ereg("^(http|https|ftp|ftps|steam)://[^<>]+$",$_POST['website']))
$updateset[] = 'website = '.sqlesc($_POST["website"]);

if(is_numeric($_POST['country']))
{
	if(mysql_fetch_array(sql_query("SELECT * FROM countries WHERE id =".$_POST['country'])))
	$updateset[] = 'country = '.$_POST['country'];
	else
	bark('Выберите страну!');
}
else bark('Выберите страну!');

// CITY CHECK
if(!empty($_POST['newcity']))
{	if(!ereg("^[a-zA-Zа-яА-Я0-9\. -]+$",$_POST['newcity']))
		bark("Это не похоже на реальное название города!");
	$arr=mysql_fetch_array(sql_query("SELECT id FROM cities WHERE lower(name)= lower(".sqlesc($_POST['newcity']).") AND country_id = ".$_POST['country']));
	if(!empty($arr['id']))
		$updateset[] = "city = ".$arr['id'];
	else{
	if(!sql_query("INSERT INTO cities (`name`,`country_id`) VALUES (".sqlesc($_POST['newcity']).", ".$_POST['country'].")"))
		bark("Неизвестная ошибка!");
	$updateset[] = "city = ".mysql_insert_id();}
}
elseif (is_valid_id($_POST["city"])) {
	if(mysql_fetch_array(sql_query("SELECT id FROM cities WHERE id = ".$_POST['city'])))
  $updateset[] = "city = ".$_POST["city"];
	else
		bark('Выберите город');
}

if(is_numeric($_POST['year'])&&$_POST['year']<(date("Y")-13))
$year = intval($_POST["year"]);
else bark('Выберите год рождения');
if(is_numeric($_POST['month'])&&$_POST['month']<=12&&$_POST['month']>=1)
$month = intval($_POST["month"]);
else bark('Выберите месяц рождения');
if(is_numeric($_POST['day'])&&$_POST['day']<=31&&$_POST['day']>=1)
$day = intval($_POST["day"]);
else bark('Выберите день рождения');

$updateset[] = 'birthday = \''.date("Y-m-d",strtotime($year.'-'.$month.'-'.$day)).'\''; 

if(!empty($_POST['icq'])&&is_numeric($_POST['icq'])&&$_POST['icq']>9999&&$_POST['icq']<=9999999999)
$updateset[] = 'icq = '.$_POST['icq'];

// Что за нахуй? У нас этого нет и не надо
/*
$msn = unesc($_POST["msn"]);
if (strlen($msn) > 30)
    bark("Жаль, Ваш msn слишком длинный  (Макс - 30)");

$aim = unesc($_POST["aim"]);
if (strlen($aim) > 30)
    bark("Жаль, Ваш aim слишком длинный  (Макс - 30)");

$yahoo = unesc($_POST["yahoo"]);
if (strlen($yahoo) > 30)
    bark("Жаль, Ваш yahoo слишком длинный  (Макс - 30)");

$mirc = unesc($_POST["mirc"]);
if (strlen($mirc) > 30)
    bark("Жаль, Ваш mirc слишком длинный  (Макс - 30)");

$skype = unesc($_POST["skype"]);
if (strlen($skype) > 20)
    bark("Жаль, Ваш skype слишком длинный  (Макс - 20)"); */
	
if(empty($wantusername))
	bark('Вы не ввели ник');
if(!validusername($wantusername))
	bark('Ник содержит недопустимые символы');
$updateset[] = 'username = '.sqlesc($wantusername);
if(empty($_POST['name']))
	bark('Вы не ввели имя');
if(!ereg("^[a-zA-Zа-яА-Я \.-]+$",$_POST['name']))
	bark('Имя содержит недопустимые символы');
$updateset[] = 'firstname = '.sqlesc($_POST['name']);
if(empty($_POST['surname']))
	bark('Вы не ввели фамилию!');
if(!ereg("^[a-zA-Zа-яА-Я \.-]+$",$_POST['surname']))
	bark('Фамилия содержит недопустимые символы');
$updateset[] = 'surname = '.sqlesc($_POST['surname']);

if(empty($wantpassword))
	bark('Вы не ввели пароль');

if (strlen($wantusername) > 40)
    bark("Ник слишком длинный (не более 40 символов)");

if(strlen($_POST['name']) > 40)
	bark('Имя не может быть более 40 символов');
	
if(strlen($_POST['surname']) > 40)
	bark('Фамилия не может быть более 40 символов');

if ($wantpassword != $passagain)
        bark("Пароли не совпадают");

if (strlen($wantpassword) < 6)
        bark("Пароль слишком короткий (минимум 6 символов)");

if ($wantpassword == $wantusername)
        bark("Пароль не может быть такой же как ник");
if ($wantpassword == $_POST['name'])
	bark('Пароль не может быть такой же как имя');
if ($wantpassword == $_POST['furname'])
	bark('Пароль не може тбыть такой же как фамилия');

if (!validemail($email))
        bark("Вы ввели неверный E-mail адрес");
if (strlen($email) > 80)
	bark('E-mail не может быть больше 80 символов');

// make sure user agrees to everything...
if ($_POST["rulesverify"] != "yes" || $_POST["faqverify"] != "yes" || $_POST["ageverify"] != "yes")
        stderr($tracker_lang['error'], "Извините, вы не подходите для того что-бы стать членом этого сайта.");

// check if email addy is already in use
$a = (@mysql_fetch_row(@sql_query("SELECT COUNT(*) FROM users WHERE email=".sqlesc($email)))) or die(mysql_error());  
if ($a[0] != 0)
        bark("E-mail адрес $email уже зарегистрирован в системе.");

$updateset[] = "email = '$email'";
$updateset[] = "ip = '".getip()."'";

if (isset($_COOKIE["uid"]) && is_numeric($_COOKIE["uid"]) && $users) {
    $cid = intval($_COOKIE["uid"]);
    $c = sql_query("SELECT enabled FROM users WHERE id = $cid ORDER BY id DESC LIMIT 1");
    $co = @mysql_fetch_row($c);
    if ($co[0] == 'no') {
                sql_query("UPDATE users SET ip = '".getip()."', last_access = NOW() WHERE id = $cid");
                bark("Ваш IP забанен на этом трекере. Регистрация невозможна.");
    } else
                bark("Регистрация невозможна!");
} else {
    $b = (@mysql_fetch_row(@sql_query("SELECT enabled, id FROM users WHERE ip = '".getip()."' ORDER BY last_access DESC LIMIT 1")));
    if ($b[0] == 'no') {
                $banned_id = $b[1];
        setcookie("uid", $banned_id, "0x7fffffff", "/");
                bark("Вы забанены на этом трекере. Регистрация невозможна.");
    }
}

$updateset[] = 'secret = '.sqlesc(mksecret());
$updateset[] = 'passhash = \''.md5($secret . $wantpassword . $secret).'\'';
$editsecret =(!$users?"":mksecret());
$updateset[] = 'editsecret = '.sqlesc($editsecret);

if ((!$users) || (!$use_email_act == true))
        $updateset[] = 'status = \'confirmed\'';
else
        $updateset[] = 'status = \'pending\'';
		
$updateset[] = 'added = \''.get_date_time().'\'';


foreach($updateset as $field)
{
	list($left,$right) = explode('=',$field);
	$fields.=($fields ? ', ' : '').trim($left);
	$values.=($values ? ', ' : '').trim($right);
}
$ret = sql_query("INSERT INTO users (".$fields.") VALUES (".$values.")");

if (!$ret) {
        bark("Неизвестная ошибка.");
}

$id = mysql_insert_id();

//sql_query("DELETE FROM invites WHERE invite = ".sqlesc($_POST["invite"]));
$bot_text = "[b]У нас на трекере зарегистрирован новый пользователь [color=red]".$_POST['name']. ' [i]'.$wantusername.'[/i] '.$_POST['surname']."[/color][/b]";
bot_msg($bot_text);
write_log("Зарегистрирован новый пользователь $wantusername","FFFFFF","tracker");

$psecret = md5($editsecret);

$body = <<<EOD
Вы зарегистрировались на $SITENAME и указали этот адрес как обратный ($email).

Если это были не вы, пожалуйста, проигнорируйте это письмо. Персона, которая ввела ваш E-Mail адресс, имеет IP адрес {$_SERVER["REMOTE_ADDR"]}. Пожалуста, не отвечайте.

Для подтверждения вашей регистрации, вам нужно пройти по следующей ссылке:

$DEFAULTBASEURL/confirm.php?id=$id&secret=$psecret

После того, как вы это сделаете, вы сможете использовать ваш аккаунт. Если вы этого не сделаете,
 ваш новый аккаунт будет удален через пару дней. Мы рекомендуем вам прочитать правила
и ЧаВо, прежде чем вы начнете использовать $SITENAME.
EOD;

if($use_email_act && $users) {
        if (!sent_mail($email,$SITENAME,$SITEEMAIL,"Подтверждение регистрации на $SITENAME",$body,false)) {
                stderr($tracker_lang['error'], "Невозможно отправить E-Mail. Попробуйте позже");
        }
} else {
        logincookie($id, $wantpasshash);
}

header("Refresh: 0; url=ok.php?type=signup&email=" . urlencode($email));

?>