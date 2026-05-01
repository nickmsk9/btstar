<?php
require_once("include/bittorrent.php");
dbconn(false);
loggedinorreturn();
function bark($msg) {
	global $tracker_lang;
	stdhead('Записи');
	stdmsg($tracker_lang['error'], $msg);
	stdfoot();
	exit;
}

function succ($msg) {
	stdhead('Записи');
	stdmsg('Успешно', $msg);
	stdfoot();
	exit;
}

function ques($msg) {
	stdhead('Записи');
	stdmsg('Вопрос', $msg);
	stdfoot();
	exit;
}

if(empty($_GET['act']))
bark('Нет действия!');
switch($_GET['act'])
{
	case 'add':
		$sql=mysql_fetch_array(sql_query("SELECT COUNT(*) FROM notes WHERE uid=".$CURUSER['id']));
		if(!empty($sql)&&$sql[0]>=$maxnotes)
			bark("Вы не можете больше создавать записи!");
		stdhead('Записи');
		begin_frame('Создать запись');
		?><table border="0" width="100%"><form name="note" action="noteedit.php?act=takeadd" method="post"><tr><td width="30%" align="right" style="padding: 2px;">
		Заголовок</td><td style="padding: 2px;"><input type="text" size="40" name="name"></td></tr>
		<tr><td align="right" valign="top" style="padding: 2px;">Тип</td><td style="padding: 2px;"><select name="access"><option selected="" value="1">Открытая</option><option value="0">Закрытая</option></select> Закрытые записи могут читать только ваши друзья<br><small>* Модераторы могут читать закрытые записи!</small></td></tr>
		<tr><td align="right" valign="top" style="padding: 2px;">Текст</td><td style="padding: 2px;">
		<?php textbbcode("note","text","",$long); ?></td></tr>
		<tr><td align="right" valign="top" style="padding: 2px;">Теги</td><td style="padding: 2px;"><input size="35" type="text" name="tags"><br><small>* Через запятую, не более 10</small></td></tr>
		<tr><td align="right" valign="top" style="padding: 2px;">Торренты</td><td style="padding: 2px;"><input size="35" type="text" name="torrents"><br><small>* ID торрентов через запятую, не более 10</small></td></tr>
		<tr><td>&nbsp;</td><td style="padding: 2px;"><input type="submit" name="ok" value="Создать"></td></tr>
		</form></table> <?php
		end_frame();
	break;
	case 'takeadd':
		$sql=mysql_fetch_array(sql_query("SELECT COUNT(*) FROM notes WHERE uid=".$CURUSER['id']));
		if(!empty($sql)&&$sql[0]>=$maxnotes)
			bark("Вы не можете больше создавать записи!");
		if(empty($_POST['name'])||empty($_POST['text']))
			bark("Заполните название и текст!");
		
		$text = trim(sqlesc($_POST['text']));
		$name = trim(sqlesc($_POST['name']));
		$timestamp = "'".date("Y-m-d H:i:s")."'";
		$tags = sqlesc($_POST['tags']);
		if(!empty($_POST['torrents'])&&ereg("^[0-9, ]+$",$_POST['torrents']))
		$torrents = sqlesc($_POST['torrents']);
		else
		$torrents='NULL';
		if($_POST['access']==0)
		$access = 0;
		else $access = 1;
		
		$id=mysql_fetch_array(sql_query("SELECT id FROM notes WHERE uid=".$CURUSER['id']." ORDER BY id LIMIT 1"));
		if(empty($id))
		$id=1;
		else
		$id=$id['id']+1;
		
		if(sql_query("INSERT INTO notes (`uid`,`id`,`name`,`timestamp`,`text`,`access`,`tags`,`torrents`) VALUES (".$CURUSER['id'].", $id, $name, $timestamp, $text, $access, $tags, $torrents)"))
		{header("Location: note".$CURUSER['id']."-".$id); die; }
		else
			bark("Неизвестная ошибка!");
	break;
	case 'edit':
		if(empty($_GET['uid'])||!is_numeric($_GET['uid'])||empty($_GET['id'])||!is_numeric($_GET['id']))
			bark("Не выбрана запись!");
		$uid = $_GET['uid'];
		$id = $_GET['id'];
		$note = mysql_fetch_array(sql_query("SELECT * FROM notes WHERE uid = ".$uid." AND id = ".$id));
		if(empty($note))
			bark("Такой записи не существует!");
		if($uid!=$CURUSER['id']&&$CURUSER['class'] < UC_MODERATOR)
			bark("Вы не можете редактировать чужие записи!");
		stdhead("Записи");
		begin_frame('Изменить запись');
		?><table border="0" width="100%"><form name="note" action="noteedit.php?act=takeedit&uid=<?=$uid;?>&id=<?=$id;?>" method="post"><tr><td width="30%" align="right" style="padding: 2px;">
		Заголовок</td><td style="padding: 2px;"><input type="text" size="40" name="name" value="<?=htmlspecialchars($note['name']);?>"></td></tr>
		<tr><td align="right" valign="top" style="padding: 2px;">Тип</td><td style="padding: 2px;"><select name="access"><option <?=($note['access']==1 ? 'selected=""' : '');?> value="1">Открытая</option><option <?=($note['access']==0 ? 'selected=""' : '');?> value="0">Закрытая</option></select> Закрытые записи могут читать только ваши друзья<br><small>* Модераторы могут читать закрытые записи!</small></td></tr>
		<tr><td align="right" valign="top" style="padding: 2px;">Текст</td><td style="padding: 2px;">
		<?php textbbcode("note","text",htmlspecialchars($note['text']),$long); ?></td></tr>
		<tr><td align="right" valign="top" style="padding: 2px;">Теги</td><td style="padding: 2px;"><input size="35" type="text" name="tags" value="<?=htmlspecialchars($note['tags']);?>"><br><small>* Через запятую, не более 10</small></td></tr>
		<tr><td align="right" valign="top" style="padding: 2px;">Торренты</td><td style="padding: 2px;"><input size="35" type="text" name="torrents" value="<?=htmlspecialchars($note['torrents']);?>"><br><small>* ID торрентов через запятую, не более 10</small></td></tr>
		<tr><td>&nbsp;</td><td style="padding: 2px;"><input type="submit" name="ok" value="Изменить"></td></tr>
		</form></table> <?php
		end_frame();
	break;
	case 'takeedit':
		if(empty($_GET['uid'])||!is_numeric($_GET['uid'])||empty($_GET['id'])||!is_numeric($_GET['id']))
			bark("Не выбрана запись!");
		$uid = $_GET['uid'];
		$id = $_GET['id'];
		$note = mysql_fetch_array(sql_query("SELECT * FROM notes WHERE uid = ".$uid." AND id = ".$id));
		if(empty($note))
			bark("Такой записи не существует!");
		if($uid!=$CURUSER['id']&&$CURUSER['class'] < UC_MODERATOR)
			bark("Вы не можете редактировать чужие записи!");
		
		$text = trim(sqlesc($_POST['text']));
		$name = trim(sqlesc($_POST['name']));
		$timestamp = "'".date("Y-m-d H:i:s")."'";
		$tags = sqlesc($_POST['tags']);
		if(!empty($_POST['torrents'])&&ereg("^[0-9, ]+$",$_POST['torrents']))
		$torrents = sqlesc($_POST['torrents']);
		else
		$torrents='NULL';
		if($_POST['access']==0)
		$access = 0;
		else $access = 1;
		if($uid!=$CURUSER['id'])
			$moderated = ' moderated_by = '.$CURUSER['id'].", moderated_time = $timestamp,";
		else
			$moderated = '';
		if(sql_query("UPDATE notes SET name = $name, text = $text, last_edit = $timestamp,$moderated tags = $tags, torrents = $torrents, access = $access WHERE uid = ".$uid." AND id = ".$id))
			succ("Запись успешно изменена!<br><a href=\"note".$uid."-".$id."\">Перейти</a>");
		else
			bark("Ошибка при изменении");
	break;
	case 'delete':
		if(empty($_GET['uid'])||!is_numeric($_GET['uid'])||empty($_GET['id'])||!is_numeric($_GET['id']))
			bark("Не выбрана запись!");
		$uid = $_GET['uid'];
		$id = $_GET['id'];
		$note = mysql_fetch_array(sql_query("SELECT * FROM notes WHERE uid = ".$uid." AND id = ".$id));
		if(empty($note))
			bark("Такой записи не существует!");
		if($uid!=$CURUSER['id']&&$CURUSER['class'] < UC_MODERATOR)
			bark("Вы не можете редактировать чужие записи!");
		ques('Вы действительно хотите удалить запись "'.htmlspecialchars($note['name']).'"?<br>[<a href="noteedit.php?uid='.$uid.'&id='.$id.'&act=deleteconf">Удалить</a>] [<a href="note'.$uid.'-'.$id.'">Вернуться</a>]');
	
	break;
	case 'deleteconf':
		if(empty($_GET['uid'])||!is_numeric($_GET['uid'])||empty($_GET['id'])||!is_numeric($_GET['id']))
			bark("Не выбрана запись!");
		$uid = $_GET['uid'];
		$id = $_GET['id'];
		$note = mysql_fetch_array(sql_query("SELECT * FROM notes WHERE uid = ".$uid." AND id = ".$id));
		if(empty($note))
			bark("Такой записи не существует!");
		if($uid!=$CURUSER['id']&&$CURUSER['class'] < UC_MODERATOR)
			bark("Вы не можете редактировать чужие записи!");
		if(sql_query("DELETE FROM `notes` WHERE uid = ".$uid." AND id = ".$id))
		{	if(sql_query("DELETE FROM `noteswall` WHERE `owner` = ".$uid." AND nid = ".$id))
				succ("Запись успешно удалена!");}
		else
			bark("Невозможно удалить запись!");
	break;
	default:
		bark('Нет действия');
}
stdfoot();
?>