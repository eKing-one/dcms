<?php
/*
=======================================
DCMS-Social 用户个人文件
作者：探索者
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
only_reg();
if ($dir['id_dires'] == '/')
	$id_dires = $dir['id_dires'] . $dir['id'] . '/';
else
	$id_dires = $dir['id_dires'] . $dir['id'] . '/';
if (isset($_POST['name']) && isset($user)) {
	$msg = $_POST['msg'];
	$name = $_POST['name'];
	$pass = $_POST['pass'];
	$osn = $dir['osn'] + 1;
	if ($dir['osn'] == 6) $err[] = '不能在两个以上的子目录中创建文件夹';
	if (strlen2($msg) > 256) {
		$err[] = '描述长度超过256个字符';
	}
	if (strlen2($name) > 30) {
		$err[] = '名称的长度超过30个字符';
	}
	if (!isset($err)) {
		dbquery("INSERT INTO `user_files` (`id_user`, `name`, `msg`,  `time`, `id_dir`, `osn`, `id_dires`, `pass`) values('$user[id]', '" . my_esc($name) . "','" . my_esc($msg) . "',  '$time', '$dir[id]', '$osn', '$id_dires', '" . my_esc($pass) . "')");
		$_SESSION['message'] = '文件夹创建成功';
		header("Location: ?");
		exit;
	}
}
$set['title'] = '创建文件夹';
include_once '../../sys/inc/thead.php';
title();
aut();
err();
echo "<div class='foot'>";
echo "<img src='/style/icons/up_dir.gif' alt='*'> " . ($dir['osn'] == 1 ? '档案' : '') . " " . user_files($dir['id_dires']) . " " . ($dir['osn'] == 1 ? '' : '&gt; ' . htmlspecialchars($dir['name'])) . "";
echo "</div>";
echo '<form class="mess" name="message" action="?add" method="post">';
echo '文件夹名称:<br/><input type="text" name="name" maxlength="30" value="" /><br />';
if ($set['web'] && is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php')) {
	include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
} else {
	echo $tPanel . '<textarea name="msg"></textarea><br />';
}
echo '密码:<br/><input type="pass" name="pass" maxlength="12" value="" /><br />';
echo '<input type="submit" name="sub" value="创建"/></form>';
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='?'>返回</a><br />";
echo "</div>";
echo "<div class='foot'>";
echo "<img src='/style/icons/up_dir.gif' alt='*'> " . ($dir['osn'] == 1 ? '档案' : '') . " " . user_files($dir['id_dires']) . " " . ($dir['osn'] == 1 ? '' : '&gt; ' . htmlspecialchars($dir['name'])) . "";
echo "</div>";
include_once('../../sys/inc/tfoot.php');
