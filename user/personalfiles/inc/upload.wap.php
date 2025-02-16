<?php
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
联系：
ICQ：587863132
http://dcms-social.ru
=======================================
*/

// 检查是否定义了常量“USER”，如果没有定义，则禁止访问此页面
if (!defined("USER")) die('No access');

// 判断用户是否已选择下载目录，如果有，则从数据库中获取目录信息
if (isset($_SESSION['down_dir'])) {
	$dir_id = dbassoc(dbquery("SELECT * FROM `downnik_dir` WHERE `id` = '" . intval($_SESSION['down_dir']) . "' LIMIT 1"));
} else {
	// 如果未选择目录，则默认使用标记为“我的”的目录
	$dir_id = dbassoc(dbquery("SELECT * FROM `downnik_dir` WHERE `my` = '1' LIMIT 1"));
}

// 如果目录允许上传文件
if ($dir_id['upload'] == 1) {
	// 检查上传入口参数
	if (isset($_GET['upload']) && $_GET['upload'] == 'enter') {
		// 验证文件上传的错误
		if (!isset($_FILES['file'])) {
			$err[] = '上传文件时出错';
		} elseif (!isset($_FILES['file']['tmp_name']) || filesize($_FILES['file']['tmp_name']) > $dir_id['maxfilesize']) {
			$err[] = '文件大小超过设定的限制';
		} else {
			// 处理上传文件的文件名
			$file = esc(stripcslashes(htmlspecialchars($_FILES['file']['name'])));
			$file = preg_replace('(\#|\?)', '', $file);
			$name = pathinfo($file, PATHINFO_FILENAME);		// 获取文件名
			$ras = pathinfo($file, PATHINFO_EXTENSION);		// 获取文件扩展名
			$type = my_esc($_FILES['file']['type']);		// 文件类型
			$size = $_FILES['file']['size'];				// 文件大小
			$ras_ok = false;

			if ($dir_id['ras'] != '*') {					// * 表示允许上传所有文件格式
				$rasss = explode(';', $dir_id['ras']);		// 允许的文件格式
				// 检查文件扩展名是否有效
				for ($i = 0; $i < count($rasss); $i++) {
					if ($rasss[$i] != NULL && $ras == $rasss[$i]) $ras_ok = true;
				}
				if (!$ras_ok) $err = '无效的文件扩展名';
			}
		}

		// 检查是否设置了“18+”标签
		if (isset($_POST['metka']) && ($_POST['metka'] == '0' || $_POST['metka'] == '1')) {
			$metka = $_POST['metka'];
		} else {
			$metka = 0;
		}

		// 获取文件描述
		$opis = NULL;
		if (isset($_POST['msg'])) $opis = stripslashes(htmlspecialchars(esc($_POST['msg'])));

		// 如果没有错误，插入文件数据到数据库
		if (!isset($err)) {
			// 更新用户临时评分
			dbquery("UPDATE `user` SET `rating_tmp` = '" . ($user['rating_tmp'] + 3) . "' WHERE `id` = '{$user['id']}' LIMIT 1");
			// 将文件信息插入到文件表中
			dbquery("INSERT INTO `downnik_files` (`metka`, `id_dir`, `name`, `ras`, `type`, `size`, `time`, `time_last`, `id_user`, `opis`, `my_dir` ) VALUES ('$metka', '$dir_id[id]', '$name', '$ras', '$type', '$size', '$time', '$time', '$user[id]', '$opis', '$dir[id]')");
			$id_file = dbinsertid();

			// 处理社交“动态”的逻辑
			if (!$dir['pass']) {
				$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $dir['id_user'] . "' AND `i` = '1'");
				while ($f = dbarray($q)) {
					$a = user::get_user($f['frend']);
					$lentaSet = dbarray(dbquery("SELECT * FROM `tape_set` WHERE `id_user` = '" . $a['id'] . "' LIMIT 1"));
					if ($f['lenta_down'] == 1 && $lentaSet['lenta_files'] == 1) {
						if (dbresult(dbquery("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'down' AND `id_file` = '$dir[id]'"), 0) == 0) {
							dbquery("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`, `count`) values('$a[id]', '$dir[id_user]', 'down', '$time', '$dir[id]', '1')");
						} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'down' AND `id_file` = '$dir[id]' AND `read` = '1'"), 0) > 0) {
							dbquery("DELETE FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'down' AND `id_file` = '$dir[id]'");
							dbquery("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`, `count`) values('$a[id]', '$dir[id_user]', 'down', '$time', '$dir[id]', '1')");
						} else {
							$tape = dbarray(dbquery("SELECT * FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'down' AND `id_file` = '$dir[id]'"));
							dbquery("UPDATE `tape` SET `count` = '" . ($tape['count'] + 1) . "', `read` = '0', `time` = '$time' WHERE `id_user` = '$a[id]' AND `type` = 'down' AND `id_file` = '$dir[id]' LIMIT 1");
						}
					}
				}
			}

			// 保存文件到服务器
			if (!copy($_FILES['file']['tmp_name'], H . "files/down/{$id_file}.dat")) {
				dbquery("DELETE FROM `downnik_files` WHERE `id` = '{$id_file}' LIMIT 1");
				$err[] = '上传时出错';
			}
		}

		// 如果一切成功，设置文件权限并创建截图
		if (!isset($err)) {
			chmod(H . "files/down/{$id_file}.dat", 0666);

			// 处理截图逻辑
			if (isset($_FILES['screen']) && $_FILES['screen']['error'] === UPLOAD_ERR_OK && $imgc = imagecreatefromstring(file_get_contents($_FILES['screen']['tmp_name']))) {
				// 创建 320x320 的缩略图
				$img_x = imagesx($imgc);
				$img_y = imagesy($imgc);
				if ($img_x == $img_y) {
					$dstW = 320; // 宽度
					$dstH = 320; // 高度
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
				$screen = img_copyright($screen); // 叠加水印
				imagegif($screen, H . "files/screens/320/$id_file.gif");
				imagedestroy($screen);
			}

			// 创建 128x128 的缩略图
			if (isset($_FILES['screen']) && $_FILES['screen']['error'] === UPLOAD_ERR_OK && $imgc = imagecreatefromstring(file_get_contents($_FILES['screen']['tmp_name']))) {
				$img_x = imagesx($imgc);
				$img_y = imagesy($imgc);
				if ($img_x == $img_y) {
					$dstW = 128; // 宽度
					$dstH = 128; // 高度
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
				$screen = img_copyright($screen); // 叠加水印
				imagegif($screen, H . "files/screens/128/$id_file.gif");
				imagedestroy($screen);
			}
			$_SESSION['down_dir'] = null;
			$_SESSION['message'] = '文件已成功上传';
			header('Location: ?');
			exit;
		}
	}
}

// 如果允许上传并且用户已登录，则显示上传表单
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
	if ($set['web'] && test_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php')) {
		include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
	} else {
		echo $tPanel . '<textarea name="msg"></textarea><br />';
	}
	echo "<label><input type='checkbox' name='metka' value='1' /> 标记 <font color=red>18+</font></label><br />";
	echo "<input class=\"submit\" type=\"submit\" value=\"上传\" /> [<img src='/style/icons/delete.gif' alt='*'> <a href='?'>取消</a>]<br />";
	if ($dir_id['ras'] != '*') {
		echo "<div class='main'>*允许上传以下格式的文件: ";
		$i5 = explode(';', $dir_id['ras']);
		for ($i = 0; $i < count($i5); $i++) {
			echo $i5[$i] . ', ';
		}
		echo "如果缺少某种格式，请告知项目管理！</div></form>";
	}
	echo "<div class='foot'>";
	echo "<img src='/style/icons/up_dir.gif' alt='*'> " . ($dir['osn'] == 1 ? '<a href="/user/personalfiles/' . $ank['id'] . '/' . $dir['id'] . '/">档案</a>' : '') . " " . user_files($dir['id_dires']) . " " . ($dir['osn'] == 1 ? '' : '&gt; <a href="/user/personalfiles/' . $ank['id'] . '/' . $dir['id'] . '/">' . text($dir['name']) . '</a>') . "";
	echo "</div>";
}
