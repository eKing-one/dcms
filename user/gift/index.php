<?php
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';

only_reg();
$width = ($webbrowser == 'web' ? '100' : '70'); // 向浏览器显示礼物的大小
if (isset($_GET['id'])) $ank['id'] = intval($_GET['id']);
else $ank['id'] = $user['id']; // 确定用户
$ank = user::get_user($ank['id']);
if (!$ank || $ank['id'] == 0) {
	header("Location: ../../index.php?" . session_id());
	exit;
}
$set['title'] = '送给 ' . $ank['nick'] . ' 的礼物';
include_once '../../sys/inc/thead.php';
title();
aut();

/*
==================================
显示用户礼品
==================================
*/
echo '<div class="foot">';
echo '<img src="../../style/icons/str2.gif" alt="*" /> '.user::nick($ank['id'],1,0,0) .' | <b>礼物</b>';
echo '</div>';

// 礼品清单
$k_post = dbresult(dbquery("SELECT COUNT(id) FROM `gifts_user` WHERE `id_user` = '$ank[id]'" . ($ank['id'] != $user['id'] ? " AND `status` = '1' " : "") . ""), 0);
if ($k_post == 0) {
	echo '<div class="mess">目前没有人送礼物。</div>';
}
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT id,status,coment,id_gift,id_ank,time FROM `gifts_user` WHERE `id_user` = '$ank[id]'" . ($ank['id'] != $user['id'] ? " AND `status` = '1' " : "") . " ORDER BY `time` DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q)) {
	$gift = dbassoc(dbquery("SELECT id,name FROM `gift_list` WHERE `id` = '$post[id_gift]' LIMIT 1"));
	$anketa = user::get_user($post['id_ank']);
	/*-----------代码-----------*/
	if ($num == 0) {
		echo '<div class="nav1">';
		$num = 1;
	} elseif ($num == 1) {
		echo '<div class="nav2">';
		$num = 0;
	}
	/*---------------------------*/
	echo '<img src="../../files/gift/' . $gift['id'] . '.png" style="max-width:' . $width . 'px;" alt="*" /><br />';
	echo '<img src="../../style/icons/present.gif" alt="*" /> <a href="gift.php?id=' . $post['id'] . '"><b>' . htmlspecialchars($gift['name']) . '</b></a> :: ';
	echo '由 ' . user::nick($anketa['id'], 1, 1, 0) . ' 在 ' . vremja($post['time']) . ' 送出';
	if ($post['status'] == 0) echo ' <font color=red>NEW</font> ';
	echo '</div>';
}

if ($k_page > 1) str('index.php?id=' . intval($_GET['id']) . '&amp;', $k_page, $page); // 输出页数

echo '<div class="foot">';
echo '<img src="../../style/icons/str2.gif" alt="*" /> ' . user::nick($ank['id'],1,0,0) . '</a> | <b>礼物</b>';
echo '</div>';

include_once '../../sys/inc/tfoot.php';
