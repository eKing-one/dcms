<?PHP
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

if (isset($_GET['edit_folder'])) {
	$folder = dbassoc(dbquery("SELECT * FROM `user_files`  WHERE `id` = '" . intval($_GET['edit_folder']) . "' LIMIT 1"));
	if ($folder['id_user'] != $user['id'] && !user_access('down_dir_edit')) {
		header("Location: /?" . SID);
		exit;
	}
	if (isset($_POST['name']) && isset($user)) {
		$msg = $_POST['msg'];
		$name = $_POST['name'];
		$pass = $_POST['pass'];
		if (strlen2($msg) > 256) {
			$err[] = '描述长度超过256个字符';
		}
		if (strlen2($name) > 30) {
			$err[] = '未返回数据（发送数据超时）。';
		}
		if (strlen2($pass) > 13) {
			$err[] = '密码长度超过12个字符';
		}
		if (strlen2($name) < 3) {
			$err[] = '名称必须至少有3个字符长';
		}
		if (!isset($err)) {
			dbquery("UPDATE `user_files` SET `name` = '" . my_esc($name) . "',  `pass` = '" . my_esc($pass) . "', `msg` = '" . my_esc($msg) . "' WHERE `id` = '$folder[id]' LIMIT 1");
			$_SESSION['message'] = '接受的更改';
			header("Location: ?" . SID);
			exit;
		}
	}
	err();
	echo "<div class='foot'>";
	echo "<img src='/style/icons/up_dir.gif' alt='*'> " . ($dir['osn'] == 1 ? '<a href="/user/personalfiles/' . $ank['id'] . '/' . $dir['id'] . '/">档案</a>' : '') . " " . user_files($dir['id_dires']) . " " . ($dir['osn'] == 1 ? '' : '&gt; <a href="/user/personalfiles/' . $ank['id'] . '/' . $dir['id'] . '/">' . text($dir['name']) . '</a>') . "";
	echo "</div>";
	echo '<form action="?edit_folder=' . $folder['id'] . '" method="post">';
	echo '标题:<br/><input type="text" name="name" maxlength="55" value="' . text($folder['name']) . '" /><br />';
	echo '资料描述:<br /><textarea name="msg">' . text($folder['msg']) . '</textarea><br />';
	echo '密码:<br/><input type="pass" name="pass" maxlength="12" value="' . text($folder['pass']) . '" /><br />';
	echo '<input type="submit" name="sub" value="保存"/></form>';
	echo "<div class='foot'>";
	echo "<img src='/style/icons/up_dir.gif' alt='*'> " . ($dir['osn'] == 1 ? '<a href="/user/personalfiles/' . $ank['id'] . '/' . $dir['id'] . '/">档案</a>' : '') . " " . user_files($dir['id_dires']) . " " . ($dir['osn'] == 1 ? '' : '&gt; <a href="/user/personalfiles/' . $ank['id'] . '/' . $dir['id'] . '/">' . text($dir['name']) . '</a>') . "";
	echo "</div>";
	include_once '../../sys/inc/tfoot.php';
}
