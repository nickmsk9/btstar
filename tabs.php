<?
require_once("include/bittorrent.php");
dbconn();
stdhead("Настройки профиля");
?>
<div id="tabs">
<span class="tab active" id="info">Общее</span>
<span class="tab" id="friends">Друзья</span>
<span class="tab" id="downloaded">Скачал</span>
<span class="tab" id="uploaded">Загрузил</span>

<span class="tab" id="downloading">Сейчас качает</span>
<span class="tab" id="uploading">Сейчас раздает</span>
<span id="loading"></span>
<div id="body">
<table width="100%" border="1" cellspacing="0" cellpadding="5">
<tr><td colspan="2"><b>Изменить пароль</b></td></tr>
<?
tr("Старый пароль", "<input type=\"password\" name=\"oldpassword\" size=\"50\" />", 1);
tr("Сменить пароль", "<input type=\"password\" name=\"chpassword\" size=\"50\" />", 1);
tr("Пароль еще раз", "<input type=\"password\" name=\"passagain\" size=\"50\" />", 1);
?>
</table>
</div>
</div>

