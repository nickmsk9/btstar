<?php
# IMPORTANT: Do not edit below unless you know what you are doing!
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

function docleanup() {
        global $torrent_dir, $signup_timeout, $max_dead_torrent_time, $use_ttl, $autoclean_interval, $points_per_cleanup, $ttl_days, $tracker_lang;

        @set_time_limit(0);
        @ignore_user_abort(1);

        do {
                $res = sql_query("SELECT id FROM torrents") or sqlerr(__FILE__,__LINE__);
                $ar = array();
                while ($row = mysql_fetch_array($res)) {
                        $id = $row[0];
                        $ar[$id] = 1;
                }

                if (!count($ar))
                        break;

                $dp = @opendir($torrent_dir);
                if (!$dp)
                        break;

                $ar2 = array();
                while (($file = readdir($dp)) !== false) {
                        if (!preg_match('/^(\d+)\.torrent$/', $file, $m))
                                continue;
                        $id = $m[1];
                        $ar2[$id] = 1;
                        if (isset($ar[$id]) && $ar[$id])
                                continue;
                        $ff = $torrent_dir . "/$file";
                        unlink($ff);
                }
                closedir($dp);

                if (!count($ar2))
                        break;

                $delids = array();
                foreach (array_keys($ar) as $k) {
                        if (isset($ar2[$k]) && $ar2[$k])
                                continue;
                        $delids[] = $k;
                        unset($ar[$k]);
                }
                if (count($delids))
                        sql_query("DELETE FROM torrents WHERE id IN (" . join(",", $delids) . ")") or sqlerr(__FILE__,__LINE__);

                $res = sql_query("SELECT torrent FROM peers GROUP BY torrent") or sqlerr(__FILE__,__LINE__);
                $delids = array();
                while ($row = mysql_fetch_array($res)) {
                        $id = $row[0];
                        if (isset($ar[$id]) && $ar[$id])
                                continue;
                        $delids[] = $id;
                }
                if (count($delids))
                        sql_query("DELETE FROM peers WHERE torrent IN (" . join(",", $delids) . ")") or sqlerr(__FILE__,__LINE__);
} while (0);

		$last_cleanup = mysql_fetch_array(sql_query("SELECT value_u FROM avps WHERE arg = 'lastcleantime'"));
		$last_cleanup = $last_cleanup['value_u'];

        $deadtime = deadtime();
        sql_query("DELETE FROM peers WHERE last_action < FROM_UNIXTIME($deadtime)") or sqlerr(__FILE__,__LINE__);

        $deadtime = deadtime();
        sql_query("UPDATE snatched SET seeder = 'no' WHERE seeder = 'yes' AND last_action < FROM_UNIXTIME($deadtime)");

        $deadtime -= $max_dead_torrent_time;
        sql_query("UPDATE torrents SET visible='no' WHERE visible='yes' AND last_action < FROM_UNIXTIME($deadtime)") or sqlerr(__FILE__,__LINE__);

        $torrents = array();
        $res = sql_query("SELECT torrent, seeder, COUNT(*) AS c FROM peers GROUP BY torrent, seeder") or sqlerr(__FILE__,__LINE__);
        while ($row = mysql_fetch_assoc($res)) {
                if ($row["seeder"] == "yes")
                        $key = "seeders";
                else
                        $key = "leechers";
                $torrents[$row["torrent"]][$key] = $row["c"];
        }

        $res = sql_query("SELECT torrent, COUNT(*) AS c FROM comments GROUP BY torrent") or sqlerr(__FILE__,__LINE__);
        while ($row = mysql_fetch_assoc($res)) {
                $torrents[$row["torrent"]]["comments"] = $row["c"];
        }

        $fields = explode(":", "comments:leechers:seeders");
        $res = sql_query("SELECT id, seeders, leechers, comments FROM torrents") or sqlerr(__FILE__,__LINE__);
        while ($row = mysql_fetch_assoc($res)) {
                $id = $row["id"];
                $torr = $torrents[$id];
                foreach ($fields as $field) {
                        if (!isset($torr[$field]))
                                $torr[$field] = 0;
                }
                $update = array();
                foreach ($fields as $field) {
                        if ($torr[$field] != $row[$field])
                                $update[] = "$field = " . $torr[$field];
                }
                if (count($update))
                        sql_query("UPDATE torrents SET " . implode(", ", $update) . " WHERE id = $id") or sqlerr(__FILE__,__LINE__);
        }

                //delete inactive user accounts
                $secs = 66666*86400;
                $dt = sqlesc(get_date_time(gmtime() - $secs));
                $maxclass = UC_POWER_USER;
                $res = sql_query("SELECT id FROM users WHERE parked='no' AND status='confirmed' AND class <= $maxclass AND last_access < $dt AND last_access <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);
                while ($arr = mysql_fetch_assoc($res)) {
                        sql_query("DELETE FROM users WHERE id = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM messages WHERE receiver = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM friends WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM friends WHERE friendid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM blocks WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM blocks WHERE blockid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM invites WHERE inviter = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM peers WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM simpaty WHERE fromuserid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM checkcomm WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                       }

       //delete parked user accounts
       $secs = 175*86400; // change the time to fit your needs
       $dt = sqlesc(get_date_time(gmtime() - $secs));
       $maxclass = UC_POWER_USER;
       $res = sql_query("SELECT id FROM users WHERE parked='yes' AND status='confirmed' AND class <= $maxclass AND last_access < $dt");
       if (mysql_num_rows($res) > 0) {
               while ($arr = mysql_fetch_array($res)) {
                        sql_query("DELETE FROM users WHERE id = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM messages WHERE receiver = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM friends WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM friends WHERE friendid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM blocks WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM blocks WHERE blockid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM invites WHERE inviter = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM peers WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM simpaty WHERE fromuserid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM checkcomm WHERE userid = ".sqlesc($arr["id"])) or sqlerr(__FILE__,__LINE__);
                }
        }

        // ************************************* Удаление приватных сообщений ***********************************************************
        //Удаляем все прочтенные системные сообщения старше 30 дней
         $secs_system = 30*86400; // Количество дней
         $dt_system = sqlesc(get_date_time(gmtime() - $secs_system)); // Сегодня минус количество дней
         sql_query("DELETE FROM messages WHERE sender = '' AND unread = 'no' AND added < $dt_system") or sqlerr(__FILE__, __LINE__);
        //Удаляем ВСЕ прочтенные сообщения старше 30 дней
         $secs_all = 30*86400; // Количество дней
         $dt_all = sqlesc(get_date_time(gmtime() - $secs_all)); // Сегодня минус количество дней
         sql_query("DELETE FROM messages WHERE unread = 'no' AND added < $dt_all") or sqlerr(__FILE__, __LINE__);
        // ************************************* Удаление приватных сообщений ***********************************************************

        // delete unconfirmed users if timeout.
        $deadtime = TIMENOW - $signup_timeout;
        $res = sql_query("SELECT id FROM users WHERE status = 'pending' AND added < FROM_UNIXTIME($deadtime) AND last_login < FROM_UNIXTIME($deadtime) AND last_access < FROM_UNIXTIME($deadtime)") or sqlerr(__FILE__,__LINE__);
        if (mysql_num_rows($res) > 0) {
                while ($arr = mysql_fetch_array($res)) {
                        sql_query("DELETE FROM users WHERE id = ".sqlesc($arr["id"]));
                }
        }

        // Update seed bonus
        sql_query("UPDATE users SET bonus = bonus + ".($points_per_cleanup*(time()-$last_cleanup)/$autoclean_interval)." WHERE users.id IN (SELECT userid FROM peers WHERE seeder = 'yes')") or sqlerr(__FILE__,__LINE__);

        //remove expired warnings
        $now = sqlesc(get_date_time());
        $modcomment = sqlesc(date("Y-m-d") . " - Предупреждение снято системой по таймауту.\n");
        $msg = sqlesc("Ваше предупреждение снято по таймауту. Постарайтесь больше не получать предупреждений и сделовать правилам.\n");
        sql_query("INSERT INTO messages (sender, receiver, added, msg, poster) SELECT 0, id, $now, $msg, 0 FROM users WHERE warned='yes' AND warneduntil < NOW() AND warneduntil <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);
        sql_query("UPDATE users SET warned='no', warneduntil = '0000-00-00 00:00:00', modcomment = CONCAT($modcomment, modcomment) WHERE warned='yes' AND warneduntil < NOW() AND warneduntil <> '0000-00-00 00:00:00'") or sqlerr(__FILE__,__LINE__);

//Пересчет количества тэгов.
sql_query('UPDATE tags AS t SET t.howmuch = (SELECT COUNT(*) FROM torrents AS ts WHERE ts.tags LIKE CONCAT(\'%\', t.name, \'%\') AND ts.category = t.category)');
sql_query('DELETE FROM tags WHERE howmuch = 0;');


        // promote power users
        $limit = 25*1024*1024*1024;
        $minratio = 1.05;
        $maxdt = sqlesc(get_date_time(gmtime() - 86400*28));
        $now = sqlesc(get_date_time());
        $msg = sqlesc("Наши поздравления, вы были авто-повышены до ранга [b]Опытный пользовать[/b].");
        $subject = sqlesc("Вы были повышены");
        $modcomment = sqlesc(date("Y-m-d") . " - Повышен до уровня \"".$tracker_lang["class_power_user"]."\" системой.\n");
        sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) SELECT 0, id, $now, $msg, 0, $subject FROM users WHERE class = 0 AND uploaded >= $limit AND uploaded / downloaded >= $minratio AND added < $maxdt") or sqlerr(__FILE__,__LINE__);
        sql_query("UPDATE users SET class = ".UC_POWER_USER.", modcomment = CONCAT($modcomment, modcomment) WHERE class = ".UC_USER." AND uploaded >= $limit AND uploaded / downloaded >= $minratio AND added < $maxdt") or sqlerr(__FILE__,__LINE__);

        // demote power users
        $minratio = 0.95;
        $now = sqlesc(get_date_time());
        $msg = sqlesc("Вы были авто-понижены с ранга [b]Опытный пользователь[/b] до ранга [b]Пользователь[/b] потому-что ваш рейтинг упал ниже [b]{$minratio}[/b].");
        $subject = sqlesc("Вы были понижены");
        $modcomment = sqlesc(date("Y-m-d") . " - Понижен до уровня \"".$tracker_lang["class_user"]."\" системой.\n");
        sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) SELECT 0, id, $now, $msg, 0, $subject FROM users WHERE class = 1 AND uploaded / downloaded < $minratio") or sqlerr(__FILE__,__LINE__);
        sql_query("UPDATE users SET class = ".UC_USER.", modcomment = CONCAT($modcomment, modcomment) WHERE class = ".UC_POWER_USER." AND uploaded / downloaded < $minratio") or sqlerr(__FILE__,__LINE__);

		$now = sqlesc(get_date_time());
        $msg = sqlesc("Вы были авто-понижены с ранга [b]VIP[/b] до ранга [b]Пользователь[/b] по истечению срока.");
        $subject = sqlesc("Вы были понижены");
        $modcomment = sqlesc(date("Y-m-d") . " - Понижен до уровня \"".$tracker_lang["class_user"]."\" системой.\n");
        sql_query("INSERT INTO messages (sender, receiver, added, msg, poster, subject) SELECT 0, id, $now, $msg, 0, $subject FROM users WHERE class = ".UC_VIP." AND vip_to != NULL AND vip_to < NOW()") or sqlerr(__FILE__,__LINE__);
		sql_query("UPDATE users SET class = ".UC_USER.", modcomment = CONCAT($modcomment, modcomment), vip_to = NULL WHERE class = ".UC_VIP." AND vip_to != NULL AND vip_to < NOW()");
		
        // delete old torrents
        if ($use_ttl) {
                $dt = sqlesc(get_date_time(gmtime() - ($ttl_days * 86400)));
                $res = sql_query("SELECT id, name FROM torrents WHERE added < $dt") or sqlerr(__FILE__,__LINE__);
                while ($arr = mysql_fetch_assoc($res)) {
                        @unlink("$torrent_dir/$arr[id].torrent");
                        sql_query("DELETE FROM torrents WHERE id=$arr[id]") or sqlerr(__FILE__,__LINE__);
						sql_query("DELETE FROM coins WHERE torrentid=$arr[id]") or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM snatched WHERE torrentid=$arr[id]") or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM peers WHERE torrent=$arr[id]") or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM comments WHERE torrent=$arr[id]") or sqlerr(__FILE__,__LINE__);
                        sql_query("DELETE FROM checkcomm WHERE checkid=$arr[id] AND torrent = 1") or sqlerr(__FILE__,__LINE__);
                        write_log("Торрент $arr[id] ($arr[name]) был удален системой (старше чем $ttl_days дней)","","torrent");
                }
        }

        $secs = 1 * 3600;
        $dt = time() - $secs;
        sql_query("DELETE FROM sessions WHERE time < $dt") or sqlerr(__FILE__,__LINE__);
		
		// TABLES OPTIMIZATION
		@sql_query("OPTIMIZE TABLE `avps`, `bans`, `blocks`, `bonus`, `bonusgen`, `categories`, `checkcomm`,
		`cities`, `clubs`, `coins`, `comments`, `countries`, `faq`, `friends`, `futurerls`, `invites`, `messages`, `much_on`,
		`news`, `newscomments`, `notconnectablepmlog`, `notes`, `noteswall`, `pages`, `peers`, `pollanswers`, `polls`, `sessions`,
		`shoutbox`, `simpaty`, `sitelog`, `snatched`, `tags`, `templates`, `thanks`, `torrents`, `users`, `wall`;");

		require_once('remote_get.php');
}

?>