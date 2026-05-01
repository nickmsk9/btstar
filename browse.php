<?
require_once("include/bittorrent.php");

gzip();

dbconn(false);
parked();
$torrentsperpage = $CURUSER["torrentsperpage"];
if (!$torrentsperpage)
        $torrentsperpage = 25;

if (isset($_GET['ajax']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

  header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);

if ((!is_valid_id($_GET['page'])) && ((int) $_GET['page'] !=0)) stdmsg($tracker_lang["error"], $tracker_lang['invalid_id']);
$page = (int) $_GET["page"];

$count = (int) $_GET["count"];
$where = base64_decode($_GET["where"]);
$orderby = "ORDER BY torrents.sticky ASC, torrents.id DESC";

list($pagertop, $pagerbottom, $limit) = browsepager($torrentsperpage, $count, "browse.php?" . $addparam);
        $query = "SELECT torrents.id, torrents.tags, torrents.image1, torrents.moderated, torrents.moderatedby, torrents.category, torrents.leechers, torrents.seeders, torrents.free, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.sticky, torrents.owner, torrents.multitracker, torrents.f_peers, torrents.f_seeders," .
        "categories.name AS cat_name, categories.image AS cat_pic, users.username, users.class FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
        $res = sql_query($query) or die(mysql_error());

print("<table class=\"embedded\" cellspacing=\"0\" cellpadding=\"5\" width=\"100%\">");

torrenttable($res, "index");

print("<tr><td class=\"index\" colspan=\"12\">");
print($pagerbottom);
print("</td></tr></table>");
die();
}
$cats = genrelist();


$searchstr = unesc($_GET["search"]);
$cleansearchstr = htmlspecialchars($searchstr);
if (empty($cleansearchstr))
unset($cleansearchstr);

$descrstr = unesc($_GET["descr"]);
$cleandescrstr = htmlspecialchars($descrstr);
if (empty($cleandescrstr))
unset($cleandescrstr);

$tagstr = unesc($_GET["tag"]);
$cleantagstr = htmlspecialchars($tagstr);
if (empty($cleantagstr))
unset($cleantagstr);

// sorting by MarkoStamcar

if ((int)$_GET['sort'] && (int)$_GET['type']) {

$column = '';
$ascdesc = '';

switch($_GET['sort']) {
case '1': $column = "name"; break;
case '2': $column = "numfiles"; break;
case '3': $column = "comments"; break;
case '4': $column = "added"; break;
case '5': $column = "size"; break;
case '6': $column = "times_completed"; break;
case '7': $column = "seeders"; break;
case '8': $column = "leechers"; break;
case '9': $column = "owner"; break;
case '10': if (get_user_class() >= UC_MODERATOR) $column = "moderatedby"; break;
default: $column = "id"; break;
}

    switch($_GET['type']) {
  case 'asc': $ascdesc = "ASC"; $linkascdesc = "asc"; break;
  case 'desc': $ascdesc = "DESC"; $linkascdesc = "desc"; break;
  default: $ascdesc = "DESC"; $linkascdesc = "desc"; break;
    }


$orderby = "ORDER BY torrents." . $column . " " . $ascdesc;
$pagerlink = "sort=" . intval($_GET['sort']) . "&type=" . $linkascdesc . "&";

} else {

$orderby = "ORDER BY torrents.sticky ASC, torrents.id DESC";
$pagerlink = "";

}

$addparam = "";
$wherea = array();
//$wherea[] = "multitracker != 1";
$wherecatina = array();

if ($_GET["incldead"] == 1)
{
        $addparam .= "incldead=1&amp;";
        if (!isset($CURUSER) || get_user_class() < UC_ADMINISTRATOR)
                $wherea[] = "banned != 'yes'";
}
elseif ($_GET["incldead"] == 2)
{
        $addparam .= "incldead=2&amp;";
                $wherea[] = "visible = 'no'";
				$wherea[] = "(f_seeders+f_peers = 0 OR multitracker != 1)";
}
elseif ($_GET["incldead"] == 3)
{
        $addparam .= "incldead=3&amp;";
                $wherea[] = "free = 100";
               $wherea[] = "visible = 'yes'";
}
elseif ($_GET["incldead"] == 4)
{
        $addparam .= "incldead=4&amp;";
                $wherea[] = "seeders+f_seeders = 0";
                $wherea[] = "visible = 'yes'";
}
        else	{
                $wherea[] = "(f_seeders != 0 OR visible = 'yes')";
				//$wherea[] = "seeders+f_seeders != 0";
				}

$category = (int)$_GET["cat"];

$all = (int)$_GET["all"];

if (!$all)
        if (!$_GET && $CURUSER["notifs"])
        {
          $all = True;
          foreach ($cats as $cat)
          {
            $all &= $cat[id];
            if (strpos($CURUSER["notifs"], "[cat" . $cat[id] . "]") !== False)
            {
              $wherecatina[] = $cat[id];
              $addparam .= "c$cat[id]=1&amp;";
            }
          }
        }
        elseif ($category)
        {
          if (!is_valid_id($category))
            stderr($tracker_lang['error'], "Invalid category ID.");
          $wherecatina[] = $category;
          $addparam .= "cat=$category&amp;";
        }
        else
        {
          $all = True;
          foreach ($cats as $cat)
          {
            $all &= $_GET["c$cat[id]"];
            if ($_GET["c$cat[id]"])
            {
              $wherecatina[] = $cat[id];
              $addparam .= "c$cat[id]=1&amp;";
            }
          }
        }

if ($all)
{
        $wherecatina = array();
  $addparam = "";
}

if (count($wherecatina) > 1)
        $wherecatin = implode(",",$wherecatina);
elseif (count($wherecatina) == 1)
        $wherea[] = "category = $wherecatina[0]";

$wherebase = $wherea;

if (isset($cleansearchstr))
{
		$wherea[] = "torrents.name LIKE '%" . sqlwildcardesc($searchstr) . "%'";
        $addparam .= "search=" . urlencode($searchstr) . "&amp;";
}

if (isset($cleandescrstr))
{
		$wherea[] = "torrents.descr LIKE '%" . sqlwildcardesc($descrstr) . "%'";
        $addparam .= "descr=" . urlencode($searchstr) . "&amp;";
}

if (isset($cleantagstr))
{
		$wherea[] = "torrents.tags LIKE '%" . sqlwildcardesc($tagstr) . "%'";
        $addparam .= "tag=" . urlencode($tagstr) . "&";
}

$where = implode(" AND ", $wherea);
if ($wherecatin)
        $where .= ($where ? " AND " : "") . "category IN (" . $wherecatin . ")";

if ($where != "")
        $where = "WHERE $where";

$res = sql_query("SELECT COUNT(*) FROM torrents $where") or die(mysql_error());
$row = mysql_fetch_array($res);
$count = $row[0];
$num_torrents = $count;

if (!$count && isset($cleansearchstr)) {
        $wherea = $wherebase;
        //$orderby = "ORDER BY id DESC";
        $searcha = explode(" ", $cleansearchstr);
        $sc = 0;
        foreach ($searcha as $searchss) {
                if (strlen($searchss) <= 1)
                        continue;
                $sc++;
                if ($sc > 5)
                        break;
                $ssa = array();
                $ssa[] = "torrents.name LIKE '%" . sqlwildcardesc($searchss) . "%'";
        }
        if ($sc) {
                $where = implode(" AND ", $wherea);
                if ($where != "")
                        $where = "WHERE $where";
                $res = sql_query("SELECT COUNT(*) FROM torrents $where");
                $row = mysql_fetch_array($res);
                $count = $row[0];
        }
}

if ($count)
{
    if ($addparam != "") {
 if ($pagerlink != "") {
  if ($addparam{strlen($addparam)-1} != ";") { // & = &amp;
    $addparam = $addparam . "&" . $pagerlink;
  } else {
    $addparam = $addparam . $pagerlink;
  }
 }
    } else {
 $addparam = $pagerlink;
    }
        list($pagertop, $pagerbottom, $limit) = browsepager($torrentsperpage, $count, "browse.php?" . $addparam);
        $query = "SELECT torrents.id, torrents.tags, torrents.image1, torrents.moderated, torrents.moderatedby, torrents.category, torrents.leechers, torrents.seeders, torrents.free, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.sticky, torrents.owner, torrents.multitracker, torrents.f_seeders, torrents.f_peers," .
        "categories.name AS cat_name, categories.image AS cat_pic, users.username, users.class FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
        $res = sql_query($query) or die(mysql_error());
}
else
        unset($res);
if (isset($cleansearchstr))
        stdhead($tracker_lang['search_results_for']." \"$searchstr\"");
else
        stdhead($tracker_lang['browse'], 'all');
		
?>

<script type="text/javascript">

function pageswitcher(page) {

var no_ajax = true;

     if (jQuery) no_ajax = false;   
jQuery("#loading-layer").show("fast");
    jQuery.get("browse.php", { ajax: "", count: <?=$count?>, where: "<?=base64_encode($where)?>", page: page }, function(data){
   jQuery("#loading-table").empty();
   jQuery("#loading-table").append(data);
   jQuery("#loading-layer").hide()
});

return no_ajax;
}
</script>

<?
begin_frame("Поиск торрентов");
?>
<table class="bottom" width="100%">
<form method="get" action="browse.php">
<tr><td style="border: none;"><b>Поиск по названию:</b></td><td style="border: none;">
<input type="text" id="searchinput" name="search" size="80" autocomplete="off" value="<?= htmlspecialchars($searchstr) ?>" />
<select name="incldead">
<option value="0"><?=$tracker_lang['active'];?></option>
<option value="1"<? print($_GET["incldead"] == 1 ? " selected" : ""); ?>><?=$tracker_lang['including_dead'];?></option>
<option value="2"<? print($_GET["incldead"] == 2 ? " selected" : ""); ?>><?=$tracker_lang['only_dead'];?></option>
<option value="3"<? print($_GET["incldead"] == 3 ? " selected" : ""); ?>><?=$tracker_lang['golden_torrents'];?></option>
<option value="4"<? print($_GET["incldead"] == 4 ? " selected" : ""); ?>><?=$tracker_lang['no_seeds'];?></option>
</select>
<select name="cat">
<option value="0">(<?=$tracker_lang['all_types'];?>)</option>
<?


//$cats = genrelist();
$catdropdown = "";
foreach ($cats as $cat) {
$catdropdown .= "<option value=\"" . $cat["id"] . "\"";
if ($cat["id"] == $_GET["cat"])
$catdropdown .= " selected=\"selected\"";
$catdropdown .= ">" . htmlspecialchars($cat["name"]) . "</option>\n";
}

?>
<?= $catdropdown ?>
</select>
</td></tr><tr><td style="border: none;">
<b>Поиск по описанию:</b></td><td style="border: none;">
<input type="text" id="searchinput" name="descr" size="80" autocomplete="off" value="<?= htmlspecialchars($descrstr) ?>" />
<select name="incldead">
<option value="0"><?=$tracker_lang['active'];?></option>
<option value="1"<? print($_GET["incldead"] == 1 ? " selected" : ""); ?>><?=$tracker_lang['including_dead'];?></option>
<option value="2"<? print($_GET["incldead"] == 2 ? " selected" : ""); ?>><?=$tracker_lang['only_dead'];?></option>
<option value="3"<? print($_GET["incldead"] == 3 ? " selected" : ""); ?>><?=$tracker_lang['golden_torrents'];?></option>
<option value="4"<? print($_GET["incldead"] == 4 ? " selected" : ""); ?>><?=$tracker_lang['no_seeds'];?></option>
</select>
<select name="cat">
<option value="0">(<?=$tracker_lang['all_types'];?>)</option>
<?= $catdropdown ?>
</select>
<input class="btn" type="submit" value="<?=$tracker_lang['search'];?>!" /></td></tr>



</form>
<script language="JavaScript" src="js/suggest.js" type="text/javascript"></script>
<div id="suggcontainer" style="text-align: left; width: 520px; display: none;">
<div id="suggestions" style="cursor: default; position: absolute; background-color: #FFFFFF; border: 1px solid #777777;"></div>
</div>
</td></tr></table>
<?
end_frame();
?>
<div id="contenttop" class="floatbox">
<div class="module">
<div class="mone">
<div class="mtwo">
<div class="mthree">
<h3><div style="float:left;">Обзор торрентов</div><div style="margin-top: 13px; display:none; float:right;" id="loading-layer"><img src="pic/upload.gif" border="0" alt="Загрузка"></div></h3>
<div class="mfour"><table class="embedded" cellspacing="0" cellpadding="2" width="100%">

<STYLE TYPE="text/css" MEDIA=screen>

  a.catlink:link, a.catlink:visited{
                text-decoration: none;
        }

        a.catlink:hover {
                color: #A83838;
        }

</STYLE>
<tr><td colspan="12" style="border:none;">

<form method="get" action="browse.php">
<table class="embedded" align="center">

        <table class="bottom" width="100%">

<?
$i = 0;
foreach ($cats as $cat)
{
        $catsperrow = 5;
        print(($i && $i % $catsperrow == 0) ? "</tr><tr>" : "");
        print("<td class=\"bottom\" style=\"padding-bottom: 2px;padding-left: 7px\"><input name=\"c$cat[id]\" type=\"checkbox\" " . (in_array($cat[id],$wherecatina) ? "checked " : "") . "value=\"1\"><a class=\"catlink\" href=\"browse.php?cat=$cat[id]\">" . htmlspecialchars($cat[name]) . "</a> <span style=\"cursor: pointer;\" onclick=\"javascript: show_tags(".$cat["id"].");\"><img border=\"0\" src=\"pic/tags.gif\" title=\"Показать тэги\"></span>&nbsp;<a href=\"rss.php?cat=".$cat["id"]."\"><img src=\"pic/rss.gif\" width=\"9\" border=\"0\" title=\"Трансляция\"></a></td></td>\n");
        $i++;
}

$alllink = "<div align=\"left\">(<a href=\"browse.php?all=1\"><b>".$tracker_lang['show_all']."</b></a>)</div>";

$ncats = count($cats);
$nrows = ceil($ncats/$catsperrow);
$lastrowcols = $ncats % $catsperrow;

if ($lastrowcols != 0)
{
        if ($catsperrow - $lastrowcols != 1)
                {
                        print("<td class=\"bottom\" rowspan=\"" . ($catsperrow  - $lastrowcols - 1) . "\">&nbsp;</td>");
                }
}
?>

        </tr>
        </td>
</form>
<table class="bottom" width="100%"><div id="tags"></div></table>

<?
if (isset($cleansearchstr))
print("<tr><td class=\"index\" colspan=\"12\">".$tracker_lang['search_results_for']." \"" . htmlspecialchars($searchstr) . "\"</td></tr>\n");

if (isset($cleantagstr))
print("<tr><td class=\"index\" colspan=\"12\">Результаты поиска по тэгу: \"" . htmlspecialchars($tagstr) . "\"</td></tr>\n");

print("</td></tr>");

if ($num_torrents) {
      print("<table id=\"loading-table\" class=\"embedded\" cellspacing=\"0\" cellpadding=\"4\" width=\"100%\">");

        torrenttable($res, "index");

        print("<tr><td class=\"index\" colspan=\"12\" style='border:0'>");
        print($pagerbottom);
        print("</td></tr>");

}  
else {
        if (isset($cleansearchstr)) {
                print("<tr><td class=\"index\" colspan=\"12\">".$tracker_lang['nothing_found']."</td></tr>\n");
                //print("<p>Попробуйте изменить запрос поиска.</p>\n");
        }
        else {
                print("<tr><td class=\"index\" colspan=\"12\">".$tracker_lang['nothing_found']."</td></tr>\n");
        }
}

print("</table>");
end_frame();
stdfoot();

?>