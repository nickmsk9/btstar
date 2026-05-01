<?php
# IMPORTANT: Do not edit below unless you know what you are doing!
if(!defined('IN_TRACKER') && !defined('IN_ANNOUNCE'))
  die('Hacking attempt!');

//$FUNDS = "$2,610.31";

$SITE_ONLINE = true;
//$SITE_ONLINE = local_user();
//$SITE_ONLINE = false;

$max_torrent_size = 1000000;
$announce_interval = 60 * 30;
$signup_timeout = 86400 * 3;
$minvotes = 1;
$max_dead_torrent_time = 6 * 3600;

// Max users on site
$maxusers = 50000; // LoL Who we kiddin' here?

// Максимальное количество записей
$maxnotes = 10;

// ONLY USE ONE OF THE FOLLOWING DEPENDING ON YOUR O/S!!!
$torrent_dir = "torrents";    # FOR UNIX ONLY - must be writable for httpd user
//$torrent_dir = "C:/web/Apache2/htdocs/tbsource/torrents";    # FOR WINDOWS ONLY - must be writable for httpd user

$doxpath = "dox";

// Email for sender/return path.
$SITEEMAIL = "admin@" . ($_SERVER["HTTP_HOST"] ?? "localhost");

$SITENAME = "BT-Star Russia";

$autoclean_interval = 900;
$pic_base_url = "./pic/";

$show_ad = false;

// [BEGIN] Custom variables from Yuna Scatari
$ttl_days = 28; // Сколько дней торрент может жить до TTL.
$default_language = "russian"; // Язык трекера по умолчанию.
$avatar_max_width = 120; // Максимальная ширина аватары.
$avatar_max_height = 120; // Максимальная высота аватары.
$ctracker = 1; // Use CrackerTracker - anti-cracking system. I personaly think it's un-needed...
$points_per_hour = 0.2; // Сколько добавлять бонусов в час, если пользователь сидирует.
$points_per_cleanup = $points_per_hour*($autoclean_interval/3600); // Don't change it!
$default_theme = "TBDev"; // Тема по умолчанию для гостей.
$nc = "no"; // Не пропускать на трекер пиров с закрытыми портами.
$deny_signup = 0; // Запретить регистрацию. 1 = регистрация отключена, 0 = регистрация включена.
$allow_invite_signup = 0; // Разрешить регистрацию через приглашения. 1 = разрешена, 0 = не разрешена.
$use_ttl = 0; // Использовать TTL.
$use_email_act = 0; // Использовать активацию по почте, иначе - автоматическая активация при регистрации.
$use_wait = 0; // Использовать ожидание на пользователях которые имеют плохой рейтинг.
$use_lang = 1; // Включить языковую систему. Выключите если вы хотите перевести шаблоны и другие файлы - тогда все фразы от системы станут пустым местом.
$use_captcha = 0; // Использовать защиту от авто-регистраций.
$use_blocks = 1; // Использовать систему блоков. 1 - да, 0 - нет. Если ее отключить то админ-панель и ее блочный модуль не смогут нормально работать при работе с блоками.
$use_gzip = 1; // Использовать сжатие GZip на страницах.
$use_ipbans = 0; // Использовать функцию блокирования IP-адресов. 0 - нет, 1 - да.
$use_sessions = 0; // Использовать сессии. 0 - нет, 1 - да.
$smtptype = "advanced";
$as_timeout=30; // PM messages delay (antiflood), seconds
$as_check_messages=1; //Compare last 5 messages (antispam), 0-no, 1-yes
// [END] Custom variables from Yuna Scatari

// Сколько можно секономить запросов отключая что-то:
/*

Капча - 1 запрос при регистрации на форме и 1 запрос на проверке регистрации. (мин. 2 запроса)
Блоки - 1 запрос на вызов блоков + все запросы из всех блоков что активны. (мин. 1 запрос. В комплектации по умолчанию сборки - 2 запроса на блоке пользователи, 1 запрос на блоке форум, 2 запроса на блоке релизы)
Сессии - 1 запрос на постоянного юзера и 2 запроса на нового (первый раз на сайте). (мин. 1 запрос)
IP-баны - 1 запрос на любой странице.

Авторизация - пока не готово. Будет возмоность отключать авторизацию т.е. вход пользователей что даст возможность в минимальном режиме работать ВООБЩЕ без запросов на чистой странице.

*/

// Не использовать кэш
$no_cache = false;

?>
