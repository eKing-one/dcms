<?php
$set = array(); // 具有设置的数组
$set_default = array();
$set_dinamic = array();
$set_replace = array();

// 正在加载默认设置。消除未定义变量的缺失
$default = @parse_ini_file(H.'sys/dat/default.ini',true);
$set_default = @$default['DEFAULT'];
$set_replace = @$default['REPLACE'];

// 检查 install 目录是否存在，如果存在就转跳到引擎安装界面
if (file_exists(H.'sys/dat/settings.php')) {
	$set_dinamic = include_once(H.'sys/dat/settings.php');
} elseif (file_exists(H.'install/index.php')) {
	header("Location: /install/");
	exit;
}

$set = @array_merge ($set_default, $set_dinamic, $set_replace);

if ($set['show_err_php']) {
	error_reporting(E_ALL); // 启用错误显示
	ini_set('display_errors',true); // 启用错误显示
}

// 解析 User-Agent 检查设备类型是否为 PC
if (isset($_SERVER["HTTP_USER_AGENT"]) && in_array(UAParser\Parser::create()->parse($_SERVER["HTTP_USER_AGENT"])->device->family, ['Desktop', 'Macintosh', 'Linux', 'Windows'])) {
    $webbrowser = true;
} else {
    $webbrowser = false;
}

$set['web'] = false;

function setset($name, $value=null) {
	global $set;
	$set[$name]= $value;
}