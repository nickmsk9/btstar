<?php
require "include/bittorrent.php";

dbconn();
loggedinorreturn();

if (!mkglobal("id"))
stderr("Ошибка", "Введите ID");
$id = (int) $_GET["id"];
if (!$id){
stderr("Ошибка", "Введите ID");
die();
}

$act = $_GET['act'];

if($act == 'delete'){

if (get_user_class() <  UC_MODERATOR)
stderr("Woot!", "Fuck off.........");

$id = (int) $_POST['id'];

sql_query("DELETE FROM futurerls WHERE id=$id");
sql_query("DELETE FROM comments WHERE trailers=$id");
header("Refresh: 0; url=futurerls.php");
die();
}

if($act == 'takerelease'){

$downid = $_POST["download"];

sql_query("UPDATE futurerls SET download=$downid WHERE id=$id");

header("Refresh: 0; url=futurerls.php");

die();
}

if($act == 'take'){
stdhead();
begin_frame("Выполнение ожидаемого релиза");

print("<center><form action=\"futurerledit.php?act=takerelease&id=$id\" name=\"takerelease\" method=post>\n");
print("<b>Введите ID релиза:</b> <input type='text' name='download'  size='30' /> ");
print("<input type=\"hidden\" name=\"id\" value=\"$id\">\n"); 
print(" <input type=submit class=btn value='Выполнить!'>\n");
print("</form>\n");
end_frame();
stdfoot();
die();
}

$res = mysql_query("SELECT futurerls.id, futurerls.name, futurerls.userid, futurerls.trailer, futurerls.comments, futurerls.added, futurerls.realeasedate, futurerls.descr, users.username FROM futurerls LEFT JOIN users ON users.id=futurerls.userid WHERE futurerls.id = $id")or sqlerr();
$row = mysql_fetch_array($res);
if (!$row)	
stderr("Ошибка", "Неверный релиз");
$id = (int) $_GET["id"];
if (!$id)
die();
if ($CURUSER["id"] != $row["userid"] && get_user_class() < UC_POWER_USER){
stderr("Ошибка", "Вы не можете редактировать этот ожидаемый релиз.");
}
$where = "WHERE userid = " . $CURUSER["id"] . "";
$res2 = mysql_query("SELECT * FROM futurerls $where") or sqlerr();
$num2 = mysql_num_rows($res2);
stdhead("Редактирование ожилаемого релиза");
begin_frame("Редактирование ожилаемого релиза");
print("<center><form action=\"takefuturerledit.php\" name=\"nrg\" method=post>\n");
print("<input type=\"hidden\" name=\"id\" value=\"$id\">\n");
?>
<table class="embedded" width="550" border="1" cellspacing="0" cellpadding="5">
<tr><td class="rowhead">Название:</td><td align="left"><input type='text' name='name' value='<?=$row['name']?>' size='80' /></td></tr>
<tr><td class="rowhead">Дата выхода:</td><td align="left"><input type='text' name='realeasedate' value='<?=$row['realeasedate']?>' size='80' /></td></tr>
<tr><td class="rowhead">Постер:</td><td align="left"><input type='text' name='text' value='<?=$row['trailer']?>' size='80' /></td></tr>
<tr><td class="rowhead">Описание:</td><td align="left">
<?php
textbbcode("upload","descr",htmlspecialchars($row["descr"]), 0);
?>
</td></tr>
<?php
	$s = "<select name=\"type\">\n";

	$cats = genrelist();
	foreach ($cats as $subrow) {
		$s .= "<option value=\"" . $subrow["id"] . "\"";
		if ($subrow["id"] == $row["category"])
			$s .= " selected=\"selected\"";
		$s .= ">" . htmlspecialchars($subrow["name"]) . "</option>\n";
	}

	$s .= "</select>\n";
print("<tr><td class='rowhead'>Категория:</td><td align='left'> ".$s." </td></tr>");
print("<tr><td colspan='2'><input type=\"hidden\" name=\"id\" value=\"$id\">\n");
print("<input type=submit class=btn value='Изменить!'>\n");
print("\n");

print("<form method=\"post\" action=\"futurerledit.php?act=delete&id=".$id."\">\n");
print("<input type=\"hidden\" name=\"id\" value=\"$id\">\n"); 
print("<input type=submit value='Удалить!' class=btn></td></tr>\n");

end_frame();
stdfoot();
?>
