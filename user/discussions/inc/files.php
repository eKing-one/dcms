<?php
/*
* 讨论题目
*/
if ($type == 'down' && $post['avtor'] != $user['id']) {
	$name = '档案 | 朋友档案';
} else if ($type == 'down' && $post['avtor'] == $user['id']) {
	$name = '档案 | 你的档案';
}

/*
* 显示
*/
if ($type == 'down') {
	$file = dbassoc(dbquery("SELECT * FROM `downnik_files` WHERE `id` = '" . $post['id_sim'] . "' LIMIT 1"));
	if (isset($file['id']) && $file['id']) {
		echo '<div class="nav1">';
		echo '<img src="../../style/icons/disk.png" alt="*" />';
		echo '<a href="../personalfiles/' . $file['id_user']  . '/' . $file['my_dir'] . '/?id_file=' . $file['id'] . '&amp;page=' . $pageEnd . '">' . $name . '</a>';
		if ($post['count'] > 0) echo "<b><font color='red'>+{$post['count']}</font></b>";
		echo ' <span class="time">' . $s1 . vremja($post['time']) . $s2 . '</span>';
		echo '</div>';

		echo '<div class="nav2">';
		echo '&raquo; <b>' . text($file['name']) . '</b><br />';
		echo '<span class="text">' . output_text($file['opis']) . '</span>';
		echo '</div>';
	} else {
		echo '<div class="nav1">';
		echo '<img src="../../style/icons/disk.png" alt="*" />';
		echo $name . ' ';
		if ($post['count'] > 0) echo "<b><font color='red'>+{$post['count']}</font></b>";
		echo '<span class="time">' . $s1 . vremja($post['time']) . $s2 . '</span>';
		echo '</div>';
		echo '<div class="mess">该文件已被删除</div>';
	}
}