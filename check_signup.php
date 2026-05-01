<?php
require_once("include/bittorrent.php");
dbconn();

header ("Content-Type: text/html; charset=utf-8");

if ($_POST["action"] == "username") {

    $wantname = $_POST["username"];
    $wantusername = convert_text(urldecode(decode_unicode_url($wantname)));
    if (empty($wantusername))
        ajaxerr("Не указан ник", "294");
    elseif (strlen($wantusername) > 40)
        ajaxerr("Ник должен быть короче 40 символов", "294");
    elseif (!ereg("^[a-zA-Z0-9а-яА-Я _-]+$",$wantusername))
        ajaxerr("Неверный ник", "294");
    else
        ajaxsucc("Вы можете использовать этот ник", "294");
}
elseif ($_POST["action"] == "password"){
    $wantpass = $_POST["password"];

    $wantpassword = convert_text(urldecode(decode_unicode_url($wantpass)));
    $pagain = $_POST["passagain"];
    $passagain = convert_text(urldecode(decode_unicode_url($pagain)));

    if (empty($wantpassword))
        ajaxerr("Введите пароль", "294");
    elseif (empty($passagain))
        ajaxerr("Продублируйте пароль", "294");
    elseif ($wantpassword != $passagain)
        ajaxerr("Пароли не совпадают.", "294");
    elseif (strlen($wantpassword) < 6)
        ajaxerr("Минимальная длина пароля 6 символов", "294");
    else
        ajaxsucc("Вы можете использовать этот пароль", "294");
}
elseif ($_POST["action"] == "email"){
    $email = $_POST["email"];
    $res = mysql_fetch_row(sql_query("SELECT COUNT(*) FROM users WHERE email = ".sqlesc($email))) or die;
    if (empty($email))
        ajaxerr("Не указан e-mail адрес", "294");
    elseif ($res[0] != 0)
        ajaxerr("Этот e-mail адрес уже зарегистрирован", "294");
    elseif (!validemail($email))
        ajaxerr("Этот e-mail не правильного формата, проверьте написание", "294");
    else
        ajaxsucc("Вы можете использовать этот e-mail адрес", "294");
}
elseif ($_POST['action'] == 'name')
{
	if(empty($_POST['name']))
		ajaxerr('Введите ваше имя', '294');
	elseif(!ereg("^[a-zA-Zа-яА-Я \.-]+$",convert_text(urldecode(decode_unicode_url($_POST['name'])))))
		ajaxerr('Ваше имя содержит недопустимые символы', '294');
	else
		ajaxsucc('Вы можете использовать это имя', '294');
}
elseif($_POST['action'] == 'surname')
{
	if(empty($_POST['surname']))
		ajaxerr('Введите вашу фамилию', '294');
	elseif(!ereg("^[a-zA-Zа-яА-Я \.-]+$",convert_text(urldecode(decode_unicode_url($_POST['surname'])))))
		ajaxerr('Ваша фамилия содержит недопустимые символы');
	else
		ajaxsucc('Вы можете использовать эту фамилию', '294');
}
?> 
