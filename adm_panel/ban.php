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
if (!user_access('user_ban_set') && !user_access('user_ban_set_h') && !user_access('user_ban_unset')) {
    header("Location: /index.php?" . SID);
    exit;
}
if (isset($_GET['id'])) $ank['id'] = intval($_GET['id']);
else {
    header("Location: /index.php?" . SID);
    exit;
}
if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$ank[id]' LIMIT 1"), 0) == 0) {
    header("Location: /index.php?" . SID);
    exit;
}
$ank = get_user($ank['id']);
if ($user['level'] <= $ank['level']) {
    header("Location: /index.php?" . SID);
    exit;
}
$set['title'] = '用户禁令 ' . $ank['nick'];
include_once '../sys/inc/thead.php';
title();
if (isset($_GET['delete']) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$ank[id]' AND `id` = '" . intval($_GET['delete']) . "'"), 0) && user_access('user_ban_unset')) {
    $ban_info = dbassoc(dbquery("SELECT * FROM `ban` WHERE `id_user` = '$ank[id]' AND `id` = '" . intval($_GET['delete']) . "'"));
    $ank2 = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$ban_info[id_ban]' LIMIT 1"));
    if (($user['level'] > $ank2['level'] || $user['id'] == $ank2['id']) || $user['level'] == 4) {
        dbquery("DELETE FROM `ban` WHERE `id` = '" . intval($_GET['delete']) . "' LIMIT 1");
        admin_log('用户', '禁令', "从用户中删除违规 '[url=/amd_panel/ban.php?id=$ank[id]]$ank[nick][/url]'");
        $_SESSION['message'] = '删除违规行为';
        header("Location: ?id=$ank[id]");
        exit;
    } else
        $err[] = '无权限';
}
if (isset($_GET['unset']) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$ank[id]' AND `id` = '" . intval($_GET['unset']) . "'"), 0) && user_access('user_ban_unset')) {
    $ban_info = dbassoc(dbquery("SELECT * FROM `ban` WHERE `id_user` = '$ank[id]' AND `id` = '" . intval($_GET['unset']) . "'"));
    $ank2 = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$ban_info[id_ban]' LIMIT 1"));
    if (($user['level'] > $ank2['level'] || $user['id'] == $ank2['id']) || $user['level'] == 4) {
        dbquery("UPDATE `ban` SET `time` = '$time', `navsegda` = '0' WHERE `id` = '" . intval($_GET['unset']) . "' LIMIT 1");
        admin_log('用户', '禁令', "删除用户的禁令 '[url=/amd_panel/ban.php?id=$ank[id]]$ank[nick][/url]'");
        $_SESSION['message'] = '禁止时间重置为零';
        header("Location: ?id=$ank[id]");
        exit;
    } else
        $err[] = '无权限';
}
if (isset($_POST['ban_pr']) && isset($_POST['time']) && isset($_POST['vremja']) && (user_access('user_ban_set') || user_access('user_ban_set_h'))) {
    $timeban = $time;
    if ($_POST['vremja'] == 'min') $timeban += intval($_POST['time']) * 60;
    if ($_POST['vremja'] == 'chas') $timeban += intval($_POST['time']) * 60 * 60;
    if ($_POST['vremja'] == 'sut') $timeban += intval($_POST['time']) * 60 * 60 * 24;
    if ($_POST['vremja'] == 'mes') $timeban += intval($_POST['time']) * 60 * 60 * 24 * 30;
    if ($timeban < $time) $err[] = '洗澡时间错误';
    if (!user_access('user_ban_set')) $timeban = min($timeban, $time + 3600 * 24);
    $pochemu = $_POST['pochemu'];
    $razdel = $_POST['razdel'];
    $post = $_POST['post'];
    $navsegda = $_POST['navsegda'];
    $prich = $_POST['ban_pr'];
    if (strlen2($prich) > 1024) {
        $err[] = '信息太长了';
    }
    if (strlen2($prich) < 10) {
        $err[] = '有必要更详细地说明原因';
    }
    $prich = my_esc($prich);
    if (!isset($err)) {
        dbquery("INSERT INTO `ban` (`id_user`, `id_ban`, `prich`, `time`, `pochemu`, `razdel`, `post`, `navsegda`) VALUES ('$ank[id]', '$user[id]', '$prich', '$timeban', '$pochemu', '$razdel', '$post', '$navsegda')");
        admin_log('用户', '禁令', "用户禁令 '[url=/adm_panel/ban.php?id=$ank[id]]$ank[nick][/url]' до " . vremja($timeban) . " по причине '$prich'");
        $_SESSION['message'] = '用户已成功被禁止';
        header("Location: ?id=$ank[id]");
        exit;
    }
}
err();
aut();
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$ank[id]'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo "<table class='post'>";
if ($k_post == 0) {
    echo "<div class='mess'>";
    echo "没有违规行为";
    echo "</div>";
}
$q = dbquery("SELECT * FROM `ban` WHERE `id_user` = '$ank[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q)) {
    /*-----------зебра-----------*/
    if ($num == 0) {
        echo "  <div class='nav1'>";
        $num = 1;
    } elseif ($num == 1) {
        echo "  <div class='nav2'>";
        $num = 0;
    }/*---------------------------*/
    $ank2 = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_ban] LIMIT 1"));
    if ($set['set_show_icon'] == 2) {
        avatar($ank2['id']);
    } elseif ($set['set_show_icon'] == 1) {
        echo status($ank2['id']) . " ";
    }
    echo "<a href='/info.php?id=$ank2[id]'>$ank2[nick]</a> " . online($ank2['id']) . ": ";
    if ($post['navsegda'] == 1) {
        echo " 浴池 <font color=red><b>所有是的</b></font><br />";
    } else {
        echo " до " . vremja($post['time']) . "<br />";
    }
    echo '<b>原因：</b> ' . $pBan[$post['pochemu']] . '<br />';
    echo '<b>说明:</b> ' . $rBan[$post['razdel']] . '<br />';
    echo '<b>评论:</b> ' . esc(trim(br(bbcode(smiles(links(stripcslashes(htmlspecialchars($post['prich'])))))))) . "<br />";
    if ($post['time'] > $time && user_access('user_ban_unset'))
        echo "<font color=red><b>活动中</b></font> | <a href='?id=$ank[id]&amp;unset=$post[id]'>Снять бан</a><br />";
    echo "<div style='text-align:right;'> <a href='?id=$ank[id]&amp;delete=$post[id]'><img src='/style/icons/delete.gif' alt='*'></a></div>";
    echo "</div>";
}
echo "</table>";
if ($k_page > 1) str('?id=' . $ank['id'] . '&amp;', $k_page, $page); // Вывод страниц
if (user_access('user_ban_set') || user_access('user_ban_set_h')) {
    echo "<form action=\"ban.php?id=$ank[id]&amp;$passgen\" method=\"post\">";
    echo "<div class='nav1'>说明:</div>";
    if ($user['group_access'] == 12 || $user['level'] > 1) echo "<input name='razdel' type='radio' value='guest'  checked='checked'/>客人 <br />";
    if ($user['group_access'] == 11 || $user['level'] > 1) echo "<input name='razdel' type='radio' value='notes'  checked='checked'/>日记 <br />";
    if ($user['group_access'] == 3 || $user['level'] > 1) echo "<input name='razdel' type='radio' value='forum'  checked='checked'/>论坛<br />";
    if ($user['group_access'] == 4 || $user['level'] > 1) echo "<input name='razdel' type='radio'  value='files'  checked='checked'/>档案 <br />";
    if ($user['group_access'] == 2 || $user['level'] > 1) echo "<input name='razdel' type='radio'  value='chat'  checked='checked'/>聊天 <br />";
    if ($user['group_access'] == 5 || $user['level'] > 1) echo "<input name='razdel' type='radio'  value='lib'  checked='checked'/>图书馆<br />";
    if ($user['group_access'] == 6 || $user['level'] > 1) echo "<input name='razdel' type='radio'  value='foto'  checked='checked'/>照片<br />";
    if ($user['level'] > 1) echo "<input name='razdel' type='radio' value='all' checked='checked'/>整个网站 <br />";
    echo "<div class='nav1'>原因：</div>";
    echo "<input name='pochemu' type='radio' value='1' checked='checked'/>垃圾邮件/广告<br />";
    echo "<input name='pochemu' type='radio' value='2' />欺诈行为<br />";
    echo "<input name='pochemu' type='radio' value='3' />淫亵用语<br />";
    echo "<input name='pochemu' type='radio' value='4' />克隆昵称<br />";
    echo "<input name='pochemu' type='radio' value='5' />煽动、挑衅和煽动侵略<br />";
    echo "<input name='pochemu' type='radio' value='6' />洪水泛滥<br />";
    echo "<input name='pochemu' type='radio' value='7' />火焰<br />";
    echo "<input name='pochemu' type='radio' value='0' />其他<br />";
    echo "<div class='nav1'>来文:</div>";
    echo "<input name='post' type='radio' value='0' checked='checked'/>显示 <br />";
    echo "<input name='post' type='radio' value='1' />隐藏<br />";
    echo "<div class='nav1'>评论:</div>";
    echo "<textarea name=\"ban_pr\"></textarea><br />";
    echo "<div class='nav1'>禁令时间 " . (user_access('user_ban_set') ? null : '(最多1天)') . ":</div>";
    echo "<input type='text' name='time' title='禁令时间' value='10' maxlength='11' size='3' />";
    echo "<select class='form' name=\"vremja\">";
    echo "<option value='min'>分钟</option>";
    echo "<option " . (($k_post > 1) ? 'selected="selected" ' : null) . "value='chas'>手表</option>";
    echo "<option value='sut'>日</option>";
    echo "<option value='mes'" . (user_access('user_ban_set') ? null : ' disabled="disabled"') . ">个月</option>";
    echo "</select><br />";
    echo "<label><input type='checkbox' name='navsegda' value='1' /> Навсег是的</label><br />";
    echo "<input type='submit' value='禁令' />";
    echo "</form>";
} else {
    echo "<div class='err'>没有禁止用户的权利</div>";
}
echo "<div class='foot'>";
echo "&raquo;<a href=\"/mail.php?id=$ank[id]\">写一封信</a><br />";
echo "&laquo;<a href=\"/info.php?id=$ank[id]\">返回资料</a><br />";
if (user_access('adm_panel_show'))
    echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
echo "</div>";
include_once '../sys/inc/tfoot.php';
