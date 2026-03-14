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

/* 用户封禁检查 */
if (isset($user)) {
	$banCount = $db->query(
		"SELECT COUNT(*) as count FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = :user_id AND (`time` > :time OR `view` = '0' OR `navsegda` = '1')",
		['user_id' => $user['id'], 'time' => $time]
	)['count'];

	if ($banCount > 0) {
		header('Location: ../../user/ban.php?' . session_id());
		exit;
	}
}

$set['title'] = '日记';
include_once '../../sys/inc/thead.php';
title();
aut(); // 授权表格

// 导航菜单
echo "<div id='comments' class='menus'>";
echo "<div class='webmenu'><a href='index.php'>日记</a></div>";
echo "<div class='webmenu last'><a href='dir.php'>类别</a></div>";
echo "<div class='webmenu'><a href='search.php' class='activ'>搜索</a></div>";
echo "</div>";

// 处理搜索输入
$usearch = stripcslashes(isset($_GET['go']) ? trim($_GET['go']) : '');

// 搜索表单
echo "<form method=\"get\" action=\"search.php\">";
echo "日记搜索<br />";
echo "<input type=\"text\" name=\"go\" maxlength=\"16\" value=\"" . htmlspecialchars($usearch) . "\" /><br />";
echo "<input type=\"submit\" value=\"搜索\" />";
echo "</form>";

// 执行搜索逻辑
if (!empty($usearch)) {
	// 计算总记录数
	$k_post = $db->query(
		"SELECT COUNT(*) as count FROM `notes` WHERE `name` LIKE :search",
		['search' => '%' . $usearch . '%']
	)['count'];

	// 分页处理
	$k_page = k_page($k_post, $set['p_str']);
	$page = page($k_page);
	$start = $set['p_str'] * $page - $set['p_str'];

	// 查询日记记录
	$notes = $db->queryAll(
		"SELECT * FROM `notes` WHERE `name` LIKE :search ORDER BY `time` DESC LIMIT {$start}, {$set['p_str']}",
		['search' => '%' . $usearch . '%'],
		PDO::FETCH_ASSOC
	);

	echo "<table class='post'>";
	if (empty($notes)) {
		echo "<div class='mess'>没有记录。</div>";
	} else {
		$num = 0;
		foreach ($notes as $post) {
			// 交替样式
			echo ($num++ % 2 == 0) ? '<div class="nav1">' : '<div class="nav2">';

			echo "<img src='../../style/icons/dnev.png' alt='*'> ";
			echo "<a href='list.php?id={$post['id']}'>" . text($post['name']) . "</a> ";
			echo "<span style='time'>(" . vremja($post['time']) . ")</span>";

			// 检查是否有新记录
			$k_n = $db->query(
				"SELECT COUNT(*) as count FROM `notes` WHERE `id` = :id AND `time` > :ftime",
				['id' => $post['id'], 'ftime' => mktime(0, 0, 0)]
			)['count'];
			if ($k_n > 0) {
				echo " <img src='../../style/icons/new.gif' alt='*'>";
			}

			echo "</div>";
		}
	}
	echo "</table>";

	// 输出分页导航
	if ($k_page > 1) {
		str('?go=' . urlencode($usearch) . '&', $k_page, $page);
	}
}

include_once '../../sys/inc/tfoot.php';
