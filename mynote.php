<?php
require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();
function bark($msg) {
	global $tracker_lang;
	stdmsg($tracker_lang['error'], $msg);
	exit;
}
stdhead('Мои записи');
$sql=sql_query("SELECT * FROM notes WHERE uid = ".$CURUSER['id']);
if(mysql_num_rows($sql)==0)
{
begin_frame('Нет зписей');
?><a href="noteedit.php?act=add">Добавить</a><?
end_frame();
}
else
{
	begin_frame('Мои записи');
	?><table width="100%" cellpadding="5" border="0"> <?
	while($note=mysql_fetch_array($sql))
	{
		$text = htmlspecialchars($note['text']);
		$text = str_replace("\n", ' ',$text);
		$text=preg_replace("#\[.*\]#is","",$text);
		if(strlen($note['text']) > 240)
		$text=substr($text,0,200).'...';
		?><tr><td><span style="font-size: 12pt;"><a href="note<?=$CURUSER['id'];?>-<?=$note['id'];?>"><?=$note['name'];?></a></span><br>Добавлена: <?=nicetime($note['timestamp'],true);?><br>
		<? if(!empty($note['last_edit'])) { ?><br><small>Последняя правка: <?=nicetime($note['last_edit'],true);?><? } ?>
		<hr>
		<?=$text;?>
		<hr>
		<span style="color: gray;font-weight: normal;"><? if($note['access']==1) { ?>Открытая запись<? } else { ?>Закрытая запись<? } ?></span>
		<br>
		<? if(!empty($note['tags'])) { ?>Теги: <?
			$tags=explode(',',$note['tags']);
			$i=0;
			foreach ($tags as $tag)
		{echo ($i!=0 ? ', ' : '').'<a href="note-tag,'.urlencode(trim($tag)).'" style="color:green;font-weight:normal;">'.trim($tag).'</a>';
		$i++;}echo "<br>";}
		?> Просмотров: <?=$note['views'];?><br> Комментариев: <?=$note['comments'];?></td></tr>
	<? } ?>
	</table> <?
	end_frame();
begin_frame('Управление');
?><a href="noteedit.php?act=add">Добавить</a><?
end_frame();
}
stdfoot();
?>