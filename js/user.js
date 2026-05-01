var loading = "<img src=\"pic/upload.gif\" alt=\"Загрузка..\" />";

jQuery(function() {
    jQuery(".tab").click ( function(){
        if(jQuery(this).hasClass("active"))
            return;
        else
        {
            jQuery("#loading").html(loading);
            var user = jQuery("#body").attr("user");
            var act = jQuery(this).attr("id");
            jQuery(this).toggleClass("active");
            jQuery(this).siblings("span").removeClass("active");
            jQuery.post("user.php",{"user":user,"act":act},function (response) {
                jQuery("#body").empty();
                jQuery("#body").append(response);
                jQuery("#loading").empty();
            });
        }
    });
    jQuery('.zebra:even').css({backgroundColor: '#EEEEEE'});
    if(jQuery.browser && jQuery.browser.msie)
    {
        width = jQuery('#profile_right h2').width();
        if (width > 422)
            jQuery('#profile_right').width(width);
        else
        {
            jQuery('#profile_right').width("422");
            jQuery('#profile_container').width("686");
        }
    }
});

function togglepic(bu, picid, formid)
{
    var pic = document.getElementById(picid);
    var form = document.getElementById(formid);

    if(pic.src == bu + "/pic/plus.gif")
    {
        pic.src = bu + "/pic/minus.gif";
        form.value = "minus";
    }
    else
    {
        pic.src = bu + "/pic/plus.gif";
        form.value = "plus";
    }
}

var azWin = '     Ё               ё       АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдежзийклмнопрстуфхцчшщъыьэюя';
var AZ=azWin;
var b64s  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
var b64a  = b64s.split('');
function enBASE64(str)
{
    var a=Array(), i;
    for( i=0; i<str.length; i++ )
    {
        var cch=str.charCodeAt(i);
        if( cch>127 )
        {
            cch=AZ.indexOf(str.charAt(i))+163;
            if(cch<163)
                continue;
        }
        a.push(cch);
    };
    var s=Array(), lPos = a.length - a.length % 3 ;
    for(i=0;i<lPos;i+=3)
    {
        var t=(a[i]<<16)+(a[i+1]<<8)+a[i+2];
        s.push( b64a[(t>>18)&0x3f]+b64a[(t>>12)&0x3f]+b64a[(t>>6)&0x3f]+b64a[t&0x3f] );
    }
    switch ( a.length-lPos )
    {
        case 1 : var t=a[lPos]<<4;
        s.push(b64a[(t>>6)&0x3f]+b64a[t&0x3f]+'==');
        break;
        case 2 : var t=(a[lPos]<<10)+(a[lPos+1]<<2);
        s.push(b64a[(t>>12)&0x3f]+b64a[(t>>6)&0x3f]+b64a[t&0x3f]+'=');
        break;
    }
    return s.join('');
}

function wall_send(to, msg)
{
    var text = enBASE64(msg);
    if (text == ''){
        alert('Ошибка. Пустое сообщение.');
        return;
    }
    jQuery.post("wall.php",{"to":to,"text":text,"act":"send"},function (response) {
        jQuery("#wall").empty();
        jQuery("#wall").append(response);
    });
    document.wall.text.value = '';
};

function wall_del(id, to)
{
    jQuery.post("wall.php",{"post":id,"to":to,"act":"delete"},function (response) {
        jQuery("#wall").empty();
        jQuery("#wall").append(response);
    });
};

function present(from, to, amount) {
    jQuery.post("present.php",{"from":from,"to":to,"amount":amount},function (response) {
       alert(response);
    });
};

function ls(user)
{
    jQuery.post("user.php",{"user":user,"act":"pm"},function (response) {
        jQuery("#actions").empty();
        jQuery("#actions").append(response);
    });
};

function stat(user)
{
    jQuery.post("user.php",{"user":user,"act":"statistics"},function (response) {
        jQuery("#actions").empty();
        jQuery("#actions").append(response);
    });
};

function moderate(user)
{
    jQuery.post("user.php",{"user":user,"act":"moderate"},function (response) {
        jQuery("#actions").empty();
        jQuery("#actions").append(response);
    });
};

function addtofriends(user, type)
{
    jQuery.post("user.php",{"user":user,"type":type,"act":"addtofriends"},function (response) {
        jQuery("#actions").empty();
        jQuery("#actions").append(response);
    });
};
