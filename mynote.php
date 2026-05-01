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
?><a href="noteedit.php?act=add">Добавить</a><?php
end_frame();
}
else
{
	begin_frame('Мои записи');
	?><table width="100%" cellpadding="5" border="0"> <?php
	while($note=mysql_fetch_array($sql))
	{
		$text = htmlspecialchars($note['text']);
		$text = str_replace("\n", ' ',$text);
		$text=preg_replace("#\[.*\]#is","",$text);
		if(strlen($note['text']) > 240)
		$text=substr($text,0,200).'...';
		?><tr><td><span style="font-size: 12pt;"><a href="note.php?uid=<?=$CURUSER['id'];?>&id=<?=$note['id'];?>"><?=$note['name'];?></a></span><br>Добавлена: <?=nicetime($note['timestamp'],true);?><br>
		<?php if(!empty($note['last_edit'])) { ?><br><small>Последняя правка: <?=nicetime($note['last_edit'],true);?><?php } ?>
		<hr>
		<?=$text;?>
		<hr>
		<span style="color: gray;font-weight: normal;"><?php if($note['access']==1) { ?>Открытая запись<?php } else { ?>Закрытая запись<?php } ?></span>
		<br>
		<?php if(!empty($note['tags'])) { ?>Теги: <?php
			$tags=explode(',',$note['tags']);
			$i=0;
			foreach ($tags as $tag)
		{echo ($i!=0 ? ', ' : '').'<a href="notetag.php?tag='.urlencode(trim($tag)).'" style="color:green;font-weight:normal;">'.trim($tag).'</a>';
		$i++;}echo "<br>";}
		?> Просмотров: <?=$note['views'];?><br> Комментариев: <?=$note['comments'];?></td></tr>
	<?php } ?>
	</table> <?php
	end_frame();
begin_frame('Управление');
?><a href="noteedit.php?act=add">Добавить</a><?php
end_frame();
}
stdfoot();
?>
