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
// 显示模式
if (isset($_GET['admin']) && user_access('user_collisions')) {
	if ($_GET['admin'] == 'close')
		$_SESSION['admin'] = null;
	else
		$_SESSION['admin'] = true;
}
$set['title'] = '现在在网站上'; //网页标题
include_once '../sys/inc/thead.php';
title();
aut();
/*
==============================================
这个脚本输出 1 个随机的“领导者”和
他们整个名单的链接。(с) DCMS-Social
==============================================
*/
$k_lider = dbresult(dbquery("SELECT COUNT(*) FROM `liders` WHERE `time` > '$time'"), 0);
$liders = dbassoc(dbquery("SELECT * FROM `liders` WHERE `time` > '$time' ORDER BY rand() LIMIT 1"));
if ($k_lider > 0) {
	echo '<div class="main">';
	$lider = user::get_user($liders['id_user']);
	echo user::nick($lider['id'], 1, 1, 0) . '<br />';//输出用户名
	if ($liders['msg'])
		echo output_text($liders['msg']) . '<br />';
	echo '<img src="/style/icons/lider.gif" alt="S"/> <a href="/user/liders/">所有管理员</a> (' . $k_lider . ')';
	echo '</div>';
}
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_last` > '" . (time() - 600) . "'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT id, ank_city, pol, ank_d_r, ank_m_r, ank_g_r, ank_o_sebe, url, level, ip, ip_xff, ip_cl, ua, date_last FROM `user` WHERE `date_last` > '" . (time() - 600) . "' ORDER BY `date_last` DESC LIMIT $start, $set[p_str]");
echo '<table class="post">';
if ($k_post == 0) {
	echo '<div class="mess">';
	echo '现在网站上没有人';
	echo '</div>';
}
while ($ank = dbassoc($q)) {
	$ank['ank_age'] = null;
	if ($ank['ank_d_r'] != NULL && $ank['ank_m_r'] != NULL && $ank['ank_g_r'] != NULL) {
		$ank['ank_age'] = date("Y") - $ank['ank_g_r'];
		if (date("n") < $ank['ank_m_r'])
			$ank['ank_age'] = $ank['ank_age'] - 1;
		elseif (date("n") == $ank['ank_m_r'] && date("j") < $ank['ank_d_r'])
			$ank['ank_age'] = $ank['ank_age'] - 1;
	}
	// 莱森卡
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;
	echo user::nick($ank['id'], 1, 1, 0) .' <br />';//输出用户名
	// 高级模式
	if (isset($user) && isset($_SESSION['admin'])) {
		// Возможные ники
		$mass[0] = $ank['id'];
		$collisions = user_collision($mass);
		if (count($collisions) > 1) {
			echo '<span class="ank_n">可能的昵称</span> ';
			echo '<span class="ank_d">';
			for ($i = 1; $i < count($collisions); $i++) {
				echo ' :: ' . user::nick($collisions[$i],1,1,0);//输出用户名
			}
			echo '</span><br />';
		}
		// 用户 IP
		if ($ank['ip'] != NULL) {
			if (user_access('user_show_ip') && $ank['ip'] != 0) {
				echo '<span class="ank_n">IP:</span> <span class="ank_d">' . long2ip($ank['ip']) . '</span>';
				if (user_access('adm_ban_ip'))
					echo ' [<a href="/adm_panel/ban_ip.php?min=' . $ank['ip'] . '">禁令</a>]';
				echo '<br />';
			}
		}
		// 客户端 IP
		if ($ank['ip_cl'] != NULL) {
			if (user_access('user_show_ip') && $ank['ip_cl'] != 0) {
				echo '<span class="ank_n">IP (CLIENT):</span> <span class="ank_d">' . long2ip($ank['ip_cl']) . '</span>';
				if (user_access('adm_ban_ip'))
					echo ' [<a href="/adm_panel/ban_ip.php?min=' . $ank['ip_cl'] . '">禁令</a>]';
				echo '<br />';
			}
		}
		// IP (XFF)
		if ($ank['ip_xff'] != NULL) {
			if (user_access('user_show_ip') && $ank['ip_xff'] != 0) {
				echo '<span class="ank_n">IP (XFF):</span> <span class="ank_d">' . long2ip($ank['ip_xff']) . '</span>';
				if (user_access('adm_ban_ip'))
					echo ' [<a href="/adm_panel/ban_ip.php?min=' . $ank['ip_xff'] . '">举报</a>]';
				echo '<br />';
			}
		}
		// 浏览器
		if (user_access('user_show_ua') && $ank['ua'] != NULL)
			echo '<span class="ank_n">浏览器:</span> <span class="ank_d">' . $ank['ua'] . '</span><br />';
		if (user_access('user_show_ip') && opsos($ank['ip']))
			echo '<span class="ank_n">IP:</span> <span class="ank_d">' . opsos($ank['ip']) . '</span><br />';
		if (user_access('user_show_ip') && opsos($ank['ip_cl']))
			echo '<span class="ank_n">IP (CL):</span> <span class="ank_d">' . opsos($ank['ip_cl']) . '</span><br />';
		if (user_access('user_show_ip') && opsos($ank['ip_xff']))
			echo '<span class="ank_n">IP (XFF):</span> <span class="ank_d">' . opsos($ank['ip_xff']) . '</span><br />';
		if ($user['level'] > $ank['level'] && $user['id'] != $ank['id']) {
			if (user_access('user_prof_edit'))
				echo '[<a href="/adm_panel/user.php?id=' . $ank['id'] . '"><img src="/style/icons/edit.gif" alt="*" /> 编辑</a>] ';
			if ($user['id'] != $ank['id']) {
				if (user_access('user_ban_set') || user_access('user_ban_set_h') || user_access('user_ban_unset'))
					echo '[<a href="/adm_panel/ban.php?id=' . $ank['id'] . '"><img src="/style/icons/blicon.gif" alt="*" /> 举报</a>] ';
				if (user_access('user_delete')) {
					echo '[<a href="/adm_panel/delete_user.php?id=' . $ank['id'] . '"><img src="/style/icons/delete.gif" alt="*" /> 删除</a>] ';
					echo '<br />';
				}
			}
		}
	} else {
		echo '<b>(' . (($ank['pol'] == 1) ? '男' : '女') . (($ank['ank_age'] == null) ? '/未指定' : '/' . $ank['ank_age']) . ')</b>';
		if ($ank['ank_city'] != NULL)
			echo ', ' . text($ank['ank_city']);
		if ($ank['ank_o_sebe'] != NULL)
			echo ', ' . text($ank['ank_o_sebe']);
		echo ', <img src="/style/icons/time.png" alt="away" /> [' . vremja($ank['date_last']) . ']';
	}
	echo '</div>';
}
echo '</table>';
if ($k_page > 1) str("?", $k_page, $page); // 输出页数
if (user_access('user_collisions')) {
?>
	<div class="foot">
		<?= (!isset($_SESSION['admin']) ? '<a href="?admin">高级模式</a> | <b>正常模式</b>' : '<b>高级模式</b> | <a href="?admin=close">正常模式</a>') ?>
	</div>
<?
}
include_once '../sys/inc/tfoot.php';
?>