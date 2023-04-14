<? //到管理面板
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
user_access('adm_banlist', null, 'index.php?' . SID);
adm_check();
$set['title'] = '禁止名单';
include_once '../sys/inc/thead.php';
title();
err();
aut();
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `time` > '$time' OR `navsegda`='1' "), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `ban` WHERE `time` > '$time' OR `navsegda`='1' ORDER BY `id` DESC LIMIT $start, $set[p_str]");
echo "<table class='post'>";
if ($k_post == 0) {
    echo "   <tr>";
    echo "  <td class='p_t'>";
    echo "没有违规行为";
    echo "  </td>";
    echo "   </tr>";
}
while ($ban = dbassoc($q)) {
    echo "   <tr>";
    $ank = user::get_user($ban['id_user']);
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
    echo "<a href='/user/info.php?id=$ank[id]'>$ank[nick]</a>" . online($ank['id']) . "";
    echo "  </td>";
    echo "   </tr>";
    echo "   <tr>";
    if ($set['set_show_icon'] == 1) echo "  <td class='p_m' colspan='2'>";
    else echo "  <td class='p_m'>";
    $user_ban = user::get_user($ban['id_ban']);
    echo "<span class=\"ank_n\">禁止，直到 " . vremja($ban['time']) . ":</span><br />";
    echo "<span class=\"ank_d\">" . output_text($ban['prich']) . "</span>($user_ban[nick])<br />";
    if ((isset($access['ban_set']) || isset($access['ban_unset'])) && ($ank['level'] < $user['level'] || $user['level'] == 4))
        echo "<a href='/adm_panel/ban.php?id=$ank[id]'>详细介绍</a><br />";
    echo "  </td>";
    echo "   </tr>";
}
echo "</table>";
if ($k_page > 1) str("?", $k_page, $page); // 输出页数
if (user_access('adm_panel_show')) {
    echo "<div class='foot'>";
    echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
    echo "</div>";
}
include_once '../sys/inc/tfoot.php';
