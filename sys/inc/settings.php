<?php
/*
 * MIT License
 * 
 * Copyright (c) 2025 GuGuan123
 * 
 * 本软件基于 MIT 许可证发布。具体许可条款如下：
 * 
 * 允许在本软件及其附带文档文件（以下简称“软件”）的基础上进行修改、复制、分发及/或销售，
 * 且在提供软件的副本时，需附上此许可证声明和版权声明。
 * 
 * 本软件按“原样”提供，不作任何形式的明示或暗示的担保，包括但不限于对适销性、适合某一特定用途的担保。
 * 在任何情况下，无论是在合同诉讼、侵权或其他诉讼中，作者或版权持有者对因使用本软件或其他交易的结果
 * 所产生的任何索赔、损害或其他责任不承担任何责任。
 * 
 * 你可以在 https://choosealicense.com/licenses/mit/ 查看详细的 MIT 原始许可证条款。
 */


// 加载网站设置
function getSet() {
	$set = array();
	$set_default = array();
	$set_dynamic = array();
	$set_replace = array();

	global $current_page;

	// 正在加载默认设置。消除未定义变量的缺失
	$default = parse_ini_file(__DIR__ . '/../dat/default.ini', true);
	$set_default = $default['DEFAULT'];
	$set_replace = $default['REPLACE'];

	// 检查 install 目录是否存在，如果存在就转跳到引擎安装界面
	if (file_exists(__DIR__ . '/../dat/settings.php')) {
		$set_dynamic = require_once(__DIR__ . '/../dat/settings.php');
	} elseif (file_exists(__DIR__ . '/../../install/index.php') && isset($current_page) && $current_page == 'index') {
		header('Location: install/');
		exit;
	} else {
		http_response_code(500);
		echo 'sys/dat/settings.php 消失了' . PHP_EOL;
		exit;
	}

	return array_merge($set_default, $set_dynamic, $set_replace);
}

$set = getSet();
if ($set['show_err_php']) {
	error_reporting(E_ALL); // 启用错误显示
	ini_set('display_errors', true); // 启用错误显示
}
$set['web'] = false;

// 解析 User-Agent 检查设备类型是否为 PC
if (!empty($_SERVER["HTTP_USER_AGENT"]) && !(new Detection\MobileDetect())->isMobile()) {
	$webbrowser = true;
} else {
	$webbrowser = false;
}
