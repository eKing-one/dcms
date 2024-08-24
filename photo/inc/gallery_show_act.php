<?
// Удаление альбома
if ((user_access('photo_alb_del') || isset($user) && $user['id'] == $ank['id']) && isset($_GET['act']) && $_GET['act'] == 'delete' && isset($_GET['ok'])) {
	$q = dbquery("SELECT * FROM `gallery_photo` WHERE `id_gallery` = '$gallery[id]'");
	while ($post = dbassoc($q)) {
		@unlink(H . "sys/gallery/48/$post[id].jpg");
		@unlink(H . "sys/gallery/50/$post[id].jpg");
		@unlink(H . "sys/gallery/128/$post[id].jpg");
		@unlink(H . "sys/gallery/640/$post[id].jpg");
		@unlink(H . "sys/gallery/photo/$post[id].jpg");
		dbquery("DELETE FROM `gallery_komm` WHERE `id_photo` = '$post[id]' LIMIT 1");
		dbquery("DELETE FROM `gallery_photo` WHERE `id` = '$post[id]' LIMIT 1");
		dbquery("DELETE FROM `mark_photo` WHERE `id_photo` = '$post[id]' LIMIT 1");
	}
	if ($user['id'] != $ank['id'])
		admin_log('图片集锦', '相片册', "删除相册 " . text($gallery['name']) . " (照片: " . dbrows($q) . ")");
	dbquery("DELETE FROM `gallery` WHERE `id` = '$gallery[id]' LIMIT 1");
	$_SESSION['message'] = '已成功删除相册';
	header("Location: /photo/$ank[id]/");
	exit;
}
// Загрузка фото
if (isset($user) && $user['id'] == $ank['id'] && isset($_FILES['file'])) {
	if ($imgc = @imagecreatefromstring(file_get_contents($_FILES['file']['tmp_name']))) {
		$name = $_POST['name'];
		if ($name == null)
			$name = esc(stripcslashes(htmlspecialchars(preg_replace('#\.[^\.]*$#i', 'NULL', $_FILES['file']['name']))));
		if (strlen2($name) < 3) $err = '标题太短了！要大于 3 字节！';
		if (strlen2($name) > 32) $err = '标题不得超过 32 字节！';
		$name = my_esc($name);
		if (isset($_POST['metka']) && ($_POST['metka'] == 0 || $_POST['metka'] == 1))
			$metka = my_esc($_POST['metka']);
		else {
			$metka = 0;
		}
		$msg = $_POST['opis'];
		if (strlen2($msg) > 1024) $err = '描述长度超过 1024 个字节的限制';
		$msg = my_esc($msg);
		$img_x = imagesx($imgc);
		$img_y = imagesy($imgc);
		if ($img_x > $set['max_upload_photo_x'] || $img_y > $set['max_upload_photo_y'])
			$err = '图像大小超过 ' . $set['max_upload_photo_x'] . '*' . $set['max_upload_photo_y'];
		if (!isset($err)) {
			if (isset($_GET['avatar'])) {
				dbquery("UPDATE `gallery_photo` SET `avatar` = '0' WHERE `id_user` = '$user[id]'");
				dbquery("INSERT INTO `gallery_photo` (`id_gallery`, `name`, `ras`, `type`, `opis`, `id_user`,`avatar`, `metka`, `time`) values ('$gallery[id]', '$name', 'jpg', 'image/jpeg', '$msg', '$user[id]','1', '$metka', '$time')");
			} else {
				dbquery("INSERT INTO `gallery_photo` (`id_gallery`, `name`, `ras`, `type`, `opis`, `id_user`, `metka`, `time`) values ('$gallery[id]', '$name', 'jpg', 'image/jpeg', '$msg', '$user[id]', '$metka', '$time')");
			}
			$id_photo = dbinsertid();
			dbquery("UPDATE `gallery` SET `time` = '$time' WHERE `id` = '$gallery[id]' LIMIT 1");
			$q = dbquery("SELECT * FROM `frends` WHERE `user` = '$user[id]' AND `lenta_photo` = '1' AND `i` = '1'");
			$photo['id'] = $id_photo;
			/*
* Лента друзей
*/
			dbquery("UPDATE `tape` SET `count` = '0' WHERE  `type` = 'album' AND `read` = '1' AND `id_file` = '$gallery[id]'");
			$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $gallery['id_user'] . "' AND `i` = '1'");
			while ($f = dbarray($q)) {
				$a = user::get_user($f['frend']);
				// 通用磁带调谐
				$lentaSet = dbarray(dbquery("SELECT * FROM `tape_set` WHERE `id_user` = '" . $a['id'] . "' LIMIT 1"));
				/* 发送过滤器 */
				if ($f['lenta_photo'] == 1 && $lentaSet['lenta_photo'] == 1) {
					/* 如果我们从页面上加载，我们将作为头像更改发送 */
					if (isset($_GET['avatar'])) {
						if ($a['id'] != $user['id'] && $photo['id'] != $avatar['id'])
							dbquery("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`, `count`, `avatar`) values('$a[id]', '$gallery[id_user]', 'avatar', '$time', '$photo[id]', '1', '$avatar[id]')");
					} else {
						/* 如果不是，就像一张新照片一样把头盔装进胶带里 */
						if (dbresult(dbquery("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'album' AND `id_file` = '$gallery[id]' LIMIT 1"), 0) == 0) {
							dbquery("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`, `count`) values('$a[id]', '$gallery[id_user]', 'album', '$time', '$gallery[id]', '1')");
						} else {
							$tape = dbarray(dbquery("SELECT * FROM `tape` WHERE `type` = 'album' AND `id_file` = '$gallery[id]'"));
							dbquery("UPDATE `tape` SET `count` = '" . ($tape['count'] + 1) . "', `read` = '0', `time` = '$time' WHERE `id_user` = '$a[id]' AND `type` = 'album' AND `id_file` = '$gallery[id]' LIMIT 1");
						}
					}
				}
			}
			if ($img_x == $img_y) {
				$dstW = 48; // ширина
				$dstH = 48; // высота 
			} elseif ($img_x > $img_y) {
				$prop = $img_x / $img_y;
				$dstW = 48;
				$dstH = ceil($dstW / $prop);
			} else {
				$prop = $img_y / $img_x;
				$dstH = 48;
				$dstW = ceil($dstH / $prop);
			}
			$screen = imagecreatetruecolor($dstW, $dstH);
			imagecopyresampled($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
			//imagedestroy($imgc);
			imagejpeg($screen, H . "sys/gallery/48/$id_photo.jpg", 90);
			@chmod(H . "sys/gallery/48/$id_photo.jpg", 0777);
			imagedestroy($screen);
			if ($img_x == $img_y) {
				$dstW = 128; // ширина
				$dstH = 128; // высота 
			} elseif ($img_x > $img_y) {
				$prop = $img_x / $img_y;
				$dstW = 128;
				$dstH = ceil($dstW / $prop);
			} else {
				$prop = $img_y / $img_x;
				$dstH = 128;
				$dstW = ceil($dstH / $prop);
			}
			$screen = imagecreatetruecolor($dstW, $dstH);
			imagecopyresampled($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
			//imagedestroy($imgc);
			// $screen = img_copyright($screen); // наложение копирайта
			imagejpeg($screen, H . "sys/gallery/128/$id_photo.jpg", 90);
			@chmod(H . "sys/gallery/128/$id_photo.jpg", 0777);
			imagedestroy($screen);
			if ($img_x > 640 || $img_y > 640) {
				if ($img_x == $img_y) {
					$dstW = 640; // ширина
					$dstH = 640; // высота 
				} elseif ($img_x > $img_y) {
					$prop = $img_x / $img_y;
					$dstW = 640;
					$dstH = ceil($dstW / $prop);
				} else {
					$prop = $img_y / $img_x;
					$dstH = 640;
					$dstW = ceil($dstH / $prop);
				}
				$screen = imagecreatetruecolor($dstW, $dstH);
				imagecopyresampled($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
				// imagedestroy($imgc);
				// $screen=img_copyright($screen); // наложение копирайта
				imagejpeg($screen, H . "sys/gallery/640/$id_photo.jpg", 90);
				imagedestroy($screen);
				$imgc = img_copyright($imgc); // наложение копирайта
				imagejpeg($imgc, H . "sys/gallery/photo/$id_photo.jpg", 90);
				@chmod(H . "sys/gallery/photo/$id_photo.jpg", 0777);
			} else {
				imagejpeg($imgc, H . "sys/gallery/640/$id_photo.jpg", 90);
				$imgc = img_copyright($imgc); // наложение копирайта
				imagejpeg($imgc, H . "sys/gallery/photo/$id_photo.jpg", 90);
				@chmod(H . "sys/gallery/photo/$id_photo.jpg", 0777);
			}
			@chmod(H . "sys/gallery/640/$id_photo.jpg", 0777);
			imagedestroy($imgc);
			crop(H . "sys/gallery/640/$id_photo.jpg", H . "sys/gallery/50/$id_photo.tmp.jpg");
			resize(H . "sys/gallery/50/$id_photo.tmp.jpg", H . "sys/gallery/50/$id_photo.jpg", 50, 50);
			@chmod(H . "sys/gallery/50/$id_photo.jpg", 0777);
			@unlink(H . "sys/gallery/50/$id_photo.tmp.jpg");
			if (isset($_GET['avatar'])) {
				$_SESSION['message'] = '已成功将照片设置为头像';
				header("Location: /user/info.php");
				exit;
			}
			$_SESSION['message'] = '照片已成功上传';
			header("Location: /photo/$ank[id]/$gallery[id]/$id_photo/");
			exit;
		}
	} else
		$err = '不支持您选择的图像格式';
}
// Редактирование альбома
if (isset($_GET['edit']) && $_GET['edit'] == 'rename' && isset($_GET['ok']) && (isset($_POST['name']) || isset($_POST['opis']))) {
	$name = $_POST['name'];
	$pass = $_POST['pass'];
	$privat = intval($_POST['privat']);
	$privat_komm = intval($_POST['privat_komm']);
	if (strlen2($name) < 3) $err = '标题太短了！要大于 3 字节！';
	if (strlen2($name) > 32) $err = '标题不得超过 32 个字符';
	$name = my_esc($name);
	$pass = my_esc($pass);
	$msg = $_POST['opis'];
	if (strlen2($msg) > 1024) $err = '描述长度超过 1024 个字符的限制';
	$msg = my_esc($msg);
	if (!isset($err)) {
		if ($user['id'] != $ank['id'])
			admin_log('图片集锦', '照片', "重命名用户相册 '[url=/user/info.php?id=$ank[id]]" . user::nick($ank['id'], 1, 0, 0) . "[/url]'");
		dbquery("UPDATE `gallery` SET `name` = '$name', `privat` = '$privat', `privat_komm` = '$privat_komm', `pass` = '$pass', `opis` = '$msg' WHERE `id` = '$gallery[id]' LIMIT 1");
		$_SESSION['message'] = '已成功接受更改';
		header("Location: /photo/$ank[id]/?");
		exit;
	}
}
