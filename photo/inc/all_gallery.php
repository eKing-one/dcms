<?php
/**
 * 相片册主页
 */


/* 封禁的用户 */
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'guest' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0')"), 0) != 0) {
	header('Location: /user/ban.php?' . session_id());
	exit;
}
$set['title'] = '相片册'; //网页标题
include_once '../sys/inc/thead.php';
title();
aut();

$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `gallery`"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo '<table class="post">';
if ($k_post == 0) {
	echo '<div class="mess">';
	echo '无相册';
	echo '</div>';
}

$q = dbquery("SELECT * FROM `gallery` ORDER BY `time` DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q)) {
	$ank = user::get_user($post['id_user']);
	// 梯子
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;
	echo '<img src="/style/themes/' . $set['set_them'] . '/loads/14/' . ($post['pass'] != null || $post['privat'] != 0 ? 'lock.gif' : 'dir.png') . '" alt="*" /> ';
	echo '<a href="/photo/' . $ank['id'] . '/' . $post['id'] . '/">' . text($post['name']) . '</a> (' . dbresult(dbquery("SELECT COUNT(*) FROM `gallery_photo` WHERE `id_gallery` = '$post[id]'"), 0) . ' 照片)<br />';
	if ($post['opis'] == null)
		echo '无描述<br />';
	else
		echo output_text($post['opis']) . '<br />';
	echo '创建时间: ' . vremja($post['time_create']) . '<br />';
	echo '作者: ' . user::nick($ank['id'], 1, 1, 0). '</div>';
}
echo '</table>';

if ($k_page > 1) str('?', $k_page, $page); // 输出页数

if (isset($user)) {
	echo '<div class="foot">';
	echo '<img src="/style/icons/str.gif" alt="*"> <a href="/photo/' . $user['id'] . '/">我的相册</a><br />';
	echo '</div>';
}
include_once '../sys/inc/tfoot.php';