<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
/* 用户封禁 */
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'forum' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
	header('Location: /user/ban.php?' . SID);
	exit;
}
//网页标题
$set['title'] = '论坛-新主题';
include_once '../sys/inc/thead.php';
title();
aut(); // форма авторизации
// 返回菜单
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" /> <a href="/forum/">论坛</a> | <b>新课题</b>';
echo '</div>';
$adm_add = [];
$adm_add2 = NULL;
if (!isset($user) || $user['level'] == 0) {
	$q222 = dbquery("SELECT * FROM `forum_f` WHERE `adm` = '1'");
	while ($adm_f = dbassoc($q222)) {
		$adm_add[] = "`id_forum` <> '$adm_f[id]'";
	}
	if (sizeof($adm_add) != 0) $adm_add2 = ' WHERE' . implode(' AND ', $adm_add);
}
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `forum_t`$adm_add2"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo '<table class="post">';
$q = dbquery("SELECT * FROM `forum_t`$adm_add2 ORDER BY `time_create` DESC  LIMIT $start, $set[p_str]");
// 如果列表为空
if ($k_post == 0) {
	echo '<div class="mess">';
	echo '您的主题不在论坛中';
	echo '</div>';
}
while ($them = dbassoc($q)) {
	// 子论坛的定义
	$forum = dbarray(dbquery("SELECT * FROM `forum_f` WHERE `id` = '$them[id_forum]' LIMIT 1"));
	// 定义分区
	$razdel = dbarray(dbquery("SELECT * FROM `forum_r` WHERE `id` = '$them[id_razdel]' LIMIT 1"));
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;
	// 主题图标
	echo '<img src="/style/themes/' . $set['set_them'] . '/forum/14/them_' . $them['up'] . $them['close'] . '.png" alt="" /> ';
	// 主题链接
	echo '<a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/' . $them['id'] . '/">' . text($them['name']) . '</a> 
	<a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/' . $them['id'] . '/?page=' . $pageEnd . '">
	(' . dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]' AND `id_them` = '$them[id]'"), 0) . ')</a><br/>';
	// 子论坛和部分
	echo '<a href="/forum/' . $forum['id'] . '/">' . text($forum['name']) . '</a> &gt; <a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/">' . text($razdel['name']) . '</a><br />';
	// 主题作者
	$ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $them[id_user] LIMIT 1"));
	echo '作者: ' . user::nick($ank['id'],1,1,0) . ' (' . vremja($them['time_create']) . ')<br />';
	// 最新文章
	$post = dbarray(dbquery("SELECT * FROM `forum_p` WHERE `id_them` = '$them[id]' AND `id_razdel` = '$razdel[id]' AND `id_forum` = '$forum[id]' ORDER BY `time` DESC LIMIT 1"));
	if (isset($post['id']) && $post['id']) {
		// 上一篇文章的作者
		$ank2 = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
		if ($ank2['id']) echo user::nick($ank2['id'],1,1,0) . ' (' . vremja($post['time']) . ')<br />';
	}
	echo '</div>';
}
echo '</table>';
// 页面输出
if ($k_page > 1) str("?", $k_page, $page);
// 返回菜单
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" /> <a href="/forum/">论坛</a> | <b>我的主题</b>';
echo '</div>';
include_once '../sys/inc/tfoot.php';
