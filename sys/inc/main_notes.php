<?php
echo '<div style="padding: 6px 10px;" class="foot"><a href="/forum/"><b>论坛</b></a></div>';

$k_post = dbresult(dbquery("SELECT COUNT(`id`) FROM `forum_t`"), 0);
if ($k_post > 0) {
	echo '<div class="mess">';
	$q = dbquery("SELECT * FROM `forum_t` ORDER BY `time_create` DESC LIMIT 5");
	while ($them = dbassoc($q)) {
		// Лесенка дивов
		if ($num == 0) {
			echo '<div class="nav1">';
			$num = 1;
		} elseif ($num == 1) {
			echo '<div class="nav2">';
			$num = 0;
		}

		// Иконка темы
		//echo '<img src="/style/themes/' . $set['set_them'] . '/forum/14/them_' . $them['up'] . $them['close'] . '.png" alt="" /> ';
		// Ссылка на тему
		echo '<a href="/forum/' . $them['id_forum'] . '/' . $them['id_razdel'] . '/' . $them['id'] . '/"><b>' . htmlspecialchars($them['name']) . '</b></a>';
		//echo rez_text($them['text'], 112) . '<br/>';
		//主题作者
		echo '作者'.user::nick($them['id_user'], 1, 0, 0) . '';

		// // 最后一个岗位
		// $post = dbarray(dbquery("SELECT `id`,`time`,`id_user` FROM `forum_p` WHERE `id_them` = '$them[id]' AND `id_forum` = '" . $them['id_forum'] . "' AND `id_razdel` = '" . $them['id_razdel'] . "'  ORDER BY `time` DESC LIMIT 1"));
		// if (isset($post['id'])) {
		// 	// 最后一篇文章的作者
		// 	echo '/' . user::nick($post['id_user'], 1, 0, 0) . '<br />';
		// }

		echo '</div>';
	}
	echo "</div>";
}


$plus = dbresult(dbquery("SELECT COUNT(`id`)FROM `notes` WHERE `time`>'" . ($time - 86000) . "'"), 0);
$count = dbresult(dbquery("SELECT COUNT(`id`)FROM `notes`"), 0);
if ($plus > 0) {
		$e = $count . " + " . $plus;
} else {
		$e = $count;
}
echo '<div style="padding: 6px 10px;" class="foot"><a href="/plugins/notes/">';

echo '<b>日记</b> (' . $e . ')</a></div>';

$q = dbquery("SELECT * FROM `notes` ORDER BY `time` DESC LIMIT 3");
if (dbrows($q) == 0) {
		echo '<div class="nav2 main_no_notes_nav2">没有记录</div>';
} else {
	while ($post = dbassoc($q)) {
		$note_name = '<a href="/plugins/notes/list.php?id=' . $post['id'] . '"><span style="color:#06f">' . text($post['name']) . '</span></a>';

		$count_comm = dbresult(dbquery("SELECT COUNT(`id`) FROM `notes_komm` WHERE `id_notes`='" . $post['id'] . "'"), 0);
		echo "<div class='nav2'>";

		echo user::nick($post['id_user'], 1, 1, 0) . ' : ' . $note_name;
		echo '<br />';
		echo rez_text($post['msg'], 80);
		echo '<br />';
		echo ($post['share'] == 1 ? "(!) <i>转发</i><br/>" : null);
		echo '<img src="/style/icons/comm_num_gray.png">' . $count_comm . '<span style="float:right;color:#666;"><small>';
		echo vremja($post['time']);
		echo '</small></div>';
	}
}
echo '<div class="nav1">';
if (isset($user)) {
	echo '<a href="/plugins/notes/add.php">写日记</a>';
}
echo '<span style="float:right;"><a href="/plugins/notes/">所有日记&rarr;</a></span><br /></div>';
