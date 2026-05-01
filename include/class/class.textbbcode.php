<?php
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');
  
  function textbbcode($form, $name, $text='') { 
?> 
<table border="0" width=100% cellspacing="0" cellpadding="0"> 
<tr> 
<td colspan="2"> 
<script type=text/javascript> 
var text_enter_url       = "Введите полный URL ссылки"; 
var text_enter_page      = "Введите номер страницы"; 
var text_enter_url_name  = "Введите название сайта"; 
var text_enter_page_name = "Введите описание ссылки"; 
var text_enter_image    = "Введите полный URL изображения"; 
var text_enter_email    = "Введите e-mail адрес"; 
var text_code           = "Использование: [code] Здесь Ваш код.. [/code]"; 
var text_quote          = "Использование: [quote] Здесь Ваша Цитата.. [/quote]"; 
var error_no_url        = "Вы должны ввести URL"; 
var error_no_title      = "Вы должны ввести название"; 
var error_no_email      = "Вы должны ввести e-mail адрес"; 
var prompt_start        = "Введите текст для форматирования"; 
var img_title           = "Введите по какому краю выравнивать картинку (left, center, right)"; 
var email_title          = "Введите описание ссылки (необязательно)"; 
var text_pages          = "Страница"; 
var image_align          = "left"; 

var selField  = "<?=$name;?>"; 
var fombj    = document.<?=$form;?>; 

function smiley ( text ){ 
    doInsert(' ' + text + ' ', '', false); 

    document.getElementById('dle_emo').style.visibility = "hidden"; 
    document.getElementById('dle_emo').style.display    = "none"; 
    ie_range_cache = null; 
} 
</script> 
<script type="text/javascript" src="js/bbcodes.js"></script> 
<div style="height:25px; border:1px solid #BBB; background-image:url('pic/bbcodes/bg.gif')">
<div id="b_b" class="editor_button" onclick="simpletag('b')"><img title="Полужирный" src="pic/bbcodes/b.gif" width="23" height="25" border="0"></div> 
<div id="b_i" class="editor_button" onclick="simpletag('i')"><img title="Наклонный текст" src="pic/bbcodes/i.gif" width="23" height="25" border="0"></div> 
<div id="b_u" class="editor_button" onclick="simpletag('u')"><img title="Подчеркнутый текст" src="pic/bbcodes/u.gif" width="23" height="25" border="0"></div> 
<div id="b_s" class="editor_button" onclick="simpletag('s')"><img title="Зачеркнутый текст" src="pic/bbcodes/s.gif" width="23" height="25" border="0"></div> 
<div class="editor_button"><img src="pic/bbcodes/brkspace.gif" width="5" height="25" border="0"></div> 

<div id="b_left" class="editor_button" onclick="simpletag('left')"><img title="Выравнивание по левому краю" src="pic/bbcodes/l.gif" width="23" height="25" border="0"></div> 
<div id="b_center" class="editor_button" onclick="simpletag('center')"><img title="По центру" src="pic/bbcodes/c.gif" width="23" height="25" border="0"></div> 
<div id="b_right"class="editor_button" onclick="simpletag('right')"><img title="Выравнивание по правому краю" src="pic/bbcodes/r.gif" width="23" height="25" border="0"></div> 
<div class="editor_button"><img src="pic/bbcodes/brkspace.gif" width="5" height="25" border="0"></div> 
<div id="b_emo" class="editor_button"  onclick="ins_emo();"><img title="Вставка смайликов" src="pic/bbcodes/emo.gif" width="23" height="25" border="0"></div> 
<div class="editor_button"  onclick="tag_url()"><img title="Вставка ссылки" src="pic/bbcodes/link.gif" width="23" height="25" border="0"></div><div class="editor_button"  onclick="tag_leech()"><img title="Вставка защищенной ссылки" src="pic/bbcodes/leech.gif" width="23" height="25" border="0"></div> 
<div class="editor_button"  onclick="tag_email()"><img title="Вставка E-Mail" src="pic/bbcodes/email.gif" width="23" height="25" border="0"></div> 
<div id="b_color" class="editor_button" onclick="ins_color();"><img src="pic/bbcodes/color.gif" width="23" height="25" border="0"></div> 
<div class="editor_button"><img src="pic/bbcodes/brkspace.gif" width="5" height="25" border="0"></div> 
<div id="b_hide" class="editor_button" onclick="simpletag('hide')"><img title="Скрытый текст" src="pic/bbcodes/hide.gif" width="23" height="25" border="0"></div> 
<div id="b_quote" class="editor_button" onclick="simpletag('quote')"><img title="Вставка цитаты" src="pic/bbcodes/quote.gif" width="23" height="25" border="0"></div> 
<div class="editor_button" onclick="translit()"><img title="Преобразовать выбранный текст из транслитерации в кириллицу" src="pic/bbcodes/translit.gif" width="23" height="25" border="0"></div> 
<div class="editbclose" onclick="closeall()"><img title="Закрыть все открытые теги" src="pic/bbcodes/close.gif" width="23" height="25" border="0"></div> 
</div> 
<iframe width="154" height="104" id="cp" src="pic/bbcodes/color.html" frameborder="0" vspace="0" hspace="0" marginwidth="0" marginheight="0" scrolling="no" style="visibility:hidden; display: none; position: absolute;"></iframe> 
<div id="dle_emo" style="visibility:hidden; display: none; position: absolute; width:140px; height: 124px; overflow: auto; border: 1px solid #BBB; background:#E9E8F2;filter: alpha(opacity=95, enabled=1) progid:DXImageTransform.Microsoft.Shadow(color=#CACACA,direction=135,strength=3);">
<table cellpadding="0" cellspacing="0" border="0" width="120"><tr><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':wink:'); return false;"><img style="border: none;" alt="wink" src="pic/bbcodes/emo/wink.gif" /></a></td><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':winked:'); return false;"><img style="border: none;" alt="winked" src="pic/bbcodes/emo/winked.gif" /></a></td><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':smile:'); return false;"><img style="border: none;" alt="smile" src="pic/bbcodes/emo/smile.gif" /></a></td></tr><tr><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':am:'); return false;"><img style="border: none;" alt="am" src="pic/bbcodes/emo/am.gif" /></a></td><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':belay:'); return false;"><img style="border: none;" alt="belay" src="pic/bbcodes/emo/belay.gif" /></a></td><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':feel:'); return false;"><img style="border: none;" alt="feel" src="pic/bbcodes/emo/feel.gif" /></a></td></tr><tr><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':fellow:'); return false;"><img style="border: none;" alt="fellow" src="pic/bbcodes/emo/fellow.gif" /></a></td><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':laughing:'); return false;"><img style="border: none;" alt="laughing" src="pic/bbcodes/emo/laughing.gif" /></a></td><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':lol:'); return false;"><img style="border: none;" alt="lol" src="pic/bbcodes/emo/lol.gif" /></a></td></tr><tr><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':love:'); return false;"><img style="border: none;" alt="love" src="pic/bbcodes/emo/love.gif" /></a></td><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':no:'); return false;"><img style="border: none;" alt="no" src="pic/bbcodes/emo/no.gif" /></a></td><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':recourse:'); return false;"><img style="border: none;" alt="recourse" src="pic/bbcodes/emo/recourse.gif" /></a></td></tr><tr><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':request:'); return false;"><img style="border: none;" alt="request" src="pic/bbcodes/emo/request.gif" /></a></td><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':sad:'); return false;"><img style="border: none;" alt="sad" src="pic/bbcodes/emo/sad.gif" /></a></td><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':tongue:'); return false;"><img style="border: none;" alt="tongue" src="pic/bbcodes/emo/tongue.gif" /></a></td></tr><tr><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':wassat:'); return false;"><img style="border: none;" alt="wassat" src="pic/bbcodes/emo/wassat.gif" /></a></td><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':crying:'); return false;"><img style="border: none;" alt="crying" src="pic/bbcodes/emo/crying.gif" /></a></td><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':what:'); return false;"><img style="border: none;" alt="what" src="pic/bbcodes/emo/what.gif" /></a></td></tr><tr><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':bully:'); return false;"><img style="border: none;" alt="bully" src="pic/bbcodes/emo/bully.gif" /></a></td><td style="padding:2px;" align="center"><a href="#" onClick="smiley(':angry:'); return false;"><img style="border: none;" alt="angry" src="pic/bbcodes/emo/angry.gif" /></a></td></tr></table></div></td>
</tr>
<tr>
<td colspan="2"><textarea name="<?= $name ?>" id="<?= $name ?>" class="f_textarea" onclick="setNewField(this.name, document.getElementById( '<?= $form ?>' ))" /><?=$text?></textarea>
</td>
</tr>
</table>
<?
}