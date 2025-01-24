<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
$banpage = true;
include_once '../sys/inc/user.php';
only_reg();
$set['title'] = '禁止';
include_once '../sys/inc/thead.php';
title();
err();
aut();
if (!isset($user)) {
	header("Location: /index.php?" . session_id());
	exit;
}
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0')"), 0) == 0) {
	header('Location: /index.php?' . session_id());
	exit;
}
dbquery("UPDATE `ban` SET `view` = '1' WHERE `id_user` = '$user[id]'"); // 看到了BAN的原因
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$user[id]'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo "<table class='post'>";
$q = dbquery("SELECT * FROM `ban` WHERE `id_user` = '$user[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q)) {
	$ank = user::get_user($post['id_ban']);
	/*-----------代码-----------*/
	if ($num == 0) {
		echo "  <div class='nav1'>";
		$num = 1;
	} elseif ($num == 1) {
		echo "  <div class='nav2'>";
		$num = 0;
	}
	/*---------------------------*/
	echo "封禁通知：" . ($ank['pol'] == 0 ? "а" : "") . " $ank[nick]: ";
	if ($post['navsegda'] == 1) {
		echo " 我们很遗憾的告诉你，你的账户因违反 CN_DCMS-Social 的相关规定，已经被<font color=red><b>永久封禁</b></font>。<br />";
	} else {
		echo "我们很遗憾的告诉你，你的账户因违反 CN_DCMS-Social 的相关规定，已经被<b>封禁</b>。封禁将持续到 " . vremja($post['time']) . "。<br />";
	}
	echo '<b>封禁原因：</b> ' . $pBan[$post['pochemu']] . '<br />';
	echo '<b>章：</b> ' . $rBan[$post['razdel']] . '<br />';
	echo '<b>附加解释：</b> ' . esc(trim(br(bbcode(smiles(links(stripcslashes(htmlspecialchars($post['prich'])))))))) . "<br />";
	if ($post['time'] > $time) echo "<font color=red><b>活跃的</b></font><br />";
	echo "   </div>";
}
echo "</table>";
if ($k_page > 1) str('?', $k_page, $page); // 输出页数
echo "请遵守本站的<a href=\"/user/rules.php\">规则</a>，共同营造良好的网络环境。<br />";
include_once '../sys/inc/tfoot.php';
