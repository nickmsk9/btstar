<?
if (!defined('UC_SYSOP'))
	die('Direct access denied.');
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ru">
<head>
<!--<base href="<?=$DEFAULTBASEURL;?>">-->
<title><?= $title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta name="generator" content="bt-star.engine">
<meta name="Description" content="У нас Вы сможете скачать все, что Вам нужно: музыка, фильмы, видео абсолютно бесплатно!<?=$desription;?>">
<meta name="Keywords" content="скачать музыку кинофильмов, cкачать видео, Торрент файлы, поиск Торрентов, качать Торренты, Торент трекер, сериал Ранетки скачать песню, скачать сезон сериала Клуб, скачать сериал Клиника, Торрент.ру, сериал Ранетки скачать серии, Торент фильмы, списки Торрентов, где скачать сериал, скачать сериал сверхъестественное, кино Торренты, сериалы Торрент, русские Торренты, аниме Торрент<?=$keywords;?>">
<meta name="verify-v1" content="dtoPAe5KrTJujLASaRMoiVH5fdRI6+sXU/5RbNSdXhw=">
<link rel="stylesheet" href="./themes/<?=$ss_uri."/".$ss_uri?>.css" type="text/css">
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/resizer.js"></script>
<script language="javascript" type="text/javascript" src="js/tooltips.js"></script>
<script language="javascript" type="text/javascript" src="js/overlib.js"></script>
<script language="javascript" type="text/javascript" src="js/functions.js"></script>
<script language="javascript" type="text/javascript" src="js/collapse.js"></script>
<link rel="alternate" type="application/rss+xml" title="Последние торренты" href="<?=$DEFAULTBASEURL?>/rss.xml">
<link rel="shortcut icon" href="<?=$DEFAULTBASEURL;?>/favicon.ico" type="image/x-icon" />
</head>
<body>
<table width="95%" align="center">
<tr>
<td align="center" style="padding:0;margin:0;border:0;">
<div id="menu">
<ul>
<li><img src="./themes/TBDev/images/left_menu.gif"></li>
<li><img src="./themes/<?=$ss_uri;?>/images/menu_spacer.jpg"></li>
<li><a href="index.php"><img src="./themes/TBDev/images/logo.png" border="0"></a></li>
<li><img src="./themes/<?=$ss_uri;?>/images/menu_spacer.jpg"></li>
<li><a href="browse.php"><?=$tracker_lang['browse'];?></a></li>
<li><img src="./themes/<?=$ss_uri;?>/images/menu_spacer.jpg"></li>
<? if($CURUSER) {?>
<li><a href="upload.php"><?=$tracker_lang['upload'];?></a></li>
<li><img src="./themes/<?=$ss_uri;?>/images/menu_spacer.jpg"></li>
<? } ?>
<li><a href="notes">Записи</a></li>
<li><img src="./themes/<?=$ss_uri;?>/images/menu_spacer.jpg"></li>
<li><a href="rules.php"><?=$tracker_lang['rules'];?></a></li>
<li><img src="./themes/<?=$ss_uri;?>/images/menu_spacer.jpg"></li>
<li><a href="faq.php"><?=$tracker_lang['faq'];?></a></li>
<li><img src="./themes/<?=$ss_uri;?>/images/menu_spacer.jpg"></li>
<? if($CURUSER) {?>
<li><a href="staff.php">Команда</a></li>
<li><img src="./themes/<?=$ss_uri;?>/images/menu_spacer.jpg"></li>
<? } ?>
</ul><div style="float:right;">
<? if($CURUSER) {?>
<li><img src="./themes/<?=$ss_uri;?>/images/menu_spacer.jpg"></li>
<li><a href="id<?=$CURUSER["id"];?>"><?=$CURUSER["firstname"]?>&nbsp;<?=$CURUSER["surname"]?></a></li>
<li><img src="./themes/<?=$ss_uri;?>/images/menu_spacer.jpg"></li>
<li><a href="logout.php">Выход</a></li>
<? } ?>
<li><img src="./themes/<?=$ss_uri;?>/images/menu_spacer.jpg"></li>
<img src="./themes/TBDev/images/right_menu.gif">
</div></div>
</td></tr><table>


<?php

$w = "width=\"95%\"";
//if ($_SERVER["REMOTE_ADDR"] == $_SERVER["SERVER_ADDR"]) $w = "width=984";

?>
<table class="mainouter" align="center" <?=$w; ?> border="0" cellspacing="0" cellpadding="3">

<!------------- MENU ------------------------------------------------------------------------>

<? $fn = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/") + 1); ?>

<td align="center" valign="top" class="outer" style="border:0;padding-right:0px;">
<?

if ($CURUSER['override_class'] != 255 && $CURUSER) // Second condition needed so that this box isn't displayed for non members/logged out members.
{
		print("<p><table border=0 cellspacing=0 cellpadding=10 bgcolor=green><tr><td style='padding: 10px; background: green'>\n");
		print("<b><a href=\"$DEFAULTBASEURL/restoreclass.php\"><font color=white>".$tracker_lang['lower_class']."</font></a></b>");
		print("</td></tr></table></p>\n");
}
 
