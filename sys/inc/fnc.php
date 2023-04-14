<?php
// 函数别名
function my_esc($text, $br = NULL)
{ // 剪切所有不可读字符
	if ($br != NULL)
		for ($i = 0; $i <= 31; $i++) $text = str_replace(chr($i), NULL, $text);
	else {
		for ($i = 0; $i < 10; $i++) $text = str_replace(chr($i), NULL, $text);
		for ($i = 11; $i < 20; $i++) $text = str_replace(chr($i), NULL, $text);
		for ($i = 21; $i <= 31; $i++) $text = str_replace(chr($i), NULL, $text);
	}
	return $text;
}

// 对于php4（替代file_put_contents）
if (!function_exists('file_put_contents')) {
	function file_put_contents($file, $data)
	{
		$f = @fopen($file, 'w');
		return @fwrite($f, $data);
		@fclose($f);
	}
}

if ($set['antidos']) { // 来自单个 IP 的频繁请求保护
	$antidos[] = array('time' => $time);
	$k_loads = 0;
	if (test_file(H . 'sys/tmp/antidos_' . $iplong . '.dat')) {
		$antidos_dat = unserialize(file_get_contents(H . 'sys/tmp/antidos_' . $iplong . '.dat'));
		for ($i = 0; $i < 150 && $i < sizeof($antidos_dat); $i++) {
			if ($antidos_dat[$i]['time'] > $time - 5) {
				$k_loads++;
				$antidos[] = $antidos_dat[$i];
			}
		}
	}
	if ($k_loads > 100) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `ban_ip` WHERE `min` <= '$iplong' AND `max` >= '$iplong'"), 0) == 0)
			dbquery("INSERT INTO `ban_ip` (`min`, `max`, `prich`) values('$iplong', '$iplong', 'AntiDos')", $db);
	}
	@file_put_contents(H . 'sys/tmp/antidos_' . $iplong . '.dat', serialize($antidos));
	@chmod(H . 'sys/tmp/antidos_' . $iplong . '.dat', 0777);
}

// 禁止文字antimat会自动发出警告，然后禁止
function antimat($str)
{
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
	// 			header('Location: /user/ban.php?' . SID);
	// 			exit;
	// 		}
	// 		return $censure;
	// 	} else return false;
	// } else return false;
	return false;
}

// 递归删除文件夹
function delete_dir($dir)
{
	if (is_dir($dir)) {
		$od = opendir($dir);
		while ($rd = readdir($od)) {
			if ($rd == '.' || $rd == '..') continue;
			if (is_dir("$dir/$rd")) {
				@chmod("$dir/$rd", 0777);
				delete_dir("$dir/$rd");
			} else {
				@chmod("$dir/$rd", 0777);
				@unlink("$dir/$rd");
			}
		}
		closedir($od);
		@chmod("$dir", 0777);
		return @rmdir("$dir");
	} else {
		@chmod("$dir", 0777);
		@unlink("$dir");
	}
}
//curl支持函数
function get_curl($url, $post=0, $referer=0, $cookie=0, $header=0, $ua=0, $nobaody=0, $addheader=0)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$httpheader[] = "Accept: */*";
	$httpheader[] = "Accept-Encoding: gzip,deflate,sdch";
	$httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
	$httpheader[] = "Connection: close";
	if($addheader){
		$httpheader = array_merge($httpheader, $addheader);
	}
	curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
	if ($post) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	if ($header) {
		curl_setopt($ch, CURLOPT_HEADER, true);
	}
	if ($cookie) {
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	}
	if($referer){

		curl_setopt($ch, CURLOPT_REFERER, $referer);

	}
	if ($ua) {
		curl_setopt($ch, CURLOPT_USERAGENT, $ua);
	}
	else {
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 4.0.4; es-mx; HTC_One_X Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0");
	}
	if ($nobaody) {
		curl_setopt($ch, CURLOPT_NOBODY, 1);
	}
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}
//获取ip位置信息
function get_ip_city($ip)
{
    $url = 'http://whois.pconline.com.cn/ipJson.jsp?json=true&ip=';
    $city = get_curl($url . $ip);
	$city = mb_convert_encoding($city, "UTF-8", "GB2312");
    $city = json_decode($city, true);
    if ($city['city']) {
        $location = $city['pro'].$city['city'];
    } else {
        $location = $city['pro'];
    }
	if($location){
		return $location;
	}else{
		return false;
	}
}
//反黑客攻击行为
if (!defined("ADMIN")) {

	$hackparam = $_SERVER['QUERY_STRING'];
	$hackparam = htmlspecialchars($hackparam);

	$hackcmd = array('chr(', 'r57shell', 'remview', '%27', 'config=', 'OUTFILE%20', 'spnuke_authors', 'spnuke_admins', 'uname%20', 'netstat%20', 'rpm%20', 'passwd', '%20', 'del%20', 'deltree%20', 'format%20', 'start%20', 'wget', 'group_access', '%3E', '%3С',  'select%20', 'SELECT', 'cmd=', 'rush=', 'union', 'javascript:', 'UNION', 'echr(', 'esystem(', 'cp%20', 'mdir%20', 'mcd%20', 'mrd%20', 'rm%20', 'mv%20', 'rmdir%20', 'chmod(', 'chmod%20', 'chown%20', 'chgrp%20', 'locate%20', 'diff%20', 'kill%20', 'kill(', 'killall', 'cmd', 'command', 'fetch', 'whereis', 'grep%20', 'ls -', 'lynx', 'su%20root', 'test', 'etc/passwd',  "'", '%60', '%00', '%F20', 'echo', 'write(', 'killall', 'passwd%20', 'telnet%20', 'vi(', 'vi%20', 'INSERT%20INTO', 'SELECT%20', 'javascript', 'fopen', 'fwrite', '$_REQUEST', '$_GET', '<script>', 'alert', '&lt', '&gt'); //禁用参数和值

	$checkcmd = str_replace($hackcmd, 'X', $hackparam);

	if ($hackparam != $checkcmd) {
		dbquery("INSERT INTO ban_ip (min, max) VALUES(\"$iplong\", \"$iplong\");");
		dbquery('INSERT INTO mail (id_user, id_kont, msg, time) VALUES("0", "1", "IP: '.$ip.' UA: '.$ua.' 位置: '.get_ip_city($ip).'正在进行黑客攻击", "'.$time.'");');
		die('<h2>攻击失败！</h2><br>你的浏览器：<b>'.$ua.'</b><br>你的IP： <b>'.$ip.'</b><br>位置：<b>'.get_ip_city($ip).'</b><br><b>已被记录，不要尝试违法操作！</b><br><br>有这时间多休息吧！！！！');
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

if (!isset($hard_process)) {
	$q = dbquery("SELECT * FROM `cron` WHERE `id` = 'visit' LIMIT 1");
	if (dbrows($q) == 0) dbquery("INSERT INTO `cron` (`id`, `time`) VALUES ('visit', '$time')");
	$visit = dbassoc($q);
	if (!isset($visit['time']) || isset($visit['time']) && $visit['time'] < time() - 60 * 60 * 24) {
		if (function_exists('set_time_limit')) @set_time_limit(600); // Ставим ограничение на 10 минут
		$last_day = mktime(0, 0, 0, date('m'), date('d') - 1); // начало вчерашних суток
		$today_time = mktime(0, 0, 0); // начало сегодняшних суток
		if (dbresult(dbquery("SELECT COUNT(*) FROM `visit_everyday` WHERE `time` = '$last_day'"), 0) == 0) {
			$hard_process = true;
			// записываем общие данные за вчерашние сутки в отдельную таблицу
			dbquery("INSERT INTO `visit_everyday` (`host` , `host_ip_ua`, `hit`, `time`) VALUES ((SELECT COUNT(DISTINCT `ip`) FROM `visit_today` WHERE `time` < '$today_time'),(SELECT COUNT(DISTINCT `ip`, `ua`) FROM `visit_today` WHERE `time` < '$today_time'),(SELECT COUNT(*) FROM `visit_today` WHERE `time` < '$today_time'),'$last_day')");
			dbquery('DELETE FROM `visit_today` WHERE `time` < ' . $today_time);
		}
	}
}

// 现场迁移记录
if (isset($_SERVER['HTTP_REFERER']) && !preg_match('#' . preg_quote($_SERVER['HTTP_HOST']) . '#', $_SERVER['HTTP_REFERER']) && $ref = @parse_url($_SERVER['HTTP_REFERER'])) {
	if (isset($ref['host'])) $_SESSION['http_referer'] = $ref['host'];
}

function br($msg, $br = '<br />')
{
	return preg_replace("#((<br( ?/?)>)|\n|\r)+#i", $br, $msg);
} // 换行

function esc($text, $br = NULL)
{ // 过滤所有不可读字符
	if ($br != NULL)
		for ($i = 0; $i <= 31; $i++) $text = str_replace(chr($i), NULL, $text);
	else {
		for ($i = 0; $i < 10; $i++) $text = str_replace(chr($i), NULL, $text);
		for ($i = 11; $i < 20; $i++) $text = str_replace(chr($i), NULL, $text);
		for ($i = 21; $i <= 31; $i++) $text = str_replace(chr($i), NULL, $text);
	}
	return $text;
}



// 语句定义
function opsos($ips = NULL)
{
	global $ip;
	if ($ips == NULL) $ips = $ip;
	$ipl = ip2long($ips);
	if (dbresult(dbquery("SELECT COUNT(*) FROM `opsos` WHERE `min` <= '$ipl' AND `max` >= '$ipl'"), 0) != 0) {
		$opsos = dbassoc(dbquery("SELECT opsos FROM `opsos` WHERE `min` <= '$ipl' AND `max` >= '$ipl' LIMIT 1"));
		return stripcslashes(htmlspecialchars($opsos['opsos']));
	} else return false;
}
// 时间输出
function vremja($time = NULL)
{
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
	$timep = str_replace("Jan", "1月", $timep);
	$timep = str_replace("Feb", "2月", $timep);
	$timep = str_replace("Mar", "3月", $timep);
	$timep = str_replace("May", "4月", $timep);
	$timep = str_replace("Apr", "5月", $timep);
	$timep = str_replace("Jun", "6月", $timep);
	$timep = str_replace("Jul", "7月", $timep);
	$timep = str_replace("Aug", "8月", $timep);
	$timep = str_replace("Sep", "9月", $timep);
	$timep = str_replace("Oct", "10月", $timep);
	$timep = str_replace("Nov", "11月", $timep);
	$timep = str_replace("Dec", "12月", $timep);
	return $timep;
}

// 只供已登记人士使用
function only_reg($link = NULL)
{
	global $user;
	if (!isset($user)) {
		if ($link == NULL) $link = '/index.php?' . SID;
		header("Location: $link");
		exit;
	}
}


// 只适用于未登记的人
function only_unreg($link = NULL)
{
	global $user;
	if (isset($user)) {
		if ($link == NULL) $link = '/index.php?' . SID;
		header("Location: $link");
		exit;
	}
}


// 仅适用于访问级别大于或等于 $level
function only_level($level = 0, $link = NULL)
{
	global $user;
	if (!isset($user) || $user['level'] < $level) {
		if ($link == NULL) $link = '/index.php?' . SID;
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
		if (function_exists('set_time_limit')) @set_time_limit(600); // Ставим ограничение на 10 минут
		dbquery("UPDATE `cron` SET `time` = '" . time() . "' WHERE `id` = 'everyday'");
		dbquery("DELETE FROM `guests` WHERE `date_last` < '" . (time() - 600) . "'");
		dbquery("DELETE FROM `chat_post` WHERE `time` < '" . (time() - 60 * 60 * 24) . "'"); // удаление старых постов в чате
		dbquery("DELETE FROM `user` WHERE `activation` != null AND `time_reg` < '" . (time() - 60 * 60 * 24) . "'"); // удаление неактивированных аккаунтов

		// 删除所有一个多月前标记为删除的联系人
		$qd = dbquery("SELECT * FROM `users_konts` WHERE `type` = 'deleted' AND `time` < " . ($time - 60 * 60 * 24 * 30));
		while ($deleted = dbarray($qd)) {
			dbquery("DELETE FROM `users_konts` WHERE `id_user` = '$deleted[id_user]' AND `id_kont` = '$deleted[id_kont]'");

			if (dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_kont` = '$deleted[id_user]' AND `id_user` = '$deleted[id_kont]'"), 0) == 0) {
				// если юзер не находится в контакте у другого, то удаляем и все сообщения
				dbquery("DELETE FROM `mail` WHERE `id_user` = '$deleted[id_user]' AND `id_kont` = '$deleted[id_kont]' OR `id_kont` = '$deleted[id_user]' AND `id_user` = '$deleted[id_kont]'");
			}
		}
		$tab = dbquery('SHOW TABLES FROM ' . $set['mysql_db_name']);
		for ($i = 0; $i < dbrows($tab); $i++) {
			dbquery("OPTIMIZE TABLE `" . $tab . "`"); // 表的优化
		}
	}
}


// 错误输出
function err()
{
	global $err;
	if (isset($err)) {
		if (is_array($err)) {
			foreach ($err as $key => $value) {
				echo "<div class='err'>$value</div>";
			}
		} else echo "<div class='err'>$err</div>";
	}
}

function msg($msg)
{
	echo "<div class='msg'>$msg</div>";
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
function save_settings($set)
{
	unset($set['web']);
	if ($fopen = @fopen(H . 'sys/dat/settings_6.2.dat', 'w')) {
		@fputs($fopen, serialize($set));
		@fclose($fopen);
		@chmod(H . 'sys/dat/settings_6.2.dat', 0755);
		return TRUE;
	} else
		return FALSE;
}

// 管理行动记录
function admin_log($mod, $act, $opis)
{
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
	dbquery("INSERT INTO `admin_log` (`time`, `id_user`, `mod`, `act`, `opis`) VALUES
('" . time() . "','$user[id]', '$id_mod', '$id_act', '" . my_esc($opis) . "')");
}


// 从文件夹"sys/fnc"加载其余功能 
$opdirbase = opendir(H . 'sys/fnc');

while ($filebase = readdir($opdirbase)) {
	if (preg_match('#\.php$#i', $filebase)) {
		include_once(H . 'sys/fnc/' . $filebase);
	}
}

// 参观记录
dbquery("INSERT INTO `visit_today` (`ip` , `ua`, `time`) VALUES ('$iplong', '" . @my_esc($_SERVER['HTTP_USER_AGENT']) . "', '$time')");


function ages($age)
{
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

//删除更新代码

function version_stable()
{
	//$content = file_get_contents("https://dcms-social.ru/launcher/social.json");
	//$data = json_decode($content, TRUE);
	return $data['stable']['version'];
}
function t_toolbar_html()
{
	global $set;

	echo '<div class="mess">
      <b>Admin Tool</b> :: <a href="/">网站主页</a>  |<a href="/plugins/admin/">管理员</a> | <a href="/adm_panel/">控制面板</a> |<a target="_blank" href="https://dcms-social.ru">DCMS-Social.ru</a>
       v' . $set['dcms_version'];
	if (status_version() < 0) {
		echo '<center><font color="red">有一个新版本 - ' . version_stable() . '! <a href="/adm_panel/update.php">详细</a></font></center>';
	}
	echo '</div>';
}

function add_header($value)
{
	static $add;
	return $add[] = $value;
	header_html($add);
}
function header_html($add = null)
{
	static $header;
	if ($add == null) {
		//   var_dump($header);
		echo "" . $header;
	} else $header = $add;
}

//获取远程更新代码
//影响网站效率
function status_version()
{
	// global $set;
	// $content = file_get_contents("https://dcms-social.ru/launcher/social.json");
	// $data = json_decode($content, TRUE);
	// return version_compare($set['dcms_version'], $data['stable']['version']);
}
