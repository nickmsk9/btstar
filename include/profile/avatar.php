<?php
print("<table width=\"100%\">");
print("<tr><td width=\"20%\" style=\"border: none;\" align=\"center\">");
if($CURUSER['avatar'])
print("<img src=\"".$DEFAULTBASEURL."/avatars/".$CURUSER['avatar']."\" title=\"Аватар\" alt=\"Загрузка...\">");
else
print("Нет<br>фотографии");
print("</td><td width=\"10px\" style=\"border: none;\">&nbsp;</td><td valign=\"top\" style=\"border:none;\">");
print("<h4 style=\"font-size: 15px;\">Загрузка фотографии</h4>");
print("Вы можете загрузить сюда фотографию формата JPG, GIF или PNG.<br>");
print("<form method=\"post\" enctype=\"multipart/form-data\" action=\"takeprofedit.php?t=avatar\"><input type=\"file\" size=\"80\" name=\"avatar\"><input type=\"submit\" value=\"Загрузить\"><input type=\"submit\" name=\"delete\" value=\"Удалить\"></form><br>");
print("<font class=\"small\">Файлы размером более 5 MB не загрузятся. В случае возникновения проблем попробуйте загрузить фотографию меньшего размера.</font>");
print("</td></tr>");
?>