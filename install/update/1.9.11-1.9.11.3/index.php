<?php
include_once '../../../sys/inc/start.php';
include_once '../../../sys/inc/compress.php';
include_once '../../../sys/inc/sess.php';
include_once '../../../sys/inc/home.php';
include_once '../../../sys/inc/settings.php';
include_once '../../../sys/inc/db_connect.php';
include_once '../../../sys/fnc/shif.php';
include_once '../../../sys/inc/ipua.php';
include_once '../../../sys/fnc/user_access.php';



// 剪切所有不可读字符
function my_esc($text, $br = NULL) { 
	if ($br != '') {
		for ($i = 0; $i <= 31; $i++) $text = str_replace(chr($i), '', $text);
	} else {
		for ($i = 0; $i < 10; $i++) $text = str_replace(chr($i), '', $text);
		for ($i = 11; $i < 20; $i++) $text = str_replace(chr($i), '', $text);
		for ($i = 21; $i <= 31; $i++) $text = str_replace(chr($i), '', $text);
	}
	return $text;
}




// 登录检测
if (isset($_SESSION['id_user']) && dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$_SESSION[id_user]' LIMIT 1"), 0) == 1) {
	$user = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $_SESSION[id_user] LIMIT 1"));
	dbquery("UPDATE `user` SET `date_last` = '$time' WHERE `id` = '$user[id]' LIMIT 1");
	$user['type_input'] = 'session';
}

// 遍历目录并执行 SQL 文件
function runSqlFiles($dir) {
	if (!is_dir($dir)) {
		echo "指定的目录不存在：$dir";
	}
	
	// 打开目录并遍历其中的所有文件
	$files = scandir($dir);
	foreach ($files as $file) {
		// 排除 "." 和 ".." 目录
		if ($file == '.' || $file == '..') continue;
		
		$filePath = $dir . DIRECTORY_SEPARATOR . $file;

		// 检查文件是否是 SQL 文件
		if (pathinfo($filePath, PATHINFO_EXTENSION) === 'sql') {
			echo "正在执行：$file\n";
			try {
				executeSqlFile($filePath);
			} catch (Exception $e) {
				echo "执行 SQL 文件时出错：{$e->getMessage()}";
			}
			
		}
	}
}

// 执行一个 SQL 文件的内容
function executeSqlFile($filePath) {
	// 读取 SQL 文件内容
	$sql = file_get_contents($filePath);
	if ($sql === false) {
		throw new Exception("无法读取文件：$filePath");
		return;
	}

	// 执行 SQL 查询
	$queries = explode(";\n", $sql); // 将 SQL 语句按分号分隔
	foreach ($queries as $query) {
		$query = trim($query);
		if (!empty($query)) {
			dbquery($query);
		}
	}
}

if (isset($_POST['update']) && $_POST['update'] == '1') {
	if (!isset($user)) $err = '请先登录';
	if (user_access('adm_set_sys')) {
		// 定义文件目录路径
		$sqlDirectory = __DIR__ . '/db_tables';
		// 执行 SQL 文件更新
		runSqlFiles($sqlDirectory);
		echo "所有 SQL 文件执行完毕。";
	} else {
		echo '权限不足';
	}
}

if (isset($_POST['nick']) && isset($_POST['pass'])) {    // 检查用户是否已经提交登录表单
	// 从数据库获取用户信息
	$user = dbassoc(dbquery("SELECT `id`, `pass` FROM `user` WHERE `nick` = '" . my_esc($_POST['nick']) . "' LIMIT 1"));

	if ($user && password_verify($_POST['pass'], $user['pass'])) {
		$_SESSION['id_user'] = $user['id'];
		dbquery("UPDATE `user` SET `date_aut` = '$time', `date_last` = '$time' WHERE `id` = '$user[id]' LIMIT 1");
		dbquery("INSERT INTO `user_log` (`id_user`, `date`, `ua`, `ip`, `method`) values('{$user['id']}', '" . date('Y-m-d H:i:s') . "', '{$user['ua']}' , '{$user['ip']}', '1')");
		echo '登录成功';
	} elseif($user && shif($_POST['pass']) == $user['pass']) {
		$_SESSION['id_user'] = $user['id'];
		dbquery("UPDATE `user` SET `date_aut` = '$time', `date_last` = '$time' WHERE `id` = '$user[id]' LIMIT 1");
		dbquery("INSERT INTO `user_log` (`id_user`, `date`, `ua`, `ip`, `method`) values('{$user['id']}', '" . date('Y-m-d H:i:s') . "', '{$user['ua']}' , '{$user['ip']}', '1')");
		echo '登录成功';
	} else {
		echo '用户名或密码不正确';
	}
}




if (isset($user)) :?>
<form method='post'>
	<input type='hidden' name='update' value='1' />
	<input type='submit' value='更新数据库' />
</form>
<?php else:?>
<!--登录表单-->
<form method='post'>
	<input type='text' name='nick' placeholder='用户名' /><br>
	<input type='password' name='pass' placeholder='密码' /><br>
	<input type='submit' value='临时登录' />
</form>
<?php endif;