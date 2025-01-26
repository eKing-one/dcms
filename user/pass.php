<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
$show_all = true; //为大家展示
include_once '../sys/inc/user.php';
only_unreg();
$set['title'] = '密码恢复';
include_once '../sys/inc/thead.php';
title();

// 删除过期的 password reset token
dbquery("DELETE FROM `password_reset_tokens` WHERE `created_at` < '" . date('Y-m-d H:i:s') . "'");

if (isset($_POST['nick']) && isset($_POST['mail']) && $_POST['nick'] != NULL && $_POST['mail'] != NULL) {
	if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `nick` = '" . my_esc($_POST['nick']) . "'"), 0) == 0) {
		$err = "使用此用户名的用户未注册";
	} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `nick` = '" . my_esc($_POST['nick']) . "' AND `email` = '" . my_esc($_POST['mail']) . "'"), 0) == 0) {
		$err = '无效的电子邮件地址或丢失的电子邮件信息';
	} else {
		// 生成链接Token
		$token = bin2hex(random_bytes(32));

		$q = dbquery("SELECT * FROM `user` WHERE `nick` = '" . my_esc($_POST['nick']) . "' LIMIT 1");
		$user2 = dbassoc($q);

		// 插入数据库，存储 token 和创建时间
		dbquery("INSERT INTO `password_reset_tokens` (`user_id`, `token`, `created_at`) VALUES ('{$user2['id']}', '{$token}', '" . date("Y-m-d H:i:s", $time + 6 * 60 * 60) . "')");
		$subject = "密码恢复";
		$regmail = "你好！ {$user2['nick']}<br />
		            您已激活密码恢复<br />
		            要设置新密码，请点击链接:<br />
		            <a href='" . get_http_type() . "://{$set['hostname']}/user/pass.php?id={$user2['id']}&amp;token={$token}'>" . get_http_type() . "://{$set['hostname']}/user/pass.php?id={$user2['id']}&amp;token={$token}</a><br />
		            此链接有效，直到您的用户名下的第一个授权({$user2['nick']})<br />真诚的，网站管理<br />";

		// 调用封装的发送邮件函数
		$emailResult = sendEmail($subject, $regmail, $user2['email'], $user2['nick']);

		if ($emailResult['status'] == 'success') {
			// 如果邮件发送成功，更新数据库
			msg("设置新密码的链接已发送到电子邮件 {$user2['email']}");
		} else {
			// 如果邮件发送失败
			$err[] = $emailResult['message'];
		}
	}
}



if (isset($_GET['token']) && isset($_GET['id'])) {
	// 验证 token 是否有效
	$validation = validatePasswordResetToken($_GET['token'], $_GET['id']);
	if ($validation['status'] == 'success') {
		// 获取用户信息
		$q = dbquery("SELECT * FROM `user` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1");
		$user2 = dbassoc($q);

		// 允许用户设置新密码
		if (isset($_POST['pass1']) && isset($_POST['pass2'])) {
			if ($_POST['pass1'] == $_POST['pass2']) {
				if (strlen2($_POST['pass1']) < 6) $err = '出于安全原因，新密码不能短于6个字符';
				if (strlen2($_POST['pass1']) > 32) $err = '密码长度超过32个字符';
			} else {
				$err = '新密码与确认不符';
			}
			if (!isset($err)) {
				dbquery("UPDATE `user` SET `pass` = '" . password_hash($_POST['pass1'], PASSWORD_DEFAULT) . "' WHERE `id` = '{$_GET['id']}' LIMIT 1");
				msg('密码更改成功');
				// 标记 token 为已使用
				markValidatePasswordResetTokenAsUsed($_GET['token']);
				echo '<div class="foot"><a href="/user/aut.php">登录账号</a></div>';
			}
			
		} else {
			err();
			aut();
			echo "<form action='/user/pass.php?id={$_GET['id']}&amp;token=" . esc($_GET['token'], 1) . "&amp;{$passgen}' method=\"post\">";
			echo "用户名:<br />";
			echo "<input type=\"text\" disabled='disabled' value='{$user2['nick']}' maxlength=\"32\" size=\"16\" /><br />";
			echo "新密码:<br /><input type='password' name='pass1' value='' /><br />";
			echo "重复密码:<br /><input type='password' name='pass2' value='' /><br />";
			echo "<input type='submit' name='save' value='修改' />";
			echo "</form>";
		}
	
	} else {
		$err = $validation['message'];
		err();
	}


} else {
	err();
	aut();
	echo "<form action=\"?{$passgen}\" method=\"post\">";
	echo "用户名:<br />";
	echo "<input type=\"text\" name=\"nick\" title=\"用户名\" value=\"\" maxlength=\"32\" size=\"16\" /><br />";
	echo "E-mail:<br />";
	echo "<input type=\"text\" name=\"mail\" title=\"E-mail\" value=\"\" maxlength=\"32\" size=\"16\" /><br />";
	echo "<input type=\"submit\" value=\"下一步\" title=\"下一步\" />";
	echo "</form>";
	echo "设置新密码的链接将发送到您的电子邮件。<br />";
	echo "如果您在资料中没有关于您的电子邮件的条目，密码恢复是不可能的。<br />";

	echo '<div class="foot">
		尚未登记？<br/>
		<a href="/user/reg.php">注册账号</a><br/>
		</div>
		<div class="foot">
		已经注册？ <br/>
		<a href="/user/aut.php">登录账号</a><br/>
		</div>';
}


include_once '../sys/inc/tfoot.php';
