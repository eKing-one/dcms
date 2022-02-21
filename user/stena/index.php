<?
$set['p_str'] = 5;
if (isset($_GET['likepost'])) {
    $stena = dbassoc(dbquery("SELECT * FROM `stena` WHERE `id` = '" . intval($_GET['likepost']) . "' LIMIT 1"));
    $ank3 = get_user($stena['id_user']);
    $l = dbresult(dbquery("SELECT COUNT(*) FROM `stena_like` WHERE `id_stena` = '$stena[id]'"), 0);
    if (isset($_GET['likepost']) && dbresult(dbquery("SELECT COUNT(*) FROM `stena_like` WHERE
 `id_stena` = '$stena[id]' AND `id_user` = '$user[id]' LIMIT 1"), 0) == 0) {
        dbquery("INSERT INTO `stena_like` (`id_user`, `id_stena`) values('$user[id]', '$stena[id]')");
        dbquery("UPDATE `user` SET `balls` = '" . ($ank3['balls'] + 1) . "' WHERE `id` = '$ank3[id]' LIMIT 1");
    }
}
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `stena` WHERE `id_stena` = '$ank[id]'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
if ($k_post == 0) {
    echo "  <div class='mess'>";
    echo "没有留言";
    echo "  </div>";
} else {
    /*------------сортировка по времени--------------*/
    if (isset($user)) {
        echo "<div id='comments' class='menus'>";
        echo "<div class='webmenu'>";
        echo "<a href='/info.php?id=$ank[id]&amp;page=$page&amp;sort=1' class='" . ($user['sort'] == 1 ? 'activ' : '') . "'>在下面</a>";
        echo "</div>";
        echo "<div class='webmenu'>";
        echo "<a href='/info.php?id=$ank[id]&amp;page=$page&amp;sort=0' class='" . ($user['sort'] == 0 ? 'activ' : '') . "'>在顶部</a>";
        echo "</div>";
        echo "</div>";
    }
    /*---------------alex-borisi---------------------*/
}
$q = dbquery("SELECT * FROM `stena` WHERE `id_stena` = '$ank[id]' ORDER BY id $sort LIMIT $start, $set[p_str]");
$num = 0;
while ($post = dbassoc($q)) {
    /*-----------代码-----------*/
    if ($num == 0) {
        echo "  <div class='nav1'>";
        $num = 1;
    } elseif ($num == 1) {
        echo "  <div class='nav2'>";
        $num = 0;
    }
    /*---------------------------*/
    $ank_stena = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
    if ($set['set_show_icon'] == 2) {
        avatar($ank_stena['id']);
    } elseif ($set['set_show_icon'] == 1) {
        echo "" . group($ank_stena['id']) . "";
    }
    echo "<a href='/info.php?id=$ank_stena[id]'>$ank_stena[nick]</a>";
    echo "" . medal($ank_stena['id']) . " " . online($ank_stena['id']) . "";
    if (isset($user)) echo " <a href='/info.php?id=$ank[id]&amp;response=$ank_stena[id]'>[*]</a>";
    echo " (" . vremja($post['time']) . ")<br />";
    echo stena($ank_stena['id'], $post['id']) . ' <br/>';
    echo output_text($post['msg']) . "<br />";
    if (isset($user)) {
        $l = dbresult(dbquery("SELECT COUNT(*) FROM `stena_like` WHERE `id_stena` = '$post[id]'"), 0);
        echo '<a href="/user/komm.php?id=' . $post['id'] . '"><img src="/style/icons/uv.png"> (' . dbresult(dbquery("SELECT COUNT(*) FROM `stena_komm` WHERE `id_stena` = '$post[id]'"), 0) . ') </a><span style="float:right;"> <a href="?id=' . $ank['id'] . '&amp;likepost=' . $post['id'] . '&amp;page=' . $page . '" >&hearts; ' . $l . '</a> ';
        if (isset($user) && $ank_stena['id'] != $user['id']) echo "<a href=\"/info.php?id=$ank[id]&amp;page=$page&amp;spam=$post[id]\"><img src='/style/icons/blicon.gif' alt='*' title='这是垃圾邮件'></a>";
        if (user_access('guest_delete') || $ank['id'] == $user['id']) {
            echo "<a href='?id=$ank[id]&amp;delete_post=$post[id]'><img src='/style/icons/delete.gif' alt='删除' /></a>";
        }
        echo "   </span>";
    }
    echo "</div>";
}
if ($k_page > 1) str('?id=' . $ank['id'] . '&', $k_page, $page); // 输出页数
if (isset($user) || (isset($set['write_guest']) && $set['write_guest'] == 1 && (!isset($_SESSION['antiflood']) || $_SESSION['antiflood'] < $time - 300))) {
    echo "<form method=\"post\" name='message' action=\"?id=$ank[id]$go_otv\">";
    if ($set['web'] && is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
        include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
    else
        echo "$tPanel<textarea name=\"msg\">$otvet</textarea><br />";
    echo "<input value=\"发送\" type=\"submit\" />";
    echo "</form><table width='99%'>";
}
