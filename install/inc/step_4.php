<?php

/**
 * 设置网站管理员用户页面
 */
$set['title'] = '注册管理员';
include_once 'inc/head.php'; // 设计主题的顶部
if (!isset($_SESSION['shif'])) $_SESSION['shif'] = $passgen;
$set['shif'] = $_SESSION['shif'];
$db = mysqli_connect($_SESSION['host'], $_SESSION['user'], $_SESSION['pass'], $_SESSION['db']);
mysqli_query($db, 'set charset utf8mb4');
mysqli_query($db, 'SET names utf8mb4');
mysqli_query($db, 'set character_set_client="utf8mb4"');
mysqli_query($db, 'set character_set_connection="utf8mb4"');
//mysql_query('set character_set_result="utf8mb4"');

if (isset($_SESSION['adm_reg_ok']) && $_SESSION['adm_reg_ok'] == true) {
	if (isset($_GET['step']) && $_GET['step'] == '5') {
		$tmp_set['title'] = strtoupper($_SERVER['HTTP_HOST']) . ' - 社区系统';
		$tmp_set['mysql_host'] = $_SESSION['host'];
		$tmp_set['mysql_user'] = $_SESSION['user'];
		$tmp_set['mysql_pass'] = $_SESSION['pass'];
		$tmp_set['mysql_db_name'] = $_SESSION['db'];
		$tmp_set['shif'] = $_SESSION['shif'];
		if (save_settings($tmp_set)) {
			unset($_SESSION['install_step'], $_SESSION['host'], $_SESSION['user'], $_SESSION['pass'], $_SESSION['db'], $_SESSION['adm_reg_ok'], $_SESSION['mysql_ok']);
			if ($_SERVER["SERVER_ADDR"] != '127.0.0.1') delete_dir(H . 'install/');
			header("Location: /index.php?" . session_id());
			exit;
		} else {
			$msg['无法保存系统设置'];
		}
	}
} elseif (isset($_POST['reg'])) {
	// 检查昵称
	if (!preg_match("#^([A-z0-9\-\_\ ])+$#ui", $_POST['nick'])) $err[] = '昵称中有禁字';
	if (!preg_match("#[a-z]+#ui", $_POST['nick'])) $err[] = '只允许使用英文字母字符';
	if (preg_match("#(^\ )|(\ $)#ui", $_POST['nick'])) {
		$err[] = '禁止在昵称的开头和结尾使用空格';
	} else {
		if (strlen2($_POST['nick']) < 3) {
			$err[] = '短于 3 个字符的用户名';
		} elseif (strlen2($_POST['nick']) > 16) {
			$err[] = '长于 16 个字符的用户名';
		} elseif (mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) AS cnt FROM `user` WHERE `nick` = '" . my_esc($_POST['nick']) . "' LIMIT 1"))['cnt'] != 0) {
			$err[] = '所选的用户名已经被另一个用户占用了';
		} else {
			$nick = $_POST['nick'];
		}
	}

	// 密码检查
	if (!isset($_POST['password']) || $_POST['password'] == null) {
		$err[] = '输入密码';
	} else {
		if (strlen2($_POST['password']) < 6) {
			$err[] = '密码短于 6 个字符';
		} elseif (strlen2($_POST['password']) > 16) {
			$err[] = '长于 16 个字符的密码';
		} elseif (!isset($_POST['password_retry'])) {
			$err[] = '输入密码确认';
		} elseif ($_POST['password'] !== $_POST['password_retry']) {
			$err[] = '密码不匹配';
		} else {
			$password = $_POST['password'];
		}
	}

	if (!isset($_POST['pol']) || !is_numeric($_POST['pol']) || ($_POST['pol'] !== '0' && $_POST['pol'] !== '1')) {
		$err[] = '选择性别时出错';
	} else {
		$pol = intval($_POST['pol']);
	}

	if (!isset($err)) {	// 如果没有错误
		mysqli_query($db, "INSERT INTO `user` (`nick`, `pass`, `date_reg`, `date_aut`, `date_last`, `pol`, `level`, `group_access`, `balls`, `money`)
		                   VALUES('$nick', '" . password_hash($password, PASSWORD_DEFAULT) . "', $time, $time, $time, '$pol', '4', '15', '5000', '500')");
		$user = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM `user` WHERE `nick` = '$nick' LIMIT 1"));
		$q = mysqli_query($db, "SELECT `type` FROM `all_accesses`");

		// 意义不明的循环，根本没有用到 $ac 和 user_acсess 表
		//while ($ac = mysqli_fetch_assoc($q)) {mysqli_query($db, "INSERT INTO `user_acсess` (`id_user`, `type`) VALUES ('$user[id]','$ac[type]')");}
		
		/*
		========================================
		创建用户设置
		========================================
		*/
		mysqli_query($db, "INSERT INTO `user_set` (`id_user`) VALUES ('$user[id]')");
		mysqli_query($db, "INSERT INTO `discussions_set` (`id_user`) VALUES ('$user[id]')");
		mysqli_query($db, "INSERT INTO `tape_set` (`id_user`) VALUES ('$user[id]')");
		mysqli_query($db, "INSERT INTO `notification_set` (`id_user`) VALUES ('$user[id]')");
		$_SESSION['id_user'] = $user['id'];
		$_SESSION['adm_reg_ok'] = true;
	}
}

if (isset($_SESSION['adm_reg_ok']) && $_SESSION['adm_reg_ok'] == true) {
	echo "<div class='msg'>管理员注册成功</div>";
	if (isset($msg)) {
		foreach ($msg as $key => $value) {
			echo "<div class='msg'>$value</div>";
		}
	}
	echo "<hr />";
	echo "<form method=\"get\" action=\"index.php\">";
	echo "<input name='gen' value='$passgen' type='hidden' />";
	echo "<input name=\"step\" value=\"" . ($_SESSION['install_step'] + 1) . "\" type=\"hidden\" />";
	echo "<input value='完成安装' type=\"submit\" />";
	echo "</form>";
	echo "* 安装后，请务必删除文件夹 /install/<br />";
} else {
	if (isset($err)) {
		foreach ($err as $key => $value) {
			echo "<div class='err'>$value</div>";
		}
		echo "<hr />";
	}
	echo "<form action='index.php?$passgen' method='post'>";
	echo "账号名 (3-16 字符):<br /><input type='text' name='nick'" . ((isset($nick)) ? " value='" . $nick . "'" : " value='Admin'") . " maxlength='16' /><br />";
	echo "密码 (6-16 字符):<br /><input type='password'" . ((isset($password)) ? " value='" . $password . "'" : null) . " name='password' maxlength='16' /><br />";
	echo "* 使用简单的密码使黑客的生活更轻松<br />";
	echo "确认密码:<br /><input type='password'" . ((isset($password)) ? " value='" . $password . "'" : null) . " name='password_retry' maxlength='16' /><br />";
	echo "您的性别:<br />";
	echo "<select name='pol'>";
	echo "<option value='1'" . ((isset($pol) && $pol === 1) ? " selected='selected'" : null) . ">男</option>";
	echo "<option value='0'" . ((isset($pol) && $pol === 0) ? " selected='selected'" : null) . ">女</option>";
	echo "</select><br />";
	echo "* 所有字段都必须填写<br />";
	echo "<input type='submit' name='reg' value='注册' /><br />";
	echo "</form>";
}
echo "<hr />";
echo "<b>步骤: $_SESSION[install_step]</b>";
include_once 'inc/foot.php'; //设计主题的底部
