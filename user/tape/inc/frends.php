<?
/*
* $name описание действий объекта 
*/
if ($type == 'frends' && $post['avtor'] != $user['id']) // дневники
{
	$name = '添加' . ($avtor['pol'] == 1 ? null : "а") . ' 作为朋友';
}
/*
* Вывод блока с содержимым 
*/
if ($type == 'frends') {
	$frend = user::get_user($post['id_file']);
	if ($frend['id']) {
		echo '<div class="nav1">';
		echo user::nick($avtor['id'], 1, 0, 0) . ' <a href="user.settings.php?id=' . $avtor['id'] . '>[!]</a> ' . $name . ' ';
		echo user::nick($frend['id'], 1, 1, 0) . ' ';
		echo $s1 . vremja($post['time']) . $s2;
		echo '</div>';
		echo '<div class="nav2">';
		if (dbresult(dbquery("SELECT COUNT(*) FROM `gallery_foto` WHERE `id_user` = '$frend[id]'"), 0) > 0) {
			echo '最后添加的照片 ' . user::nick($frend['id'], 1, 0, 0) . '<br />';
			$g = dbquery("SELECT * FROM `gallery_foto` WHERE `id_user` = '$frend[id]' ORDER BY `id` DESC LIMIT 4");
			while ($xx = dbassoc($g)) {
				$gallery = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '" . $xx['id_gallery'] . "' LIMIT 1"));
				echo "<a href='/foto/$gallery[id_user]/$gallery[id]/$xx[id]/'><img style=' margin: 2px;' src='/foto/foto50/$xx[id].$xx[ras]' alt='*'/></a>";
			}
		} else {
			echo '用户' . user::nick($frend['id'], 1, 0, 0) . ' 还没有上传照片=(';
		}
	} else {
		echo '<div class="nav1">';
		echo '录赂帽拢潞 =(';
	}
}
