<?php
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
$set['title'] = '更改密码';
include_once '../../sys/inc/thead.php';
title();

if (isset($_POST['save'])) {
	// 检查旧密码是否正确
	$userData = dbassoc(dbquery("SELECT `pass` FROM `user` WHERE `id` = '$user[id]' LIMIT 1"));
	
	if ($userData && password_verify($_POST['pass'], $userData['pass'])) {
		// 检查新密码和确认密码是否一致
		if (isset($_POST['pass1']) && isset($_POST['pass2'])) {
			if ($_POST['pass1'] == $_POST['pass2']) {
				if (strlen2($_POST['pass1']) < 6) {
					$err = '出于安全原因，新密码不能短于6个字符';
				} elseif (strlen2($_POST['pass1']) > 32) {
					$err = '密码长度超过32个字符';
				}
			} else {
				$err = '新密码与确认不符';
			}
		} else {
			$err = '请输入新密码';
		}
	} else {
		$err = '旧密码不正确';
	}

	if (!isset($err)) {
		// 使用 password_hash() 来加密新密码
		$hashedPassword = password_hash($_POST['pass1'], PASSWORD_DEFAULT);
		dbquery("UPDATE `user` SET `pass` = '$hashedPassword' WHERE `id` = '$user[id]' LIMIT 1");

		// 更新 Cookie 中的密码（可选）
		setcookie('auth_token', cookie_encrypt($_POST['pass1'], $user['id']), time() + 60 * 60 * 24 * 365, '/');

		// 设置成功消息并重定向
		$_SESSION['message'] = '密码更改成功';
		header("Location: ?");
		exit;
	}
}

err();
aut();

// 输出导航菜单
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
echo "<a href='/user/notification/settings.php'>@提到我的</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='/user/info/settings.privacy.php'>隐私保护</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='/user/info/secure.php' class='activ'>更改密码</a>";
echo "</div>";
echo "</div>";

// 输出密码修改表单
echo "<form method='post' action='?{$passgen}'>";
echo "旧密码:<br /><input type='password' name='pass' value='' /><br />";
echo "新密码:<br /><input type='password' name='pass1' value='' /><br />";
echo "确认密码:<br /><input type='password' name='pass2' value='' /><br />";
echo "<input type='submit' name='save' value='修改' />";
echo "</form>";

include_once '../../sys/inc/tfoot.php';
