<?php
// 初始化元数据关键词和描述，若未设置则为null
$set['meta_keywords'] = (isset($set['meta_keywords'])) ? $set['meta_keywords'] : null;
$set['meta_description'] = (isset($set['meta_description'])) ? $set['meta_description'] : null;

// 如果设置了关键词，则定义函数并使用输出缓冲来插入meta标签
if ($set['meta_keywords'] != NULL) {
	function meta_keywords($str) {
		global $set;
		return str_replace('</head>', '<meta name="keywords" content="' . $set['meta_keywords'] . '" />' . "</head>", $str); // 在<head>结束前插入meta关键词
	}
	ob_start('meta_keywords'); // 开启输出缓冲
}

// 如果设置了描述，则定义函数并使用输出缓冲来插入meta标签
if ($set['meta_description'] != NULL) {
	function meta_description($str) {
		global $set;
		return str_replace('</head>', '<meta name="description" content="' . $set['meta_description'] . '" />' . "</head>", $str); // 在<head>结束前插入meta描述
	}
	ob_start('meta_description'); // 开启输出缓冲
}

// 检查主题文件是否存在，并包含头部文件
if (file_exists(H . "style/themes/$set[set_them]/head.php")) {
	include_once H . "style/themes/$set[set_them]/head.php"; // 包含自定义主题的头部文件
} else {
	$set['web'] = false; // 设置网站状态为false
	//header("Content-type: application/vnd.wap.xhtml+xml");
	//header("Content-type: application/xhtml+xml");
	header("Content-type: text/html");
	echo '<?xml version="1.0" encoding="utf-8"?>';
	echo '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>' . $set['title'] . '</title> <!-- 设置页面标题 -->
		<link rel="shortcut icon" href="/favicon.ico" /> <!-- 网站图标 -->
		<link rel="stylesheet" href="/style/themes/' . $set['set_them'] . '/style.css" type="text/css" /> <!-- 引入样式表 -->
		<link rel="alternate" title="订阅RSS" href="/news/rss.php" type="application/rss+xml" /> <!-- RSS订阅链接 -->
	</head>
	<body>
		<div class="body">'; // 页面主体
}

// 如果用户等级大于4，显示工具栏
if (isset($user) and $user['level'] > 4) {
	if (setget('toolbar', 1) == 1) {
		t_toolbar_html(); // 调用工具栏函数
	}
}

// 检查网站是否关闭，并可能显示警告消息
if (empty(setget('job', 1))) {
	if (isset($user) and $user['level'] >= 5)
		echo "<div style='color:red' class='err'>注意！网站已经关闭<a href='/adm_panel/settings_sys.php?'>管理员</a></div>"; // 提示网站关闭
}

// 显示会话消息
if (isset($_SESSION['message'])) {
	echo '<div class="msg">' . $_SESSION['message'] . '</div>'; // 输出消息
	$_SESSION['message'] = NULL; // 清空消息
}

// 显示错误信息
if (isset($_SESSION['err'])) {
	echo '<div class="err">' . $_SESSION['err'] . '</div>'; // 输出错误信息
	$_SESSION['err'] = NULL; // 清空错误信息
}

header_html(); // 调用头部HTML输出函数
echo '<link rel="stylesheet" href="/style/system.css" type="text/css" /> <!-- 引入系统样式表 -->
		<div id="load"></div>'; // 创建一个加载区域
