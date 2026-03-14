<?php
include_once '../sys/inc/home.php'; 
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
only_reg();
$set['title'] = '登录历史';
include_once '../sys/inc/thead.php';
title();
aut();

$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `user_log` WHERE `id_user` = '$user[id]'"),0);
$k_page = k_page($k_post,$set['p_str']);
$page = page($k_page);
$start = $set['p_str']*$page-$set['p_str'];

if (isset($_GET['logout'])) {
	$logout = intval($_GET['logout']);
	$q = dbquery("SELECT * FROM `user_log` WHERE `id` = '$logout' AND `id_user` = '$user[id]' LIMIT 1");
	if (dbrows($q)) {
		$post = dbassoc($q);
		if ($post['ban'] == 0) {
			if (isset($_GET['ok']) && $_GET['ok'] == 1) {
				dbquery("UPDATE `user_log` SET `ban` = '1' WHERE `id` = '$logout' AND `id_user` = '$user[id]' LIMIT 1");
				$_SESSION['message'] = '登录历史注销成功';
			} else {
				echo '<div class="mess" style="text-align:center;">';
				echo '确定要注销此登录历史吗?<br />';
				echo "[<a href='?logout=$logout&amp;ok=1'><img src='../style/icons/ok.gif'> 注销</a>] [<a href='?'><img src='../style/icons/delete.gif'> 取消</a>]";
				echo '</div>';
				echo '<div class="nav1">';
				echo '<img src="../style/my_menu/logout_16.png" alt="" />';
				if ($post['method'] != 1) {
					echo '登录历史<br />';
				} else {
					echo "使用用户名及密码登录 ({$post['date']})<br />";
				}
				echo "IP: {$post['ip']}<br />";
				echo 'UA: ' . output_text($post['ua']);
				echo '<div class="foot">';
				echo '<img src="../style/icons/str.gif" alt="*" /> <a href="info.php">我的页面</a><br />';
				echo '<img src="../style/icons/str.gif" alt="*" /> <a href="my_aut.php">我的菜单</a><br />';
				echo '</div>';
				include_once '../sys/inc/tfoot.php';
			}
		} else {
			$_SESSION['err'] = '此条登录历史已经注销';
		}
	} else {
		$_SESSION['err'] = '此条登录历史不存在';
	}
	header("Location: ?");
	exit;
}

if (isset($_GET['delete'])) {
	$delete = intval($_GET['delete']);
	$q = dbquery("SELECT * FROM `user_log` WHERE `id` = '$delete' AND `id_user` = '$user[id]' LIMIT 1");
	if (dbrows($q)) {
		$post = dbassoc($q);
		if (isset($_GET['ok']) && $_GET['ok'] == 1) {
			dbquery("DELETE FROM `user_log` WHERE `id` = '$delete' AND `id_user` = '$user[id]' LIMIT 1");
			$_SESSION['message'] = '登录历史删除成功';
		} else {
			echo '<div class="mess" style="text-align:center;">';
			echo '确定要删除此登录历史吗?<br />';
			echo "[<a href='?delete=$delete&amp;ok=1'><img src='../style/icons/ok.gif'> 删除</a>] [<a href='?'><img src='../style/icons/delete.gif'> 取消</a>]";
			echo '</div>';
			echo '<div class="nav1">';
			echo '<img src="../style/my_menu/logout_16.png" alt="" />';
			if ($post['method'] != 1) {
				echo '登录历史<br />';
			} else {
				echo "使用用户名及密码登录 ({$post['date']})<br />";
			}
			echo "IP: {$post['ip']}<br />";
			echo 'UA: ' . output_text($post['ua']);
			echo '<div class="foot">';
			echo '<img src="../style/icons/str.gif" alt="*" /> <a href="info.php">我的页面</a><br />';
			echo '<img src="../style/icons/str.gif" alt="*" /> <a href="my_aut.php">我的菜单</a><br />';
			echo '</div>';
			include_once '../sys/inc/tfoot.php';
		}
	} else {
		$_SESSION['err'] = '此条登录历史不存在';
	}
	header("Location: ?");
	exit;
}

echo '<table class="post">';
if (empty($k_post)) {
	echo '<div class="mess">没有登录历史</div>';
}

$q = dbquery("SELECT * FROM `user_log` WHERE `id_user` = '{$user['id']}' ORDER BY `id` DESC  LIMIT {$start}, {$set['p_str']}");
while ($post = dbassoc($q)) {
	$ank = user::get_user($user['id']);
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;
	echo '<img src="../style/my_menu/logout_16.png" alt="" />';
	if ($post['method'] != 1) {
		echo ' 登录历史<br />';
	} else {
		echo '使用用户名及密码登录 (' . vremja(strtotime($post['date'])) . ')<br />';
	}
	echo "IP: {$post['ip']}<br />";
	echo 'UA: ' . output_text($post['ua']) . '<br />';
	echo '最后在线：' . vremja(strtotime($post['last_online']));
	echo '<div style="text-align:right;">';
	if ($post['ban'] == 0 && strtotime($post['expire_date']) > time()) echo '<a href="?logout=' . $post['id'] . '"><img src="../style/icons/blicon.gif" alt="*">注销</a>';
	if (false) echo ' <a href="?delete=' . $post['id'] . '"><img src="../style/icons/delete.gif" alt="*">删除</a>';	// 暂时还不允许删除
	echo "</div>";
	echo '</div>';
}
echo '</table>';

// 输出页数
if ($k_page > 1) str("?", $k_page, $page);  
echo '<div class="foot">';
echo '<img src="../style/icons/str.gif" alt="*" /> <a href="info.php">我的页面</a><br />';
echo '<img src="../style/icons/str.gif" alt="*" /> <a href="my_aut.php">我的菜单</a><br />';
echo '</div>';
include_once '../sys/inc/tfoot.php';
