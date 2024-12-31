<?php
// 如果没有设置用户且没有通过GET请求传递用户ID，则重定向到首页并退出
if (!isset($user) && !isset($_GET['id_user'])) {
	header("Location: /photo/?" . SID);
	exit;
}
// 如果设置了用户，则将用户ID赋值给ank数组
if (isset($user)) $ank['id'] = $user['id'];
// 如果通过GET请求传递了用户ID，则将该ID转换为整数并赋值给ank数组
if (isset($_GET['id_user'])) $ank['id'] = intval($_GET['id_user']);

// 获取相册主人的信息
$ank = user::get_user($ank['id']);

// 如果没有获取到用户信息，则重定向到首页并退出
if (!$ank) {
	header('Location: /photo/?' . SID);
	exit;
}

// 如果用户被禁止访问相册
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'photo' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
	header('Location: /user/ban.php?' . SID);
	exit;
}

// 获取相册信息
$gallery['id'] = intval($_GET['id_gallery']);

// 如果相册不存在或者不属于当前用户，则重定向到用户相册页面并退出
if (dbresult(dbquery("SELECT COUNT(*) FROM `gallery` WHERE `id` = '$gallery[id]' AND `id_user` = '$ank[id]' LIMIT 1"), 0) == 0) {
	header('Location: /photo/' . $ank['id'] . '/?' . SID);
	exit;
}

$gallery = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '$gallery[id]' AND `id_user` = '$ank[id]' LIMIT 1"));

// 设置网页标题
$set['title'] = $ank['nick'] . ' - ' . text($gallery['name']);

// 包含编辑相册和上传照片的脚本
include 'inc/gallery_show_act.php';

// 包含头部文件
include_once '../sys/inc/thead.php';
title();
aut();
err();

// 包含表单脚本
include 'inc/gallery_show_form.php';

// 显示页脚信息
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> ' . user::nick($ank['id'],1,0,0) . ' | <a href="/photo/' . $ank['id'] . '/">相册</a> | <b>' . text($gallery['name']) . '</b></div>';

// 包含隐私设置文件
include H . 'sys/add/user.privace.php';

/*
* 如果相册设置了隐私
*/
if ($gallery['privat'] == 1 && ($frend != 2 || !isset($user)) && $user['level'] <= $ank['level'] && $user['id'] != $ank['id']) {
	echo '<div class="mess">';
	echo '只有该用户的好友才能查看该用户的相册';
	echo '</div>';

	$block_photo = true;
} elseif ($gallery['privat'] == 2 && $user['id'] != $ank['id'] && $user['level'] <= $ank['level']) {
	echo '<div class="mess">';
	echo '用户已禁止查看此相册！';
	echo '</div>';

	$block_photo = true;
}

/*--------------------如果相册有密码-------------------*/

if ($user['id'] != $ank['id'] && $gallery['pass'] != NULL) {
	if (isset($_POST['password'])) {
		$_SESSION['pass'] = my_esc($_POST['password']);

		if ($_SESSION['pass'] != $gallery['pass']) {
			$_SESSION['message'] = '密码不正确';
			$_SESSION['pass'] = NULL;
		}
		header("Location: ?");
	}

	if (!isset($_SESSION['pass']) || $_SESSION['pass'] != $gallery['pass']) {
		echo '<form action="?" method="POST">密码:<br /><input type="pass" name="password" value="" /><br />		
		<input type="submit" value="登录"/></form>';

		echo '<div class="foot">';
		echo '<img src="/style/icons/str2.gif" alt="*"> ' . user::nick($ank['id'],1,0,0) . ' | <a href="/photo/' . $ank['id'] . '/">相册</a> | <b>' . text($gallery['name']) . '</b>';
		echo '</div>';

		include_once '../sys/inc/tfoot.php';
		exit;
	}
}
/*---------------------------------------------------------*/

if (!isset($block_photo)) {
	$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `gallery_photo` WHERE `id_gallery` = '$gallery[id]'"), 0);
	$k_page = k_page($k_post, $set['p_str']);
	$page = page($k_page);
	$start = $set['p_str'] * $page - $set['p_str'];

	echo '<table class="post">';

	if ($k_post == 0) {
		echo '<div class="mess">';
		echo '无照片';
		echo '</div>';
	}

	$q = dbquery("SELECT * FROM `gallery_photo` WHERE `id_gallery` = '$gallery[id]' ORDER BY `id` DESC LIMIT $start, $set[p_str]");

	while ($post = dbassoc($q)) {
		// 交替显示的类名
		echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
		$num++;

		echo '<img src="/style/themes/' . $set['set_them'] . '/loads/14/jpg.png" alt="*"/>';
		echo '<a href="/photo/' . $ank['id'] . '/' . $gallery['id'] . '/' . $post['id'] . '/">' . text($post['name']);

		if ($post['metka'] == 1) echo ' <font color=red>(18+)</font>';

		echo '<br /><img src="/photo/photo128/' . $post['id'] . '.' . $post['ras'] . '" alt="Photo Screen" /></a><br />';

		if ($post['opis'] == null)
			echo '无描述<br />';
		else
			echo '<div class="text">' . output_text($post['opis']) . '</div>';

		echo '<img src="/style/icons/uv.png"> (' . dbresult(dbquery("SELECT COUNT(*) FROM `gallery_komm` WHERE `id_photo` = '$post[id]'"), 0) . ')';
		echo '<img src="/style/icons/add_fav.gif"> (' . dbresult(dbquery("SELECT COUNT(`id`)FROM `bookmarks` WHERE `id_object`='" . $post['id'] . "' AND `type`='photo'"), 0) . ')';

		echo '</div>';
	}

	echo '</table>';

	// 输出页数
	if ($k_page > 1) str('?', $k_page, $page);
}
if (isset($user) && (user_access('photo_alb_del') || $ank['id'] == $user['id'])) {
	echo '<div class="mess">';
	echo '<img src="/style/icons/apply14.png" width="16"> <a href="?act=upload">上传照片</a><br/>';
	echo '<img src="/style/icons/edit.gif" width="16"> <a href="/photo/' . $ank['id'] . '/' . $gallery['id'] . '/?edit=rename">编辑相册</a><br/>';
	echo '<img src="/style/icons/delete.gif" width="16"> <a href="/photo/' . $ank['id'] . '/' . $gallery['id'] . '/?act=delete"">删除相册</a></div>';
}
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> ' . user::nick($ank['id'],1,0,0) . ' | <a href="/photo/' . $ank['id'] . '/">相册</a> | <b>' . text($gallery['name']) . '</b>';
echo '</div>';

include_once '../sys/inc/tfoot.php';
exit;