<?php
$err = NULL;
$db = NULL;
$time = NULL;
$passgen = NULL;
$sess = NULL;
$ip = NULL;
$ua = NULL;
$iplong = NULL;
$webbrowser = NULL;
$tpanel = NULL;
//if (function_exists('error_reporting')) @error_reporting(0); // 禁用错误显示
// 将脚本执行限制为 60 秒
//if (function_exists('set_time_limit')) @set_time_limit(60);
//if (function_exists('ini_set')) {
//	ini_set('display_errors', false); // 禁用错误显示
//	ini_set('register_globals', false); // 消除全局变量
//	ini_set('session.use_cookies', true); // 使用 Cookie 进行会话
//	ini_set('session.use_trans_sid', true); // 使用 URL 传输会话
//	ini_set('arg_separator.output', "&amp;"); // URL 中的变量分隔符（用于与 XML 匹配）
// }


// 强制削减全局变量
if (ini_get('register_globals')) {
	$allowed = array('_ENV' => 1, '_GET' => 1, '_POST' => 1, '_COOKIE' => 1, '_FILES' => 1, '_SERVER' => 1, '_REQUEST' => 1, 'GLOBALS' => 1);
	foreach ($GLOBALS as $key => $value) {
		if (!isset($allowed[$key])) {
			unset($GLOBALS[$key]);
		}
	}
}

list($msec, $sec) = explode(chr(32), microtime()); // 脚本启动时间
$conf['headtime'] = $sec + $msec;
$time = time();





$phpvervion = explode('.', phpversion());
$conf['phpversion'] = $phpvervion[0];


$upload_max_filesize = ini_get('upload_max_filesize');
if (preg_match('#([0-9]*)([a-z]*)#i', $upload_max_filesize, $varrs)) {
	if ($varrs[2] == 'M') $upload_max_filesize = $varrs[1] * 1048576;
	elseif ($varrs[2] == 'K') $upload_max_filesize = $varrs[1] * 1024;
	elseif ($varrs[2] == 'G') $upload_max_filesize = $varrs[1] * 1024 * 1048576;
}

function fiera($msg) {
	$msg = str_replace("script", "sсript", $msg);
	$msg = str_replace("javаscript:", "javаscript:", $msg);
	if ($_SERVER['PHP_SELF'] != '/adm_panel/mysql.php')
		$msg = addslashes(stripslashes(trim($msg)));
	return $msg;
}
// Полночь
$ftime = mktime(0, 0, 0);

// 引入第三方库
require_once __DIR__ . '/../../vendor/autoload.php';