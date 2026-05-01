<?php
require_once 'include/bittorrent.php';
dbconn(false);
loggedinorreturn();

header("Content-Type: text/html; charset=".$tracker_lang['language_charset']);
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
{
	$do = strip_tags(trim($_POST['do']));
	$torrentid = intval($_POST['tid']);
	if($torrentid==0) die();
	
	switch( $do )
	{
		case 'show_thanks' :
		  if( $torrentid )
		  {
			  $count_sql = sql_query("SELECT COUNT(*) FROM thanks WHERE torrentid = $torrentid");
	          $count_row = mysql_fetch_row($count_sql);
	          $count = intval($count_row['0']);

	          if ($count == 0) {
		        $thanksby = "Никто не поставил спасибо этому торренту.";
				
				echo $thanksby;
	          } else {
			  $thanked_sql = sql_query("SELECT thanks.userid, thanks.added, users.username, users.class FROM thanks INNER JOIN users ON thanks.userid = users.id WHERE thanks.torrentid = $torrentid ORDER BY thanks.id ASC");
		      while ($thanked_row = mysql_fetch_assoc($thanked_sql))
		      {
				  if(($thanked_row["userid"] == $CURUSER["id"]) || ($thanked_row["userid"] == $row["owner"]))
			        $can_not_thanks = true;
			
			        $userid   = intval($thanked_row['userid']);
			        $username = htmlspecialchars($thanked_row['username']);
			        $class    = intval($thanked_row['class']);
			        $thanksby .= "<a href=\"userdetails.php?id={$userid}\">".get_user_class_color($class, $username)."</a>&nbsp;<i>(".nicetime($thanked_row['added']).")</i>, ";
			  }
			  
		if ($thanksby)
			$thanksby = substr($thanksby, 0, -2);
			
			echo $thanksby;
		  }
		  }
		  
		break;
		case 'send_thanks' :
		  $userid     = $CURUSER['id'];
		  $author_row = mysql_fetch_array(sql_query("SELECT owner FROM torrents WHERE id = $torrentid LIMIT 1"));;
		  $is_author  = ($author_row['owner'] == $userid ? true : false);
		  list($count)  = mysql_fetch_row(sql_query("SELECT COUNT(*) FROM thanks WHERE torrentid = $torrentid AND userid = $userid"));
		  
		  //echo $count;
		  
		  if($count <= 0 && !$is_author)
		    if(sql_query("INSERT INTO thanks (`torrentid`, `userid`, `added`) VALUES ($torrentid, $userid, ".sqlesc(date( 'Y-m-d H:i:s ') ).")"));
			echo "<b>Ваша благодарность успешно добавлена!</b>";
		break;
	}
}
?>