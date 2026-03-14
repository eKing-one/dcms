<?php
/*
* 输出包含内容的块
*/
if ($type == 'frends') {
	$frend = user::get_user($post['id_file']);
	if ($frend['id']) {
		echo '<div class="nav1">';
		echo user::nick($avtor['id'], 1, 0, 0) . ' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a> ';
		echo '添加 ' . user::nick($frend['id'], 1, 1, 0) . ' 作为朋友 ';
		echo $s1 . vremja($post['time']) . $s2;
		echo '</div>';
		echo '<div class="nav2">';
		if (dbresult(dbquery("SELECT COUNT(*) FROM `gallery_photo` WHERE `id_user` = '$frend[id]'"), 0) > 0) {
			echo user::nick($frend['id'], 1, 0, 0) . ' 最后添加的照片<br />';
			$g = dbquery("SELECT * FROM `gallery_photo` WHERE `id_user` = '$frend[id]' ORDER BY `id` DESC LIMIT 4");
			while ($xx = dbassoc($g)) {
				$gallery = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '" . $xx['id_gallery'] . "' LIMIT 1"));

				// 判断相册的隐私设置
				$canView = false;

				if ($gallery['privat'] == 2) {
					// 相册仅自己可见
					if (isset($user) && $user['id'] == $frend['id']) {
						$canView = true;
					}
				} elseif ($gallery['privat'] == 1) {
					// 相册仅朋友可见
					if (isset($user) && ($user['id'] == $frend['id'] || $frend == 2)) {
						$canView = true;
					}
				} else {
					// 相册没有隐私限制（公开）
					$canView = true;
				}
		
				// 如果相册设置了密码并且当前访问者不是自己，且没有输入密码，则不展示图片
				if ($gallery['pass'] != NULL) {
					if (!isset($user) || $user['id'] != $frend['id']) {
						$canView = false; // 非自己且没有密码，不能查看
					}
				}
		
				// 如果满足查看条件，展示图片
				if ($canView) {
					echo "<a href='/photo/$gallery[id_user]/$gallery[id]/$xx[id]/'><img style=' margin: 2px;' src='/photo/photo50/$xx[id].$xx[ras]' alt='*'/></a>";
				}
			}
		} else {
			echo '用户' . user::nick($frend['id'], 1, 0, 0) . ' 还没有上传照片=(';
		}
	} else {
		echo '<div class="nav1">';
		echo '用户已删除 =(';
	}
}
