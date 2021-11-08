<? //网页标题


include_once 'sys/inc/start.php';


include_once 'sys/inc/compress.php';


include_once 'sys/inc/sess.php';


include_once 'sys/inc/home.php';


include_once 'sys/inc/settings.php';


include_once 'sys/inc/db_connect.php';


include_once 'sys/inc/ipua.php';


include_once 'sys/inc/fnc.php';


include_once 'sys/inc/user.php';


$set['title'] = 'Гости на сайте'; // заголовок страницы


include_once 'sys/inc/thead.php';


title();


aut();


$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `guests` WHERE `date_last` > '" . (time() - 600) . "' AND `pereh` > '0'"), 0);


$k_page = k_page($k_post, $set['p_str']);


$page = page($k_page);


$start = $set['p_str'] * $page - $set['p_str'];


$q = dbquery("SELECT * FROM `guests` WHERE `date_last` > '" . (time() - 600) . "' AND `pereh` > '0' ORDER BY `date_aut` DESC LIMIT $start, $set[p_str]");


echo "<table class='post'>\n";


if ($k_post == 0) {


    echo "   <tr>\n";


    echo "  <td class='p_t'>\n";


    echo "网站上没有客人\n";


    echo "  </td>\n";


    echo "   </tr>\n";


}


while ($guest = dbassoc($q)) {


    echo "   <tr>\n";


    if ($set['set_show_icon'] == 2) {


        echo "  <td class='icon48' rowspan='2'>\n";


        echo "<img src='/style/themes/$set[set_them]/guest.png' alt='' />";


        echo "  </td>\n";


    }


    echo "  <td class='p_t'>\n";


    echo "客人\n";


    echo "  </td>\n";


    echo "   </tr>\n";


    echo "   <tr>\n";


    echo "  <td class='p_m'>\n";


    echo "<span class=\"ank_n\">参观后:</span> <span class=\"ank_d\">" . vremja($guest['date_last']) . "</span><br />\n";


    echo "<span class=\"ank_n\">过渡时期:</span> <span class=\"ank_d\">$guest[pereh]</span><br />\n";


    if ($guest['ua'] != NULL) echo "<span class=\"ank_n\">UA:</span> <span class=\"ank_d\">$guest[ua]</span><br />\n";


    if (isset($user) && ($user['level'] > 0)) {


        if (user_access('guest_show_ip') && $guest['ip'] != 0) echo "<span class=\"ank_n\">IP:</span> <span class=\"ank_d\">" . long2ip($guest['ip']) . "</span><br />\n";


        if (user_access('guest_show_ip') && opsos($guest['ip'])) echo "<span class=\"ank_n\">普罗旺斯:</span> <span class=\"ank_d\">" . opsos($guest['ip']) . "</span><br />\n";


        if (otkuda($guest['url'])) echo "<span class=\"ank_n\">URL:</span> <span class=\"ank_d\"><a href='$guest[url]'>" . otkuda($guest['url']) . "</a></span><br />\n";


    }


    echo "  </td>\n";


    echo "   </tr>\n";


}


echo "</table>\n";


if ($k_page > 1) str("?", $k_page, $page); // Вывод страниц


include_once 'sys/inc/tfoot.php';


?>