<?
require_once("include/bittorrent.php");
dbconn(false);

loggedinorreturn();
parked();

stdhead("Загрузить Фильм на TorretSide" , all);
begin_frame('Загрузить Фильм на TorrentSide');
if (get_user_class() < UC_USER)
{
  stdmsg($tracker_lang['error'], $tracker_lang['upget']);
  stdfoot();
  exit;
}

if (strlen($CURUSER['passkey']) != 32) {
$CURUSER['passkey'] = md5($CURUSER['username'].get_date_time().$CURUSER['passhash']);
sql_query("UPDATE users SET passkey='$CURUSER[passkey]' WHERE id=$CURUSER[id]");
}
?>
<div align=center>
<p><span style="color: green; font-weight: bold;">После загрузки торрента, вам нужно будет скачать торрент и поставить качаться в папку где лежат оригиналы файлов.</span></p>
<form id=upload name="upload" enctype="multipart/form-data" action="takeuploadfilm.php" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="<?=$max_torrent_size?>" />
<table border="1" cellspacing="0" cellpadding="5">
<tr><td class="colhead" colspan="2"><?=$tracker_lang['upload_torrent'];?></td></tr>
<?
tr($tracker_lang['torrent_file'], "<input type=file name=tfile size=80>\n", 1);
tr($tracker_lang['poster'], "<input type=file name=image0 size=80>\n", 1);
tr("Название", "<input type=\"text\" name=\"name\" size=\"80\" /><br />(Например - <b>Матрица</b>)\n", 1);
tr("Оригинальное название", "<input type=\"text\" name=\"origname\" size=\"80\" /><br />(Например - <b>Matrix</b>)\n", 1);  
tr("Год выхода", "<input type=\"text\" name=\"year\" size=\"4\" />\n", 1);
tr("Жанр", "<input type=\"text\" name=\"janr\" size=\"40\" />\n", 1);
tr("Режиссер", "<input type=\"text\" name=\"director\" size=\"40\" />\n", 1);
tr("В ролях", "<input type=\"text\" name=\"roles\" size=\"100\" />\n", 1);

print("</td></tr>\n");
print("<tr><td class=rowhead style='padding: 10px'>О фильме:</td><td>");
textbbcode("upload","descr","",0);  
print("</td></tr>\n");
tr("Кем выпущено", "<input type=\"text\" name=\"publisher\" size=\"40\" />\n", 1);
tr("Продолжительность", "<input type=\"text\" name=\"time\" size=\"40\" />\n", 1);
$perevod = array ('Любительский (Одноголосный)', 'Любительский (Многоголосный)', 'Любительский (Гоблин)', 'Профессиональный (Одноголосный)', 'Профессиональный (Многоголосный)', 'Профессиональный (Дублированный)', 'Отсутствует', 'Не требуется');
$pr = "<select name=\"perevod\">\n<option value=\"0\">(Выбрать)</option>\n";
        while (list($key, $val) = each($perevod)) {
                $pr .= "<option value=\"$val\">$val</option>\n";
                }
        $pr .= "</select>\n";
$pr = "$pr</td></tr>\n";
tr("Перевод", $pr, 1);

$kach = array ('DVDRip', 'DVD5', 'DVD9','HDTV', 'TVRip', 'SATRip', 'TeleCine', 'TeleSync', 'CAMRip', 'VHSRip', 'DVDScreener', 'BDRip');
$k = "<select name=\"kachestvo\">\n<option value=\"0\">(Выбрать)</option>\n";
        while (list($key, $val) = each($kach)) {
                $k .= "<option value=\"$val\">$val</option>\n";
                }
        $k .= "</select>\n";
$k = "$k</td></tr>\n";
tr("Качество", $k,1);

$format = array ('AVI', 'DVD Video', 'OGM', 'MKV', 'WMV', 'MPEG');
$fr = "<select name=\"format\">\n<option value=\"0\">(Выбрать)</option>\n";
        while (list($key, $val) = each($format)) {
                $fr .= "<option value=\"$val\">$val</option>\n";
                }
        $fr .= "</select>\n";
$fr = "$fr</td></tr>\n";
tr("Формат", $fr, 1);


tr("Видео", "Разрешение: <input type=\"text\" name=\"resolution\" size=\"9\" /> Кодек: <input type=\"text\" name=\"videocodec\" size=\"6\" /> Битрейт: <input type=\"text\" name=\"videobitrate\" size=\"6\" />\n", 1);
tr("Аудио", "Кодек: <input type=\"text\" name=\"audiocodec\" size=\"6\" /> Битрейт: <input type=\"text\" name=\"audiobitrate\" size=\"6\" />\n", 1);  

$s = "<select name=\"type\">\n<option value=\"0\">(".$tracker_lang['choose'].")</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$s .= "</select>\n";
tr($tracker_lang['type'], $s, 1);

if (get_user_class() >= UC_USER)
$prc .= "<select name=\"free\">";
for ($i = 0; $i <= 10; ++$i)
{
$selected = ($row['free'] == $i*10) ? " selected=\"selected\"" : "";
$prc .= "<option value=".$i."0".$selected.">".$i."0</option>";
}
$prc .= "</select> процентов"; 
tr("Скидка ", $prc, 1);  

if (get_user_class() >= UC_ADMINISTRATOR)
    tr("Важный", "<input type=\"checkbox\" name=\"sticky\" value=\"yes\">Прикрепить этот торрент (всегда наверху)", 1);

?>
<tr><td align="center" colspan="2"><input type="submit" class=btn value="<?=$tracker_lang['upload'];?>" />
<input type="button" value="Предпросмотр" onClick="javascript:ajaxpreview('descr');" >
<script language="javascript" type="text/javascript" src="js/preview.js"></script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<div id="loading-layer" style="display:none;font-family: Verdana;font-size: 11px;width:200px;height:50px;background:#FFF;padding:10px;text-align:center;border:1px solid #000">
     <div style="font-weight:bold" id="loading-layer-text">Загрузка. Пожалуйста, подождите...</div><br />
     <img src="pic/loading.gif" border="0" />
</div>
<br><br><div id="preview"></div>
</td></tr>
</table>
</form>
<?
end_frame();
?>
