var loading = "<img src=\"pic/upload.gif\" alt=\"Загрузка..\" />";

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

function wall_send(uid, id, msg)
{
    var text = enBASE64(msg);
    if (text == ''){
        alert('Ошибка. Пустое сообщение.');
        return;
    }
    jQuery.post("nwall.php",{"uid":uid,"id":id,"text":text,"act":"send"},function (response) {
        jQuery("#wall").empty();
        jQuery("#wall").append(response);
    });
    document.wall.text.value = '';
};

function wall_del(id)
{
    jQuery.post("nwall.php",{"id":id,"act":"delete"},function (response) {
        jQuery("#wall").empty();
        jQuery("#wall").append(response);
    });
};