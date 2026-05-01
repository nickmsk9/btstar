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
        $to = (int)$_POST['to'];
        $text = base64_decode($_POST['text']);
        if(empty($CURUSER['id']) || empty($to) || empty($text))
            die("Прямой доступ закрыт");
        $dt = get_date_time();
        sql_query("INSERT INTO wall (text, owner, user, added) VALUES (" . sqlesc($text) . ", $to, " . sqlesc($CURUSER['id']) . ", " . sqlesc($dt) . ")") or sqlerr(__FILE__,__LINE__);
        $show = true;
    }

    elseif ($act = "delete")
    {
        $post = (int)$_POST['post'];
		$to = (int)$_POST['to'];
        if (get_user_class() < UC_MODERATOR)
        {
            $res = sql_query("SELECT user, owner FROM wall WHERE id = $post") or sqlerr(__FILE__,__LINE__);
            $row = mysql_fetch_array($res);
            if ($CURUSER['id'] != $row['owner'] && $CURUSER['id'] != $row['user'] && get_user_class() < UC_MODERATOR)
                die("У вас нет прав.");
        }
        sql_query("DELETE FROM wall WHERE id = $post") or sqlerr(__FILE__,__LINE__);
        $show = true;
    }

    else
       die("Прямой доступ закрыт");

    if (isset($show))
    {
        $count = get_row_count("wall", "WHERE owner = $to");
        $limited = 25;
        list($pagertop, $pagerbottom, $limit) = pager($limited, $count, "userdetails.php?id=$to&", array(lastpagedefault => 1));
        $res = sql_query("SELECT w.*, u.username, u.class, u.avatar FROM wall AS w LEFT JOIN users AS u ON u.id = w.user WHERE w.owner = $to ORDER BY w.added $limit") or sqlerr(__FILE__,__LINE__);
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