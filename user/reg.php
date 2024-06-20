<?php
//include_once '../sys/inc/mp3.php';
//include_once '../sys/inc/zip.php';
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/shif.php';
$show_all = true; // 给大家看
include_once '../sys/inc/user.php';
only_unreg();
$set['title'] = '注册账号';
include_once '../sys/inc/thead.php';
title();
aut();
if ($set['guest_select'] == '1') {
	msg("只有授权用户才能访问该网站");
}
if ((!isset($_SESSION['refer']) || $_SESSION['refer'] == NULL)
	&& isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL &&
	!preg_match('#mail\.php#', $_SERVER['HTTP_REFERER'])
)
	$_SESSION['refer'] = str_replace('&', '&amp;', preg_replace('#^http://[^/]*/#', '/', $_SERVER['HTTP_REFERER']));
if ($set['reg_select'] == 'close') {
	$err = '暂停登记';
	err();
	echo "<a href='/user/aut.php'>登录账号</a><br />";
	include_once '../sys/inc/tfoot.php';
} elseif ($set['reg_select'] == 'open_mail' && isset($_GET['id']) && isset($_GET['activation']) && $_GET['activation'] != NULL) {
	if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '" . intval($_GET['id']) . "' AND `activation` = '" . my_esc($_GET['activation']) . "'"), 0) == 1) {
		dbquery("UPDATE `user` SET `activation` = null WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1");
		$user = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"));
		dbquery("INSERT INTO `reg_mail` (`id_user`,`mail`) VALUES ('$user[id]','$user[ank_mail]')");
		msg('您的帐户已成功启动');
		$_SESSION['id_user'] = $user['id'];
		include_once '../sys/inc/tfoot.php';
	}
}
if (isset($_SESSION['step']) && $_SESSION['step'] == 1 && dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `login` = '" . $_SESSION['reg_nick'] . "'"), 0) == 0 && isset($_POST['pass1']) && $_POST['pass1'] != NULL && $_POST['pass2'] && $_POST['pass2'] != NULL) {
	if ($set['reg_select'] == 'open_mail') {
		if (!isset($_POST['ank_mail']) || $_POST['ank_mail'] == NULL) $err[] = '必须输入电子邮件';
		elseif (!preg_match('#^[A-z0-9-\._]+@[A-z0-9]{2,}\.[A-z]{2,4}$#ui', $_POST['ank_mail'])) $err[] = '无效的电子邮件格式';
		elseif (dbresult(dbquery("SELECT COUNT(*) FROM `reg_mail` WHERE `mail` = '" . my_esc($_POST['ank_mail']) . "'"), 0) != 0) {
			$err[] = "使用此电子邮件的用户已注册";
		}
	}
	if (strlen2($_POST['pass1']) < 6) $err[] = '出于安全原因，密码不能短于6个字符';
	if (strlen2($_POST['pass1']) > 32) $err[] = '密码长度超过32个字符';
	if ($_POST['pass1'] != $_POST['pass2']) $err[] = '密码不匹配';
	if (!isset($_SESSION['captcha']) || !isset($_POST['chislo']) || $_SESSION['captcha'] != $_POST['chislo']) {
		$err[] = '验证号码无效';
	}
	if (!isset($err)) {
		if ($set['reg_select'] == 'open_mail') {
			$activation = md5(passgen());
			dbquery("INSERT INTO `user` (`login`, `nick`, `pass`, `date_reg`, `date_last`, `pol`, `activation`, `ank_mail`) values('" . $_SESSION['reg_nick'] . "', '" . $_SESSION['reg_nick'] . "', '" . shif($_POST['pass1']) . "', '$time', '$time', '" . intval($_POST['pol']) . "', '$activation', '" . my_esc($_POST['ank_mail']) . "')", $db);
			$id_reg = dbinsertid();
			$subject = "帐户激活";
			$regmail = "你好！ $_SESSION[reg_nick]<br />
			要激活您的帐户，请点击链接:<br />
<a href='http://$_SERVER[HTTP_HOST]/user/reg.php?id=$id_reg&amp;activation=$activation'>http://$_SERVER[HTTP_HOST]/user/reg.php?id=" . dbinsertid() . "&amp;activation=$activation</a><br />
如果帐户在24小时内未激活，它将被删除<br />
真诚的，网站管理<br />
";
			$adds = "From: \"password@$_SERVER[HTTP_HOST]\" <password@$_SERVER[HTTP_HOST]>";
			//$adds = "From: <$set[reg_mail]>";
			//$adds .= "X-sender: <$set[reg_mail]>";
			$adds .= "Content-Type: text/html; charset=utf-8";
			mail($_POST['ank_mail'], '=?utf-8?B?' . base64_encode($subject) . '?=', $regmail, $adds);
		} else
			dbquery("INSERT INTO `user` (`login`,`nick`, `pass`, `date_reg`, `date_last`, `pol`) values('" . $_SESSION['reg_nick'] . "', '" . $_SESSION['reg_nick'] . "', '" . shif($_POST['pass1']) . "', '$time', '$time', '" . intval($_POST['pol']) . "')", $db);
		$user = dbassoc(dbquery("SELECT * FROM `user` WHERE `login`= '" . my_esc($_SESSION['reg_nick']) . "' AND `nick` = '" . my_esc($_SESSION['reg_nick']) . "' AND `pass` = '" . shif($_POST['pass1']) . "' LIMIT 1"));
		/*
========================================
创建用户设置 
========================================
*/
		dbquery("INSERT INTO `user_set` (`id_user`) VALUES ('$user[id]')");
		dbquery("INSERT INTO `discussions_set` (`id_user`) VALUES ('$user[id]')");
		dbquery("INSERT INTO `tape_set` (`id_user`) VALUES ('$user[id]')");
		dbquery("INSERT INTO `notification_set` (`id_user`) VALUES ('$user[id]')");
		if (isset($_SESSION['http_referer']))
			dbquery("INSERT INTO `user_ref` (`time`, `id_user`, `type_input`, `url`) VALUES ('$time', '$user[id]', 'reg', '" . my_esc($_SESSION['http_referer']) . "')");
		$_SESSION['id_user'] = $user['id'];
		setcookie('id_user', $user['id'], time() + 60 * 60 * 24 * 365);
		setcookie('pass', cookie_encrypt($_POST['pass1'], $user['id']), time() + 60 * 60 * 24 * 365);
		if ($set['reg_select'] == 'open_mail') {
			msg('您需要使用发送到电子邮件的链接激活您的帐户');
		} else {
			dbquery("update `user` set `wall` = '0' where `id` = '$user[id]' limit 1");
			header('Location: /user/my_aut.php?login=' . htmlspecialchars($_POST['reg_nick']) . '&pass=' . htmlspecialchars($_POST['pass1']));
		}
		echo "如果您的浏览器不支持Cookie，您可以创建一个自动登录书签<br />";
		echo "<input type='text' value='http://$_SERVER[SERVER_NAME]/user/login.php?id=$user[id]&amp;pass=" . htmlspecialchars($_POST['pass1']) . "' /><br />";
		if ($set['reg_select'] == 'open_mail') unset($user);
		echo "<div class='foot'>";
		echo "&raquo;<a href='settings.php'>我的设置</a><br />";
		echo "&raquo;<a href='umenu.php'>我的菜单</a><br />";
		echo "</div>";
		include_once '../sys/inc/tfoot.php';
	}
} elseif (isset($_POST['login']) && $_POST['login'] != NULL) {
	if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `login` = '" . my_esc($_POST['login']) . "'"), 0) == 0) {
		$login = my_esc($_POST['login']);
		if (!preg_match("#^([A-z0-9\-\_\ ])+$#ui", $_POST['login'])) $err[] = '用户名中有禁字';
		// if (preg_match("#[a-z]+#ui", $_POST['login'])) $err[] = '只允许使用英文字母字符';
		if (preg_match("#(^\ )|(\ $)#ui", $_POST['login'])) $err[] = '禁止在昵称的开头和结尾使用空格';
		if (strlen2($login) < 3) $err[] = '短用户名';
		if (strlen2($login) > 32) $err[] = '昵称长度超过32个字符';
	} else $err[] = '用户名 "' . stripcslashes(htmlspecialchars($_POST['login'])) . '"已登记';
	if (!isset($err)) {
		$_SESSION['reg_nick'] = $login;
		$_SESSION['step'] = 1;
		msg("用户名 \"$login\" 可以成功注册");
	}
}
err();
if (isset($_SESSION['step']) && $_SESSION['step'] == 1) {
	echo "<form method='post' action='/user/reg.php?$passgen'>";
	echo "你的用户名[A-z0-9 -_]:<br /><input type='text' name='login' maxlength='32' value='$_SESSION[reg_nick]' /><br />";
	echo "<input type='submit' value='另一个' />";
	echo "</form><br />";
	echo "<form method='post' action='/user/reg.php?$passgen'>";
	echo "你的性别:<br /><select name='pol'><option value='1'>男</option><option value='0'>女</option></select><br />";
	if ($set['reg_select'] == 'open_mail') {
		echo "E-mail:<br /><input type='text' name='ank_mail' /><br />";
		echo "* 指定您的真实电子邮件地址。您将收到一个激活您的帐户的代码.<br />";
	}
	echo "输入密码（6-32个字符）:<br /><input type='password' name='pass1' maxlength='32' /><br />";
	echo "重复密码:<br /><input type='password' name='pass2' maxlength='32' /><br />";
	echo "<img src='/captcha.php?$passgen&amp;SESS=$sess' width='100' height='30' alt='核证号码' /><br /><input name='chislo' size='5' maxlength='5' value='' type='text' /><br/>";
	echo "通过注册，您自动同意 <a href='/user/rules.php'>规则</a> 网站<br />";
	echo "<input type='submit' value='继续' />";
	echo "</form><br />";
} else {
	echo "<form class='mess' method='post' action='/user/reg.php?$passgen'>";
	echo "你的用户名 [A-z0-9 -_]:<br /><input type='text' name='login' maxlength='32' /><br />";
	echo "通过注册，您自动同意 <a href='/user/rules.php'>网站规则</a> <br />";
	echo "<input type='submit' value='继续' />";
	echo "</form><br />";
}
echo "<div class = 'foot'>已经注册？<br />&raquo;<a href='/user/aut.php'>登录账号</a></div>
<div class = 'foot'>不记得密码？<br />&raquo;<a href='/user/pass.php'>恢复密码</a></div>";
include_once '../sys/inc/tfoot.php';
