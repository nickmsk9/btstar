<?php
if(!defined('IN_TRACKER'))
  die('Hacking attempt!');
  
function browsepager($rpp, $count, $href, $opts = array()) {
    $pages = ceil($count / $rpp);

    if (!$opts["lastpagedefault"])
        $pagedefault = 0;
    else {
        $pagedefault = floor(($count - 1) / $rpp);
        if ($pagedefault < 0)
            $pagedefault = 0;
    }

    if (isset($_GET["page"])) {
        $page = (int) $_GET["page"];
        if ($page < 0)
            $page = $pagedefault;
    }
    else
        $page = $pagedefault;

       $pager = "<td class=\"pager\">Страницы:</td><td class=\"pagebr\">&nbsp;</td>";

    $mp = $pages - 1;
    $as = "<b>«</b>";
    if ($page >= 1) {
        $pager .= "<td class=\"pager\">";
        $pager .= "<a href=\"{$href}page=" . ($page - 1) . "\" onclick=\"return pageswitcher(" . ($page - 1) . ")\" style=\"text-decoration: none;\">$as</a>";
        $pager .= "</td><td class=\"pagebr\">&nbsp;</td>";
    }

    $as = "<b>»</b>";
    if ($page < $mp && $mp >= 0) {
        $pager2 .= "<td class=\"pager\">";
        $pager2 .= "<a href=\"{$href}page=" . ($page + 1) . "\" onclick=\"return pageswitcher(" . ($page + 1) . ")\" style=\"text-decoration: none;\">$as</a>";
        $pager2 .= "</td>$bregs";
    }else     $pager2 .= $bregs;

    if ($count) {
        $pagerarr = array();
        $dotted = 0;
        $dotspace = 3;
        $dotend = $pages - $dotspace;
        $curdotend = $page - $dotspace;
        $curdotstart = $page + $dotspace;
        for ($i = 0; $i < $pages; $i++) {
            if (($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend)) {
                if (!$dotted)
                   $pagerarr[] = "<td class=\"pager\">...</td><td class=\"pagebr\">&nbsp;</td>";
                $dotted = 1;
                continue;
            }
            $dotted = 0;
            $start = $i * $rpp + 1;
            $end = $start + $rpp - 1;
            if ($end > $count)
                $end = $count;

             $text = $i+1;
            if ($i != $page)
                $pagerarr[] = "<td class=\"pager\"><a title=\"$start&nbsp;-&nbsp;$end\" href=\"{$href}page=$i\" onclick=\"return pageswitcher($i)\" style=\"text-decoration: none;\"><b>$text</b></a></td><td class=\"pagebr\">&nbsp;</td>";
            else
                $pagerarr[] = "<td class=\"highlight\"><b>$text</b></td><td class=\"pagebr\">&nbsp;</td>";

                  }
        $pagerstr = join("", $pagerarr);
        $pagertop = "<table class=\"main\"><tr>$pager $pagerstr $pager2</tr></table>\n";
        $pagerbottom = "Всего $count на $i страницах по $rpp на каждой странице.<br /><br /><table class=\"main\">$pager $pagerstr $pager2</table>\n";
    }
    else {
        $pagertop = $pager;
        $pagerbottom = $pagertop;
    }

    $start = $page * $rpp;

    return array($pagertop, $pagerbottom, "LIMIT $start,$rpp");
}

?>