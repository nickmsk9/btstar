<?
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();
stdhead('Администраторская панель');
if(get_user_class() < UC_ADMINISTRATOR)
die('Access denied');
?>
<table cellpadding="4" cellspacing="0" border="0" style="width:100%" class="tableinborder">
	<style>
	.alt1, .alt1Active
	{
		background: #ffffff;
		color: #000000;
		cursor: pointer;
		font: 8pt verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif;
		border: 1px solid #AEB6CD;
	}
	.alt2, .alt2Active
	{
		background: lightblue;
		color: #ffffff;
		cursor: pointer;
		font: 8pt verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif;
		border: 1px solid #AEB6CD;
	}
	.smalltext
	{
		font: 7pt verdana, geneva, lucida, 'lucida grande', arial, helvetica, sans-serif;
		color: #848282;
	}
	</style>
<tr><td class="alt1Active" onmouseover="this.className='alt2Active';" onmouseout="this.className='alt1Active';" onclick="window.location.href='adduser.php';">Добавить пользователя<p class="smalltext">Добавить нового пользователя на трекер</p></td>
<td class="alt1Active" onmouseover="this.className='alt2Active';" onmouseout="this.className='alt1Active';" onclick="window.location.href='staffbox.php';">Сообщения администрации.<p class="smalltext">Сообщения юзеров для всего админ состава</p></td></tr>
<tr><td class="alt1Active" onmouseover="this.className='alt2Active';" onmouseout="this.className='alt1Active';" onclick="window.location.href='category.php';">Категории<p class="smalltext">Редактировать категории на трекере</p></td>
<td class="alt1Active" onmouseover="this.className='alt2Active';" onmouseout="this.className='alt1Active';" onclick="window.location.href='staffmess.php';">Сообщения от администрации<p class="smalltext">Массовая отправка сообщений от алминистрации.</p></td></tr>
<tr><td class="alt1Active" onmouseover="this.className='alt2Active';" onmouseout="this.className='alt1Active';" onclick="window.location.href='bans.php';">Баны<p class="smalltext">Забанить пользователя на трекере</p></td>
<td class="alt1Active" onmouseover="this.className='alt2Active';" onmouseout="this.className='alt1Active';" onclick="window.location.href='unco.php';">Неподтвержденный пользователи<p class="smalltext">Аккаунты которые не подтверждены</p></td></tr>
<tr><td class="alt1Active" onmouseover="this.className='alt2Active';" onmouseout="this.className='alt1Active';" onclick="window.location.href='warned.php';">Предупрежденный пользователи<p class="smalltext">Пользователи которым светит бан</p></td>
<td class="alt1Active" onmouseover="this.className='alt2Active';" onmouseout="this.className='alt1Active';" onclick="window.location.href='stats.php';">Статистика трекера<p class="smalltext">Лучшие аплоадеры и самые лучшие категории</p></td></tr>
<tr><td class="alt1Active" onmouseover="this.className='alt2Active';" onmouseout="this.className='alt1Active';" onclick="window.location.href='tpls.php';">Шаблоны раздач</td>
<td class="alt1Active" onmouseover="this.className='alt2Active';" onmouseout="this.className='alt1Active';">&nbsp;</td></tr>
</table>