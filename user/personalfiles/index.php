<?php
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
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';


/* 禁止封禁用户访问 */
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'files' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
	header('Location: /user/ban.php?' . session_id());
	exit;
}

include_once '../../sys/inc/thead.php';

if (isset($_GET['id'])) {
	$ank['id'] = intval($_GET['id']);
} elseif (isset($user)) {
	$ank['id'] = $user['id'];
} else {
	$ank['id'] = 0;
}

if ($ank['id'] == 0) {
	// 如果ID为0，提示错误信息并退出
	$err = "错误：文件不存在！";
	err();
	include_once '../../sys/inc/tfoot.php';
}

// 获取文件夹作者的ID
$ank = user::get_user($ank['id']);
if (!$ank) {
	$err = "错误：文件或目录不存在！";
	err();
	include_once '../../sys/inc/tfoot.php';
}

// 如果用户没有主文件夹，则创建
if (dbresult(dbquery("SELECT COUNT(*) FROM `user_files` WHERE `id_user` = '{$ank['id']}' AND `osn` = '1'"), 0) == 0) {
	$t = dbquery("INSERT INTO `user_files` (`id_user`, `name`,  `osn`) values('{$ank['id']}', '文件', '1')");	// 在数据库中插入主文件夹记录

	$dir = dbassoc(dbquery("SELECT * FROM `user_files`  WHERE `id_user` = '{$ank['id']}' AND `osn` = '1'"));	// 获取刚创建的主文件夹信息
	header("Location: /user/personalfiles/{$ank['id']}/{$dir['id']}/" . session_id());	// 跳转到新创建的主文件夹页面
}

// 主文件夹信息
$dir_osn = dbassoc(dbquery("SELECT * FROM `user_files` WHERE `id_user` = '{$ank['id']}' AND `osn` = '1' LIMIT 1"));

// 当前文件夹信息
if (isset($_GET['dir'])) $dir = dbassoc(dbquery("SELECT * FROM `user_files` WHERE `id_user` = '{$ank['id']}' AND `id` = '" . intval($_GET['dir']) . "' LIMIT 1"));

// 如果文件夹不存在，则阻止访问
if (!isset($dir['id_user']) || $dir['id_user'] != $ank['id']) {
	$err = "错误！文件夹可能已被删除，请检查地址是否正确！";
	err();
	include_once '../../sys/inc/tfoot.php';
}

if (isset($_GET['id']) && isset($_GET['dir'])  && !isset($_GET['add']) && !isset($_GET['upload']) && !isset($_GET['id_file'])) {
	// 显示文件夹内容
	include_once 'inc/folder.php';

} else if (isset($_GET['id']) && isset($_GET['dir']) && isset($_GET['add']) && !isset($_GET['upload']) && !isset($_GET['id_file'])) {
	// 创建和编辑文件夹
	include_once 'inc/folder.create.php';

} else if (isset($_GET['id']) && isset($_GET['dir']) && isset($_GET['upload']) && !isset($_GET['id_file']) && !isset($_GET['add'])) {
	// 上传文件
	include_once 'inc/upload.wap.php';

} else if (isset($_GET['id']) && isset($_GET['dir']) && isset($_GET['id_file']) && !isset($_GET['upload']) && !isset($_GET['add'])) {
	// 向用户展示文件
	include_once 'inc/file.php';
}

// (c) Искатель
include_once '../../sys/inc/tfoot.php';