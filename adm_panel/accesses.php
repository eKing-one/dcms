<?php
//网页标题
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
user_access('adm_accesses', null, 'index.php?' . SID);
adm_check();
if (isset($_GET['id_group']) && dbresult(dbquery("SELECT COUNT(*) FROM `user_group` WHERE `id` = '" . intval($_GET['id_group']) . "'"), 0)) {
    $group = dbassoc(dbquery("SELECT * FROM `user_group` WHERE `id` = '" . intval($_GET['id_group']) . "'"));
    $set['title'] = output_text('团体 "' . $group['name'] . '" - 特权'); // 页面标题
    include_once '../sys/inc/thead.php';
    title();
    if (isset($_POST['accesses'])) {
        dbquery("DELETE FROM `user_group_access` WHERE `id_group` = '$group[id]'");
        $q = dbquery("SELECT * FROM `all_accesses`");
        while ($post = dbassoc($q)) {
            $type = $post['type'];
            if (isset($_POST[$type]) && $_POST[$type] == 1)
                dbquery("INSERT INTO `user_group_access` (`id_group`, `id_access`) VALUES ('$group[id]', '$post[type]')");
        }
        msg('权限更改成功');
    }
    aut();
    echo "<form method='post' action='?id_group=$group[id]&amp;$passgen'>";
    $q = dbquery("SELECT * FROM `all_accesses` ORDER BY `name` ASC");
    while ($post = dbassoc($q)) {
        echo "<label>";
        echo "<input type='checkbox'" . (dbresult(dbquery("SELECT COUNT(*) FROM `user_group_access` WHERE `id_group` = '$group[id]' AND `id_access` = '$post[type]' LIMIT 1"), 0) == 1 ? " checked='checked'" : null) . " name='$post[type]' value='1' />";
        echo $post['name'];
        echo "</label><br />";
    }
    echo "<input value='申请' name='accesses' type='submit' />";
    echo "</form>";
    echo "<div class='foot'>";
    echo "&laquo;<a href='accesses.php'>组别</a><br />";
    echo "&laquo;<a href='index.php'>管理面板</a><br />";
    echo "</div>";
    include_once '../sys/inc/tfoot.php';
}
$set['title'] = '用户组'; // заголовок страницы
include_once '../sys/inc/thead.php';
title();
aut();
echo "<div class='menu'>";
$accesses = dbquery("SELECT * FROM `user_group` ORDER BY `level` ASC");
while ($res = dbassoc($accesses)) {
    echo "<a href='?id_group=$res[id]'>$res[name] (L$res[level], " . dbresult(dbquery("SELECT COUNT(*) FROM `user_group_access` WHERE `id_group` = '$res[id]'"), 0) . ")</a><br />";
}
echo "</div>";
if (user_access('adm_panel_show')) {
    echo "<div class='foot'>";
    echo "&laquo;<a href='index.php'>管理面板</a><br />";
    echo "</div>";
}
include_once '../sys/inc/tfoot.php';
