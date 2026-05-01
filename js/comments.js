var AZ = '     Ё               ё       АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдежзийклмнопрстуфхцчшщъыьэюя'
var b64s  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'
var b64a  = b64s.split('')

function enBASE64(str) {
    var a=Array(), i
    for( i=0; i<str.length; i++ ){
        var cch=str.charCodeAt(i)
        if( cch>127 ){  cch=AZ.indexOf(str.charAt(i))+163; if(cch<163) continue; }
        a.push(cch)
    };
    var s=Array(), lPos = a.length - a.length % 3
    for(i=0;i<lPos;i+=3){
        var t=(a[i]<<16)+(a[i+1]<<8)+a[i+2]
        s.push( b64a[(t>>18)&0x3f]+b64a[(t>>12)&0x3f]+b64a[(t>>6)&0x3f]+b64a[t&0x3f] )
    }
    switch ( a.length-lPos ) {
        case 1 : var t=a[lPos]<<4; s.push(b64a[(t>>6)&0x3f]+b64a[t&0x3f]+'=='); break
        case 2 : var t=(a[lPos]<<10)+(a[lPos+1]<<2); s.push(b64a[(t>>12)&0x3f]+b64a[(t>>6)&0x3f]+b64a[t&0x3f]+'='); break
    }
    return s.join('')
}

jQuery(function() {
    var loading = "<div align=\"center\" style=\"padding:20px;\"><img src=\"pic/progress7.gif\" alt=\"Загрузка..\" /></div";

    jQuery("a.add_comment").click ( function(){
        var torrent = jQuery(this).attr("id");
        var comment = jQuery(this).attr("comment");
        var method = jQuery(this).attr("method");
        var text = enBASE64(document.comment.text.value);
        if (text == ''){
            alert('Ошибка. Пустое сообщение.');
            return;
        }
        jQuery("#takecomment").empty();
        jQuery("#takecomment").html(loading);
        jQuery.post("takecomment.php",{"tid":torrent,"cid":comment,"method":method,"text":text,"action":"add"},function (response) {
        jQuery.getScript("js/comments.js");
        jQuery("#takecomment").empty();
        jQuery("#takecomment").append(response);
        document.comment.text.value = '';
        });
    });

    jQuery("span.comment_delete").click ( function(){
        var torrent = jQuery(this).attr("torrent");
        var comment = jQuery(this).attr("comment");
        jQuery.post("takecomment.php",{"tid":torrent,"cid":comment,"action":"delete"},function (response) {
        jQuery("#takecomment").empty();
        jQuery.getScript("js/comments.js");
        jQuery("#takecomment").append(response);
        });
    });

    jQuery("a.comment_edit").click ( function(){
        var torrent = jQuery(this).attr("torrent");
        var comment = jQuery(this).attr("comment");
        jQuery("#takecomment").empty();
        jQuery("#takecomment").html(loading);
        jQuery.post("takecomment.php",{"tid":torrent,"cid":comment,"action":"edit"},function (response) {
        jQuery("#takecomment").empty();
        jQuery.getScript("js/comments.js");
        jQuery("#takecomment").append(response);
        });
    });

    jQuery("span.comment_quote").click ( function(){
        var torrent = jQuery(this).attr("torrent");
        var comment = jQuery(this).attr("comment");
        jQuery("#takecomment").empty();
        jQuery("#takecomment").html(loading);
        jQuery.post("takecomment.php",{"tid":torrent,"cid":comment,"action":"quote"},function (response) {
        jQuery("#takecomment").empty();
        jQuery.getScript("js/comments.js");
        jQuery("#takecomment").append(response);
        });
    });

    jQuery("span.comment_original").click ( function(){
        var torrent = jQuery(this).attr("torrent");
        var comment = jQuery(this).attr("comment");
        jQuery("#takecomment").empty();
        jQuery("#takecomment").html(loading);
        jQuery.post("takecomment.php",{"tid":torrent,"cid":comment,"action":"original"},function (response) {
        jQuery("#takecomment").empty();
        jQuery.getScript("js/comments.js");
        jQuery("#takecomment").append(response);
        });
    });
});