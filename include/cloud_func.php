<?
function get_tags() {
if (cache_check("tags", 300))
    $res = cache_read("tags");
else {
    $res = sql_query("SELECT name, howmuch FROM tags WHERE howmuch > 0 ORDER BY id DESC");
	    $tags_cache = array();
    while ($cache_data = mysql_fetch_array($res))
        $tags_cache[] = $cache_data;

    cache_write("tags", $tags_cache);
    $res = $tags_cache;
}
       foreach($res as $row) {
            $arr[$row['name']] = $row['howmuch'];

    }
    return $arr;
}

function cloud($small, $big, $colour = false) {
    $tags = get_tags();
    if (empty($tags))
        $data = "Нет тэгов";
    else {
        $minimum_count = min(array_values($tags));
        $maximum_count = max(array_values($tags));
        $spread = $maximum_count - $minimum_count;

        if($spread == 0) {$spread = 1;}

        $data = '';

        $cloud = array();

        foreach ($tags as $tag => $count) {
            $size = $small + ($count - $minimum_count) * ($big - $small) / $spread;
            $colours = array('#003EFF', '#0000FF', '#7EB6FF', '#0099CC', '#62B1F6'); // Диапазон цветов (только для статичного облака)
            $cloud[] = "<a href=\"browse.php?tag=" . urlencode($tag) . "&cat=0&incldead=1\" style=\"".($colour ? "color:".$colours[mt_rand(0, 4)]."; " : "")."font-size:". floor($size) . "px;\" rel=\"tag\" title=\"Содержится в $count торрентах\">"
            . htmlentities($tag, ENT_QUOTES, "cp1251") . "</a>\n";
        }
        $data = join($cloud);
    }
    return $data;
}

function flash_cloud($width, $height, $small, $big) {
    $divname = 'tagcloud';
    $soname = 'settings';
    $movie = '/swf/tagcloud.swf'; // Путь до флэш-файла
    $path = '/js/'; // Директория js-скриптов
    $options['bgcolor'] = 'FFFFFF'; // Цвет фона (HEX)
    $options['trans'] = 'true'; // Прозрачность (true - включена, false - выключена)
    $options['tcolor'] = '000000'; // Первый цвет тэгов (HEX)
    $options['tcolor2'] = '111111'; // Второй цвет тэгов (HEX)
    $options['hicolor'] = '222222'; // Цвет перемешки (HEX)
    $options['speed'] = '50'; // Скорость вращения
    $options['distr'] = 'true';
    $options['mode'] = 'tags';

    ob_start();
    echo cloud($small, $big);
    $tags = urlencode( str_replace( "&nbsp;", " ", ob_get_clean() ) );

    $flashtag .= '<script type="text/javascript" src="'.$path.'swfobject.js"></script>';
    $flashtag .= '<div id="'.$divname.'"><p style="display:none;">';
    $flashtag .= urldecode($tags);
    $flashtag .= '</p></div>';
    $flashtag .= '<script type="text/javascript">';
    $flashtag .= 'var rnumber = Math.floor(Math.random()*9999999);';
    $flashtag .= 'var '.$soname.' = new SWFObject("'.$movie.'?r="+rnumber, "tagcloudflash", "'.$width.'", "'.$height.'", "9", "#'.$options['bgcolor'].'");';
    if( $options['trans'] == 'true' ){
        $flashtag .= $soname.'.addParam("wmode", "transparent");';
    }
    $flashtag .= $soname.'.addParam("allowScriptAccess", "always");';
    $flashtag .= $soname.'.addVariable("tcolor", "0x'.$options['tcolor'].'");';
    $flashtag .= $soname.'.addVariable("tcolor2", "0x' . ($options['tcolor2'] == "" ? $options['tcolor'] : $options['tcolor2']) . '");';
    $flashtag .= $soname.'.addVariable("hicolor", "0x' . ($options['hicolor'] == "" ? $options['tcolor'] : $options['hicolor']) . '");';
    $flashtag .= $soname.'.addVariable("tspeed", "'.$options['speed'].'");';
    $flashtag .= $soname.'.addVariable("distr", "'.$options['distr'].'");';
    $flashtag .= $soname.'.addVariable("mode", "'.$options['mode'].'");';
    $flashtag .= $soname.'.addVariable("tagcloud", "'.urlencode('<tags>') . $tags . urlencode('</tags>').'");';
    $flashtag .= $soname.'.write("'.$divname.'");';
    $flashtag .= '</script>';

    return $flashtag;
}

function simple_cloud($small, $big) {
    $data = '<style>
        #tag_cloud a {padding : 3px;text-decoration: none;font-family : verdana;font-weight: normal;}
        #tag_cloud a:link {text-decoration: none;border : 1px solid transparent;}
        #tag_cloud a:visited {border : 1px solid transparent;}
        #tag_cloud a:hover {background: #ddd;border : 1px solid #bbb;}
        #tag_cloud a:active {background : #fff;border : 1px solid transparent;}
        #tag_cloud p {line-height : 28px;text-align : justify;}
        </style>';
    $data .= '<div id="tag_cloud">';
    $data .= '<p>'.cloud($small, $big, true).'</p>';
    $data .= '</div>';
    return $data;
}
?>