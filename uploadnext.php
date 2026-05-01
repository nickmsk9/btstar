<?
require_once("include/bittorrent.php");

dbconn(false);

loggedinorreturn();
parked();

stdhead($tracker_lang['upload_torrent']);

$type = $_POST["type"];

if (!isset($type))
{
  stdmsg($tracker_lang['error'], "Не выбрана категория");
  stdfoot();
  exit;
}

if (strlen($CURUSER['passkey']) != 32) {
$CURUSER['passkey'] = md5($CURUSER['username'].get_date_time().$CURUSER['passhash']);
sql_query("UPDATE users SET passkey='$CURUSER[passkey]' WHERE id=$CURUSER[id]");
}
begin_frame($tracker_lang['upload_torrent']);
?>
<script type="text/javascript" language="javascript" src="js/templates.js"></script>
<form name="upload" id="upload" enctype="multipart/form-data" action="takeupload.php" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="<?=$max_torrent_size?>" />
<table width="100%" border="1" cellspacing="0" cellpadding="5">
<?
tr($tracker_lang['torrent_file'], "<input type=file name=tfile size=80>\n", 1);
tr($tracker_lang['torrent_name'], "<input type=\"text\" name=\"name\" size=\"80\" />\n", 1);
tr($tracker_lang['poster'], "<input type=file name=image0 size=80>\n", 1);
print("<tr><td class=rowhead style='padding: 3px'>".$tracker_lang['description']."</td><td>");
textbbcode("upload","descr","");
print("</td></tr>\n");

?>
<script type="text/javascript" language="javascript">
var field = jQuery("form[@name=upload]").jQuery("textarea[@name=descr]");
</script>
    <style type="text/css" media="screen">
        code {font:99.9%/1.2 consolas,'courier new',monospace;}
        #from a {margin:2px 2px;font-weight:normal;}
        #tags {width:36em;}
        a.selected {background:#c00; color:#fff;}
        .addition {margint-top:2em; text-align:right;}
    </style>
    <script type="text/javascript" src="js/tagto.js"></script>
    <script type="text/javascript">
    var $ = jQuery.noConflict();
        (function($){
            $(document).ready(function(){
                $("#from").tagTo("#tags");
            });
        })(jQuery);
    </script>

<?
$s = '<input type="text" id="tags" name="tags">';
$s .= '<div id="from">';
$tags = taggenrelist($type);
if (!$tags)
$s .= "Нет тегов для данной категории. Вы можете добавить собственные.";
else
  {
   foreach ($tags as $row)
   $s .= "<a href='#'>" . htmlspecialchars($row["name"]) . "</a>\n";
  }
$s .= "</div>\n";
tr("Тэги", $s, 1);

///// Скидка раздачи
       if (get_user_class() >= UC_MODERATOR) {
        $prc .= "<b>Скачивание не будет учитыватся на </b><select name=\"free\">";
        for ($i = 0; $i <= 10; ++$i)
        {
        $selected = ($row['free'] == $i*10) ? " selected=\"selected\"" : "";
        $prc .= "<option value=".$i."0".$selected.">".$i."0</option>";
        }
        $prc .= "</select>%";
        tr("Скидка", $prc, 1);
		}
////// Скидка раздачи

	tr("Мульти-трекер", "<input type=\"checkbox\" name=\"multi\" value=\"1\">Разрешить раздовать на других трекерах", 1);

if (get_user_class() >= UC_ADMINISTRATOR)
    tr("Важный", "<input type=\"checkbox\" name=\"sticky\" value=\"yes\">Прикрепить этот торрент (всегда наверху)", 1);
?>	
<tr><td align="center" colspan="2"><input type="hidden" name="type" value="<?=$type?>"><input type="submit" class=btn value="<?=$tracker_lang['upload'];?>" />&nbsp;<input type="button" value="Предпросмотр" onClick="javascript:ajaxpreview('descr');" >
<script language="javascript" type="text/javascript" src="js/preview.js"></script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<div id="loading-layer" style="display:none;font-family: Verdana;font-size: 11px;width:200px;height:50px;background:#FFF;padding:10px;text-align:center;border:1px solid #000">
     <div style="font-weight:bold" id="loading-layer-text">Загрузка. Пожалуйста, подождите...</div><br />
     <img src="pic/loading.gif" border="0" />
</div>
<br><br><div id="preview"></div></td></tr>
</table>
</form>
<?
end_frame();
stdfoot();

?>