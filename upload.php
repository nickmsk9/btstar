<?php

require_once("include/bittorrent.php");

dbconn(false);

loggedinorreturn();
parked();

stdhead($tracker_lang['upload_torrent']);

begin_frame("Выберите категорию раздачи");
?>
<div align=center>
<form name="upload" action="uploadnext.php" method="post">
<table border="1" cellspacing="0" cellpadding="5">
<?php

$s = "<select name=\"type\">\n<option value=\"0\">(".$tracker_lang['choose'].")</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$s .= "</select>\n";
echo $s;

?>
<input type="submit" class=btn value="Далее" /></table>
</form>
<?php
end_frame();
stdfoot();

?>