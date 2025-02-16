<?php
/*
* $name 个体操作描述 
*/
if ($type == 'status_like' && $post['avtor'] != $user['id']) {	// 喜欢
	$name = '认为状态很酷';
} else if ($type == 'status_like' && $post['avtor'] == $user['id']) {
	$name = '认为你的状态很酷';
} else if ($type == 'status' && $post['avtor'] != $user['id']) {
	$name = '更新了' . ($avtor['pol'] == 1 ? null : "а") . ' 状态';
}


/*
* 内容块输出 
*/
if ($type == 'status_like' || $type == 'status') {
	$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id` = '" . $post['id_file'] . "' LIMIT 1"));
	$otkogo = user::get_user($post['ot_kogo']);
	if (isset($status['id'])) {
		echo '<div class="nav1">';
		if ($post['ot_kogo']) {
			echo user::nick($otkogo['id'], 0, 0, 0) . '  <a href="user.settings.php?id=' . $otkogo['id'] . '">[!]</a>';
		} else {
			echo user::nick($avtor['id'], 0, 0, 0) . '  <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a>';
		}
		echo $name;
		if ($type != 'status') {
			echo user::nick($avtor['id'], 1, 1, 0) . ' ';
		}
		echo $s1 . vremja($post['time']) . $s2;
		echo '</div>';
		echo '<div class="nav2">';
		echo '<div class="st_1"></div>';
		echo '<div class="st_2">';
		echo output_text($status['msg']) . '<br />';
		echo '</div>';
		echo '<a href="/user/status/komm.php?id=' . $status['id'] . '"><img src="/style/icons/bbl4.png" alt=""/> ' . dbresult(dbquery("SELECT COUNT(*) FROM `status_komm` WHERE `id_status` = '$status[id]'"), 0) . '</a>';
		$l = dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]'"), 0);
		if (isset($user) && $user['id'] != $avtor['id']) {
			if ($user['id'] != $avtor['id'] && dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]' AND `id_user` = '$user[id]' LIMIT 1"), 0) == 0) {
				echo ' <a href="?likestatus=' . $status['id'] . '&amp;page=$page"><img src="/style/icons/like.gif" alt=""/>赞!</a> &bull; ';
				$like = $l;
			} else {
				echo ' <img src="/style/icons/like.gif" alt=""/> 你和 ';
				$like = $l - 1;
			}
		} else {
			echo ' <img src="/style/icons/like.gif" alt=""/> ';
			$like = $l;
		}
		echo '<a href="/user/status/like.php?id=' . $status['id'] . '">' . $like . ' 个用户</a>觉得很赞！';
	} else {
		echo '<div class="nav1">';
		echo user::nick($avtor['id'], 1, 0, 0) . ' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a><br />';
		echo '状态已被删除 =(';
	}
}
