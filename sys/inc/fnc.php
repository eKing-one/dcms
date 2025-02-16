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

// 对于php4（替代file_put_contents）
if (!function_exists('file_put_contents')) {
	function file_put_contents($file, $data) {
		$f = @fopen($file, 'w');
		return @fwrite($f, $data);
		@fclose($f);
	}
}

// DOS攻击防护
if ($set['antidos']) {
	// 插入当前请求记录
	dbquery("INSERT INTO ip_requests (`ip`) VALUES ('$ip')");

	// 查询该 IP 在过去 5 秒内的请求次数，如果请求次数超过 100，则封禁 IP
	if (dbresult(dbquery("SELECT COUNT(*) FROM ip_requests WHERE ip = '$ip' AND time > FROM_UNIXTIME('$time' - 5)"), 0) > 100) {
		// 如果请求次数超过 100，则封禁 IP
		if (dbresult(dbquery("SELECT COUNT(*) FROM `ban_ip` WHERE `min` <= '$ip' AND `max` >= '$ip'"), 0) == 0) {
			dbquery("INSERT INTO `ban_ip` (`min`, `max`, `prich`) values('$ip', '$ip', 'AntiDos')");
		}
	}

	// 定期清理过期的请求记录
	dbquery("DELETE FROM ip_requests WHERE time < '" . date('Y-m-d H:i:s', $time - 3600) . "'");  // 删除 1 小时之前的记录

}

/**
 * 删除超过一小时的 IP 封禁记录
 * 
 * 仅删除 `prich` 字段为 `AntiDos` 且 `created_at` 早于一天前的记录
 */
dbquery("DELETE FROM `ban_ip` WHERE `prich` = 'AntiDos' AND `created_at` < '" . date('Y-m-d H:i:s', time() - 3600 * 24) . "'");
dbquery("DELETE FROM `ban_ip` WHERE `prich` = 'Inject' AND `created_at` < '" . date('Y-m-d H:i:s', time() - 3600 * 24) . "'");


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

// curl相关函数
function get_curl($url, $post_data=null, $referer=null, $cookie=null, $header=false, $ua=null, $nobody=false, $addheader=null) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);			// 设置URL
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);	// 启用SSL证书验证
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);	// 启用SSL主机验证
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	// 将结果以字符串形式返回
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);  		// 设置超时10秒

	$httpheader = [
		"Accept: */*",
		"Accept-Encoding: gzip,deflate,sdch",
		"Accept-Language: zh-CN,zh;q=0.8",
		"Connection: close"
	];
	if ($addheader) {
		$httpheader = array_merge($httpheader, $addheader);
	}
	curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);

	if ($post_data) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	}

	if ($header) {
		curl_setopt($ch, CURLOPT_HEADER, true);
	}

	if ($cookie) {
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	}

	if ($referer) {
		curl_setopt($ch, CURLOPT_REFERER, $referer);
	}

	if ($ua) {
		curl_setopt($ch, CURLOPT_USERAGENT, $ua);
	} else {
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 4.0.4; es-mx; HTC_One_X Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0");
	}

	if ($nobody) {
		curl_setopt($ch, CURLOPT_NOBODY, 1);
	}

	$ret = curl_exec($ch);

	if (curl_errno($ch)) {
		$error_msg = curl_error($ch);
		curl_close($ch);
		return ['error' => $error_msg];
	}

	curl_close($ch);
	return $ret;
}

/**
 * 检查一个IP地址是否位于给定的最小IP和最大IP之间
 *
 * 该函数验证传入的 `minIp`、`maxIp` 和 `detectIp` 是否是有效的IP地址，
 * 然后判断 `detectIp` 是否在 `minIp` 和 `maxIp` 之间的范围内。
 * 
 * @param string $detectIp 要检测的IP地址
 * @param string $minIp 最小IP地址（范围的下限）
 * @param string $maxIp 最大IP地址（范围的上限）
 * @return bool 如果 `detectIp` 在给定范围内，返回 `true`，否则返回 `false`。
 * 
 * @throws InvalidArgumentException 如果任何IP地址无效，将返回 `false`。
 */
function isIpInRangeBetweenBounds($detectIp, $minIp, $maxIp) {
	if ((filter_var($detectIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($detectIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) && (filter_var($minIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($minIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) && (filter_var($maxIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($maxIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))) {
		if (\IPLib\Factory::parseAddressString($detectIp)->matches(\IPLib\Factory::getRangeFromBoundaries($minIp, $maxIp))) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

// 获取ip位置信息
function get_ip_address($ip) {
	$url = 'http://ip-api.com/json/';
	$otherParameters = '?fields=status,message,country,countryCode,region,regionName,city,district,lat,lon,isp,org,as,reverse,mobile,proxy,hosting&lang=zh-CN';
	$address = get_curl($url . $ip . $otherParameters);
	
	// 处理可能的curl错误
	if (isset($address['error'])) {
		return false;
	}

	$address = json_decode($address, true);

	// 处理JSON解析错误
	if (json_last_error() !== JSON_ERROR_NONE) {
		return 'JSON error';
	}

	$location = $address['country'];
	if (!empty($address['regionName'])) {$location .= ',' . $address['regionName'];}
	if (!empty($address['city'])) {$location .= ',' . $address['city'];}
	if ($address['proxy'] == true) {$location .= ',通过代理访问';}

	return $location ?: 'N/A';
}

//反黑客攻击行为
if (!defined("ADMIN")) {
	$hackparam = htmlspecialchars((string) ($_SERVER['QUERY_STRING'] ?? ''));

	$hackcmd = array('chr(', 'r57shell', 'remview', '%27', 'config=', 'OUTFILE%20', 'spnuke_authors', 'spnuke_admins', 'uname%20', 'netstat%20', 'rpm%20', 'passwd', '%20', 'del%20', 'deltree%20', 'format%20', 'start%20', 'wget', 'group_access', '%3E', '%3С',  'select%20', 'SELECT', 'cmd=', 'rush=', 'union', 'javascript:', 'UNION', 'echr(', 'esystem(', 'cp%20', 'mdir%20', 'mcd%20', 'mrd%20', 'rm%20', 'mv%20', 'rmdir%20', 'chmod(', 'chmod%20', 'chown%20', 'chgrp%20', 'locate%20', 'diff%20', 'kill%20', 'kill(', 'killall', 'cmd', 'command', 'fetch', 'whereis', 'grep%20', 'ls -', 'lynx', 'su%20root', 'test', 'etc/passwd',  "'", '%60', '%00', '%F20', 'echo', 'write(', 'killall', 'passwd%20', 'telnet%20', 'vi(', 'vi%20', 'INSERT%20INTO', 'SELECT%20', 'javascript', 'fopen', 'fwrite', '$_REQUEST', '$_GET', '<script>', 'alert', '&lt', '&gt'); //禁用参数和值

	$checkcmd = str_replace($hackcmd, 'X', $hackparam);

	if ($hackparam != $checkcmd) {
		dbquery("INSERT INTO ban_ip (min, max, prich) VALUES(\"$ip\", \"$ip\", \"Inject\");");
		dbquery('INSERT INTO mail (id_user, id_kont, msg, time) VALUES("0", "1", "IP: '.$ip.' UA: '.$ua.' 位置: '.get_ip_address($ip).' 正在进行黑客攻击", "'.$time.'");');
		die('<h2>检测到攻击！</h2><br>你的浏览器：<b>'.$ua.'</b><br>你的IP： <b>'.$ip.'</b><br><b>已被记录，不要尝试违法操作！</b><br><br>有这时间多休息吧！！！');
	}
}

// 正在清除临时文件夹
if (!isset($hard_process)) {
	$q = dbquery("SELECT * FROM `cron` WHERE `id` = 'clear_tmp_dir'");
	if (dbrows($q) == 0) dbquery("INSERT INTO `cron` (`id`, `time`) VALUES ('clear_tmp_dir', '$time')");
	$clear_dir = dbassoc($q);
	if (!isset($clear_dir['time']) || isset($clear_dir['time']) && $clear_dir['time'] < $time - 60 * 60 * 24) {
		$hard_process = true;
		dbquery("UPDATE `cron` SET `time` = '$time' WHERE `id` = 'clear_tmp_dir'");
		// if (function_exists('curl_init')) {
		// 	$ch = curl_init();
		// 	curl_setopt($ch, CURLOPT_URL, 'https://dcms-social.ru/curl.php?site=' . $_SERVER['HTTP_HOST'] . '&version=' . $set['dcms_version'] . '&title=' . $set['title']);
		// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// 	$data = curl_exec($ch);
		// 	curl_close($ch);
		// }
		$od = opendir(H . 'sys/tmp/');
		while ($rd = readdir($od)) {
			if (!preg_match('#^\.#', $rd) && filectime(H . 'sys/tmp/' . $rd) < $time - 60 * 60 * 24) {
				@delete_dir(H . 'sys/tmp/' . $rd);
			}
		}
		closedir($od);
	}
}
// 统计数据汇总

// 每日访问记录
if (!isset($hard_process)) {
	$q = dbquery("SELECT * FROM `cron` WHERE `id` = 'visit' LIMIT 1");
	if (dbrows($q) == 0) dbquery("INSERT INTO `cron` (`id`, `time`) VALUES ('visit', '$time')");
	$visit = dbassoc($q);
	if (!isset($visit['time']) || isset($visit['time']) && $visit['time'] < time() - 60 * 60 * 24) {
		if (function_exists('set_time_limit')) @set_time_limit(600); // 将限制设置为 10 分钟
		$last_day = mktime(0, 0, 0, date('m'), date('d') - 1); // 昨天的开始
		$today_time = mktime(0, 0, 0); // 今天的开始
		if (dbresult(dbquery("SELECT COUNT(*) FROM `visit_everyday` WHERE `time` = '$last_day'"), 0) == 0) {
			$hard_process = true;
			// 在单独的表中记下昨天的一般数据
			dbquery("INSERT INTO `visit_everyday` (`host` , `host_ip_ua`, `hit`, `time`) VALUES ((SELECT COUNT(DISTINCT `ip`) FROM `visit_today` WHERE `time` < '$today_time'),(SELECT COUNT(DISTINCT `ip`, `ua_hash`) FROM `visit_today` WHERE `time` < '$today_time'),(SELECT COUNT(*) FROM `visit_today` WHERE `time` < '$today_time'),'$last_day')");
			dbquery('DELETE FROM `visit_today` WHERE `time` < ' . $today_time);
		}
	}
}

// 现场迁移记录
if (isset($_SERVER['HTTP_REFERER']) && !preg_match('#' . preg_quote($_SERVER['HTTP_HOST']) . '#', $_SERVER['HTTP_REFERER']) && $ref = @parse_url($_SERVER['HTTP_REFERER'])) {
	if (isset($ref['host'])) $_SESSION['http_referer'] = $ref['host'];
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

/**
 * 根据给定的 IP 地址查询数据库，检查其是否在某个 IP 范围内，如果在范围内则返回对应的 'opsos' 字段值。
 * 
 * 该函数首先检查传入的 IP 地址 `$ips` 是否有效，如果没有传入参数，则使用全局变量 `$ip`。
 * 然后，它会查询数据库中所有的 IP 范围（`min` 到 `max`）以及与之相关联的 `opsos` 字段。
 * 对于每一条记录，使用 `isIpInRangeBetweenBounds` 函数判断传入的 IP 是否在当前记录的 `min` 和 `max` 范围内。
 * 如果 IP 在范围内，则返回对应的 `opsos` 值；否则，继续检查下一条记录。
 * 如果没有找到匹配的记录，返回 `false`。
 * 
 * @param string $ips 要查询的 IP 地址。若未提供，则使用全局变量 `$ip`。
 * @return mixed 如果找到符合条件的记录，返回经过 `htmlspecialchars` 和 `stripcslashes` 处理的 `opsos` 字段值；否则返回 `false`。
 */
function opsos($ips = NULL) {
	global $ip;
	// 如果没有传入 IP 地址，使用全局变量 $ip
	if ($ips == NULL) $ips = $ip;

	// 查询数据库，获取所有的 min、max 和 opsos 字段
	$result = dbquery("SELECT min, max, opsos FROM `opsos`");
	// 遍历查询结果
	while ($row = dbassoc($result)) {
		// 使用 isIpInRangeBetweenBounds 判断 IP 是否在当前记录的 min 和 max 范围内
		if (isIpInRangeBetweenBounds($ips, $row['min'], $row['max'])) {
			// 如果 IP 在范围内，返回处理后的 opsos 值
			return stripcslashes(htmlspecialchars($row['opsos']));
		}
	}
	// 如果没有找到符合条件的记录，返回 false
	return false;
}

// 时间输出
function vremja($time = NULL) {
	global $user;
	if ($time == NULL) $time = time();
	if (isset($user)) $time = $time + $user['set_timesdvig'] * 60 * 60;
	$timep = "" . date("Y m d H:i", $time) . "";
	$time_p[0] = date("Y m d", $time);
	$time_p[1] = date("H:i", $time);
	if ($time_p[0] == date("Y m d")) $timep = date("H:i:s", $time);
	if (isset($user)) {
		if ($time_p[0] == date("Y m d", time() + $user['set_timesdvig'] * 60 * 60)) $timep = date("H:i:s", $time);
		if ($time_p[0] == date("Y m d", time() - 60 * 60 * (24 - $user['set_timesdvig']))) $timep = "昨天$time_p[1]";
	} else {
		if ($time_p[0] == date("Y m d")) $timep = date("H:i:s", $time);
		if ($time_p[0] == date("Y m d", time() - 60 * 60 * 24)) $timep = "昨天$time_p[1]";
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

if (!isset($hard_process)) {
	$q = dbquery("SELECT * FROM `cron` WHERE `id` = 'everyday'");
	if (dbrows($q) == 0) dbquery("INSERT INTO `cron` (`id`, `time`) VALUES ('everyday', '" . time() . "')");
	$everyday = dbassoc($q);
	if (!isset($everyday['time']) || isset($everyday['time']) && $everyday['time'] < time() - 60 * 60 * 24) {
		$hard_process = true;
		if (function_exists('set_time_limit')) set_time_limit(600); // 将限制设置为 10 分钟
		dbquery("UPDATE `cron` SET `time` = '" . time() . "' WHERE `id` = 'everyday'");
		dbquery("DELETE FROM `guests` WHERE `date_last` < '" . (time() - 600) . "'");
		dbquery("DELETE FROM `chat_post` WHERE `time` < '" . (time() - 60 * 60 * 24) . "'"); // 删除旧的聊天帖子
		dbquery("DELETE FROM `user` WHERE `activation` != null AND `date_reg` < '" . (time() - 60 * 60 * 24) . "'"); // 删除未激活的账户

		// 删除过期的 password reset token
		dbquery("DELETE FROM `password_reset_tokens` WHERE `created_at` < '" . date('Y-m-d H:i:s') . "'");

		// 删除所有一个多月前标记为删除的联系人
		$qd = dbquery("SELECT * FROM `users_konts` WHERE `type` = 'deleted' AND `time` < " . ($time - 60 * 60 * 24 * 30));
		while ($deleted = dbarray($qd)) {
			dbquery("DELETE FROM `users_konts` WHERE `id_user` = '{$deleted['id_user']}' AND `id_kont` = '{$deleted['id_kont']}'");

			if (dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_kont` = '{$deleted['id_user']}' AND `id_user` = '{$deleted['id_kont']}'"), 0) == 0) {
				// 如果用户未与其他人联系，则删除所有消息
				dbquery("DELETE FROM `mail` WHERE `id_user` = '{$deleted['id_user']}' AND `id_kont` = '{$deleted['id_kont']}' OR `id_kont` = '{$deleted['id_user']}' AND `id_user` = '{$deleted['id_kont']}'");
			}
		}
		$tab = dbquery('SHOW TABLES FROM ' . $set['mysql_db_name']);
		while ($table = mysqli_fetch_row($tab)) {
			dbquery("OPTIMIZE TABLE `{$table[0]}`"); // 表的优化
		}
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




// 发送预定邮件
$q = dbquery("SELECT * FROM `mail_to_send` LIMIT 1");
if (dbrows($q) != 0) {
	$mail = dbassoc($q);
	$adds = "From: \"admin@$_SERVER[HTTP_HOST]\" <admin@$_SERVER[HTTP_HOST]>\n";
	$adds .= "Content-Type: text/html; charset=utf-8\n";
	mail($mail['mail'], '=?utf-8?B?' . base64_encode($mail['them']) . '?=', $mail['msg'], $adds);
	dbquery("DELETE FROM `mail_to_send` WHERE `id` = '$mail[id]'");
}

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

// 管理行动记录
function admin_log($mod, $act, $opis) {
	global $user;

	$q = dbquery("SELECT * FROM `admin_log_mod` WHERE `name` = '" . my_esc($mod) . "' LIMIT 1");
	if (dbrows($q) == 0) {
		dbquery("INSERT INTO `admin_log_mod` (`name`) VALUES ('" . my_esc($mod) . "')");
		$id_mod = dbinsertid();
	} else $id_mod = dbresult($q, 0);

	$q2 = dbquery("SELECT * FROM `admin_log_act` WHERE `name` = '" . my_esc($act) . "' AND `id_mod` = '$id_mod' LIMIT 1");
	if (dbrows($q2) == 0) {
		dbquery("INSERT INTO `admin_log_act` (`name`, `id_mod`) VALUES ('" . my_esc($act) . "', '$id_mod')");
		$id_act = dbinsertid();
	} else $id_act = dbresult($q2, 0);
	dbquery("INSERT INTO `admin_log` (`time`, `id_user`, `mod`, `act`, `opis`) VALUES ('" . time() . "','$user[id]', '$id_mod', '$id_act', '" . my_esc($opis) . "')");
}


// 从文件夹"sys/fnc"加载其余功能 
$opdirbase = opendir(H . 'sys/fnc');

while ($filebase = readdir($opdirbase)) {
	if (preg_match('#\.php$#i', $filebase)) {
		include_once(H . 'sys/fnc/' . $filebase);
	}
}

// 参观记录
dbquery("INSERT INTO `visit_today` (`ip`, `ua`, `ua_hash`, `time`) VALUES ('$ip', '" . my_esc(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . "', '" . md5(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . "', '$time')");


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

function t_toolbar_html() {
	global $set;

	$status_version_data = getLatestStableRelease();
	echo '<div class="mess">
	  <b>Admin Tool</b> :: <a href="/">网站首页</a> | <a href="/plugins/admin/">管理员</a> | <a href="/adm_panel/">控制面板</a>
	   v' . $set['dcms_version'];
	if (version_compare($set['dcms_version'], $status_version_data['version']) < 0) {
		echo '<center><font color="red">有一个新版本 - ' . $status_version_data['version'] . '! <a href="/adm_panel/update.php">详细</a></font></center>';
	}
	echo '</div>';
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