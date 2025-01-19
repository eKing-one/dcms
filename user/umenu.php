<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
if (isset($user)) $ank['id'] = $user['id'];
only_reg();
$set['title'] = '个人中心';
include_once '../sys/inc/thead.php';
title();
aut();

	echo "<div class='main'>";
	echo user::avatar($ank['id']);
	echo "	" . user::nick($ank['id'], 0);
	echo "</div>";


// 检查用户是否成功登录
if (isset($_GET['nick']) && isset($_GET['pass'])) {
	echo '<div class="mess">';
	echo '如果您的浏览器不支持Cookie，您可以创建一个自动登录链接<br />';
	echo '<input type="text" value="http://' . text($_SERVER['SERVER_NAME']) . '/user/login.php?id=' . $user['id'] . '&amp;pass=' . text($_GET['pass']) . '" /><br />';
	echo '</div>';	
}
?>
<div class="main" id="umenu">
<img src='/style/my_menu/ank.png' alt='' /> <a href='/user/info.php'>我的页面</a><br />
</div>
<div class="main" id="umenu">
<img src='/style/my_menu/ank.png' alt='' /> <a href='/user/info/anketa.php'>个人资料</a> [<a href='user/info/edit.php'>编辑.</a>]<br />
</div>
<div class="main" id="umenu">
<img src="/style/my_menu/secure.png" alt="" /> <a href="/user/secure.php">更改密码</a><br />
</div>
<div class="main" id="umenu">
<img src="/style/my_menu/rules.png" alt="" /> <a href="/user/rules.php">规则</a><br />
</div>
<div class="main" id="umenu">
<img src="/style/my_menu/set.png" alt="" /> <a href="/user/info/settings.php">我的设置</a><br />
</div>
<div class="main" id="umenu">
<img src="/style/my_menu/set.png" alt="" /> <a href="./my_aut.php">登录历史</a><br />
</div>

<?
// 管理权限
if (user_access('adm_panel_show')) {
	echo '<div class="main" id="umenu">';
	echo '<img src="/style/my_menu/adm_panel.png" alt="" /> <a href="/adm_panel/">管理面板</a><br />';
	echo '</div>';
}

// 仅适用于wap
if ($set['web'] == false) {
	echo '<div class="main" id="umenu">';
	echo '<a href="/user/exit.php"><img src="/style/icons/delete.gif" /> 退出登录 ' . user::nick($user['id'],0,0,0) . '</a><br />';
	echo '</div>';
}
include_once '../sys/inc/tfoot.php';