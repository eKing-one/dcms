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


include_once '../sys/inc/start.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';

if (!isset($_GET['id']) || !isset($_GET['mode'])) {
	http_response_code(404);
	exit;
}
if ($_GET['mode'] == 'view') {
	$size = intval($_GET['size'] ?? NULL);
	$inlineFile = true;
} elseif ($_GET['mode'] == 'download') {
	$size = 0;
	$inlineFile = false;
}
if ($size === NULL) {
	http_response_code(404);
	exit;
}


// 获取图片信息
$if_photo = intval($_GET['id']);
$photo = dbassoc(dbquery("SELECT * FROM `gallery_photo` WHERE `id` = '$if_photo'  LIMIT 1"));
if (!$photo) {
	http_response_code(404);
	exit;
}

// 获取相册信息
$gallery = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '$photo[id_gallery]'  LIMIT 1"));

// 获取图片发布者信息
//$ank = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '$gallery[id_user]' LIMIT 1"));
$ank = user::get_user($gallery['id_user']);

// 如果此图片不是头像，检查是否有访问权限
if ($photo['avatar'] == 0) {
	// 获取用户设置
	$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));
	if ($uSet['privat_str'] == 2 && $gallery['privat'] != 2) $gallery['privat'] = 1;	// 仅对好友可见
	if ($uSet['privat_str'] == 0) $gallery['privat'] = 2;	// 仅对自己可见

	// 未登录用户权限检查
	if (empty($user['id']) && $gallery['privat'] != 0) {
		http_response_code(403);
		exit;
	}


	// 已登录用户权限检查（仅好友可见）
	if ($gallery['privat'] == 1 && ($ank['id'] != $user['id'] && isset($user['group_access']) && ($user['group_access'] == 0 || $user['group_access'] <= $ank['group_access']))) {
		// 检查当前用户是否与发布者为好友
		$frend = $db->queryColumn("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$ank[id]') OR (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1");
		if ($frend != 2) {
			http_response_code(403);
			exit;
		}
	}

	// 已登录用户权限检查（仅自己可见）
	if ($gallery['privat'] == 2 && ($ank['id'] != $user['id'] && isset($user['group_access']) && ($user['group_access'] == 0 || $user['group_access'] <= $ank['group_access']))) {
		http_response_code(403);
		exit;
	}

	/*--------------------相册有密码-------------------*/
	if ($gallery['pass'] != NULL) {
		if (isset($user['id'])) {
			if (($user['id'] != $ank['id']) && isset($user['group_access']) && ($user['group_access'] == 0 || $user['group_access'] <= $ank['group_access']) && (!isset($_SESSION['pass']) || $_SESSION['pass'] != $gallery['pass'])) {
				http_response_code(403);
				exit;
			}
		} else {
			if (!isset($_SESSION['pass']) || $_SESSION['pass'] != $gallery['pass']) {
				http_response_code(403);
				exit;
			}
		}
	}
}

header("Expires: ".gmdate("D, d M Y H:i:s", time() + 2592000)." GMT");
header("Cache-Control: max-age=2592000");
header('Access-Control-Allow-Origin: *');

if ($size == 0) {
	$file_path = H . "files/gallery/photo/{$if_photo}.{$photo['ras']}";
	// 检查文件是否存在
	if (is_file($file_path)) {
		// 输出文件
		DownloadFile($file_path, "photo_{$if_photo}.{$photo['ras']}", ras_to_mime($photo['ras']), $inlineFile);
	} else {
		error_log("[photo/img.php] Error: File not found at path: $file_path");
		http_response_code(404);
	}
} else {
	$file_path = H . "files/gallery/{$size}/{$if_photo}.jpg";
	// 检查文件是否存在
	if (is_file($file_path)) {
		// 输出文件
		DownloadFile($file_path, "photo_{$if_photo}.jpg", ras_to_mime('jpg'), true);
	} else {
		error_log("[photo/img.php] Error: File not found at path: $file_path");
		http_response_code(404);
	}
}
