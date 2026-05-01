<?
//Mod created by catarr - http://bt-star.ru
  require_once("include/bittorrent.php");
  dbconn(false);

  loggedinorreturn();
header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);

if(isset($_GET['ajax']) && isset($_POST['msg'])) {
$realmsg = base64_decode($_POST['msg']);

$ret = "<span id=\"preview\" style=\"display: block;\"><fieldset id='preview' style='border: 2px solid gray;min-width:95%;display: block;'><legend> Предпросмотр <a href=\"#\" style=\"font-weight:normal\" onClick=\"javascript:this.style.display='none';document.getElementById('preview').innerHTML='';\">[свернуть]</a></legend><table class=\"bottom\" width=\"100%\"><tr><td style=\"border:none;\"><font color=\"black\">".format_comment($realmsg)."</font></td></tr></table></fieldset></span>";
die($ret);
}
  
?> 