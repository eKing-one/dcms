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
include_once '../sys/inc/user.php';
/* Бан пользователя */
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'chat' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
    header('Location: /ban.php?' . SID);
    exit;
}
$set['title'] = '聊天室-谁在这里？'; // заголовок страницы
include_once '../sys/inc/thead.php';
title();
aut();
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_last` > '" . (time() - 100) . "' AND `url` like '/chat/%'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `user` WHERE `date_last` > '" . (time() - 100) . "' AND `url` like '/chat/%' ORDER BY `date_last` DESC LIMIT $start, $set[p_str]");
echo "<table class='post'>";
if ($k_post == 0) {
    echo "   <tr>";
    echo "  <td class='p_t'>";
    echo "没有人";
    echo "  </td>";
    echo "   </tr>";
}
while ($chat = dbarray($q)) {
    echo "   <tr>";
    if ($set['set_show_icon'] == 2) {
        echo "  <td class='icon48' rowspan='2'>";
        user::avatar($chat['id']);
        echo "  </td>";
    } elseif ($set['set_show_icon'] == 1) {
        echo "  <td class='icon14'>";
        echo "" . user::avatar($chat['id']) . "";
        echo "  </td>";
    }
    echo "  <td class='p_t'>";
    echo "<a href='/info.php?id=$chat[id]'>$chat[nick]</a>";
    echo "  " . medal($chat['id']) . " " . online($chat['id']) . "";
    echo "  </td>";
    echo "   </tr>";
}
echo "</table>";
if ($k_page > 1) str("?", $k_page, $page); // 输出页数
include_once '../sys/inc/tfoot.php';
