<?
/*
=======================================
Лента друзей для Dcms-Social
Автор: Искатель
---------------------------------------
此脚本在许可下被破坏
DCMS-Social 引擎。
使用时，指定引用到
网址 http://dcms-social.ru
---------------------------------------
接点
ICQ：587863132
http://dcms-social.ru
=======================================
*/
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
if (isset($user)) $ank['id'] = $user['id'];
if (isset($_GET['id'])) $ank['id'] = intval($_GET['id']);
$ank = user::get_user($ank['id']);
if (!$ank || $ank['id'] == 0) {
    header("Location: /index.php?" . SID);
    exit;
}
only_reg();
$frend = dbarray(dbquery("SELECT * FROM `frends` WHERE `user` = '" . $user['id'] . "' AND `frend` = '$ank[id]' AND `i` = '1'"));
if (isset($_POST['save'])) {
    // Лента фото
    if (isset($_POST['lenta_photo']) && ($_POST['lenta_photo'] == 0 || $_POST['lenta_photo'] == 1)) {
        dbquery("UPDATE `frends` SET `lenta_photo` = '" . intval($_POST['lenta_photo']) . "' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
    }
    // Лента файлов
    if (isset($_POST['lenta_down']) && ($_POST['lenta_down'] == 0 || $_POST['lenta_down'] == 1)) {
        dbquery("UPDATE `frends` SET `lenta_down` = '" . intval($_POST['lenta_down']) . "' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
    }
    // Лента смены аватара
    if (isset($_POST['lenta_avatar']) && ($_POST['lenta_avatar'] == 0 || $_POST['lenta_avatar'] == 1)) {
        dbquery("UPDATE `frends` SET `lenta_avatar` = '" . intval($_POST['lenta_avatar']) . "' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
    }
    // Лента новых друзей
    if (isset($_POST['lenta_frends']) && ($_POST['lenta_frends'] == 0 || $_POST['lenta_frends'] == 1)) {
        dbquery("UPDATE `frends` SET `lenta_frends` = '" . intval($_POST['lenta_frends']) . "' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
    }
    // Лента статусов
    if (isset($_POST['lenta_status']) && ($_POST['lenta_status'] == 0 || $_POST['lenta_status'] == 1)) {
        dbquery("UPDATE `frends` SET `lenta_status` = '" . intval($_POST['lenta_status']) . "' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
    }
    // Лента оценок статуса
    if (isset($_POST['lenta_status_like']) && ($_POST['lenta_status_like'] == 0 || $_POST['lenta_status_like'] == 1)) {
        dbquery("UPDATE `frends` SET `lenta_status_like` = '" . intval($_POST['lenta_status_like']) . "' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
    }
    // Лента дневников
    if (isset($_POST['lenta_notes']) && ($_POST['lenta_notes'] == 0 || $_POST['lenta_notes'] == 1)) {
        dbquery("UPDATE `frends` SET `lenta_notes` = '" . intval($_POST['lenta_notes']) . "' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
    }
    // Лента форум
    if (isset($_POST['lenta_forum']) && ($_POST['lenta_forum'] == 0 || $_POST['lenta_forum'] == 1)) {
        dbquery("UPDATE `frends` SET `lenta_forum` = '" . intval($_POST['lenta_forum']) . "' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
    }
    $_SESSION['message'] = '更改已成功接受';
    header('Location: index.php');
    exit;
}
$set['title'] = '设置供稿 ' . $ank['nick'];
include_once '../../sys/inc/thead.php';
title();
err();
aut();
echo "<div id='comments' class='menus'>";
echo "<div class='webmenu'>";
echo "<a href='index.php'>信息中心</a>";
echo "</div>";
echo "<div class='webmenu'>";
echo "<a href='settings.php'>设置</a>";
echo "</div>";
echo "</div>";
echo "<form action='?id=$ank[id]' method=\"post\">";
// Лента друзей
echo "<div class='mess'>";
echo "关于新朋友的通知 $ank[nick].";
echo "</div>";
echo "<div class='nav1'>";
echo "<input name='lenta_frends' type='radio' " . ($frend['lenta_frends'] == 1 ? ' checked="checked"' : null) . " value='1' /> 是的 ";
echo "<input name='lenta_frends' type='radio' " . ($frend['lenta_frends'] == 0 ? ' checked="checked"' : null) . " value='0' /> 否定 ";
echo "</div>";
// Лента Дневников
echo "<div class='mess'>";
echo "关于新日记的通知 $ank[nick].";
echo "</div>";
echo "<div class='nav1'>";
echo "<input name='lenta_notes' type='radio' " . ($frend['lenta_notes'] == 1 ? ' checked="checked"' : null) . " value='1' /> 是的 ";
echo "<input name='lenta_notes' type='radio' " . ($frend['lenta_notes'] == 0 ? ' checked="checked"' : null) . " value='0' /> 否定 ";
echo "</div>";
// Лента Форума
echo "<div class='mess'>";
echo "关于新主题的通知 $ank[nick] 在论坛上。";
echo "</div>";
echo "<div class='nav1'>";
echo "<input name='lenta_forum' type='radio' " . ($frend['lenta_forum'] == 1 ? ' checked="checked"' : null) . " value='1' /> 是的 ";
echo "<input name='lenta_forum' type='radio' " . ($frend['lenta_forum'] == 0 ? ' checked="checked"' : null) . " value='0' /> 否定 ";
echo "</div>";
// Лента фото
echo "<div class='mess'>";
echo "关于新照片的通知 $ank[nick].";
echo "</div>";
echo "<div class='nav1'>";
echo "<input name='lenta_photo' type='radio' " . ($frend['lenta_photo'] == 1 ? ' checked="checked"' : null) . " value='1' /> 是的 ";
echo "<input name='lenta_photo' type='radio' " . ($frend['lenta_photo'] == 0 ? ' checked="checked"' : null) . " value='0' /> 否定 ";
echo "</div>";
// Лента о смене аватара
echo "<div class='mess'>";
echo "有关更改头像的通知 $ank[nick].";
echo "</div>";
echo "<div class='nav1'>";
echo "<input name='lenta_avatar' type='radio' " . ($frend['lenta_avatar'] == 1 ? ' checked="checked"' : null) . " value='1' /> 是的 ";
echo "<input name='lenta_avatar' type='radio' " . ($frend['lenta_avatar'] == 0 ? ' checked="checked"' : null) . " value='0' /> 否定 ";
echo "</div>";
// Лента файлов
echo "<div class='mess'>";
echo "关于新文件的通知 $ank[nick].";
echo "</div>";
echo "<div class='nav1'>";
echo "<input name='lenta_down' type='radio' " . ($frend['lenta_down'] == 1 ? ' checked="checked"' : null) . " value='1' /> 是的 ";
echo "<input name='lenta_down' type='radio' " . ($frend['lenta_down'] == 0 ? ' checked="checked"' : null) . " value='0' /> 否定 ";
echo "</div>";
// Лента статусов
echo "<div class='mess'>";
echo "关于新状态的通知 $ank[nick].";
echo "</div>";
echo "<div class='nav1'>";
echo "<input name='lenta_status' type='radio' " . ($frend['lenta_status'] == 1 ? ' checked="checked"' : null) . " value='1' /> 是的 ";
echo "<input name='lenta_status' type='radio' " . ($frend['lenta_status'] == 0 ? ' checked="checked"' : null) . " value='0' /> 否定 ";
echo "</div>";
// Лента оценок статуса
echo "<div class='mess'>";
echo "有关的通知 \"Like\" 对朋友的状态 $ank[nick].";
echo "</div>";
echo "<div class='nav1'>";
echo "<input name='lenta_status_like' type='radio' " . ($frend['lenta_status_like'] == 1 ? ' checked="checked"' : null) . " value='1' />是的 ";
echo "<input name='lenta_status_like' type='radio' " . ($frend['lenta_status_like'] == 0 ? ' checked="checked"' : null) . " value='0' /> 否定 ";
echo "</div>";
echo "<div class='main'>";
echo "<input type='submit' name='save' value='保存' />";
echo "</div>";
echo "</form>";
include_once '../../sys/inc/tfoot.php';
