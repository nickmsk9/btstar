<?php
require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	header("Content-Type: text/html; charset=".$tracker_lang['language_charset']);
	if (empty($_POST["id"])) {
		stdmsg($tracker_lang['error'], "Вы не выбрали тип бонуса!");
		die();
	}
	$id = (int) $_POST["id"];
	if (!is_valid_id($id)) {
		stdmsg($tracker_lang['error'], $tracker_lang['access_denied']);
		die();
	}
	$res = sql_query("SELECT * FROM bonus WHERE id = $id") or sqlerr(__FILE__,__LINE__);
	$arr = mysql_fetch_array($res);
	$points = $arr["points"];
	$type = $arr["type"];
	if ($CURUSER["bonus"] < $points) {
		stdmsg($tracker_lang['error'], "У вас недостаточно бонусов!");
		die();
	}
	switch ($type) {
		case "traffic":
			$traffic = $arr["quanity"];
			if (!sql_query("UPDATE users SET bonus = bonus - $points, uploaded = uploaded + $traffic WHERE id = ".sqlesc($CURUSER["id"]))) {
				stdmsg($tracker_lang['error'], "Не могу обновить бонус!");
				die();
			}
			stdmsg($tracker_lang['success'], "Бонус обменян на траффик!");
		break;
		case "invite":
			$invites = $arr["quanity"];
			if (!sql_query("UPDATE users SET bonus = bonus - $points, invites = invites + $invites WHERE id = ".sqlesc($CURUSER["id"]))) {
				stdmsg($tracker_lang['error'], "Не могу обновить бонус!");
				die();
			}
			stdmsg($tracker_lang['success'], "Бонус обменян на приглашения!");
		break;
		case "clearratio":
            if ($CURUSER["uploaded"]/$CURUSER["downloaded"] >= 1){
            if (!sql_query("UPDATE users SET bonus = bonus - $points, downloaded = uploaded WHERE id = ".sqlesc($CURUSER["id"])))   {
            stdmsg($tracker_lang['error'], "Не могу обновить бонус!");
            die();
            }
            stdmsg($tracker_lang['success'], "Ваш рейтинг приравнен единице!");
            }
        break;
		case 'vip':
			if($CURUSER['class']!=UC_USER&&$CURUSER['class']!=UC_POWER_USER)
			{
				stdmsg($tracker_lang['error'],'VIP статус могут получать только обычные пользователи!'); die(); }
			if(sql_query("UPDATE users SET class = ".UC_VIP.", vip_to = UNIX_TIMESTAMP(IF(vip_to, vip_to, NOW())) + ".($arr["quanity"]*86400)." WHERE id = ".$CURUSER['id']))
				stdmsg('Ваш статус VIP');
			
		break;
		default:
			stdmsg($tracker_lang['error'], "Unknown bonus type!");
	}
} else {
stdhead($tracker_lang['my_bonus']);
?>
<script type="text/javascript">
function send(){

    var frm = document.mybonus;
	var bonus_type = '';

    for (var i=0;i < frm.elements.length;i++) {
        var elmnt = frm.elements[i];
        if (elmnt.type=='radio') {
            if(elmnt.checked == true){ bonus_type = elmnt.value; break;}
        }
    }

      (function($){
   $("#ajax").empty();
   $("#ajax").append('<div align="center"><img src="pic/loading.gif" border="0"/></div>');
    $.post("mybonus.php", { ajax: "", id: bonus_type }, function(data){
   $("#ajax").empty();
   $("#ajax").append(data);
});
})(jQuery);

}
</script>

<?php
begin_frame("Обмен бонусов");
?>
<div id="ajax">
<table class="embedded" width="100%" border="0" cellspacing="0" cellpadding="3">
<?php
	$my_points = $CURUSER["bonus"];
	$res = sql_query("SELECT * FROM bonus") or sqlerr(__FILE__,__LINE__);
	while ($arr = mysql_fetch_assoc($res)) {
		$id = $arr["id"];
		$bonus = $arr["name"];
		$points = $arr["points"];
		$descr = $arr["description"];
		$output .= "<tr><td><b>$bonus</b><br />$descr</td><td><center>$points</center></td><td><center><input type=\"radio\" name=\"bonus_id\" value=\"$id\" /></center></td></tr>\n";
	}
?>
	<tr><td class="colhead">Тип бонуса</td><td class="colhead">Стоимость</td><td class="colhead">Выбор</td></tr>
	<form action="mybonus.php" name="mybonus" method="post">
<?=$output;?>
		<tr><td colspan="3"><input type="submit" onClick="send(); return false;" value="Обменять" /></td></tr>
	</form>
	<tr><td class="success" colspan="3">Мой баланс (<?=$CURUSER["bonus"];?> бонус(ов) в наличии / <?=$points_per_hour;?> бонуса в час)</td></tr>
</table>
</div>
<?php
end_frame();
stdfoot();
}
?>