<?php
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();
header("Content-Type: text/html; charset=Windows-1251");

if ($_POST["do"] == "shout") {
    $shout = convert_text(decode_unicode_url($_POST["shout"]));
    if ($shout == "/prune" && get_user_class() >= UC_ADMINISTRATOR) {
        sql_query("TRUNCATE TABLE shoutbox");
        die("Сообшений нет");
    }
	elseif(ereg("^/ban",$shout) && get_user_class() >= UC_ADMINISTRATOR) {
		preg_replace("#^/ban ([0-9\.]+)#eis", "sql_query(\"INSERT INTO bans (`added`,`addedby`,`comment`,`first`,`last`) VALUES (NOW(),".$CURUSER['id'].", \".sqlesc('Banned from chat, IP: \\1').\", INET_ATON(\".sqlesc('\\1').\"), INET_ATON(\".sqlesc('\\1').\"))\");",$shout);
		die('IP забанен');
	}
	elseif($shout == "thetime") {
	print("1");
	}
    $sender = $CURUSER["id"];
    if (!empty($shout)) {
        $shout = preg_replace("/\/me /", $CURUSER["username"]." ", $shout);
$datee = time();
        sql_query("INSERT INTO shoutbox (date,  text, userid) VALUES (".implode(", ", array_map("sqlesc", array($datee, $shout, $sender))).")") or sqlerr(__FILE__,__LINE__);
    } else
        print("<script>alert('Введи сообщение');</script>");
} elseif ($_POST["do"] == "delete" && get_user_class() >= UC_ADMINISTRATOR && is_valid_id($_POST["id"])) {
    $id = $_POST["id"];
    sql_query("DELETE FROM shoutbox WHERE id = $id") or sqlerr(__FILE__,__LINE__);
}

$res = sql_query("SELECT shoutbox.*, users.username, users.warned, users.id as uid, users.class, users.firstname FROM shoutbox INNER JOIN users ON shoutbox.userid = users.id ORDER BY id DESC LIMIT 35") or sqlerr(__FILE__,__LINE__);
		$botid = 482;             //Ид пользователя бота которого вы создали
        $botclass = 1;            //Класс вашего пользователя бота
        $botname = "Говорун";    //Имя вашего бота

if (mysql_num_rows($res) == 0)
    die("Сообшений нет");
print("\n");
while ($arr = mysql_fetch_array($res)) {


if ($arr["warned"] == "yes")
$warn = "<img src=\"pic/warned.gif\" alt=\"Warned\"/>";
else
$warn = "";
$username = $arr["username"];

$arr["text"] = format_comment($arr["text"]);

$arr["text"] = str_replace($CURUSER['firstname'].' '.$CURUSER['username'],"<font color=#000000></font><b style='color: green; background: #FFFFFF;'>".$CURUSER['firstname'].' '.$CURUSER['username']."</b><font color=#000000></font>",$arr["text"]);

$arr["text"] = preg_replace("/\[((\s|.)+?)\]/", "<font color=#000000></font><b style='color: black;'>[\\1]</b><font color=#000000></font>", $arr["text"]);



if (strpos($arr["text"], "privat($CURUSER[username])") !== false) {

$variabila = "privat($CURUSER[username])";
$nume = substr($variabila, 7);
$nume = substr($nume, 0, strlen($nume)-1);
$id = $arr['id'];

if (($CURUSER["username"] == $nume) || ($CURUSER["id"] == "".$arr["userid"]."")) {

$arr["text"] = str_replace("privat($CURUSER[username])","<b style='color: orange;'>".$CURUSER[username]."</b>",$arr["text"]);



print("<table width=\"100%\" border=\"0\">");
print("<tr class=\"zebra\"><td style=\"border: none\"><span class='date'>[".strftime("%H:%M:%S",$arr["date"])."]</span>" . (get_user_class() >= UC_MODERATOR ? "<span onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer; color: red; font-weight: bold; text-decoration: underline\"><img src=\"pic/warned2.gif\" style=\"border: 0px;\" /></span>" : "") . "
<a target=_blank href=pmto-".$arr['userid']." title=\"Отправить ЛС\"><img src=\"pic/pn_inbox.gif\" border=\"0\"></a> <a href=id".$arr["uid"]." target='_blank'><img src=\"pic/info/guest.gif\"  border=0  title=\"Посмотреть профиль\"></a> <a href=\"id".$arr["uid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$username."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($arr["class"], $arr['firstname'].' '.$arr["username"]) . "</a>$warn: ".($arr["text"])."</td></tr></table>\n");
}
} else
if ((($CURUSER["id"] == "".$arr["userid"]."") OR (get_user_class() >= UC_MODERATOR)) AND (get_user_class() >= $arr["class"]) AND (strpos($arr["text"], "privat(") !== false)) {

$arr["text"] = preg_replace("/privat\(([^()<>\s]+?)\)/i","<b style='color: #orange; background: #FFFFFF;'>{\\1}</b>", $arr["text"]);

print("<table width=\"100%\" border=\"0\">");
print("<tr class=\"zebra\"><td style=\"border: none\">[".strftime("%H:%M:%S",$arr["date"])."]</span>" . (get_user_class() >= UC_MODERATOR ? "<span onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer; color: red; font-weight: bold; text-decoration: underline\"><img src=\"pic/warned2.gif\" style=\"border: 0px;\" /></span>" : "") . "
    <a target=_blank href=pmto-".$arr['userid']." title=\"Отправить ЛС\"><img src=\"pic/pn_inbox.gif\" border=\"0\"></a> <a href=id".$arr["uid"]." target='_blank'><img src=\"pic/info/guest.gif\"  border=0  title=\"Посмотреть профиль\"></a> <a href=\"id".$arr["uid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$arr['firstname'].' '.$username."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($arr["class"], $arr['firstname'].' '.$arr["username"]) . "</a>$warn: ".($arr["text"])."</td></tr></table>\n");
} elseif (strpos($arr["text"], "privat(") !== false) {
} else {

print("<table width=\"100%\" border=\"0\">");
print("<tr class=\"zebra\"><td style=\"border: none\"><span class='date'>[".strftime("%H:%M:%S",$arr["date"])."]</span>" . (get_user_class() >= UC_MODERATOR ? "<span onclick=\"deleteShout($arr[id]);\" style=\"cursor: pointer; color: red; font-weight: bold; text-decoration: underline\"><img src=\"pic/warned2.gif\" style=\"border: 0px;\" /></span>" : "") . "
<a target=_blank href=pmto-".$arr['userid']." title=\"Отправить ЛС\"><img src=\"pic/pn_inbox.gif\" border=\"0\"></a> <a href=id".$arr["uid"]." target='_blank'><img src=\"pic/info/guest.gif\"  border=0  title=\"Посмотреть профиль\"></a> <a href=\"id".$arr["uid"]."\" onClick=\"parent.document.shoutform.shout.focus();parent.document.shoutform.shout.value='[b]".$arr['firstname'].' '.$username."[/b]: '+parent.document.shoutform.shout.value;return false;\">".get_user_class_color($arr["class"], $arr['firstname'].' '.$arr["username"]) . "</a>$warn: ".($arr["text"])."</td></tr>\n");
print("</table>");
}
}

 	if ((strpos($shout,'[b]Говорун[/b]') !== false)&&($shout!=="[b]Говорун[/b]:")) {
 		
 			
 		$bot="[b]Говорун[/b]:";
//////////////////////////
if ((strpos($shout,'последний торрент') !== false)&& get_user_class() >= UC_MODERATOR){

$res = mysql_query("SELECT name,owner,added,(SELECT username FROM users WHERE id=torrents.owner) AS classusername  FROM torrents WHERE  banned = 'no' ORDER BY added DESC LIMIT 1") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0 )
while ($arr = mysql_fetch_assoc($res)) {
{
$owned="[b]Последний торрент[/b]: ".format_comment($arr["name"])." был залит ".$arr["classusername"]." в ".$arr["added"];
}}}
else
{
$owned="извините, только для администрации сайта.";
}
/////////////////////////
if (strpos($shout,'моя подпись') !== false){
$userid=$CURUSER["id"];
$res = mysql_query("SELECT info FROM users WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0 )
while ($arr = mysql_fetch_assoc($res)) {
{ 
	if ($arr["info"]==""){
$info="ваша подпись пуста";}
else
$info="[i]Ваша подпись ниже[/i]:\n".htmlspecialchars_uni($arr["info"]);
}}
}
/////////////////////////
if (strpos($shout,'твоя подпись') !== false){
$userid=$CURUSER["id"];
$res = mysql_query("SELECT info FROM users WHERE id=92") or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) > 0 )
while ($arr = mysql_fetch_assoc($res)) {
{ 
	if ($arr["info"]==""){
$vikainfo="моя подпись пуста";}
else
$vikainfo="[i]Моя подпись ниже[/i]:\n".htmlspecialchars_uni($arr["info"]);
}}
}
/////////////////////////
 		 if ((strpos($shout,'мои сообщения') !== false) && get_user_class() >= UC_MODERATOR){
$res1 = sql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND location=1 AND unread='yes'") or print(mysql_error());
$arr1 = mysql_fetch_row($res1);
$unread = $arr1[0];
$newmessage1 = $unread . " нов" . ($unread > 1 ? "ых" : "ое"); 
$newmessage2 = " сообщен" . ($unread > 1 ? "ий" : "ие"); 
$newmessage = "$newmessage1 $newmessage2"; 

    if ($unread)
    {

$unread2="[b][url=$DEFAULTBASEURL/message.php] у вас $newmessage [/url][/b]";

    }
    else 
    
    $unread2="[b]у тебя нет новых сообщений[/b]";
    
	
	}
	else
	  {
    $unread2="извини, но эта функция только для администрации";
	}
	;
    
                  switch ($shout)
                  {
                  	
                 case (stripos($shout,'Бот')!==FALSE):
                     $a="".$CURUSER['username']." :unsure: с чего взял ?";          
                     $b="".$CURUSER['username']." обижаешь";          
                    break;
                            
					case $shout == "$bot бот";
					case $shout == "$bot Бот";
					case (stripos($shout,'пупсик')!==FALSE):
					case (stripos($shout,'бот')!==FALSE):
                    $a="".$CURUSER['username']." сам такой, гад :P";          
                    $b="".$CURUSER['username']." посмотри на себя, малышь";          
                    break;
             
                  case $shout == "$bot да";
				  case $shout == "$bot Да";
                  $a="".$CURUSER['username']." неа :P";          
                  $b="".$CURUSER['username']." нет конечно";          
                    break;
                   
                  
				   case (stripos($shout,'Кто?')!==FALSE):
					case (stripos($shout,'Кто???')!==FALSE):
                    $a="".$CURUSER['username']." как кто ТЫ!!!";          
                   $b="".$CURUSER['username']." ТЫ!!!";          
                    break;
                    
                    
                    
				   case $shout == "$bot ip";
			       $a="".$CURUSER['username']." ваш ip равен ".$CURUSER['ip']."";          
                   $b="".$CURUSER['username']." ваш ip равен ".$CURUSER['ip']."";          
                    break;
                   
                     case $shout == "$bot браузер";
			       $a="".$CURUSER['username']." ваш браузер равен ".getenv("HTTP_USER_AGENT")."";          
                   $b="".$CURUSER['username']." ваш браузер равен ".getenv("HTTP_USER_AGENT")."";          
                    break;
                    
                   
                   case $shout == (stripos($shout,'мои сообщения') !== false):
			       $a="".$CURUSER['username']." $unread2";          
                   $b="".$CURUSER['username']." $unread2";          
                    break;
                    
                      case $shout == (stripos($shout,'последний торрент') !== false):
			       $a="".$CURUSER['username']." $owned";          
                   $b="".$CURUSER['username']." $owned";          
                    break;
                   
                       case $shout == (stripos($shout,'моя подпись') !== false):
			       $a="".$CURUSER['username']." $info";          
                   $b="".$CURUSER['username']." $info";          
                    break;
                   
                  
                      case $shout == (stripos($shout,'твоя подпись') !== false):
			       $a="".$CURUSER['username']." $vikainfo";          
                   $b="".$CURUSER['username']." $vikainfo";          
                    break;
                   
                    case $shout == "$bot нет";
				    case $shout == "$bot Нет";
                    $a="".$CURUSER['username']." да :D";          
                    $b="".$CURUSER['username']." ууу да.";          
                    break;
                                      
                   
                    case $shout == (stripos($shout,'чего?') !== false):
                    $a="".$CURUSER['username']." сего :D";          
                    $b="".$CURUSER['username']."...";          
                    break;
                   
                      case $shout == (stripos($shout,'интересно') !== false):
                       case $shout == (stripos($shout,'интерестно') !== false):
                    $a="".$CURUSER['username']." Интересоваться в библиотеке будешь!";          
                    $b="".$CURUSER['username']." иди в библиотеку, там интересуйся :P";          
                    break;
                    
                    
                   
                    case $shout == (stripos($shout,'....') !== false):
                    $a="".$CURUSER['username']." ... взаимно";          
                     $b="".$CURUSER['username']." и что за тишина в твоих пробеллах ?.";          
                    break;
                   
                                         
                    case $shout == (stripos($shout,'в обиде') !== false):
                    $a="".$CURUSER['username']." мне пох ;-)";          
                     $b="".$CURUSER['username']." хихи.";          
                    break;
                    
                    
                    case $shout == (stripos($shout,'эй!') !== false):
                    $a="".$CURUSER['username']." гей что ли?";          
                     $b="".$CURUSER['username']." тебе лишь бы погееть.";          
                    break;
                    
                    
                    case $shout == (stripos($shout,'столько же') !== false):
                    $a="".$CURUSER['username']." прикольненько";          
                     $b="".$CURUSER['username']." ясно.";          
                    break;
                    
                    case $shout == (stripos($shout,'что делать ?') !== false):
                    $a="".$CURUSER['username']." займись делом каким то! а не мною.";          
                     $b="".$CURUSER['username']." сидеть.";          
                    break;
                    
                        
                    case $shout == (stripos($shout,'учишься?') !== false):
                    case $shout == (stripos($shout,'работаешь?') !== false):
                    $a="".$CURUSER['username']." я всего лишь учусь быть хорошим ботом на трекере ;-)";                        $b="".$CURUSER['username']." не имеет значение";          
                    break;
                    
                     case $shout == (stripos($shout,'знаешь меня?') !== false):
                    case $shout == (stripos($shout,'слышала обо мне?') !== false):
                    $a="".$CURUSER['username']." неа, а кто ты ?";                       
					$b="".$CURUSER['username']." в нете?.";          
                    break;
                    
                    case $shout == (stripos($shout,'тебя зовут') !== false):
                    case $shout == (stripos($shout,'тебя звать') !== false):
                    case $shout == (stripos($shout,'твое имя') !== false):
                    
                    $a="".$CURUSER['username']." Говорун, меня создал Иван Рут";          
                    $b="".$CURUSER['username']." Говорун я";          
                    break;
                    
                    
                     case $shout == (stripos($shout,'Привет') !== false):
                     case $shout == (stripos($shout,'Даров') !== false):
                     case $shout == (stripos($shout,'прива') !== false):
                     case $shout == (stripos($shout,'Приветик') !== false):
           
                     $a="".$CURUSER['username']." Приветик.";          
                     $b="".$CURUSER['username']." Привет :)";          
                    break;
                   
                   case $shout == (stripos($shout,'на хуй') !== false):
                   $a="".$CURUSER['username']." заткнись пидарас я тебя найду когда из программного кода вылезу. Гнида сраная.";
                   $b="".$CURUSER['username']." пидрила гнойная вылезу из программного кода в рот тебе навалю."; 
                   break;
                   
                   case $shout == (stripos($shout,'в пизду') !== false):
                   $a="".$CURUSER['username']." в твою то прорву. Кстати помни я уже доложил все создателю по имени [b]catarr[/b].";
                   $b="".$CURUSER['username']." в твой то волосатый пирожок ? фууу."; 
                   break;
                   
                                   
              default: 
            $a="";              
                  }
                 

 	 if (!$a==""){

 	$newshout = array($a,$b);
shuffle($newshout);

$newshout = $newshout[0];
 $date = time()+15; 
  	  
sql_query("INSERT INTO shoutbox (id, userid, class, username, date, text, orig_text) VALUES ('id='," .  sqlesc($botid) . ", " .  sqlesc($botclass) . ", "  .  sqlesc($botname) . ", ".$date.", " . sqlesc($newshout) . ", " . sqlesc($newshout) . ")") or sqlerr(__FILE__, __LINE__); 
}

 }

?>