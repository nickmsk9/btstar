<?
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();
stdhead('Шаблоны раздач');
if(get_user_class() < UC_ADMINISTRATOR)
die('Access denied');

if(empty($_GET['act'])) {
$sql = sql_query("SELECT * FROM templates");
begin_frame('Шаблоны раздач');
?> <table border="0" width="100%"> <?
while($tpl = mysql_fetch_array($sql))
{
    ?><tr><td width="30%" align="right" style="padding: 2px;">
		<?=$tpl['name'];?></td><td style="padding: 2px;">[<a href="tpls.php?act=edit&tpl=<?=$tpl['id'];?>">Редактировать</a>] [<a href="tpls.php?act=del&tpl=<?=$tpl['id'];?>">Удалить</a>]</td></tr><?
}
?> </table> <?
end_frame();

begin_frame('Управление');
?> <a href="tpls.php?act=add">Добавить</a> <?
end_frame();
}
elseif($_GET['act']=='add')
{
	begin_frame('Добавить щаблон');
		?><table border="0" width="100%"><form name="tpl" action="tpls.php?act=takeadd" method="post"><tr><td width="30%" align="right" style="padding: 2px;">
		Название</td><td style="padding: 2px;"><input type="text" size="40" name="name"></td></tr>
		<tr><td align="right" valign="top" style="padding: 2px;">Текст</td><td style="padding: 2px;">
		<? textbbcode("tpl","text","",$long); ?>
		<tr><td>&nbsp;</td><td style="padding: 2px;"><input type="submit" name="ok" value="Создать"></td></tr>
		</form></table> <?
	end_frame();
}
elseif($_GET['act']=='takeadd')
{
	if(empty($_POST['name'])||empty($_POST['text']))
	{	echo "Не все поля заполнены";
		stdfoot(); die(); }
	if(sql_query("INSERT INTO templates (`name`,`template`) VALUES (".sqlesc($_POST['name']).", ".sqlesc($_POST['text']).")"))
		echo "Шаблон успешно создан";
	else
		echo "Ошибка при создании шаблона: ".mysql_error();
}
elseif($_GET['act']=='del')
{
	if(empty($_GET['tpl'])||!is_numeric($_GET['tpl']))
	{	echo "Выберите шаблон";
		stdfoot(); die(); }
	if(sql_query("DELETE FROM templates WHERE id = ".$_GET['tpl']))
		echo "Шаблон удален";
	else
		echo "Ошибка при удалении шаблона: ".mysql_error();
}
elseif($_GET['act']=='edit')
{
	if(empty($_GET['tpl'])||!is_numeric($_GET['tpl']))
	{	echo "Выберите шаблон";
		stdfoot(); die(); }
	$tpl = mysql_fetch_array(sql_query("SELECT * FROM templates WHERE id = ".$_GET['tpl']));
	if(empty($tpl))
	{	echo "Выберите шаблон";
		stdfoot(); die(); }
	begin_frame('Изменить щаблон');
		?><table border="0" width="100%"><form name="tpl" action="tpls.php?act=takeedit&tpl=<?=$tpl['id'];?>" method="post"><tr><td width="30%" align="right" style="padding: 2px;">
		Название</td><td style="padding: 2px;"><input type="text" size="40" name="name" value="<?=$tpl['name'];?>"></td></tr>
		<tr><td align="right" valign="top" style="padding: 2px;">Текст</td><td style="padding: 2px;">
		<? textbbcode("tpl","text",$tpl['template'],$long); ?>
		<tr><td>&nbsp;</td><td style="padding: 2px;"><input type="submit" name="ok" value="Изменить"></td></tr>
		</form></table> <?
	end_frame();
}
elseif($_GET['act']=='takeedit')
{
	if(empty($_GET['tpl'])||!is_numeric($_GET['tpl']))
	{	echo "Выберите шаблон";
		stdfoot(); die(); }
	if(sql_query("UPDATE templates SET name = ".sqlesc($_POST['name']).", template = ".sqlesc($_POST['text'])." WHERE id = ".$_GET['tpl']))
		echo "Шаблон успешно изменен";
	else
		echo "Ошибка при изменении шаблона: ".mysql_error();
}
stdfoot();
?>