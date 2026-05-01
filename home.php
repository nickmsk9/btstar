<?php
require_once("include/bittorrent.php");
dbconn();
stdhead("Приветствуем");
begin_frame("Добро пожаловать");
print("<b>BT-Star - универсальное средство поиска фильмов и друзей.</b><br><br>");
print("Все смотрят фильмы, у каждого есть любимые жанры, и у каждого есть друзья.<br>");
print("Наш проект решил обьеденить все в одном месте, теперь общайтесь и скачивайте файлы в одном месте.<br><br>");
$stats = cache_read("stats");
$registered = $stats["registered"];
print("Нас уже <b title=\"Да, у нас нет тикающего счетчика :)\">".$registered."</b><br><br>");
?>
<script language="javascript" type="text/javascript">
function login()
{
    jQuery.get("fastlogin.php" ,function (response) {
        jQuery("#login").empty();
        jQuery("#login").append(response);
    });
};
</script>

<div class="UILinkButton""><input type="submit" class="UILinkButton_A" onclick="login()" value="Вход" /><div class="UILinkButton_RW"><div class="UILinkButton_R"></div></div></div><div style="margin-left: 3px" class="UILinkButton""><form method=get action=signup.php><input type="submit" class="UILinkButton_A" value="Регистрация" /><div class="UILinkButton_RW"><div class="UILinkButton_R"></div></div></div></form>
<br><div id="login"></div>
<?php
if(!empty($_COOKIE['return'])&&!empty($_COOKIE['canret'])&&$_COOKIE['canret']==1)
print('<br><a href="'.urldecode($_COOKIE['return']).'">» Я не хочу пока регистрироваться, верните меня на запрошенную страницу</a>');
elseif(!empty($_COOKIE['return']))
print('<br>» Запрошенная страница доступна только зарегистрированным пользователям');
end_frame();
stdfoot();
?>
