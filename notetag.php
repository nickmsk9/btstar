<?php
require_once('include/bittorrent.php');
dbconn();

if(empty($_GET['tag']))
{	stdhead('Записи || Теги');
	stderr('Не указан тег!');
	stdfoot();
	exit();
}

$tag = sqlwildcardesc(htmlspecialchars($_GET['tag']));

$sql = sql_query("SELECT notes.*, u.username, u.firstname, u.surname, u.class FROM notes LEFT JOIN users AS u ON u.id = notes.uid WHERE notes.access = 1 AND notes.tags LIKE '%".$tag."%' ORDER BY notes.`timestamp` DESC LIMIT 10");
if(mysql_num_rows($sql)==0)
	{
		stdhead('Записи || Теги');
		stderr('Нет записей с этим тегом');
		stdfoot();
		exit();
	}
stdhead('Записи || Теги || '.htmlspecialchars($_GET['tag']));
begin_frame('Записи с тегом "'.htmlspecialchars($_GET['tag']).'"');
?> <table width="100%" cellpadding="5" border="0"> <?php
	while($note=mysql_fetch_array($sql))
	{
		$text = htmlspecialchars($note['text']);
		$text = str_replace("\n", ' ',$text);
		$text=preg_replace("#\[.*\]#is","",$text);
		if(strlen($note['text']) > 240)
		$text=substr($text,0,200).'...';
		?><tr><td><span style="font-size: 12pt;"><a href="note.php?uid=<?=$note['uid'];?>&id=<?=$note['id'];?>"><?=$note['name'];?></a></span> (<?=nicetime($note['timestamp'],true);?>)<br>
		Автор: <a href="userdetails.php?id=<?=$note['uid'];?>"><?=$note['firstname'];?> <i><?=get_user_class_color($note['class'],$note['username']);?></i> <?=$note['surname'];?></a>
		<hr>
		<?=$text;?>
		<hr>
		<?php if(!empty($note['tags'])) {
			$tags=explode(',',$note['tags']);
			$i=0;
			foreach ($tags as $tag)
		{echo ($i!=0 ? ', ' : '').'<a href="notetag.php?tag='.urlencode(trim($tag)).'" style="color:green;font-weight:normal;">'.trim($tag).'</a>';
		$i++;}echo "<br>";}
		?> Просмотров: <?=$note['views'];?>, комментариев: <?=$note['comments'];?></td></tr>
	<?php } ?>
	</table> <?php
end_frame();


stdfoot();
?>
