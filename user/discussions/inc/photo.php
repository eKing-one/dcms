<?php
/*
* 讨论标题
*/
if ($type == 'photo' && $post['avtor'] != $user['id']) {
	$name = '朋友的照片';
} else if ($type == 'photo' && $post['avtor'] == $user['id']) {
	$name = '你的照片';
}

/*
* 显示
*/
if ($type == 'photo') {
	$photo = dbassoc(dbquery("SELECT * FROM `gallery_photo` WHERE `id` = '" . $post['id_sim'] . "' LIMIT 1"));
	if (isset($photo['id']) && $photo['id']) {
		echo '<div class="nav1">
		      <img src="/style/icons/camera.png" alt="*" /> <a href="/photo/' . $avtor['id'] . '/' . $photo['id_gallery'] . '/' . $photo['id'] . '/?page=' . $pageEnd . '">' . $name . '</a> ';
		if ($post['count'] > 0) echo '<b><font color="red">+' . $post['count'] . ' </font></b>';
		echo '<span class="time">' . $s1 . vremja($post['time']) . $s2 . '</span>';
		echo '</div>';
		echo '<div class="nav2">';
		echo "<b><font color='green'>{$avtor['nick']}</font></b>";
		echo ($avtor['id'] != $user['id'] ? '<a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a>' : '');
		echo medal($avtor['id']) . ' ' . online($avtor['id']) . ' &raquo; <b>' . text($photo['name']) . '</b><br />';
		echo '<img src="/photo/photo50/' . $photo['id'] . '.' . $photo['ras'] . '" alt="Image" />';
		echo '</div>';
	} else {
		echo '<div class="nav1"><img src="/style/icons/camera.png" alt="*" />' . $name . ' ' . $s1 . vremja($post['time']) . $s2 . '</div>';
		echo '<div class="mess">照片已被删除</div>';
	}
}