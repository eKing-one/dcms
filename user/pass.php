<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/shif.php';
$show_all = true; //为大家展示
include_once '../sys/inc/user.php';
only_unreg();
$set['title'] = '密码恢复';
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['nick']) && isset($_POST['mail']) && $_POST['nick'] != NULL && $_POST['mail'] != NULL) {
	if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `nick` = '" . my_esc($_POST['nick']) . "'"), 0) == 0) {
		$err = "使用此用户名的用户未注册";
	} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `nick` = '" . my_esc($_POST['nick']) . "' AND `ank_mail` = '" . my_esc($_POST['mail']) . "'"), 0) == 0) {
		$err = '无效的电子邮件地址或丢失的电子邮件信息';
	} else {
		$q = dbquery("SELECT * FROM `user` WHERE `nick` = '" . my_esc($_POST['nick']) . "' LIMIT 1");
		$user2 = dbassoc($q);
		$new_sess = substr(md5(passgen()), 0, 20);
		$subject = "密码恢复";
		$regmail = "你好！ $user2[nick]<br />
		            您已激活密码恢复<br />
		            要设置新密码，请点击链接:<br />
		            <a href='http://$_SERVER[HTTP_HOST]/user/pass.php?id=$user2[id]&amp;set_new=$new_sess'>http://$_SERVER[HTTP_HOST]/user/pass.php?id=$user2[id]&amp;set_new=$new_sess</a><br />
		            此链接有效，直到您的用户名下的第一个授权($user2[nick])<br />真诚的，网站管理<br />";
		$adds = "From: \"password@$_SERVER[HTTP_HOST]\" <password@$_SERVER[HTTP_HOST]>";
		//$adds = "From: <$set[reg_mail]>";
		//$adds .= "X-sender: <$set[reg_mail]>";
		$adds .= "Content-Type: text/html; charset=utf-8";
		mail($user2['ank_mail'], '=?utf-8?B?' . base64_encode($subject) . '?=', $regmail, $adds);
		dbquery("UPDATE `user` SET `sess` = '$new_sess' WHERE `id` = '$user2[id]' LIMIT 1");
		msg("设置新密码的链接已发送到电子邮件 \"$user2[ank_mail]\"");
	}
}
if (isset($_GET['id']) && isset($_GET['set_new']) && strlen($_GET['set_new']) == 20 && dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '" . intval($_GET['id']) . "' AND `sess` = '" . my_esc($_GET['set_new']) . "'"), 0) == 1) {
	$q = dbquery("SELECT * FROM `user` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1");
	$user2 = dbassoc($q);
	if (isset($_POST['pass1']) && isset($_POST['pass2'])) {
		if ($_POST['pass1'] == $_POST['pass2']) {
			if (strlen2($_POST['pass1']) < 6) $err = '出于安全原因，新密码不能短于6个字符';
			if (strlen2($_POST['pass1']) > 32) $err = '密码长度超过32个字符';
		} else $err = '新密码与确认不符';
		if (!isset($err)) {
			setcookie('id_user', $user2['id'], time() + 60 * 60 * 24 * 365);
			dbquery("UPDATE `user` SET `pass` = '" . password_hash($_POST['pass1'], PASSWORD_BCRYPT) . "' WHERE `id` = '$user2[id]' LIMIT 1");
			setcookie('pass', cookie_encrypt($_POST['pass1'], $user2['id']), time() + 60 * 60 * 24 * 365);
			msg('密码更改成功');
		}
	}
	err();
	aut();
	echo "<form action='/user/pass.php?id=$user2[id]&amp;set_new=" . esc($_GET['set_new'], 1) . "&amp;$passgen' method=\"post\">";
	echo "用户名:<br />";
	echo "<input type=\"text\" disabled='disabled' value='$user2[nick]' maxlength=\"32\" size=\"16\" /><br />";
	echo "新密码:<br /><input type='password' name='pass1' value='' /><br />";
	echo "确认书:<br /><input type='password' name='pass2' value='' /><br />";
	echo "<input type='submit' name='save' value='修改' />";
	echo "</form>";
} else {
	err();
	aut();
	echo "<form action=\"?$passgen\" method=\"post\">";
	echo "用户名:<br />";
	echo "<input type=\"text\" name=\"nick\" title=\"用户名\" value=\"\" maxlength=\"32\" size=\"16\" /><br />";
	echo "E-mail:<br />";
	echo "<input type=\"text\" name=\"mail\" title=\"E-mail\" value=\"\" maxlength=\"32\" size=\"16\" /><br />";
	echo "<input type=\"submit\" value=\"下一步\" title=\"下一步\" />";
	echo "</form>";
	echo "设置新密码的链接将发送到您的电子邮件。<br />";
	echo "如果您在资料中没有关于您的电子邮件的条目，密码恢复是不可能的。<br />";
}
	echo '<div class="foot">
		尚未登记？<br/>
		<a href="/user/reg.php">注册账号</a><br/>
	</div>
	<div class="foot">
		已经注册？ <br/>
		<a href="/user/aut.php">登录账号</a><br/>
	</div>';
include_once '../sys/inc/tfoot.php';
