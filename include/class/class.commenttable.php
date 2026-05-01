<?
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');

function commenttable($rows, $redaktor = "comment") {
        global $CURUSER, $avatar_max_width;

        $count = 0;
        foreach ($rows as $row)        {
                            if ($row["downloaded"] > 0) {
                                    $ratio = $row['uploaded'] / $row['downloaded'];
                                    $ratio = number_format($ratio, 2);
                            } elseif ($row["uploaded"] > 0) {
                                    $ratio = "Inf.";
                            } else {
                                    $ratio = "---";
                            }
                             if (strtotime($row["last_access"]) > gmtime() - 600) {
                                     $online = "online";
                                     $online_text = "В сети";
                             } else {
                                     $online = "offline";
                                     $online_text = "Не в сети";
                             }
							 
							 if($row["gender"] == 1) {
							 $gender = "написал"; 
							 } else {
							 $gender = "написала";
							 }

           print("<table class=maibaugrand width=100% border=0 cellspacing=0 cellpadding=3>");
		   $avatar = ($CURUSER["avatars"] == "yes" ? $DEFAULTBASEURL.'/avatars/65/'.$row["avatar"] : "");
        if ($avatar && !$row["avatar"]){$avatar = "pic/default_avatar.gif"; }
           print("<tr><td valign='middle' align='left' width='65' style='border:none;'><img src=\"$avatar\" width=\"65\"></td><td valign='top' align='left' style='border:none;'><div style='border-top: 1px solid #516A88; border-bottom: 1px solid #DCDCDC;'>");
    if (isset($row["username"]))
                {
                        $title = $row["title"];
                        if ($title == ""){
                                $title = get_user_class_name($row["class"]);
                        }else{
                                $title = htmlspecialchars_uni($title);
                        }
                   print("<a name=comm". $row["id"]." href=userdetails.php?id=" . $row["user"] . " class=altlink_white><b>". get_user_class_color($row["class"], htmlspecialchars_uni($row["username"])) . "</b></a>&nbsp;".$gender."\n")
                       ."  ";

               } else {
                        print("<a name=\"comm" . $row["id"] . "\"><i>[Anonymous]</i></a>\n");
               }

         $text = format_comment($row["text"]);

        if ($row["editedby"]) {
               $text .= "<p><font size=1 class=small>Последний раз редактировалось <a href=userdetails.php?id=$row[editedby]><b>$row[editedbyname]</b></a> в $row[editedat]</font></p>\n";
         }
			    print("<br><font size=\"small\">".nicetime($row["added"], true)."</font>");
                print("</div><div style=\"margin-top:1px;\">$text</div>\n");
				print"<div style=\"margin-top:10px; float: left; border-bottom: 1px solid #DCDCDC;'>"
                .($CURUSER ? " <a href=\"".$redaktor.".php?action=quote&amp;cid=$row[id]\" class=\"altlink_white\">Цитировать </a>&nbsp;|&nbsp;" : "")
                .($row["user"] == $CURUSER["id"] || get_user_class() >= UC_MODERATOR ? " <a href=".$redaktor.".php?action=edit&amp;cid=$row[id] class=\"altlink_white\">Редактировать</a>&nbsp;|&nbsp;" : "")
                .(get_user_class() >= UC_MODERATOR ? " <a href=\"".$redaktor.".php?action=delete&amp;cid=$row[id]\" class=\"altlink_white\">Удалить</a>" : "")
                .($row["editedby"] && get_user_class() >= UC_MODERATOR ? " [<a href=\"".$redaktor.".php?action=vieworiginal&amp;cid=$row[id]\" class=\"altlink_white\">Оригинал</a>]" : "")

                ."</div>";
                print("</td></tr>\n");



                print("</table><br>");
  }

}
?>