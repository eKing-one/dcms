<?php
/*
=======================================
模块”分享论坛主题" от PluginS
=======================================
*/
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
if (isset($user) && dbresult(dbquery("SELECT COUNT(`id`) FROM `ban` WHERE `razdel` = 'forum' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
    header('Location: /user/ban.php?' . SID);
    exit;
}
$set['title'] = '分享他们';
include_once '../sys/inc/thead.php';
title();
aut();
$not = dbquery("SELECT * FROM `forum_t` WHERE `id`='" . intval($_GET['id']) . "' LIMIT 1");
if (dbrows($not) == 0) {
    echo "<div class='error'>没有这样的话题</div>";
    include_once '../sys/inc/tfoot.php';
    exit;
}
if (dbresult(dbquery("SELECT COUNT(`id`)FROM `notes` WHERE `id_user`='" . $user['id'] . "' AND `share_id`='" . intval($_GET['id']) . "' AND `share_type`='forum' LIMIT 1"), 0) == 1) {
    echo "<div class='error'>您已经分享了这个主题</div>";
    include_once '../sys/inc/tfoot.php';
    exit;
} else {
    $notes = dbassoc($not);
    $avtor = user::get_user($notes['id_user']);
    if (isset($_POST['ok'])) {
        dbquery("INSERT INTO `notes`(`id_user`,`name`,`msg`,`share`,`share_text`,`share_id`,`share_id_user`,`share_name`,`time`,`share_type`) values('" . $user['id'] . "','" . text($notes['name']) . "','" . my_esc($_POST['share_text']) . "','1','" . my_esc($notes['text']) . "','" . $notes['id'] . "','" . $notes['id_user'] . "','" . my_esc($notes['name']) . "','" . $time . "','forum')");
        $id = dbinsertid();
        msg('一切顺利');
        header('Location:/plugins/notes/list.php?id=' . $id);
        exit;
    }
?>
    <div class='nav2'>
        <div class="friends_access_list attach_block mt_0 grey"> <? echo group($avtor['id']) . " "; ?> <a href="/user/info.php?id=<?= $notes['id_user'] ?>"><span style="color:#79358c"><b><? echo " " . $avtor['nick'] . " "; ?> </b></span></a> : <? echo '<a href="/forum/' . $notes['id_forum'] . '/' . $notes['id_razdel'] . '/' . $notes['id'] . '/">'; ?>
            <span style="color:#06F;"><? echo $notes['name']; ?></span></a>
        </div>
    <?
    echo "<form method='post' action='share.php?id=" . intval($_GET['id']) . "'>";
    echo $tPanel;
    echo "<textarea name='share_text'></textarea>";
    echo "<br/><input type='submit' name='ok' value='分享'>";
    echo "</form></div>";
}
include_once '../sys/inc/tfoot.php';
    ?>