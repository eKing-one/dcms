<?php
// 函数别名
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

// 用于兼容php4的file_put_contents替代函数（不再需要）
if (!function_exists('file_put_contents')) {
	function file_put_contents($file, $data) {
		$f = @fopen($file, 'w');
		return @fwrite($f, $data);
		@fclose($f);
	}
}

// 禁止文字antimat会自动发出警告，然后禁止
function antimat($str) {
	global $user, $time, $set;
	// if ($set['antimat']) {
	// 	$antimat = &$_SESSION['antimat'];
	// 	include_once H . 'sys/inc/censure.php';
	// 	$censure = censure($str);
	// 	if ($censure) {
	// 		$antimat[$censure] = $time;
	// 		if (count($antimat) > 3 && isset($user) && $user['level']) // 如果发出超过3次警告
	// 		{
	// 			$prich = "检测到禁止文字: $censure";
	// 			$timeban = $time + 60 * 60; // бан на час
	// 			dbquery("INSERT INTO `ban` (`id_user`, `id_ban`, `prich`, `time`) VALUES ('$user[id]', '0', '$prich', '$timeban')");
	// 			admin_log('用户', '禁令', "用户禁令 '[url=/amd_panel/ban.php?id=$user[id]]$user[nick][/url]' (id#$user[id]) 以前 " . vremja($timeban) . " 这是有原因的 '$prich'");
	// 			header('Location: /user/ban.php?' . session_id());
	// 			exit;
	// 		}
	// 		return $censure;
	// 	} else return false;
	// } else return false;
	return false;
}

// 递归删除文件夹
function delete_dir($dir) {
	if (is_dir($dir)) {
		$od = opendir($dir);
		while ($rd = readdir($od)) {
			if ($rd == '.' || $rd == '..') continue;
			if (is_dir("$dir/$rd")) {
				chmod("$dir/$rd", 0777);
				delete_dir("$dir/$rd");
			} else {
				chmod("$dir/$rd", 0777);
				unlink("$dir/$rd");
			}
		}
		closedir($od);
		chmod("$dir", 0777);
		return rmdir("$dir");
	} else {
		chmod("$dir", 0777);
		unlink("$dir");
	}
}

function br($msg, $br = '<br />') {
	return preg_replace("#((<br( ?/?)>)|\n|\r)+#i", $br, $msg);
} // 换行

function esc($text, $br = NULL) { // 过滤所有不可读字符
	if ($br != NULL) {
		for ($i = 0; $i <= 31; $i++) $text = str_replace(chr($i), '', $text);
	} else {
		for ($i = 0; $i < 10; $i++) $text = str_replace(chr($i), '', $text);
		for ($i = 11; $i < 20; $i++) $text = str_replace(chr($i), '', $text);
		for ($i = 21; $i <= 31; $i++) $text = str_replace(chr($i), '', $text);
	}
	return $text;
}

// 时间输出
function vremja($time = NULL) {
	global $user;
	if ($time == NULL) $time = time();
	if (isset($user)) $time = $time + $user['set_timesdvig'] * 60 * 60;
	$timep = "" . date("Y/m/d H:i", $time) . "";
	$time_p[0] = date("Y/m/d", $time);
	$time_p[1] = date("H:i", $time);
	if ($time_p[0] == date("Y/m/d")) $timep = date("H:i:s", $time);
	if (isset($user)) {
		if ($time_p[0] == date("Y/m/d", time() + $user['set_timesdvig'] * 60 * 60)) $timep = date("H:i:s", $time);
		if ($time_p[0] == date("Y/m/d", time() - 60 * 60 * (24 - $user['set_timesdvig']))) $timep = "昨天$time_p[1]";
	} else {
		if ($time_p[0] == date("Y/m/d")) $timep = date("H:i:s", $time);
		if ($time_p[0] == date("Y/m/d", time() - 60 * 60 * 24)) $timep = "昨天$time_p[1]";
	}
	$timep = str_replace("Jan", "1", $timep);
	$timep = str_replace("Feb", "2", $timep);
	$timep = str_replace("Mar", "3", $timep);
	$timep = str_replace("May", "4", $timep);
	$timep = str_replace("Apr", "5", $timep);
	$timep = str_replace("Jun", "6", $timep);
	$timep = str_replace("Jul", "7", $timep);
	$timep = str_replace("Aug", "8", $timep);
	$timep = str_replace("Sep", "9", $timep);
	$timep = str_replace("Oct", "10", $timep);
	$timep = str_replace("Nov", "11", $timep);
	$timep = str_replace("Dec", "12", $timep);
	return $timep;
}

// 只供已登记人士使用
function only_reg($link = NULL) {
	global $user;
	if (!isset($user)) {
		if ($link == NULL) $link = '/index.php?' . session_id();
		header("Location: $link");
		exit;
	}
}


// 只适用于未登记的人
function only_unreg($link = NULL) {
	global $user;
	if (isset($user)) {
		if ($link == NULL) $link = '/index.php?' . session_id();
		header("Location: $link");
		exit;
	}
}


// 仅适用于访问级别大于或等于 $level
function only_level($level = 0, $link = NULL) {
	global $user;
	if (!isset($user) || $user['level'] < $level) {
		if ($link == NULL) $link = '/index.php?' . session_id();
		header("Location: $link");
		exit;
	}
}

// 错误输出
function err() {
	global $err;
	if (isset($err)) {
		if (is_array($err)) {
			foreach ($err as $key => $value) {
				echo "<div class='err'>{$value}</div>";
			}
		} else echo "<div class='err'>{$err}</div>";
	}
}

function msg($msg) {
	echo "<div class='msg'>{$msg}</div>";
} // 消息输出

// 保存系统设置
function save_settings($set) {
	// 从数组中移除特定键
	unset($set['web']);
	
	// 构建配置文件内容
	$configContent = "<?php\nreturn " . var_export($set, true) . ";\n";

	// 定义配置文件路径
	$filePath = H . 'sys/dat/settings.php';

	// 尝试打开文件写入内容
	if ($fopen = fopen($filePath, 'w')) {
		fputs($fopen, $configContent);
		fclose($fopen);
		chmod($filePath, 0777);
		return true;
	} else {
		return false;
	}
}

/**
 * 写入管理操作日志
 *
 * @param string   $mod  模块名
 * @param string   $act  动作名
 * @param string   $opis 描述文本（可包含任意字符，无需转义）
 *
 * @return int 日志 id
 * @throws Exception 抛出异常
 */
function admin_log(string $mod, string $act, string $opis): int {
	global $db;
	/* 开启事务，保证 mod / act 两条记录与 log 记录同时成功/失败 */
	$db->beginTransaction();

	try {
		/* 1. 确保模块存在，不存在则插入并返回 id */
		$id_mod = $db->queryColumn(
			'SELECT `id` FROM `admin_log_mod` WHERE `name` = ? LIMIT 1',
			[$mod]
		);
		if ($id_mod === null) {
			$id_mod = (int)$db->insert(
				'INSERT INTO `admin_log_mod` (`name`) VALUES (?)',
				[$mod]
			);
		}

		/* 2. 确保动作存在，不存在则插入并返回 id */
		$id_act = $db->queryColumn(
			'SELECT `id` FROM `admin_log_act` WHERE `name` = ? AND `id_mod` = ? LIMIT 1',
			[$act, $id_mod]
		);
		if ($id_act === null) {
			$id_act = (int)$db->insert(
				'INSERT INTO `admin_log_act` (`name`, `id_mod`) VALUES (?, ?)',
				[$act, $id_mod]
			);
		}

		/* 3. 插入真正的日志记录 */
		$log_id = (int)$db->insert(
			'INSERT INTO `admin_log` (`time`, `id_user`, `mod`, `act`, `opis`)
			 VALUES (?, ?, ?, ?, ?)',
			[time(), $GLOBALS['user']['id'], $id_mod, $id_act, $opis]
		);

		$db->commit();
		return $log_id;

	} catch (Exception $e) {
		/* 任何一步出错都回滚并向上传递 */
		$db->rollBack();
		throw $e;
	}
}

/**
 * 对输入字符串进行安全处理，防止潜在的脚本注入和SQL注入。
 *
 * 这个函数主要用于清理和转义用户输入的字符串。它会将 "script" 替换为带有西里尔字母的 "sсript"，
 * 以防止 XSS 攻击（跨站脚本攻击），并在特定条件下对字符串进行去空格和转义处理，以准备用于数据库操作。
 * 注意：如果当前脚本是 '/adm_panel/mysql.php'，则不会进行转义处理。
 *
 * @param string $msg 需要处理的输入字符串，通常来自用户输入。
 * @return string 返回处理后的字符串，已替换特殊字符并根据条件进行转义。
 */
function fiera($msg) {
	$msg = str_replace("script", "sсript", $msg);
	$msg = str_replace("javascript:", "javаscript:", $msg);
	if ($_SERVER['PHP_SELF'] != '/adm_panel/mysql.php' && $_SERVER['PHP_SELF'] != '/adm_panel/settings_email.php')
		$msg = addslashes(stripslashes(trim($msg)));
	return $msg;
}

/**
 * 获取或设置全局配置变量的值
 *
 * @param string $name 配置项名称
 * @param mixed $default 默认值，默认为 NULL
 * @return mixed 返回配置项的值
 */
function setget($name, $default = NULL) {
	global $set;
	// 如果配置项未设置，则初始化为默认值
	if (!isset($set[$name])) {
		if ($default === NULL) $set[$name] = NULL;
		else $set[$name] = $default;
	}
	return $set[$name];
}

/**
 * 测试文件是否为普通文件（使用 check_replace 检查路径）
 *
 * @param string $file 文件路径
 * @return bool 如果是普通文件返回 true，否则返回 false
 */
function test_file($file) {
	return (is_file(check_replace($file)));
}

/**
 * 测试文件是否存在（使用 check_replace 检查路径）
 *
 * @param string $file 文件路径
 * @return bool 如果文件存在返回 true，否则返回 false
 */
function test_file2($file) {
	return (file_exists(check_replace($file)));
}

function ages($age) {
	$str = '';
	$num = $age > 100 ? substr($age, -2) : $age;
	if ($num >= 5 && $num <= 14) $str = "年";
	else {
		$num = substr($age, -1);
		if ($num == 0 || ($num >= 5 && $num <= 9)) $str = '年';
		if ($num == 1) $str = '年';
		if ($num >= 2 && $num <= 4) $str = '年';
	}
	return $age . ' ' . $str;
}

function add_header($value) {
	static $add;
	return $add[] = $value;
	header_html($add);
}

function header_html($add = null) {
	static $header;
	if ($add == null) {
		//   var_dump($header);
		echo "" . $header;
	} else $header = $add;
}

// 从文件夹"sys/fnc"加载其余功能 
$opdirbase = opendir(H . 'sys/fnc');
while ($filebase = readdir($opdirbase)) {
	if (preg_match('#\.php$#i', $filebase)) {
		include_once(H . 'sys/fnc/' . $filebase);
	}
}




// ============================== 定期执行的功能 ====================================


/**
 * 删除超过一小时的 IP 封禁记录
 * 
 * 仅删除 `prich` 字段为 `AntiDos` 且 `created_at` 早于一天前的记录
 */
$db->delete('DELETE FROM ban_ip WHERE created_at < NOW() - INTERVAL 1 HOUR AND prich IN (?, ?);', ['AntiDos', 'Inject']);

// 禁止被封禁的 IP 访问
if (!(isset($ban_ip_page) && $ban_ip_page == true) && checkBanIp($ip)) {
	header('Location: /user/ban_ip.php');
	exit;
}

if (isset($_SESSION['refer']) && $_SESSION['refer'] != NULL && !preg_match('#(rules)|(smiles)|(secure)|(aut)|(reg)|(umenu)|(zakl)|(mail)|(anketa)|(settings)|(avatar)|(info)\.php#',$_SERVER['SCRIPT_NAME'])) $_SESSION['refer'] = NULL;

(function() {
	global $set, $db, $hard_process, $ip, $ua;

	// DOS 攻击防护
	if ($set['antidos']) {
		// 插入当前请求记录
		$db->insert(
			"INSERT INTO ip_requests (`ip`, `time`) VALUES (:ip, NOW())",
			['ip' => $ip]
		);

		// 查询该 IP 在过去 5 秒内的请求次数
		$requestCount = $db->query(
			"SELECT COUNT(*) as count FROM ip_requests WHERE ip = :ip AND time > :time_limit",
			[
				'ip' => $ip,
				'time_limit' => date('Y-m-d H:i:s', time() - 5)
			]
		)['count'];

		// 如果请求次数超过 100，则封禁 IP
		if ($requestCount > 100) {
			$banExists = $db->query(
				"SELECT COUNT(*) as count FROM `ban_ip` WHERE `min` <= :ip AND `max` >= :ip",
				['ip' => $ip]
			)['count'];

			if ($banExists == 0) {
				$db->insert(
					"INSERT INTO `ban_ip` (`min`, `max`, `prich`) VALUES (:min, :max, 'AntiDos')",
					['min' => $ip, 'max' => $ip]
				);
			}
		}

		// 定期清理过期的请求记录（1 小时前）
		$db->delete(
			"DELETE FROM ip_requests WHERE time < :time_limit",
			['time_limit' => date('Y-m-d H:i:s', time() - 3600)]
		);
	}


	// 反黑客攻击行为
	if (!defined("ADMIN") && isset($set['hacker_attacks']) && $set['hacker_attacks'] == 1) {
		$hackparam = htmlspecialchars((string) ($_SERVER['QUERY_STRING'] ?? ''));

		$hackcmd = array('chr(', 'r57shell', 'remview', '%27', 'config=', 'OUTFILE%20', 'spnuke_authors', 'spnuke_admins', 'uname%20', 'netstat%20', 'rpm%20', 'passwd', '%20', 'del%20', 'deltree%20', 'format%20', 'start%20', 'wget', 'group_access', '%3E', '%3С',  'select%20', 'SELECT', 'cmd=', 'rush=', 'union', 'javascript:', 'UNION', 'echr(', 'esystem(', 'cp%20', 'mdir%20', 'mcd%20', 'mrd%20', 'rm%20', 'mv%20', 'rmdir%20', 'chmod(', 'chmod%20', 'chown%20', 'chgrp%20', 'locate%20', 'diff%20', 'kill%20', 'kill(', 'killall', 'cmd', 'command', 'fetch', 'whereis', 'grep%20', 'ls -', 'lynx', 'su%20root', 'test', 'etc/passwd',  "'", '%60', '%00', '%F20', 'echo', 'write(', 'killall', 'passwd%20', 'telnet%20', 'vi(', 'vi%20', 'INSERT%20INTO', 'SELECT%20', 'javascript', 'fopen', 'fwrite', '$_REQUEST', '$_GET', '<script>', 'alert', '&lt', '&gt'); //禁用参数和值

		$checkcmd = str_replace($hackcmd, 'X', $hackparam);

		if ($hackparam != $checkcmd) {
			dbquery("INSERT INTO ban_ip (min, max, prich) VALUES(\"$ip\", \"$ip\", \"Inject\");");
			dbquery('INSERT INTO mail (id_user, id_kont, msg, time) VALUES("0", "1", "IP: ' . $ip . ' UA: ' . $ua . ' 位置: ' . get_ip_address($ip) . ' 正在进行黑客攻击", "' . time() . '");');
			die('<h2>检测到攻击！</h2><br>你的浏览器：<b>' . $ua . '</b><br>你的IP： <b>' . $ip . '</b><br><b>已被记录，不要尝试违法操作！</b><br><br>有这时间多休息吧！！！');
		}
	}


	// 正在清除临时文件夹
	if (!isset($hard_process)) {
		$q = dbquery("SELECT * FROM `cron` WHERE `id` = 'clear_tmp_dir'");
		if (dbrows($q) == 0) dbquery("INSERT INTO `cron` (`id`, `time`) VALUES ('clear_tmp_dir', '" . time() . "')");
		$clear_dir = dbassoc($q);
		if (!isset($clear_dir['time']) || isset($clear_dir['time']) && $clear_dir['time'] < time() - 60 * 60 * 24) {
			$hard_process = true;
			dbquery("UPDATE `cron` SET `time` = '" . time() . "' WHERE `id` = 'clear_tmp_dir'");
			// if (function_exists('curl_init')) {
			// 	$ch = curl_init();
			// 	curl_setopt($ch, CURLOPT_URL, 'https://dcms-social.ru/curl.php?site=' . $_SERVER['HTTP_HOST'] . '&version=' . $set['dcms_version'] . '&title=' . $set['title']);
			// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			// 	$data = curl_exec($ch);
			// 	curl_close($ch);
			// }
			$od = opendir(H . 'sys/tmp/');
			while ($rd = readdir($od)) {
				if (!preg_match('#^\.#', $rd) && filectime(H . 'sys/tmp/' . $rd) < time() - 60 * 60 * 24) {
					delete_dir(H . 'sys/tmp/' . $rd);
				}
			}
			closedir($od);
		}
	}


	// 每日访问记录
	if (!isset($hard_process)) {
		if ($db->queryColumn('SELECT 1 FROM `cron` WHERE `id` = ? LIMIT 1;', ['visit']) != 1) {
			$db->insert('INSERT INTO cron (`id`, `time`) VALUES (?, ?)', ['visit', time()]);
		}

		$visit = $db->query('SELECT * FROM cron WHERE id = ? LIMIT 1', ['visit']);
		if (!isset($visit['time']) || $visit['time'] < time() - 60 * 60 * 24) {
			//if (function_exists('set_time_limit')) set_time_limit(600); // 将限制设置为 10 分钟

			$datetime = new DateTime('today');
			$today_time = $datetime->format('Y-m-d 00:00:00');
			$last_day = (clone $datetime)->modify('yesterday')->format('Y-m-d 00:00:00');
			// 检查是否已记录昨天的数据
			if ($db->queryColumn('SELECT 1 FROM `visit_everyday` WHERE `date` = ?', [$last_day]) != 1 && $db->queryColumn('SELECT EXISTS (SELECT 1 FROM visit_today WHERE `last_time` < ?);', [$today_time]) == 1) {
				$hard_process = true;

				// 统计昨天的数据并插入 visit_everyday
				$db->insert("INSERT INTO `visit_everyday` (`visitors`, `hit`, `date`)
								SELECT
									COUNT(*) AS visitors,
									SUM(`hit_count`) AS hit,
									? AS date
								FROM `visit_today`
								WHERE `last_time` < ?", [$last_day, $today_time]);

				// 清理昨天的数据
				$db->delete("DELETE FROM `visit_today` WHERE `first_time` < CURRENT_DATE();");
			}
		}
	}

	// 记录当前访问
	$ip_ua_hash = md5($ip . $ua); // 基于 ip 和 ua 生成哈希
	// 检查是否已有记录
	if ($db->queryColumn('SELECT 1 FROM `visit_today` WHERE `ip_ua_hash` = ? LIMIT 1;', [$ip_ua_hash])) {
		// 记录存在，更新计数和最后访问时间
		$db->update('UPDATE visit_today SET hit_count = `hit_count` + 1, `last_time` = ? WHERE `ip_ua_hash` = ?', [date("Y-m-d H:i:s"), $ip_ua_hash]);
	} else {
		// 新访客，插入记录
		$db->insert('INSERT INTO visit_today (ip_ua_hash, ip, ua) VALUES (?, ?, ?)', [$ip_ua_hash, $ip, $ua]);
	}


	// 删除过期的captcha_token
	$db->query("DELETE FROM captcha_tokens WHERE expires_at < NOW()");
})();


// 现场迁移记录
if (isset($_SERVER['HTTP_REFERER']) && !preg_match('#' . preg_quote($_SERVER['HTTP_HOST']) . '#', $_SERVER['HTTP_REFERER']) && $ref = @parse_url($_SERVER['HTTP_REFERER'])) {
	if (isset($ref['host'])) $_SESSION['http_referer'] = $ref['host'];
}

if (!isset($hard_process)) {
	// 检查并获取 cron 记录
	$everyday = $db->query("SELECT * FROM `cron` WHERE `id` = ?", ['everyday']);

	if (!$everyday) {
		$db->insert("INSERT INTO `cron` (`id`, `time`) VALUES (?, ?)", ['everyday', time()]);
		// 重新获取一下刚才插入的对象
		$everyday = ['time' => time()];
	}

	// 检查是否满足“每天执行一次”的条件
	if (!isset($everyday['time']) || $everyday['time'] < (time() - 86400)) {
		$hard_process = true;
		if (function_exists('set_time_limit')) set_time_limit(600);

		// 更新执行时间
		$db->update("UPDATE `cron` SET `time` = ? WHERE `id` = ?", [time(), 'everyday']);

		// 执行清理任务（直接用 $db->executeStatement 处理无返回值的操作）
		$db->executeStatement("DELETE FROM `guests` WHERE `date_last` < ?", [time() - 600]);
		$db->executeStatement("DELETE FROM `user` WHERE `activation` IS NOT NULL AND `date_reg` < ?", [time() - 86400]);
		$db->executeStatement("DELETE FROM `password_reset_tokens` WHERE `created_at` < ?", [date('Y-m-d H:i:s')]);

		// 处理过期联系人
		$month_ago = time() - (86400 * 30);
		$deleted_konts = $db->queryAll("SELECT * FROM `users_konts` WHERE `type` = 'deleted' AND `time` < ?", [$month_ago]);

		foreach ($deleted_konts as $deleted) {
			$db->executeStatement("DELETE FROM `users_konts` WHERE `id_user` = ? AND `id_kont` = ?", [$deleted['id_user'], $deleted['id_kont']]);

			// 检查对方是否也没有联系了
			$count = $db->queryColumn("SELECT COUNT(*) FROM `users_konts` WHERE `id_kont` = ? AND `id_user` = ?", [$deleted['id_user'], $deleted['id_kont']]);
			if ($count == 0) {
				$db->executeStatement("DELETE FROM `mail` WHERE (`id_user` = ? AND `id_kont` = ?) OR (`id_kont` = ? AND `id_user` = ?)", 
					[$deleted['id_user'], $deleted['id_kont'], $deleted['id_user'], $deleted['id_kont']]);
			}
		}

		// 数据库优化
		$tables = $db->queryAll("SHOW TABLES FROM `" . $set['sql_db_name'] . "`");
		foreach ($tables as $table) {
			$tableName = reset($table);
			$db->executeStatement("OPTIMIZE TABLE `$tableName`");
		}
	}
}

// 发送预定邮件
$q = dbquery("SELECT * FROM `mail_to_send` LIMIT 1");
if (dbrows($q) != 0) {
	$mail = dbassoc($q);
	$adds = "From: \"admin@$_SERVER[HTTP_HOST]\" <admin@$_SERVER[HTTP_HOST]>\n";
	$adds .= "Content-Type: text/html; charset=utf-8\n";
	mail($mail['mail'], '=?utf-8?B?' . base64_encode($mail['them']) . '?=', $mail['msg'], $adds);
	dbquery("DELETE FROM `mail_to_send` WHERE `id` = '$mail[id]'");
}

// 确保所有通过 GET/POST 方法传入的数据都被清理和转义（没卵用）
/*
if(isset($_GET)) {
	foreach($_GET as $key => $value) {
		$_GET[$key] = fiera($value);
	}
}
if (isset($_POST)) {
	foreach($_POST as $key => $value) {
		$_POST[$key] = fiera($value);
	}
}
*/
