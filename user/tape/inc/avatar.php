<?php
/*
* $name 个体操作描述 
*/
if ($type == 'avatar' && $post['avtor'] != $user['id']) {
	if ($post['avatar']) {
		$name = '修改了' . ($avtor['pol'] == 1 ? null : "а") . ' 主页上的照片';
	} else {
		$name = '已安装' . ($avtor['pol'] == 1 ? null : "а") . ' 主页上的照片';	
	}
}

/*
* 内容块输出 
*/
if ($type == 'avatar') {
	echo '<div class="nav1">';
	echo  user::nick($avtor['id'],1,1,0);
	echo medal($avtor['id']) . ' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a> ' . $name . ' ' . $s1 . vremja($post['time']) . $s2;
	echo '</div>';
	echo '<div class="nav2">';

	// 获取新头像的图片信息
	$photo = dbassoc(dbquery("SELECT * FROM `gallery_photo` WHERE `id` = '" . $post['id_file'] . "' LIMIT 1"));
	if (isset($photo['id']) && $photo['id']) {
		$gallery = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '" . $photo['id_gallery'] . "' LIMIT 1"));
		echo '<b>' . text($photo['name']) . '</b>';
	}

	// 展示以前的头像
	if ($post['avatar']) {
		$avatar = dbassoc(dbquery("SELECT * FROM `gallery_photo` WHERE `id` = '" . $post['avatar'] . "' LIMIT 1"));
		if (isset($avatar['id_gallery'])) $gallery2 = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '" . $avatar['id_gallery'] . "' LIMIT 1"));
		if (isset($avatar['id']) && $avatar['id']) echo ' &raquo; <b>' . text($avatar['name']) . '</b>';
		if (isset($avatar['id']) && $avatar['id'] || (isset($photo['id']) && $photo['id'])) echo '<br />';
		if (isset($avatar['id']) && $avatar['id']) echo '<a href="/photo/' . $avtor['id'] . '/' . $gallery2['id'] . '/' . $avatar['id'] . '/">';
		echo '<img style="max-width:50px; margin:3px;" src="/photo/photo50/' . $post['avatar'] . '.jpg" alt="*" />';
		if (isset($avatar['id']) && $avatar['id']) echo '</a>';
		echo ' <img src="/style/icons/arRt2.png" alt="*"/> ';
	}

	if (isset($photo['id']) && $photo['id']) echo '<a href="/photo/' . $avtor['id'] . '/' . $gallery['id'] . '/' . $photo['id'] . '/">';
	echo '<img style=" max-width:50px; margin:3px;" src="/photo/photo50/' . $post['id_file'] . '.jpg" alt="*" />';
	if (isset($photo['id']) && $photo['id']) echo '</a>';

	echo '<br />';
	if (isset($photo['id']) && $photo['id']) echo '<a href="/photo/' . $avtor['id'] . '/' . $gallery['id'] . '/' . $photo['id'] . '/"><img src="/style/icons/bbl5.png" alt="*"/> (' . dbresult(dbquery("SELECT COUNT(*) FROM `gallery_komm` WHERE `id_photo` = '$photo[id]'"),0) . ')</a> ';
}
