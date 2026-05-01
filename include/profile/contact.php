<?php

tr("Номер ICQ", "<input maxLength=\"30\" size=\"25\" name=\"icq\" value=\"" . ($CURUSER["icq"] ? $CURUSER['icq'] : '') . "\" >",1);
tr("Адрес сайта", "<input maxLength=\"30\" size=\"25\" name=\"website\" value=\"" . ($CURUSER["website"] ? $CURUSER['website'] : '') . "\" >",1);
tr($tracker_lang['my_mail'], "<input type=\"text\" name=\"email\" size=50 value=\"" . htmlspecialchars($CURUSER["email"]) . "\" />", 1);
print("<tr><td colspan=\"2\" align=left><b>Примечание:</b> Если вы смените ваш Email адрес, то вам придет запрос о подтверждении на ваш новый Email-адрес. Если вы не подтвердите письмо, то Email адрес не будет изменен.</td></tr>\n");

?>