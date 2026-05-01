<?php
if (!defined("ADMIN_FILE")) die("Illegal File Access");

function addtheme($name, $action, $id) {
	global $admin_file;

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($action == "add") {
            $name = sqlesc($name);
			sql_query("INSERT INTO stylesheets (`name`, `uri`) VALUES($name, $name)") or sqlerr(__FILE__,__LINE__);
            stdmsg("Успещно", "Тема успешно добавлена");
            header("Refresh: 1; url=".$admin_file.".php?op=addtheme");
            }
            if ($action == "delete") {
            $id = sqlesc($id);
            sql_query("DELETE FROM stylesheets WHERE id=$id") or sqlerr(__FILE__,__LINE__);
             stdmsg("Успешно", "Тема удалена успешно");
            header("Refresh: 1; url=".$admin_file.".php?op=addtheme");
            }
    }
    else {
      echo "<form method=\"post\" action=\"".$admin_file.".php?op=addtheme\">"
		."<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\" width=\"50%\">"
		."<tr><td class=\"colhead\" colspan=\"2\">Добавить тему</td></tr>"
		."<tr>"
		."<td width=10%><b>Название</b></td>"
		."<td><input name=\"name\" type=\"text\" style=\"width:100%\"></td>"
		."</tr>"
		."<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"isub\" value=\"Добавить\"></td></tr>"
		."</table>"
		."<input type=\"hidden\" name=\"op\" value=\"addtheme\" />"
        ."<input type=\"hidden\" name=\"action\" value=\"add\" />"
		."</form><h3>Существующие темы</h3>";
      echo '<table width="50%"><tr><td class=colhead align=center>ID</td><td class=colhead align=center>Название</td><td class=colhead align=center>URI</td><td class=colhead align=center>Удалить</td></tr>';
      $res = sql_query("SELECT * FROM stylesheets") or sqlerr(__FILE__,__LINE__);
      while ($row = mysql_fetch_array($res))
        echo '<tr><td align=center>'.$row['id'].'</td><td align=center>'.$row['name'].'</td><td align=center>'.$row['uri'].'</td><td align=center><form method="post" action="'.$admin_file.'.php?op=addtheme"><input type="submit" value="Удалить"><input type="hidden" name="op" value="addtheme"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="'.$row['id'].'"></td></tr>';
      echo '</table>';
    }
}
switch ($op) {
	case "addtheme":
	addtheme($name, $action, $id);
	break;
}

?>