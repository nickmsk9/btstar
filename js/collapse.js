function getCookie(name)
{
    var start = document.cookie.indexOf(name + "=");
    var len = start + name.length + 1;
    if ((!start) && (name != document.cookie.substring(0, name.length)))
    {
        return null;
    }
    if (start == -1)
        return null;
    var end = document.cookie.indexOf(';', len);
    if (end == -1)
        end = document.cookie.length;
    return unescape(document.cookie.substring(len, end));
}

function setCookie(name, value, expires, path, domain, secure)
{
    var today = new Date();
    today.setTime(today.getTime());
    if (expires)
        expires = expires * 1000 * 60 * 60 * 24;
    var expires_date = new Date(today.getTime() + (expires));
    document.cookie = name+'='+escape(value) + ((expires) ? ';expires='+expires_date.toGMTString() : '') + ((path) ? ';path=' + path : '') + ((domain) ? ';domain=' + domain : '') + ((secure) ? ';secure' : '');
}

function implode( glue, pieces )
{
    return ((pieces instanceof Array) ? pieces.join(glue) : pieces);
}

function explode( delimiter, string )
{
    return string.toString().split(delimiter.toString());
}

function in_array(needle, haystack, strict)
{
    var found = false, key, strict = !!strict;
    for (key in haystack)
    {
        if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle))
        {
            found = true;
            break;
        }
    }
    return found;
}

function collapse(block)
{
    jQuery("#" + block + " div").slideToggle("slow");
    jQuery("#" + block + " em").toggleClass("close");
    var collapsed = jQuery("em.close").get();
    var arr = new Array();
    jQuery(collapsed).each(
        function()
        {
            arr.push(jQuery(this).attr("lang"));
        }
    );
    data = implode('|', arr);
    setCookie('collapsed', data, 365);
}

jQuery(document).ready(function() {
    var cookie = explode("|", getCookie('collapsed'));
    var all = jQuery("em.open").get();
    jQuery(all).each(
        function()
        {
            value = jQuery(this).attr("lang");
            if (in_array(value, cookie))
            {
                jQuery("#" + value + " em").toggleClass("close");
                jQuery("#" + value + " div").hide();
            }
        }
    );
});