<? //网页标题
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_show_adm', null, 'index.php?' . SID);
adm_check();
$set['title'] = '管理工作'; // заголовок страницы
include_once '../sys/inc/thead.php';
title();
aut();
$k_post = dbresult(dbquery("SELECT COUNT(`user`.`id`) FROM `user` LEFT JOIN `user_group` ON `user`.`group_access` = `user_group`.`id` WHERE `user_group`.`level` != 0 AND `user_group`.`level` IS NOT NULL"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo "<table class='post'>";
if ($k_post == 0) {
    echo "   <tr>";
    echo "  <td class='p_t'>";
    echo "没有结果";
    echo "  </td>";
    echo "   </tr>";
}
$q = dbquery("SELECT `user`.`id` FROM `user` LEFT JOIN `user_group` ON `user`.`group_access` = `user_group`.`id` WHERE `user_group`.`level` != 0 AND `user_group`.`level` IS NOT NULL ORDER BY `user_group`.`level` DESC LIMIT $start, $set[p_str]");
while ($ank = dbassoc($q)) {
    $ank = user::get_user($ank['id']);
    echo "   <tr>";
    if ($set['set_show_icon'] == 2) {
        echo "  <td class='icon48' rowspan='2'>";
        user::avatar($ank['id']);
        echo "  </td>";
    } elseif ($set['set_show_icon'] == 1) {
        echo "  <td class='icon14'>";
        echo "" . user::avatar($ank['id']) . "";
        echo "  </td>";
    }
    echo "  <td class='p_t'>";
    if (user_access('adm_log_read') && $ank['level'] != 0 && ($ank['id'] == $user['id'] || $ank['level'] < $user['level']))
        echo "<a href='adm_log.php?id=$ank[id]'>$ank[nick]</a> ($ank[group_name])" . online($ank['id']) . "";
    else
        echo user::nick($ank['id'],1,1,0)." ($ank[group_name])";
    echo "  </td>";
    echo "   </tr>";
    echo "   <tr>";
    if ($set['set_show_icon'] == 1) echo "  <td class='p_m' colspan='2'>";
    else echo "  <td class='p_m'>";
    echo "<span class=\"ank_n\">性别:</span> <span class=\"ank_d\">" . (($ank['pol'] == 1) ? '男' : '女') . "</span><br />";
    $adm_log_c_all = dbresult(dbquery("SELECT COUNT(*) FROM `admin_log` WHERE `id_user` = '$ank[id]'"), 0);
    $mes = mktime(0, 0, 0, date('m') - 1); // время месяц назад
    $adm_log_c_mes = dbresult(dbquery("SELECT COUNT(*) FROM `admin_log` WHERE `id_user` = '$ank[id]' AND `time` > '$mes'"), 0);
    echo "<span class='ank_n'>所有活动:</span> <span class='ank_d'>$adm_log_c_all</span><br />";
    echo "<span class='ank_n'>每月活动:</span> <span class='ank_d'>$adm_log_c_mes</span><br />";
    echo "<span class=\"ank_n\">最后登录:</span> <span class=\"ank_d\">" . vremja($ank['date_last']) . "</span><br />";
    if (isset($user) && ($user['level'] > $ank['level'] || $user['level'] == 4)) {
        echo "<a href='/adm_panel/user.php?id=$ank[id]'>编辑个人资料</a><br />";
    }
    echo "  </td>";
    echo "   </tr>";
}
echo "</table>";
if ($k_page > 1) str("?", $k_page, $page); // 输出页数
if (user_access('adm_panel_show')) {
    echo "<div class='foot'>";
    echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
    echo "</div>";
}
include_once '../sys/inc/tfoot.php';

?>