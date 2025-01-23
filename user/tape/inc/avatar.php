<?php
/*
* $name 个体操作描述 
*/
if ($type == 'avatar' && $post['avtor'] != $user['id']) {	// 阿凡达
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
	$photo = dbassoc(dbquery("SELECT * FROM `gallery_photo` WHERE `id` = '" . $post['id_file'] . "' LIMIT 1"));
	$avatar = dbassoc(dbquery("SELECT * FROM `gallery_photo` WHERE `id` = '" . $post['avatar'] . "' LIMIT 1"));
	$gallery = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '" . $photo['id_gallery'] . "' LIMIT 1"));
	if (isset($avatar['id_gallery'])) $gallery2 = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '" . $avatar['id_gallery'] . "' LIMIT 1"));
	echo '<div class="nav1">';
	echo  user::nick($avtor['id'],1,1,0);
	echo medal($avtor['id']) . ' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a> ' . $name;
	echo $s1 . vremja($post['time']) . $s2;
	echo '</div>';
	echo '<div class="nav2">';
	if ($photo['id']) echo '<b>' . text($photo['name']) . '</b>';
	if (isset($avatar['id']) && $avatar['id']) echo ' &raquo; <b>' . text($avatar['name']) . '</b>';
	if (isset($avatar['id']) && $avatar['id'] || $photo['id'])echo '<br />';
	if ($photo['id']) echo '<a href="/photo/' . $avtor['id'] . '/' . $gallery['id'] . '/' . $photo['id'] . '/">';
	echo '<img style=" max-width:50px; margin:3px;" src="/photo/photo50/' . $post['id_file'] . '.jpg" alt="*" />';
	if ($photo['id']) echo '</a>';
	if ($post['avatar']) {
		echo ' <img src="/style/icons/arRt2.png" alt="*"/> ';
		if ($avatar['id']) echo '<a href="/photo/' . $avtor['id'] . '/' . $gallery2['id'] . '/' . $avatar['id'] . '/">';
		echo '<img style="max-width:50px; margin:3px;" src="/photo/photo50/' . $post['avatar'] . '.jpg" alt="*" />';
		if ($avatar['id']) echo '</a>';
	}
	echo '<br />';
	if ($photo['id']) echo '<a href="/photo/' . $avtor['id'] . '/' . $gallery['id'] . '/' . $photo['id'] . '/"><img src="/style/icons/bbl5.png" alt="*"/> (' . dbresult(dbquery("SELECT COUNT(*) FROM `gallery_komm` WHERE `id_photo` = '$photo[id]'"),0) . ')</a> ';
}
