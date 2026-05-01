<?php
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn(true);
require_once('languages/lang_russian/lang_pages.php');

if (isset($_GET['ajax']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) $ajax=1; else $ajax=0;

if (isset($_GET['add'])) {

    if (get_user_class() < UC_MODERATOR) stderr($tracker_lang['error'],$tracker_lang['access_denied']);
     stdhead($tracker_lang['adding_page']);
     begin_frame("Добавить персону");
     print("<table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\">");
     print("<form action=\"pages.php?saveadd\" method=\"post\" id=\"add\">");
	 print("<tr><td class='rowhead'>Имя и Фамилия</td><td align='left'><input type=\"text\" size=\"80\" name=\"name\"></td></tr>");
     print("<tr><td class='rowhead'>Фотография</td><td align='left'><input type=\"text\" size=\"80\" name=\"photo\"></td></tr>");
    $year .= "<select name=year><option value=\"0000\">".$tracker_lang['my_year']."</option>\n";
$i = "1920";
while ($i <= (date('Y',time())-13)) {
	$year .= "<option value=" .$i. ">".$i."</option>\n";
	$i++;
}
$year .= "</select>\n";
$birthmonths = array(
	"01" => $tracker_lang['my_months_january'],
	"02" => $tracker_lang['my_months_february'],
	"03" => $tracker_lang['my_months_march'],
	"04" => $tracker_lang['my_months_april'],
	"05" => $tracker_lang['my_months_may'],
	"06" => $tracker_lang['my_months_june'],
	"07" => $tracker_lang['my_months_jule'],
	"08" => $tracker_lang['my_months_august'],
	"09" => $tracker_lang['my_months_september'],
	"10" => $tracker_lang['my_months_october'],
	"11" => $tracker_lang['my_months_november'],
	"12" => $tracker_lang['my_months_december'],
);
$month = "<select name=\"month\"><option value=\"00\">".$tracker_lang['my_month']."</option>\n";
foreach ($birthmonths as $month_no => $show_month) {
	$month .= "<option value=$month_no>$show_month</option>\n";
}
$month .= "</select>\n";
$day .= "<select name=day><option value=\"00\">".$tracker_lang['my_day']."</option>\n";
$i = 1;
while ($i <= 31) {
	if ($i < 10) {
		$day .= "<option value=0".$i.">0".$i."</option>\n";
	} else {
		$day .= "<option value=".$i.">".$i."</option>\n";
	}
	$i++;
}
$day .="</select>\n";
	 print("<tr><td class='rowhead'>Дата рождения</td><td align='left'>".$year.$month.$day."</td></tr>");
	 print("<tr><td class=\"rowhead\">{$tracker_lang['page_content']}</td>");
     print("<td align=\"left\">");
     textbbcode("add","content",""); 
     print("</td></tr>\n");
     print("<tr><td colspan=\"2\"><input type=\"submit\" value=\"{$tracker_lang['adding_page']}\"></form></td></tr></table>");
     end_frame();

  } elseif (isset($_GET['saveadd'])) {
    if (get_user_class() < UC_MODERATOR) stderr($tracker_lang['error'],$tracker_lang['access_denied']);
	$year = $_POST["year"];
    $month = $_POST["month"];
    $day = $_POST["day"];
    sql_query("INSERT INTO pages (name,birthday,photo,content) VALUES (".implode(",",array_map("sqlesc",array($_POST['name'],$year.$month.$day,$_POST['photo'],$_POST['content']))).")");
    $id = mysql_insert_id();
    stderr($tracker_lang['success'],$tracker_lang['adding_page'].' <a href="pages.php?id='.$id.'">pages.php?id='.$id.'</a>','success');
  }

elseif (!is_valid_id($_GET['id'])) {
$_GET['q'] = (string) $_GET['q'];
         if ($ajax) $_GET['q'] = base64_decode($_GET['q']);
         
       if (!empty($_GET['q'])) { $where = "WHERE name LIKE '%" . sqlwildcardesc($_GET['q']) . "%' "; $search=1; }

   if ($ajax) header ("Content-Type: text/html; charset=" . $tracker_lang['language_charset']);
          $addparam='q='.urlencode($_GET['q']).'&';
   $totalpages = get_row_count("pages");
   list($pagertop, $pagerbottom, $limit) = browsepager(25, $totalpages, "pages.php?".$addparam , "#pages-table");

   $row = sql_query('SELECT id, name FROM pages '.$where.'ORDER BY id '.$limit);
   if (!$ajax) { stdhead($tracker_lang['our_pages']);
   print ('<script language="javascript" type="text/javascript">

var no_ajax = true;

function pageswitcher(page) {

   (function($){
     if ($) no_ajax = false;
   $("#pages-table").empty();
   $("#pages-table").append(\'<div align="center"><img src="pic/loading.gif" border="0"/></div>\');
   $.get("pages.php", { ajax: "", q: "'.base64_encode($_GET['q']).'", page: page }, function(data){
   $("#pages-table").empty();
   $("#pages-table").append(data);

});
})(jQuery);

window.location.href = "#pages-table";

return no_ajax;
}
</script>');

   begin_frame("Персоны кино".((get_user_class() >= UC_ADMINISTRATOR)?"&nbsp;<small>[<a href=\"pages.php?add\">Добавить персону</a>]</small>":''));
  }
  if (!$ajax)

      while (list($id,$name) = mysql_fetch_array($row)) {
     $s.=("<tr><td><a href=\"pages.php?id=$id\">$name</a>".((get_user_class() >= UC_ADMINISTRATOR)?"&nbsp;<small>[<a href=\"pages.php?edit&id=$id\">{$tracker_lang['edit']}</a>]&nbsp[<a href=\"pages.php?delete&id=$id\" onclick=\"confirm ('{$tracker_lang['delete']}?');\">{$tracker_lang['delete']}</a>]</small>":'')."</td></tr>");
   }
      if (!$s) stdmsg($tracker_lang['error'],($search?$tracker_lang['nothing_found']:$tracker_lang['no_pages'])); else {
           print('<div id="pages-table"><table width="100%" cellspacing="0" cellpadding="5">');
        print ($s);
              print("<tr><td class=\"index\">");
        print($pagerbottom);
        print("</td></tr>");
           print('</table></div>');
		end_frame();
		begin_frame("Поиск киноактеров"); 
		print('<div align="center"><form action="pages.php" method="get"><input size="100" type="text" name="q" value="'.htmlentities($_GET['q'],ENT_COMPAT,"UTF-8").'"><input type="submit" value="'.$tracker_lang['search'].'"></form></div>'); 
        }

   if ($ajax) die();
   end_frame();

} else {
  $id = (int) $_GET['id'];
     $row = sql_query('SELECT name,birthday,photo,content FROM pages WHERE id='.$id);
     $res = mysql_fetch_assoc($row);
     if (!$res) stderr($tracker_lang['error'],$tracker_lang['no_page_with_this_id']);
	 
     
  if (isset($_GET['delete'])) {
    if (get_user_class() < UC_MODERATOR) stderr($tracker_lang['error'],$tracker_lang['access_denied']);
    sql_query("DELETE FROM pages WHERE id=$id LIMIT 1");
    stderr($tracker_lang['success'],$tracker_lang['page_deleted'].$tracker_lang['to_list_of_pages'],'success');
  }
  elseif (isset($_GET['edit'])) {
     if (get_user_class() < UC_MODERATOR) stderr($tracker_lang['error'],$tracker_lang['access_denied']);
     stdhead($tracker_lang['editing_page'].' '.$res['name']);
     begin_frame($tracker_lang['editing_page'].' '.$res['name']);
	 print("<table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\">");
     print("<form action=\"pages.php?saveedit&id=$id\" method=\"post\">");
	 print("<tr><td class='rowhead'>Имя и Фамилия</td><td align='left'><input type=\"text\" size=\"80\" name=\"name\" value=\"".$res['name']."\"></td></tr>");
     print("<tr><td class='rowhead'>Фотография</td><td align='left'><input type=\"text\" size=\"80\" name=\"photo\" value=\"".$res['photo']."\"></td></tr>");
	 print("<tr><td class=\"rowhead\">{$tracker_lang['page_content']}</td>");
     print("<td align=\"left\">");
     textbbcode("add","content",$res['content']); 
     print("</td></tr>\n");
     print("<tr><td colspan=\"2\"><input type=\"submit\" value=\"{$tracker_lang['edit']}\"></form></td></tr></table>");
     end_frame();
    // print ("<table width=\"100%\"><form action=\"pages.php?saveedit&id=$id\" method=\"post\"><tr><td class=\"colhead\">{$tracker_lang['page_name']}</td></tr>
    // <tr><td><input type=\"text\" size=\"80\" name=\"name\" value=\"{$res['name']}\"></td></tr>
    // <tr><td class=\"colhead\">{$tracker_lang['tags']}</td></tr>
    // <tr><td><input type=\"text\" size=\"80\" name=\"tags\" value=\"{$res['searchwords']}\"><br/>{$tracker_lang['tags_notice']}</td></tr>
    // <tr><td class=\"colhead\">{$tracker_lang['page_content']}</td></tr>
    // <tr><td><textarea name=\"content\" id=\"mce\" rows=\"15\" cols=\"80\">{$res['content']}</textarea></td></tr>
    // <tr><td><input type=\"submit\" value=\"{$tracker_lang['edit']}\"></form></td></tr></table>");
    // end_frame();

  } elseif (isset($_GET['saveedit'])) {
    if (get_user_class() < UC_MODERATOR) stderr($tracker_lang['error'],$tracker_lang['access_denied']);
    sql_query("UPDATE pages SET name=".sqlesc($_POST['name']).", photo=".sqlesc($_POST['photo']).", content=".sqlesc($_POST['content'])." WHERE id=$id");
    stderr($tracker_lang['success'],$tracker_lang['editing_page'].' <a href="pages.php?id='.$id.'">pages.php?id='.$id.'</a>','success');
  } else {

     stdhead($res['name']);
     begin_frame($res['name'].((get_user_class() >= UC_MODERATOR)?"&nbsp;<small>[<a href=\"pages.php?edit&id=$id\">{$tracker_lang['edit']}</a>]&nbsp[<a href=\"pages.php?delete&id=$id\" onclick=\"confirm ('{$tracker_lang['delete']}?');\">{$tracker_lang['delete']}</a>]</small>":''));
	 print("<table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\">");
	 print("<tr><td width=\"400\"><img src=\"".$res['photo']."\"></td><td valign=\"top\">");
	 print("<b>Имя и Фамилия:</b>&nbsp;&nbsp;".$res['name']."<br>");
	 print("<b>Дата рождения:</b>&nbsp;&nbsp;".$res['birthday']."<br>");
	 print("<b>Биография:</b>&nbsp;&nbsp;".format_comment($res['content'])."");
	 print("</td></tr></table>");
	 end_frame();
	 begin_frame("Фильмография на трекере");
	 $resource = sql_query("SELECT * FROM torrents WHERE descr LIKE '%".sqlwildcardesc($res['name'])."%'");
	 while( $array = mysql_fetch_assoc($resource) ) {
print('<table border="1" cellpadding="5" cellspacing="0" width="100%"><tbody><tr><td colspan="2" align="left">'); 
print("<a href=\"details.php?id=" . $array['id'] . "&hit=1\"><b>" . $array['name'] . "</b></a></td>"); 
print("</tr>");  
print("</table>");
}
	 end_frame();
     }
}

   stdfoot();

?>
