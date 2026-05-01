<?
require_once("include/bittorrent.php");
dbconn();
stdhead("Помощь трекеру");
begin_frame("Нашему трекеру нужна ваша помощь");
print("<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\"><tr><td>");

print("За каждый рубль на ваш аккаунт будет начислено 2 бонуса :) Бонус пока можно обменять на трафик, но в ближайшие дни будет расширение функционала.");
print("</td></tr></table>");
end_frame();
begin_frame("Реквезиты");
print("<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">");
print("<tr>");
print("<td class=\"rowhead\">WebMoney</td><td align=\"left\">");
print("<b>R424582178941</b>&nbsp;-&nbsp;Эквивалент&nbsp;в&nbsp;рублях.");
print("<br><b>Z353530554487</b>&nbsp;-&nbsp;Эквивалент&nbsp;в&nbsp;долларах.");
print("<br><b>E242423324828</b>&nbsp;-&nbsp;Эквивалент&nbsp;в&nbsp;евро.");
print("<br><b>U798575084060</b>&nbsp;-&nbsp;Эквивалент&nbsp;в&nbsp;гривнах.");
print("</td></tr>");
print("<tr><td class=\"rowhead\">Яндекс.Деньги</td><td align=\"left\"><b><font color=\"green\">41001289828071</font></b></td></tr>");
print("<tr><td colspan=\"2\">После&nbsp;перевода&nbsp;пишем&nbsp;<a href=\"pmto-1\">сюда</a>,&nbsp;сумму&nbsp;и&nbsp;тип&nbsp;перевода.</td></tr>");
print("</table>");
end_frame();
stdfoot();
?>


