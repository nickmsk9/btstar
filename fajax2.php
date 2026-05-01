<?php
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();
header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);
header ("Cache-control: no-store");
header ("Pragma: no-cache");

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'POST')
{
	if(empty($_POST['act'])||empty($_POST['id'])||!is_numeric($_POST['id']))
		die("Error 1");
	switch($_POST['act'])
	{
		case 'aprove':
			$qw=sql_query("SELECT `status` FROM	friends WHERE userid = ".$_POST['id']." AND friendid = ".$CURUSER['id']);
		if(empty($qw)||mysql_num_rows($qw)==0)
			die("Error 2");;
		$qw=mysql_fetch_array($qw);
		if($qw['status'] != 'pending')
			die("Error 3");
			$q1=sql_query("UPDATE friends SET `status` = 'yes' WHERE userid = ".$_POST['id']." AND friendid = ".$CURUSER['id']);
			$q2=sql_query("REPLACE INTO friends (`userid`,`friendid`,`status`) VALUES (".$CURUSER['id'].",".$_POST['id'].",'yes')");
			if($q1&&$q2)
			echo"<div class=\"success\">Пользователь добавлен в список друзей!</div>";
			else
			echo"<div class=\"error\">Произошла неизвестная ошибка!</div>";
		break;
		case 'deny':
					$qw=sql_query("SELECT `status` FROM	friends WHERE userid = ".$_POST['id']." AND friendid = ".$CURUSER['id']);
		if(empty($qw)||mysql_num_rows($qw)==0)
			die("Error 2");;
		$qw=mysql_fetch_array($qw);
		if($qw['status'] != 'pending')
			die("Error 3");
			$q1=sql_query("UPDATE friends SET `status` = 'no' WHERE userid = ".$_POST['id']." AND friendid = ".$CURUSER['id']);
			$q2=sql_query("REPLACE INTO friends (`userid`,`friendid`,`status`) VALUES (".$CURUSER['id'].",".$_POST['id'].",'no')");
			if($q1&&$q2)
			echo"<div class=\"success\">Заявка отклонена!</div>";
			else
			echo"<div class=\"error\">Произошла неизвестная ошибка!</div>";
		break;
		default:
		die("Error 5");
		break;
		
	}

}
else
die("Error 4");
?>