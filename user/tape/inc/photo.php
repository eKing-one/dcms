<?
/*
* $name описание действий объекта 
*/
if ($type == 'album' && $post['avtor'] != $user['id']) {
	$name = '相册中的新照片';
}
/*
* Вывод блока с содержимым 
*/
if ($type  ==  'album') {
	$gallery = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '" . $post['id_file'] . "' LIMIT 1"));
	if ($post['count'] > 5) {
		$kol = '5';
		$kol2 = $post['count'] - 5;
	} else {
		$kol = $post['count'];
	}
	if ($gallery['id']) {
		echo '<div class="nav1">';
		echo user::nick($avtor['id'], 1, 1, 0) . ' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a> ' . $name . ' <img src="/style/icons/camera.png" alt=""/>  <a href="/photo/' . $avtor['id'] . '/' . $gallery['id'] . '/"><b>' . text($gallery['name']) . '</b></a> ';
		echo $s1 . vremja($post['time']) . $s2;
		echo '</div>';
		echo '<div class="nav2">';
		$as = dbquery("SELECT * FROM `gallery_photo` WHERE `id_gallery` = '$gallery[id]' ORDER BY `id` DESC LIMIT $kol");
		while ($xx = dbassoc($as)) {
			echo '<a href="/photo/' . $gallery['id_user'] . '/' . $gallery['id'] . '/' . $xx['id'] . '/"><img style=" margin: 2px;" src="/photo/photo50/' . $xx['id'] . '.' . $xx['ras'] . '" alt="*"/></a>';
		}
		if (isset($kol2)) echo '和更多' . $kol2 . ' 照片';
	} else {
		echo '<div class="nav1">';
		echo "删除相册 =(";
	}
}
