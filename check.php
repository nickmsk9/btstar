<?

require_once("include/bittorrent.php");

if (!mkglobal("id"))
    die();

$id = (int)$id;
if (!$id)
    die();

dbconn();

loggedinorreturn();

$updateset = array();

if(get_user_class() >= UC_MODERATOR)

$updateset[] = "moderated = 'yes'";
$updateset[] = "moderatedby = ".sqlesc($CURUSER["id"]);
$updateset[] = "moderatorname = ".sqlesc($CURUSER["username"]);

sql_query("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $id");


$returl = "details.php?id=$id";
if (isset($_POST["returnto"]))
    $returl .= "&returnto=" . urlencode($_POST["returnto"]);
header("Refresh: 0; url=$returl");
?> 