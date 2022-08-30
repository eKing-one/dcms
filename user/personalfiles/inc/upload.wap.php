<?
/*
=======================================
DCMS-Social 用户个人文件
作者：探索者
---------------------------------------
此脚本在许可下被破坏
DCMS-Social 引擎。
使用时，指定引用到
网址 http://dcms-social.ru
---------------------------------------
接点
ICQ：587863132
http://dcms-social.ru
=======================================
*/
if (!defined("USER")) die('No access');
if (isset($_SESSION['down_dir'])) {
	$dir_id = dbassoc(dbquery("SELECT * FROM `downnik_dir` WHERE `id` = '" . intval($_SESSION['down_dir']) . "' LIMIT 1"));
} else {
	$dir_id = dbassoc(dbquery("SELECT * FROM `downnik_dir` WHERE `my` = '1' LIMIT 1"));
}
if ($dir_id['upload'] == 1) {
	if (isset($_GET['upload']) && $_GET['upload'] == 'enter') {
		if (!isset($_FILES['file'])) $err[] = '上传文件时出错';
		elseif (!isset($_FILES['file']['tmp_name']) || filesize($_FILES['file']['tmp_name']) > $dir_id['maxfilesize']) $err[] = '文件大小超过设定的限制';
		else {
			$file = esc(stripcslashes(htmlspecialchars($_FILES['file']['name'])));
			$file = preg_replace('(\#|\?)', NULL, $file);
			$name = preg_replace('#\.[^\.]*$#', NULL, $file); // имя файла без расширения
			$ras = strtolower(preg_replace('#^.*\.#', NULL, $file));
			$type = my_esc($_FILES['file']['type']);
			$size = filesize($_FILES['file']['tmp_name']);
			$rasss = explode(';', $dir_id['ras']);
			$ras_ok = false;
			for ($i = 0; $i < count($rasss); $i++) {
				if ($rasss[$i] != NULL && $ras == $rasss[$i]) $ras_ok = true;
			}
			if (!$ras_ok) $err = '无效的文件扩展名';
		}
		if (isset($_POST['metka']) && ($_POST['metka'] == '0' || $_POST['metka'] == '1')) $metka = $_POST['metka'];
		else $metka = 0;
		$opis = NULL;
		if (isset($_POST['msg']))
			$opis = stripslashes(htmlspecialchars(esc($_POST['msg'])));
		if (!isset($err)) {
			dbquery("UPDATE `user` SET `rating_tmp` = '" . ($user['rating_tmp'] + 3) . "' WHERE `id` = '$user[id]' LIMIT 1");
			dbquery("INSERT INTO `downnik_files` (`metka`, `id_dir`, `name`, `ras`, `type`, `size`, `time`, `time_last`, `id_user`, `opis`, `my_dir` )
VALUES ('$metka', '$dir_id[id]', '$name', '$ras', '$type', '$size', '$time', '$time', '$user[id]', '$opis' , '$dir[id]')");
			$id_file = mysql_insert_id();
			/*----------------------Лента------------------------*/
			if (!$dir['pass']) {
				$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $dir['id_user'] . "' AND `i` = '1'"); /* Список друзей пользователя */
				while ($f = dbarray($q)) {
					$a = user::get_user($f['frend']);
					$lentaSet = dbarray(dbquery("SELECT * FROM `tape_set` WHERE `id_user` = '" . $a['id'] . "' LIMIT 1")); // Общая настройка ленты
					if ($f['lenta_down'] == 1 && $lentaSet['lenta_files'] == 1) /* Фильтр рассылки */ {
						if (dbresult(dbquery("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'down' AND `id_file` = '$dir[id]'"), 0) == 0) {
							/* Если нет в ленте этой папки */
							dbquery("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`, `count`) values('$a[id]', '$dir[id_user]', 'down', '$time', '$dir[id]', '1')");
						} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'down' AND `id_file` = '$dir[id]' AND `read` = '1'"), 0) > 0) {
							/* Если папка есть в ленте то удаляем запись и создаем новую */
							dbquery("DELETE FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'down' AND `id_file` = '$dir[id]'");
							dbquery("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`, `count`) values('$a[id]', '$dir[id_user]', 'down', '$time', '$dir[id]', '1')");
						} else {
							/* Обновляем колличество новых файлов */
							$tape = dbarray(dbquery("SELECT * FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'down' AND `id_file` = '$dir[id]'"));
							dbquery("UPDATE `tape` SET `count` = '" . ($tape['count'] + 1) . "', `read` = '0', `time` = '$time' WHERE `id_user` = '$a[id]' AND `type` = 'down' AND `id_file` = '$dir[id]' LIMIT 1");
						}
					}
				}
			}
			/*-------------------alex-borisi--------------------*/
			if (!@copy($_FILES['file']['tmp_name'], H . "sys/down/files/$id_file.dat")) {
				dbquery("DELETE FROM `downnik_files` WHERE `id` = '$id_file' LIMIT 1");
				$err[] = '上传时出错';
			}
		}
		if (!isset($err)) {
			chmod(H . "sys/down/files/$id_file.dat", 0666);
			if (isset($_FILES['screen']) && $imgc = @imagecreatefromstring(file_get_contents($_FILES['screen']['tmp_name']))) {
				$img_x = imagesx($imgc);
				$img_y = imagesy($imgc);
				if ($img_x == $img_y) {
					$dstW = 320; // ширина
					$dstH = 320; // высота 
				} elseif ($img_x > $img_y) {
					$prop = $img_x / $img_y;
					$dstW = 320;
					$dstH = ceil($dstW / $prop);
				} else {
					$prop = $img_y / $img_x;
					$dstH = 320;
					$dstW = ceil($dstH / $prop);
				}
				$screen = imagecreatetruecolor($dstW, $dstH);
				imagecopyresampled($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
				imagedestroy($imgc);
				$screen = img_copyright($screen); // наложение копирайта
				imagegif($screen, H . "sys/down/screens/320/$id_file.gif");
				imagedestroy($screen);
			}
			if (isset($_FILES['screen']) && $imgc = @imagecreatefromstring(file_get_contents($_FILES['screen']['tmp_name']))) {
				$img_x = imagesx($imgc);
				$img_y = imagesy($imgc);
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
				imagedestroy($imgc);
				$screen = img_copyright($screen); // наложение копирайта
				imagegif($screen, H . "sys/down/screens/128/$id_file.gif");
				imagedestroy($screen);
			}
			$_SESSION['down_dir'] = null;
			$_SESSION['message'] = '文件已成功上传';
			header('Location: ?');
			exit;
		}
	}
}
if ($dir_id['upload'] == 1 && isset($user)) {
	$set['title'] = '档案下载';
	include_once '../../sys/inc/thead.php';
	title();
	aut();
	err();
	echo "<div class='foot'>";
	echo "<img src='/style/icons/up_dir.gif' alt='*'> " . ($dir['osn'] == 1 ? '<a href="/user/personalfiles/' . $ank['id'] . '/' . $dir['id'] . '/">档案</a>' : '') . " " . user_files($dir['id_dires']) . " " . ($dir['osn'] == 1 ? '' : '&gt; <a href="/user/personalfiles/' . $ank['id'] . '/' . $dir['id'] . '/">' . text($dir['name']) . '</a>') . "";
	echo "</div>";
	if (isset($_SESSION['down_dir'])) {
		echo '<div class="mess">';
		echo '该文件将被上传到该文件夹 <b>' . text($dir_id['name']) . '</b> 下载中心 ';
		echo '</div>';
	}
	echo "<form class='foot' enctype=\"multipart/form-data\" name='message' action='?upload=enter&wap' method=\"post\">
档案: (<" . size_file($dir_id['maxfilesize']) . ")<br />
	 <input name='file' type='file' maxlength='$dir_id[maxfilesize]' /><br />
	 截图:<br />
	 <input name='screen' type='file' accept='image/*' /><br />";
	if ($set['web'] && test_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
		include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
	else {
		echo $tPanel . '<textarea name="msg"></textarea><br />';
	}
	echo "<label><input type='checkbox' name='metka' value='1' /> 标记 <font color=red>18+</font></label><br />";
	echo "<input class=\"submit\" type=\"submit\" value=\"上传\" /> [<img src='/style/icons/delete.gif' alt='*'> <a href='?'>取消</a>]<br />
	 <div class='main'>*允许上传以下格式的文件: ";
	$i5 = explode(';', $dir_id['ras']);
	for ($i = 0; $i < count($i5); $i++) {
		echo $i5[$i] . ', ';
	}
	echo "如果缺少某种格式，请告知项目管理！</div></form>";
	echo "<div class='foot'>";
	echo "<img src='/style/icons/up_dir.gif' alt='*'> " . ($dir['osn'] == 1 ? '<a href="/user/personalfiles/' . $ank['id'] . '/' . $dir['id'] . '/">档案</a>' : '') . " " . user_files($dir['id_dires']) . " " . ($dir['osn'] == 1 ? '' : '&gt; <a href="/user/personalfiles/' . $ank['id'] . '/' . $dir['id'] . '/">' . text($dir['name']) . '</a>') . "";
	echo "</div>";
}
