<?
/*
* $name 个体操作描述 
*/
if ($type == 'obmen' && $post['avtor'] != $user['id']) {
	$name = '文件夹中的新文件';
}
/*
* 内容块输出 
*/
if ($type == 'obmen') {
	$dir = dbassoc(dbquery("SELECT * FROM `user_files` WHERE `id` = '" . $post['id_file'] . "' LIMIT 1"));
	if ($post['count'] > 5) {
		$kol = '5';
		$kol2 = $post['count'] - 5;
	} else {
		$kol = $post['count'];
	}
	echo '<div class="nav1">';
	echo  user::nick($avtor['id'],0,0,0) .
		' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a> ' . $name . ' <img src="/style/themes/' . $set['set_them'] . '/loads/14/dir.png" alt="*"/> <a href="/user/personalfiles/' . $dir['id_user'] . '/' . $dir['id'] . '/">' . text($dir['name']) . '</a>  ' . $s1 . vremja($post['time']) . $s2;
	echo '</div>';
	echo '<div class="nav2">';
	$files = dbquery("SELECT * FROM `obmennik_files` WHERE `my_dir` = '$dir[id]' ORDER BY `id` DESC LIMIT $kol");
	while ($file = dbassoc($files)) {
		if ($file['id']) {
			$ras = $file['ras'];
			if (is_file(H . 'style/themes/' . $set['set_them'] . '/loads/14/' . $ras . '.png')) // Иконка файла
				echo '<img src="/style/themes/' . $set['set_them'] . '/loads/14/' . $ras . '.png" alt="*" /> ';
			else
				echo '<img src="/style/themes/' . $set['set_them'] . '/loads/14/file.png" alt="*" /> ';
			echo '<a href="/user/personalfiles/' . $file['id_user'] . '/' . $dir['id'] . '/?id_file=' . $file['id'] . '&amp;page=1"><b>' . text($file['name']) . '.' . $ras . '</b></a> (' . size_file($file['size']) . ')<br />';
		} else {
			echo user::nick($avtor['id'],0,0,0) . '  <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a>';
			echo '<br />';
			echo '该文件已被删除 =(<br />';
		}
	}
	if (isset($kol2)) echo '和更多 ' . $kol2 . ' 档案';
}
