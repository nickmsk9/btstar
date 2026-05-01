<?
ob_start("ob_gzhandler");
header("Cache-Control: no-cache");
header("Pragma: nocache");

include("include/bittorrent.php");
dbconn();
loggedinorreturn();

// Cookie settings
$expire = time() + 99999999;

// IF JAVASCRIPT IS ENABLED


function getPerc($id){

	$total = 0;
	$rows = 0;

	$sel = mysql_query("SELECT rating_num FROM ratetorrents WHERE rating_id = '$id'");
	if(mysql_num_rows($sel) > 0){

		while($data = mysql_fetch_assoc($sel)){

			$total = $total + $data['rating_num'];
			$rows++;
		}

		$perc = ($total/$rows) * 20;

		$newPerc = round($perc,2);
		return $newPerc;

	} else {

		return '0';

	}
}


if($_POST){

	$id = (int) $_POST['id'];
	$rating = (int) $_POST['rating'];

	if($rating <= 5 && $rating >= 1){

		if(@mysql_fetch_assoc(mysql_query("SELECT id FROM ratetorrents WHERE userid = '".$CURUSER['id']."' AND rating_id = '$id'")) || isset($_COOKIE['has_voted_'.$id])){

			echo 'already_voted';


		} else {



			setcookie('has_voted_'.$id,$id,$expire,'/');
			mysql_query("INSERT INTO ratetorrents (rating_id, rating_num, userid) VALUES ('$id', '$rating', '".$CURUSER['id']."')") or die(mysql_error());
            mysql_query("UPDATE torrents SET ratio = '".getPerc($id)."' WHERE id = '$id'") or die(mysql_error());

			$total = 0;
			$rows = 0;

			$sel = mysql_query("SELECT rating_num FROM ratetorrents WHERE rating_id = '$id'");
			while($data = mysql_fetch_assoc($sel)){

				$total = $total + $data['rating_num'];
				$rows++;
			}

			$perc = ($total/$rows) * 20;

			echo round($perc,2);
			//echo round($perc/5)*5;

		}

	}

}

// IF JAVASCRIPT IS DISABLED

if($_GET){

	$id = (int) $_POST['id'];
	$rating = (int) $_GET['rating'];

	// If you want people to be able to vote more than once, comment the entire if/else block block and uncomment the code below it.

	if($rating <= 5 && $rating >= 1){

		if(@mysql_fetch_assoc(mysql_query("SELECT id FROM ratetorrents WHERE userid = '".$CURUSER['id']."' AND rating_id = '$id'")) || isset($_COOKIE['has_voted_'.$id])){

			echo 'already_voted';

		} else {

			setcookie('has_voted_'.$id,$id,$expire,'/');
			mysql_query("INSERT INTO ratetorrents (rating_id, rating_num, userid) VALUES ('$id', '$rating', '".$CURUSER['id']."')") or die(mysql_error());
            mysql_query("UPDATE torrents SET ratio = '".getPerc($id)."' WHERE id = '$id'") or die(mysql_error());
		}

		header("Location:".$_SERVER['HTTP_REFERER']."");
		die;

	}
	else {

		echo 'You cannot rate this more than 5 or less than 1 <a href="'.$_SERVER['HTTP_REFERER'].'">back</a>';

	}



}

?>
