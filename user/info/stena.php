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
$set['title'] = '我的个人资料';
include_once '../../sys/inc/thead.php';
title();
if (isset($_POST['save'])) {
	if (isset($_POST['stena_photo']) && $_POST['stena_photo'] == 0) {
		$user['stena_photo'] = 0;
		dbquery("UPDATE `user` SET `stena_photo` = '0' WHERE `id` = '$user[id]' LIMIT 1");
	} else {
		$user['stena_photo'] = 1;
		dbquery("UPDATE `user` SET `stena_photo` = '1' WHERE `id` = '$user[id]' LIMIT 1");
	}
	if (!isset($err)) msg('更改已成功接受');
}
err();
aut();
echo "<div id='comments' class='menu'>";
echo "<div class='webmenu'>";
echo "<a href='settings.php'>普通</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='stena.php' class='activ'>动态</a>";
echo "</div>";
echo "</div>";
echo "<form method='post' action='?$passgen'>";
echo "<label><input type='checkbox' name='stena_photo'" . ($user['stena_photo'] == 0 ? ' checked="checked"' : null) . " value='0' /> 照片</label><br />
	<input type='submit' name='save' value='保存' />
	</form>
	<div class='foot'>
	&raquo;<a href='anketa.php'>查看资料</a><br />";
if (isset($_SESSION['refer']) && $_SESSION['refer'] != NULL && otkuda($_SESSION['refer']))
	echo "&laquo;<a href='$_SESSION[refer]'>" . otkuda($_SESSION['refer']) . "</a><br />";
echo "&laquo;<a href='/user/my_aut.php'>我的菜单</a><br /></div>";
include_once '../../sys/inc/tfoot.php';
