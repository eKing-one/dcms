<?
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
user_access('adm_log_read', null, 'index.php?' . SID);
adm_check();
$set['title'] = '网站日志';
include_once '../sys/inc/thead.php';
title();
err();
aut();
if (isset($_GET['id'])) $ank = user::get_user($_GET['id']);
else $ank = false;
if ($ank && user_access('adm_log_read') && ($ank['id'] == $user['id'] || $ank['level'] < $user['level'])) {
    echo user::nick($ank['id'],1,0,0)."($ank[group_name])<br />";
    $adm_log_c_all = dbresult(dbquery("SELECT COUNT(*) FROM `admin_log` WHERE `id_user` = '$ank[id]'"), 0);
    $mes = mktime(0, 0, 0, date('m') - 1); // время месяц назад
    $adm_log_c_mes = dbresult(dbquery("SELECT COUNT(*) FROM `admin_log` WHERE `id_user` = '$ank[id]' AND `time` > '$mes'"), 0);
    echo "<span class='ank_n'>所有活动:</span> <span class='ank_d'>$adm_log_c_all</span><br />";
    echo "<span class='ank_n'>每月活动:</span> <span class='ank_d'>$adm_log_c_mes</span><br />";
} else {
    $adm_log_c_all = dbresult(dbquery("SELECT COUNT(*) FROM `admin_log`"), 0);
    $mes = mktime(0, 0, 0, date('m') - 1); // время месяц назад
    $adm_log_c_mes = dbresult(dbquery("SELECT COUNT(*) FROM `admin_log` WHERE `time` > '$mes'"), 0);
    echo "<span class='ank_n'>所有活动:</span> <span class='ank_d'>$adm_log_c_all</span><br />";
    echo "<span class='ank_n'>每月活动:</span> <span class='ank_d'>$adm_log_c_mes</span><br />";
}
if (isset($_GET['id_mod']) && isset($_GET['id_act']) && dbresult(dbquery("SELECT COUNT(*) FROM `admin_log` WHERE `mod` = '" . intval($_GET['id_mod']) . "' AND `act` = '" . intval($_GET['id_act']) . "'" . ($ank ? " AND `id_user` = '$ank[id]'" : null)), 0) != 0) {
    $mod = dbassoc(dbquery("SELECT * FROM `admin_log_mod` WHERE `id` = '" . intval($_GET['id_mod']) . "' LIMIT 1"));
    $act = dbassoc(dbquery("SELECT * FROM `admin_log_act` WHERE `id` = '" . intval($_GET['id_act']) . "' LIMIT 1"));
    $k_post = dbresult(dbquery("SELECT COUNT(*) FROM `admin_log` WHERE `mod` = '$mod[id]' AND `act` = '$act[id]'" . ($ank ? " AND `admin_log`.`id_user` = '$ank[id]'" : null)), 0);
    $k_page = k_page($k_post, $set['p_str']);
    $page = page($k_page);
    $start = $set['p_str'] * $page - $set['p_str'];
    echo "<table class='post'>";
    if ($k_post == 0) {
        echo "   <tr>";
        echo "  <td class='p_t'>";
        echo "没有活动";
        echo "  </td>";
        echo "   </tr>";
    }
    $q = dbquery("SELECT * FROM `admin_log` WHERE `mod` = '$mod[id]' AND `act` = '$act[id]'" . ($ank ? " AND `admin_log`.`id_user` = '$ank[id]'" : null) . " ORDER BY id DESC LIMIT $start, $set[p_str]");
    while ($post = dbassoc($q)) {
        $ank2 = user::get_user($post['id_user']);
        echo "   <tr>";
        if ($set['set_show_icon'] == 2) {
            echo "  <td class='icon48' rowspan='2'>";
            user::avatar($ank2['id']);
            echo "  </td>";
        } elseif ($set['set_show_icon'] == 1) {
            echo "  <td class='icon14'>";
            echo "" . user::avatar($ank2['id']) . "";
            echo "  </td>";
        }
        echo "  <td class='p_t'>";
        echo "" . user::nick($ank2['id'],1,1,0) . " (" . vremja($post['time']) . ")";
        echo "  </td>";
        echo "   </tr>";
        echo "   <tr>";
        if ($set['set_show_icon'] == 1) echo "  <td class='p_m' colspan='2'>";
        else echo "  <td class='p_m'>";
        echo output_text($post['opis']) . "<br />";
        echo "  </td>";
        echo "   </tr>";
    }
    echo "</table>";
    if ($k_page > 1) str('?id_mod=' . $mod['id'] . '&amp;id_act=' . $act['id'] . '&amp;', $k_page, $page); // 输出页数
    echo "&laquo;<a href='?id_mod=$mod[id]" . ($ank ? "&amp;id=$ank[id]" : null) . "'>行动清单</a><br />";
    echo "&laquo;<a href='?$passgen" . ($ank ? "&amp;id=$ank[id]" : null) . "'>模块列表</a><br />";
} elseif (isset($_GET['id_mod']) && dbresult(dbquery("SELECT COUNT(*) FROM `admin_log` WHERE `mod` = '" . intval($_GET['id_mod']) . "'" . ($ank ? " AND `id_user` = '$ank[id]'" : null)), 0) != 0) {
    // 模块中的操作
    $mod = dbassoc(dbquery("SELECT * FROM `admin_log_mod` WHERE `id` = '" . intval($_GET['id_mod']) . "' LIMIT 1"));
    $q = dbquery("SELECT `admin_log_act`.`name`, `admin_log_act`.`id`, COUNT(`admin_log`.`id`) AS `count` FROM `admin_log` LEFT JOIN `admin_log_act` ON `admin_log`.`act` = `admin_log_act`.`id` WHERE `admin_log`.`mod` = '$mod[id]'" . ($ank ? " AND `admin_log`.`id_user` = '$ank[id]'" : null) . " GROUP BY `admin_log`.`act`");
    echo "<div class='menu'>";
    if (dbrows($q) == 0) echo "模块中没有操作'$mod[name]'";
    while ($act = dbassoc($q))
        echo "<a href='?id_mod=$mod[id]&amp;id_act=$act[id]" . ($ank ? "&amp;id=$ank[id]" : null) . "'>$act[name]</a> ($act[count])<br />";
    echo "</div>";
    echo "&laquo;<a href='?$passgen" . ($ank ? "&amp;id=$ank[id]" : null) . "'>模块列表</a><br />";
} else {
    // 模块操作
    $q = dbquery("SELECT `admin_log_mod`.`name`, `admin_log_mod`.`id`, COUNT(`admin_log`.`id`) AS `count` FROM `admin_log` LEFT JOIN `admin_log_mod` ON `admin_log`.`mod` = `admin_log_mod`.`id`" . ($ank ? " WHERE `admin_log`.`id_user` = '$ank[id]'" : null) . " GROUP BY `admin_log`.`mod`");
    echo "<div class='menu'>";
    if (dbrows($q) == 0) echo "模块中没有操作";
    while ($mod = dbassoc($q))
        echo "<a href='?id_mod=$mod[id]" . ($ank ? "&amp;id=$ank[id]" : null) . "'>$mod[name]</a> ($mod[count])<br />";
    echo "</div>";
}
if (user_access('adm_panel_show')) {
    echo "<div class='foot'>";
    if (user_access('adm_show_adm')) echo "&raquo;<a href='administration.php'>管理工作</a><br />";
    echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
    echo "</div>";
}
include_once '../sys/inc/tfoot.php';

?>
