<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/downloadfile.php';
include_once '../sys/inc/user.php';
header("Expires: ".gmdate("D, d M Y H:i:s", time() + 3600)." GMT");
header("Cache-Control: max-age=3600");
if (!isset($_GET['id']) || !isset($_GET['size'])) exit;
$size = intval($_GET['size']);
$if_photo = intval($_GET['id']);
$photo = dbassoc(dbquery("SELECT * FROM `gallery_photo` WHERE `id` = '$if_photo'  LIMIT 1"));
$gallery = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '$photo[id_gallery]'  LIMIT 1"));
$ank = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '$gallery[id_user]' LIMIT 1"));
if (isset($_SESSION['id_user'])) {
	$user = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '$_SESSION[id_user]' LIMIT 1"));
} else {
	$user = array('id' => '0', 'level' => '0', 'group_access' => '0');
}

if ($ank['id'] != $user['id'] && isset($user['group_access']) && ($user['group_access'] == 0 || $user['group_access'] <= $ank['group_access']) && isset($photo['avatar']) && $photo['avatar'] == 0) {
	// 用户设置
	$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));
	// 是否是好友
	$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE 
	 (`user` = '$user[id]' AND `frend` = '$ank[id]') OR 
	 (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"),0);
	// 检查好友请求
	$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE 
	 (`user` = '$user[id]' AND `to` = '$ank[id]') OR 
	 (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"),0);
	// 如果页面有隐私设置，开始输出
	if ($uSet['privat_str'] == 2 && $frend != 2) $if_photo = 0; // 仅对好友可见
	// 仅对自己可见
	if ($uSet['privat_str'] == 0) $if_photo = 0;	
	/*
	* 如果相册设置了隐私
	*/	
	if ($gallery['privat'] == 1 && ($frend != 2 || !isset($user)) && $user['level'] <= $ank['level'] && $user['id'] != $ank['id']) {
		$if_photo = 0;	
	} elseif ($gallery['privat'] == 2 && $user['id'] != $ank['id'] && $user['level'] <= $ank['level']) {
		$if_photo = 0;	
	}
	/*--------------------相册有密码-------------------*/
	if ($user['id'] != $ank['id'] && $gallery['pass'] != NULL) {
		if (!isset($_SESSION['pass']) || $_SESSION['pass'] != $gallery['pass']) {
			$if_photo = 0;	
		}
	}
	/*---------------------------------------------------------*/
}

if ($size == '48') {
	if (is_file(H.'sys/gallery/48/'.$if_photo.'.png')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/48/'.$if_photo.'.png'))." GMT");
		DownloadFile(H.'sys/gallery/48/'.$if_photo.'.png', '相片.png', ras_to_mime('png'));
		exit;
	}
	if (is_file(H.'sys/gallery/48/'.$if_photo.'.gif')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/48/'.$if_photo.'.gif'))." GMT");
		DownloadFile(H.'sys/gallery/48/'.$if_photo.'.gif', '相片.gif', ras_to_mime('gif'));
		exit;
	}
	if (is_file(H.'sys/gallery/48/'.$if_photo.'.jpg')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/48/'.$if_photo.'.jpg'))." GMT");
		DownloadFile(H.'sys/gallery/48/'.$if_photo.'.jpg', '相片.jpg', ras_to_mime('jpg'));
		exit;
	}
}
if ($size == '128') {
	if (is_file(H.'sys/gallery/128/'.$if_photo.'.png')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/128/'.$if_photo.'.png'))." GMT");
		DownloadFile(H.'sys/gallery/128/'.$if_photo.'.png', '相片.png', ras_to_mime('png'));
		exit;
	}
	if (is_file(H.'sys/gallery/128/'.$if_photo.'.gif')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/128/'.$if_photo.'.gif'))." GMT");
		DownloadFile(H.'sys/gallery/128/'.$if_photo.'.gif', '相片.gif', ras_to_mime('gif'));
		exit;
	}
	if (is_file(H.'sys/gallery/128/'.$if_photo.'.jpg')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/128/'.$if_photo.'.jpg'))." GMT");
		DownloadFile(H.'sys/gallery/128/'.$if_photo.'.jpg', '相片.jpg', ras_to_mime('jpg'));
		exit;
	}
}
if ($size == '50') {
	if (is_file(H.'sys/gallery/50/'.$if_photo.'.png')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/50/'.$if_photo.'.png'))." GMT");
		DownloadFile(H.'sys/gallery/50/'.$if_photo.'.png', '相片.png', ras_to_mime('png'));
		exit;
	}
	if (is_file(H.'sys/gallery/50/'.$if_photo.'.gif')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/50/'.$if_photo.'.gif'))." GMT");
		DownloadFile(H.'sys/gallery/50/'.$if_photo.'.gif', '相片.gif', ras_to_mime('gif'));
		exit;
	}
	if (is_file(H.'sys/gallery/50/'.$if_photo.'.jpg')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/50/'.$if_photo.'.jpg'))." GMT");
		DownloadFile(H.'sys/gallery/50/'.$if_photo.'.jpg', '相片.jpg', ras_to_mime('jpg'));
		exit;
	}
}
if ($size == '640') {
	if (is_file(H.'sys/gallery/640/'.$if_photo.'.png')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/640/'.$if_photo.'.png'))." GMT");
		DownloadFile(H.'sys/gallery/640/'.$if_photo.'.png', '相片.png', ras_to_mime('png'));
		exit;
	}
	if (is_file(H.'sys/gallery/640/'.$if_photo.'.gif')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/640/'.$if_photo.'.gif'))." GMT");
		DownloadFile(H.'sys/gallery/640/'.$if_photo.'.gif', '相片.gif', ras_to_mime('gif'));
		exit;
	}
	if (is_file(H.'sys/gallery/640/'.$if_photo.'.jpg')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/640/'.$if_photo.'.jpg'))." GMT");
		DownloadFile(H.'sys/gallery/640/'.$if_photo.'.jpg', '相片.jpg', ras_to_mime('jpg'));
		exit;
	}
}
if ($size == '0') {
	if (is_file(H.'sys/gallery/photo/'.$if_photo.'.png')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/photo/'.$if_photo.'.png'))." GMT");
		DownloadFile(H.'sys/gallery/photo/'.$if_photo.'.png', 'photo_'.$if_photo.'.png', ras_to_mime('png'));
		exit;
	}
	if (is_file(H.'sys/gallery/photo/'.$if_photo.'.gif')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/photo/'.$if_photo.'.gif'))." GMT");
		DownloadFile(H.'sys/gallery/photo/'.$if_photo.'.gif', 'photo_'.$if_photo.'.gif', ras_to_mime('gif'));
		exit;
	}
	if (is_file(H.'sys/gallery/photo/'.$if_photo.'.jpg')) {
		header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime(H.'sys/gallery/photo/'.$if_photo.'.jpg'))." GMT");
		DownloadFile(H.'sys/gallery/photo/'.$if_photo.'.jpg', 'photo_'.$if_photo.'.jpg', ras_to_mime('jpg'));
		exit;
	}
}