<?php
require_once("include/bittorrent.php");
dbconn(false);
loggedinorreturn(true);
stdhead("Записи");
begin_frame("Последние 10 записей");
$sql = sql_query("SELECT notes.*, u.username, u.firstname, u.surname, u.class FROM notes LEFT JOIN users AS u ON u.id = notes.uid WHERE notes.access = 1 ORDER BY notes.`timestamp` DESC LIMIT 10");
if(mysql_num_rows($sql)==0)
	echo "Нет записей";
else
{	?><table width="100%" cellpadding="5" border="0"> <?
	while($note=mysql_fetch_array($sql))
	{
		$text = htmlspecialchars($note['text']);
		$text = str_replace("\n", ' ',$text);
		$text=preg_replace("#\[.*\]#is","",$text);
		if(strlen($note['text']) > 240)
		$text=substr($text,0,200).'...';
		?><tr><td><span style="font-size: 12pt;"><a href="note<?=$note['uid'];?>-<?=$note['id'];?>"><?=$note['name'];?></a></span> (<?=nicetime($note['timestamp'],true);?>)<br>
		Автор: <a href="id<?=$note['uid'];?>"><?=$note['firstname'];?> <i><?=get_user_class_color($note['class'],$note['username']);?></i> <?=$note['surname'];?></a>
		<hr>
		<?=$text;?>
		<hr>
		<? if(!empty($note['tags'])) {
			$tags=explode(',',$note['tags']);
			$i=0;
			foreach ($tags as $tag)
		{echo ($i!=0 ? ', ' : '').'<a href="note-tag,'.urlencode(trim($tag)).'" style="color:green;font-weight:normal;">'.trim($tag).'</a>';
		$i++;}echo "<br>";}
		?> Просмотров: <?=$note['views'];?>, комментариев: <?=$note['comments'];?></td></tr>
	<? } ?>
	</table> <?
}
end_frame();
begin_frame("Ваши записи");
?> <a href="mynote.php">Перейти</a>, <a href="noteedit.php?act=add">Добавить</a> <?
end_frame();
stdfoot();
?>