<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
only_reg();
$set['title'] = '设置通知';
include_once '../../sys/inc/thead.php';
title();
$notSet = dbarray(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '" . $user['id'] . "' LIMIT 1"));
if (isset($_POST['save'])) {
    // Комментарии
    if (isset($_POST['komm']) && ($_POST['komm'] == 0 || $_POST['komm'] == 1)) {
        dbquery("UPDATE `notification_set` SET `komm` = '" . intval($_POST['komm']) . "' WHERE `id_user` = '$user[id]'");
    }
    $_SESSION['message'] = '更改已成功接受';
    header('Location: settings.php');
    exit;
}
err();
aut();
echo "<div id='comments' class='menus'>";
echo "<div class='webmenu'>";
echo "<a href='/user/info/settings.php'>通用</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='/user/tape/settings.php'>通知消息</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='/user/discussions/settings.php'>讨论</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='/user/notification/settings.php' class='activ'>@提到我的</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='/user/info/settings.privacy.php' >隐私保护</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='/user/info/secure.php' >更改密码</a>";
echo "</div>";
echo "</div>";
echo "<form action='?' method=\"post\">";
// Лента фото
echo "<div class='mess'>";
echo "关于评论中的回复的通知";
echo "</div>";
echo "<div class='nav1'>";
echo "<input name='komm' type='radio' " . ($notSet['komm'] == 1 ? ' checked="checked"' : null) . " value='1' /> 是的 ";
echo "<input name='komm' type='radio' " . ($notSet['komm'] == 0 ? ' checked="checked"' : null) . " value='0' /> 否定 ";
echo "</div>";
echo "<div class='main'>";
echo "<input type='submit' name='save' value='保存' />";
echo "</div>";
echo "</form>";
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*' /> <a href='index.php'>通知书</a> | <b>设置</b><br />";
echo "</div>";
include_once '../../sys/inc/tfoot.php';
