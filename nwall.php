<?php

require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();
header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);
header ("Cache-control: no-store");
header ("Pragma: no-cache");

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && $_SERVER["REQUEST_METHOD"] == 'POST')
{
    $act = (string)$_POST['act'];
    ?>
    <script language="JavaScript" type="text/javascript">
        jQuery(function($) {$('.zebra:even').css({backgroundColor: '#EEEEEE'});});
    </script>
    <?php
    if ($act == "send")
    {
        $uid = (int)$_POST['uid'];
		$id = (int)$_POST['id'];
        $text = base64_decode($_POST['text']);
        if(empty($CURUSER['id']) || empty($uid) || empty($id) || empty($text))
            die("Прямой доступ закрыт");
        $dt = get_date_time();
		$note = mysql_fetch_array(sql_query("SELECT n.id, n.uid, n.access, friends.id AS fid FROM notes AS n LEFT JOIN friends ON friends.friendid = ".$CURUSER['id']." AND friends.userid = ".$uid." AND friends.status = 'yes' WHERE n.uid = ".$uid." AND n.id = ".$id));
        if(empty($note))
			die("Прямой доступ запрещен!");
		if($note['access']==0 and (!$CURUSER['id']==$uid||!$note['fid']) and $CURUSER['class'] < UC_MODERATOR)
			die("Эту запись можно просматривать только друзьям ".$note['firstname']." ".$note['username']." ".$note['surname']."!");
		sql_query("INSERT INTO noteswall (text, owner, nid, user, added) VALUES (" . sqlesc($text) . ", $uid, ".$id.", ".$CURUSER['id'].", ".sqlesc($dt).")") or die("Неизвестная ошибка!");
		sql_query("UPDATE LOW_PRIORITY notes SET comments = comments + 1 WHERE uid =".$uid." AND id = ".$id);
		$show = true;
    }

    elseif ($act = "delete")
    {
        $id = (int)$_POST['id'];
        $res = sql_query("SELECT user, owner, nid FROM noteswall WHERE id = $id") or sqlerr(__FILE__,__LINE__);
        $row = mysql_fetch_array($res);
		if(empty($row)) die("Не выбрано сообщение!");
            if ($CURUSER['id'] != $row['owner'] && $CURUSER['id'] != $row['user'] && get_user_class() < UC_MODERATOR)
                die("У вас нет прав.");
		$kid = $id;
		$id = $row['nid']; $uid = $row['owner'];
        sql_query("DELETE FROM noteswall WHERE id = $kid") or sqlerr(__FILE__,__LINE__);
		sql_query("UPDATE LOW_PRIORITY notes SET comments = comments - 1 WHERE uid =".$uid." AND id = ".$id);
        $show = true;
    }

    else
       die("Прямой доступ закрыт");

    if (isset($show))
    {	
        $count = get_row_count("noteswall", "WHERE `owner` = $uid AND `nid` = $id");
        $limited = 25;
        list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "note".$uid."-".$id.",", array(lastpagedefault => 1));
		$res = sql_query("SELECT w.*, u.username, u.class, u.avatar, u.gender FROM noteswall AS w LEFT JOIN users AS u ON u.id = w.user WHERE w.owner = $uid AND w.nid = $id ORDER BY w.added $limit") or sqlerr(__FILE__,__LINE__);
		if (mysql_num_rows($res) < 1)
            print("<p>Нет записей.</p>\n");
        else
        {
            print("<table class=\"inlay\" width=\"100%\">\n");
            while ($row = mysql_fetch_array($res))
            {
                print("<tr class=\"zebra\" valign=\"top\">
                <td width=\"50\"><img src=\"" . ($row['avatar'] ? $DEFAULTBASEURL.'/avatars/small/'.$row['avatar'] : "pic/default_avatar.gif") . "\" style=\"border:1px solid #999;padding:5px;width:50px;\" title=\"\" alt=\"\" /></td>
                <td>
                <div style=\"float:left;\"><a href=\"id" . $row['user'] . "\">" . get_user_class_color($row['class'], $row['username']) . "</a></div>
                <div style=\"float:right;\"><font size=\"1\" color=\"#C0C0C0\">" . nicetime($row['added'], true) . "&nbsp;" . (($CURUSER['id'] == $row['owner'] || $CURUSER['id'] == $row['user']  || get_user_class() >= UC_MODERATOR) ? "<a href=\"javascript:void(0);\" onclick=\"wall_del('" . $row['id'] . "', '" . $row['owner'] . "');\"><img src=\"pic/warned2.gif\" border=\"0\" /></a>" : "") . "</font></div><br />" . format_comment($row['text']) . "</td>
                </tr>\n");
                print("<tr><td></td></tr>\n");
            }
            print("</table>\n");
            print("<table border=\"0\">\n");
            print("<tr><td style=\"border:none;\">");
            print($pagertop);
            print("</td></tr>");
            print("</table>\n");
        }
    }
    else
        die("Прямой доступ закрыт");
}
else
    die("Прямой доступ закрыт");

?>