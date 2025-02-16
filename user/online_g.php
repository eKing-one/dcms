<?php //网页标题
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
$set['title'] = '网站上的游客'; // 页标题
include_once '../sys/inc/thead.php';
title();
aut();

$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `guests` WHERE `date_last` > '" . (time() - 600) . "' AND `pereh` > '0'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `guests` WHERE `date_last` > '" . (time() - 600) . "' AND `pereh` > '0' ORDER BY `date_aut` DESC LIMIT $start, $set[p_str]");

echo "<table class='post'>";
if ($k_post == 0) {
    echo "   <tr>";
    echo "  <td class='p_t'>";
    echo "目前网站上没有游客";
    echo "  </td>";
    echo "   </tr>";
}
while ($guest = dbassoc($q)) {
    echo "   <tr>";
    if ($set['set_show_icon'] == 2) {
        echo "  <td class='icon48' rowspan='2'>";
        echo "<img src='/style/themes/$set[set_them]/guest.png' alt='' />";
        echo "  </td>";
    }
    echo "  <td class='p_t'>";
    echo "游客";
    echo "  </td>";
    echo "   </tr>";
    echo "   <tr>";
    echo "  <td class='p_m'>";
    echo "<span class=\"ank_n\">最后访问:</span> <span class=\"ank_d\">" . vremja($guest['date_last']) . "</span><br />";
    echo "<span class=\"ank_n\">访问次数:</span> <span class=\"ank_d\">$guest[pereh]</span><br />";
    if ($guest['ua'] != NULL) echo "<span class=\"ank_n\">UA:</span> <span class=\"ank_d\">$guest[ua]</span><br />";
    if (isset($user) && ($user['level'] > 0)) {
        if (user_access('guest_show_ip') && $guest['ip'] != 0) echo "<span class=\"ank_n\">IP:</span> <span class=\"ank_d\">{$guest['ip']}</span><br />";
        if (user_access('guest_show_ip') && opsos($guest['ip'])) echo "<span class=\"ank_n\">UA:</span> <span class=\"ank_d\">" . opsos($guest['ip']) . "</span><br />";
        if (otkuda($guest['url'])) echo "<span class=\"ank_n\">URL:</span> <span class=\"ank_d\"><a href='$guest[url]'>" . otkuda($guest['url']) . "</a></span><br />";
    }
    echo "  </td>";
    echo "   </tr>";
}
echo "</table>";
if ($k_page > 1) str("?", $k_page, $page); // 输出页数
include_once '../sys/inc/tfoot.php';
