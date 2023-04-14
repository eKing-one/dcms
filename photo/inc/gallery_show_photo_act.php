<?
/*
* Установка аватара на главной
*/
if (isset($_GET['act']) && $_GET['act'] == 'avatar') {
	if ($user['id'] == $ank['id']) {
		/* Отправляем в ленту смену аватара */
		$avatar = dbarray(dbquery("SELECT * FROM `gallery_photo` WHERE `avatar` = '1' AND `id_user` = '$user[id]' LIMIT 1"));
		if ($avatar['id'] != $photo['id']) {
			/*---------друзьям автора--------------*/
			$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $gallery['id_user'] . "' AND `i` = '1'");
			while ($f = dbarray($q)) {
				$a = user::get_user($f['frend']);
				if ($a['id'] != $user['id'] && $photo['id'] != $avatar['id'] && $f['lenta_avatar'] == 1)
					dbquery("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`, `count`, `avatar`) values('$a[id]', '$gallery[id_user]', 'avatar', '$time', '$photo[id]', '1', '$avatar[id]')");
			}
			dbquery("UPDATE `gallery_photo` SET `avatar` = '0' WHERE `id_user` = '$user[id]'");
			dbquery("UPDATE `gallery_photo` SET `avatar` = '1' WHERE `id` = '$photo[id]' LIMIT 1");
			dbquery("INSERT INTO `stena` (`id_user`,`id_stena`,`time`,`info`,`info_1`,`type`) values('" . $user['id'] . "','" . $user['id'] . "','" . $time . "','новый аватар','" . $photo['id'] . "','photo')");
			$_SESSION['message'] = '照片已成功安装在主照片上！';
		}
		header("Location: ?");
		exit;
	}
}
/*
* Удаление фотографии
*/
if ((user_access('photo_photo_edit') || isset($user) && $user['id'] == $ank['id']) && isset($_GET['act']) && $_GET['act'] == 'delete' && isset($_GET['ok'])) {
	if ($user['id'] != $ank['id'])
		admin_log('图片集锦', '照片', "删除用户的照片 '[url=/user/info.php?id=$ank[id]]" . user::nick($ank['id'], 1, 0, 0) . "[/url]'");
	@unlink(H . "sys/gallery/48/$photo[id].jpg");
	@unlink(H . "sys/gallery/128/$photo[id].jpg");
	@unlink(H . "sys/gallery/640/$photo[id].jpg");
	@unlink(H . "sys/gallery/photo/$photo[id].jpg");
	dbquery("DELETE FROM `gallery_photo` WHERE `id` = '$photo[id]' LIMIT 1");
	$_SESSION['message'] = '照片已成功删除';
	header("Location: /photo/$ank[id]/$gallery[id]/");
	exit;
}
/*
* Редактирование фотографии
*/
if ((user_access('photo_photo_edit') || isset($user) && $user['id'] == $ank['id']) && isset($_GET['act']) && $_GET['act'] == 'rename' && isset($_GET['ok']) && isset($_POST['name']) && isset($_POST['opis'])) {
	$name = esc(stripcslashes(htmlspecialchars($_POST['name'])), 1);
	$mat = antimat($name);
	if ($mat) $err = '标题中存在禁止的字符';
	if (strlen2($name) < 3) $err = '短标题';
	if (strlen2($name) > 32) $err = '标题不得超过 32 个字符';
	$name = my_esc($name);
	$msg = $_POST['opis'];
	if (strlen2($msg) > 1024) $err = '描述长度超过 1024 个字符的限制';
	$msg = my_esc($msg);
	if (isset($_POST['metka']) && $_POST['metka'] == 1) $metka = 1;
	else $metka = 0;
	if (!isset($err)) {
		if ($user['id'] != $ank['id'])
			admin_log('图片集锦', '照片', "重命名用户照片 '[url=/user/info.php?id=$ank[id]]" . user::nick($ank['id'], 1, 0, 0) . "[/url]'");
		dbquery("UPDATE `gallery_photo` SET `name` = '$name', `metka` = '$metka', `opis` = '$msg' WHERE `id` = '$photo[id]' LIMIT 1");
		$photo = dbassoc(dbquery("SELECT * FROM `gallery_photo` WHERE `id` = '$photo[id]'  LIMIT 1"));
		$_SESSION['message'] = '照片已成功重命名';
		header("Location: ?");
		exit;
	}
}
