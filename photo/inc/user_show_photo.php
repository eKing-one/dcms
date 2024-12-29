<?
if (!isset($user) && !isset($_GET['id_user'])) {
	header("Location: /photo/?" . SID);
	exit;
}
if (isset($user)) $ank['id'] = $user['id'];
if (isset($_GET['id_user'])) $ank['id'] = intval($_GET['id_user']);
$ank = user::get_user($ank['id']);
if (!$ank) {
	header("Location: /photo/?" . SID);
	exit;
}
/* Бан пользователя */
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'photo' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
	header('Location: /user/ban.php?' . SID);
	exit;
}
$gallery['id'] = intval($_GET['id_gallery']);
if (dbresult(dbquery("SELECT COUNT(*) FROM `gallery` WHERE `id` = '$gallery[id]' AND `id_user` = '$ank[id]' LIMIT 1"), 0) == 0) {
	header("Location: /photo/$ank[id]/?" . SID);
	exit;
}
$gallery = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '$gallery[id]' AND `id_user` = '$ank[id]' LIMIT 1"));
$photo['id'] = intval($_GET['id_photo']);
if (dbresult(dbquery("SELECT COUNT(*) FROM `gallery_photo` WHERE `id` = '$photo[id]' LIMIT 1"), 0) == 0) {
	header("Location: /photo/$ank[id]/$gallery[id]/?" . SID);
	exit;
}
$photo = dbassoc(dbquery("SELECT * FROM `gallery_photo` WHERE `id` = '$photo[id]'  LIMIT 1"));
/*
================================
Закладки
================================
*/
// Добавляем в закладки
if (isset($_GET['fav']) && $_GET['fav'] == 1) {
	if (dbresult(dbquery("SELECT COUNT(`id`) FROM `bookmarks` WHERE `id_user` = '" . $user['id'] . "' AND `id_object` = '" . $photo['id'] . "' AND `type`='photo' LIMIT 1"), 0) == 0) {
		dbquery("INSERT INTO `bookmarks` (`type`,`id_object`, `id_user`, `time`) VALUES ('photo','$photo[id]', '$user[id]', '$time')");
		$_SESSION['message'] = '添加到书签的照片';
		header("Location: /photo/$ank[id]/$gallery[id]/$photo[id]/?page=" . intval($_GET['page']));
		exit;
	}
}
// Удаляем из закладок
if (isset($_GET['fav']) && $_GET['fav'] == 0) {
	if (dbresult(dbquery("SELECT COUNT(`id`) FROM `bookmarks` WHERE `id_user` = '" . $user['id'] . "' AND `id_object` = '" . $photo['id'] . "' `type`='photo' LIMIT 1"), 0) == 1) {
		dbquery("DELETE FROM `bookmarks` WHERE `id_user` = '$user[id]' AND  `id_object` = '$photo[id]' AND `type`='photo'");
		$_SESSION['message'] = '从书签中删除的照片';
		header("Location: /photo/$ank[id]/$gallery[id]/$photo[id]/?page=" . intval($_GET['page']));
		exit;
	}
}
$IS = GetImageSize(H . 'sys/gallery/photo/' . $photo['id'] . '.' . $photo['ras']);
printf("", $IS[0], $IS[1]);
$w = $IS[0];
$h = $IS[1];
if ((user_access('photo_photo_edit')) || (isset($user) && $ank['id'] == $user['id']))
	include 'inc/gallery_show_photo_act.php';
/*------------очищаем счетчик этого обсуждения-------------*/
if (isset($user)) {
	dbquery("UPDATE `discussions` SET `count` = '0' WHERE `id_user` = '$user[id]' AND `type` = 'photo' AND `id_sim` = '$photo[id]' LIMIT 1");
	dbquery("UPDATE `notification` SET `read` = '1' WHERE `type` = 'photo_komm' AND `id_user` = '$user[id]' AND `id_object` = '$photo[id]'");
}
/*---------------------------------------------------------*/
/*
==========================
Оценка к фото
==========================
*/
if (isset($user) && $user['id'] != $ank['id'] && dbresult(dbquery("SELECT COUNT(*) FROM `gallery_rating` WHERE `id_user` = '$user[id]' AND `id_photo` = '$photo[id]'"), 0) == 0) {
	if (isset($_GET['rating']) && $_GET['rating'] > 0 && $_GET['rating'] < 7) {
		$c = dbresult(dbquery("SELECT COUNT(*) FROM `user_set` WHERE `id_user` = '$user[id]' AND `ocenka` > '$time'"), 0);
		if ($c == 0 && $_GET['rating'] == 6) {
			$_SESSION['message'] = '您需要激活服务';
			header("Location: /user/money/plus5.php");
			exit;
		}
		dbquery("INSERT INTO `gallery_rating` (`id_user`, `id_photo`, `like`, `time`, `avtor`) values('$user[id]', '$photo[id]', '" . intval($_GET['rating']) . "', '$time', $photo[id_user])", $db);
		dbquery("UPDATE `gallery_photo` SET `rating` = '" . ($photo['rating'] + intval($_GET['rating'])) . "' WHERE `id` = '$photo[id]' LIMIT 1", $db);
		$_SESSION['message'] = '你的积分被接受';
		header("Location: ?");
		exit;
	}
}
/*
==========================
Комментарий
==========================
*/
if (isset($_POST['msg']) && isset($user)) {
	$msg = $_POST['msg'];
	$mat = antimat($msg);
	if ($mat) $err[] = '在消息的文本中发现了一个非法字符: ' . $mat;
	if (strlen2($msg) > 1024) {
		$err = '信息太长了';
	} elseif (strlen2($msg) < 2) {
		$err = '短消息';
	} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `gallery_komm` WHERE `id_photo` = '$photo[id]' AND `id_user` = '$user[id]' AND `msg` = '" . my_esc($msg) . "' LIMIT 1"), 0) != 0) {
		$err = '您的消息重复前一个';
	} elseif (!isset($err)) {
		// Начисление баллов за активность
		include_once H . 'sys/add/user.active.php';
		/*
		==========================
		Уведомления об ответах
		==========================
		*/
		if (isset($ank_reply['id'])) {
			$notifiacation = dbassoc(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '" . $ank_reply['id'] . "' LIMIT 1"));
			if ($notifiacation['komm'] == 1 && $ank_reply['id'] != $user['id'])
				dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_reply[id]', '$photo[id]', 'photo_komm', '$time')");
		}
		/*
		====================================
		Обсуждения
		====================================
		*/
		// Отправляем друзьям
		$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $gallery['id_user'] . "' AND `i` = '1'");
		while ($f = dbarray($q)) {
			$a = user::get_user($f['frend']);
			$discSet = dbarray(dbquery("SELECT * FROM `discussions_set` WHERE `id_user` = '" . $a['id'] . "' LIMIT 1")); // Общая настройка обсуждений
			if ($f['disc_photo'] == 1 && $discSet['disc_photo'] == 1) {
				if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$a[id]' AND `type` = 'photo' AND `id_sim` = '$photo[id]' LIMIT 1"), 0) == 0) {
					if ($a['id'] != $user['id'] || $a['id'] != $photo['id_user'])
						dbquery("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$a[id]', '$gallery[id_user]', 'photo', '$time', '$photo[id]', '1')");
				} else {
					$disc = dbarray(dbquery("SELECT * FROM `discussions` WHERE `id_user` = '$a[id]' AND `type` = 'photo' AND `id_sim` = '$photo[id]' LIMIT 1"));
					if ($gallery['id_user'] != $user['id'] || $a['id'] != $photo['id_user'])
						dbquery("UPDATE `discussions` SET `count` = '" . ($disc['count'] + 1) . "', `time` = '$time' WHERE `id_user` = '$a[id]' AND `type` = 'photo' AND `id_sim` = '$photo[id]' LIMIT 1");
				}
			}
		}
		// Отправляем автору
		if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$gallery[id_user]' AND `type` = 'photo' AND `id_sim` = '$photo[id]' LIMIT 1"), 0) == 0) {
			if ($gallery['id_user'] != $user['id'])
				dbquery("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$gallery[id_user]', '$gallery[id_user]', 'photo', '$time', '$photo[id]', '1')");
		} else {
			$disc2 = dbarray(dbquery("SELECT * FROM `discussions` WHERE `id_user` = '$gallery[id_user]' AND `type` = 'photo' AND `id_sim` = '$photo[id]' LIMIT 1"));
			if ($gallery['id_user'] != $user['id'])
				dbquery("UPDATE `discussions` SET `count` = '" . ($disc2['count'] + 1) . "', `time` = '$time' WHERE `id_user` = '$gallery[id_user]' AND `type` = 'photo' AND `id_sim` = '$photo[id]' LIMIT 1");
		}
		dbquery("INSERT INTO `gallery_komm` (`id_photo`, `id_user`, `time`, `msg`) values('$photo[id]', '$user[id]', '$time', '" . my_esc($msg) . "')");
		$_SESSION['message'] = '消息已成功添加';
		header("Location: ?page=" . intval($_GET['page']));
		exit;
	}
}
if ((user_access('photo_komm_del') || $ank['id'] == $user['id']) && isset($_GET['delete']) && dbresult(dbquery("SELECT COUNT(*) FROM `gallery_komm` WHERE `id`='" . intval($_GET['delete']) . "' AND `id_photo`='$photo[id]' LIMIT 1"), 0) != 0) {
	dbquery("DELETE FROM `gallery_komm` WHERE `id`='" . intval($_GET['delete']) . "' LIMIT 1");
	admin_log('相册', '照片', "删除照片上的评论 [url=/user/info.php?id=$ank[id]]" . user::nick($ank['id'], 1, 0, 0) . "[/url]");
	$_SESSION['message'] = '评论成功删除';
	header("Location: ?page=" . intval($_GET['page']));
	exit;
}
$set['title'] = text($gallery['name']) . ' - ' . text($photo['name']); //网页标题
include_once '../sys/inc/thead.php';
title();
err();
aut();
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> ' . user::nick($ank['id'], 1, 0, 0) . ' | <a href="/photo/' . $ank['id'] . '/">相册</a> | ';
echo '<a href="/photo/' . $ank['id'] . '/' . $gallery['id'] . '/">' . text($gallery['name']) . '</a> | ';
echo '<b>' . text($photo['name']) . '</b>';
if ($photo['metka'] == 1) echo ' <font color=red>(18+)</font>';
echo '</div>';
// Подключаем приватность стр. 
include H . 'sys/add/user.privace.php';
/*
* Если установлена приватность альбома
*/
if ($gallery['privat'] == 1 && ($frend != 2 || !isset($user)) && $user['level'] <= $ank['level'] && $user['id'] != $ank['id']) {
	echo '<div class="mess">';
	echo '只有用户的朋友才能查看相册！';
	echo '</div>';
	$block_photo = true;
} elseif ($gallery['privat'] == 2 && $user['id'] != $ank['id'] && $user['level'] <= $ank['level']) {
	echo '<div class="mess">';
	echo '用户已禁止观看此相册！';
	echo '</div>';
	$block_photo = true;
}
/*--------------------Альбом под паролем-------------------*/
if ($user['id'] != $ank['id'] && $gallery['pass'] != NULL) {
	if (isset($_POST['password'])) {
		$_SESSION['pass'] = my_esc($_POST['password']);
		if ($_SESSION['pass'] != $gallery['pass']) {
			$_SESSION['message'] = '密码无效';
			$_SESSION['pass'] = NULL;
		}
		header("Location: ?");
	}
	if (!isset($_SESSION['pass']) || $_SESSION['pass'] != $gallery['pass']) {
		echo '<form action="?" method="POST">密码:<br /><input type="pass" name="password" value="" /><br />		
		<input type="submit" value="登录"/></form>';
		echo '<div class="foot">';
		echo '<img src="/style/icons/str2.gif" alt="*"> ' . user::nick($ank['id'], 1, 0, 0) . ' | <a href="/photo/' . $ank['id'] . '/">相册</a> | <b>' . text($gallery['name']) . '</b>';
		echo '</div>';
		include_once '../sys/inc/tfoot.php';
		exit;
	}
}
/*---------------------------------------------------------*/
if (!isset($block_photo)) {
	// +5 оценка 
	$rat = dbresult(dbquery("SELECT COUNT(*) FROM `gallery_rating` WHERE `id_photo` = $photo[id] AND `like` = '6'"), 0);
	if (($user['abuld'] == 1 || $photo['metka'] == 0 || $photo['id_user'] == $user['id'])) // Метка 18+ 
	{
		echo '<div class="nav2">';
		if ($webbrowser == 'web' && $w > 128) {
			echo "<a href='/photo/photo0/$photo[id].$photo[ras]' title='下载原文'><img style='max-width:90%' src='/photo/photo640/$photo[id].$photo[ras]'/></a>";
			if ($rat > 0) echo "<div style='display:inline;margin-left:-45px;vertical-align:top;'><img style='padding-top:15px;' src='/style/icons/5_plus.png'/></div>";
		} else {
			echo "<a href='/photo/photo0/$photo[id].$photo[ras]' title='下载原文'><img src='/photo/photo128/$photo[id].$photo[ras]'/></a>";
			if ($rat > 0) echo "<div style='display:inline;margin-left:-25px;vertical-align:top;'><img style='padding-top:10px;' src='/style/icons/6.png'/></div>";
		}
		echo '</div>';
		/*
		===============================
		Оценка фото
		===============================
		*/
		if (isset($user) && $user['id'] != $ank['id']) {
			echo '<div class="nav2">';
			if ($user['id'] != $ank['id'] &&  dbresult(dbquery("SELECT COUNT(*) FROM `gallery_rating` WHERE `id_user` = '$user[id]' AND `id_photo` = '$photo[id]'"), 0) == 0) {
				echo "<a href=\"?rating=6\" title=\"5+\"><img src='/style/icons/6.png' alt=''/></a>";
				echo "<a href=\"?rating=5\" title=\"5\"><img src='/style/icons/5.png' alt=''/></a>";
				echo "<a href=\"?rating=4\" title=\"4\"><img src='/style/icons/4.png' alt=''/></a>";
				echo "<a href=\"?rating=3\" title=\"3\"><img src='/style/icons/3.png' alt=''/></a>";
				echo "<a href=\"?rating=2\" title=\"2\"><img src='/style/icons/2.png' alt=''/></a>";
				echo "<a href=\"?rating=1\" title=\"1\"><img src='/style/icons/1.png' alt=''/></a>";
			} else {
				$rate = dbassoc(dbquery("SELECT * FROM `gallery_rating` WHERE `id_photo` = $photo[id] AND `id_user` = '$user[id]' LIMIT 1"));
				if (isset($user) && $user['id'] != $ank['id'])
					echo '你的评价 <img src="/style/icons/' . $rate['like'] . '.png" alt=""/></a>';
			}
			echo '</div>';
		}
	} elseif (!isset($user)) {
		echo '<div class="mess">';
		echo '<img src="/style/icons/small_adult.gif" alt="*"><br /> 此图像包含与性有关的内容/性行为的刻画/性器官的接触与接合等/使人联想起性行为的事物。只有年龄达到18岁以上的用户才能查看此类图像。 <br />';
		echo '<a href="/user/aut.php">登录</a> | <a href="/user/reg.php">注册</a>';
		echo '</div>';
	} else {
		echo '<div class="mess">';
		echo '<img src="/style/icons/small_adult.gif" alt="*"><br /> 
		此图像包含与性有关的内容/性行为的刻画/性器官的接触与接合等/使人联想起性行为的事物。只有年龄达到18岁以上的用户才能查看此类图像。 
		如果你的年龄达到18岁及以上，那么你可以 <a href="?sess_abuld=1">继续浏览</a>.';
		echo '</div>';
	}
	/*----------------------листинг-------------------*/
	$listr = dbassoc(dbquery("SELECT * FROM `gallery_photo` WHERE `id_gallery` = '$gallery[id]' AND `id` < '$photo[id]' ORDER BY `id` DESC LIMIT 1"));
	$list = dbassoc(dbquery("SELECT * FROM `gallery_photo` WHERE `id_gallery` = '$gallery[id]' AND `id` > '$photo[id]' ORDER BY `id`  ASC LIMIT 1"));
	echo '<div class="c2" style="text-align: center;">';
	if (isset($list['id']))	echo '<span class="page">' . ($list['id'] ? "<a href='/photo/$ank[id]/$gallery[id]/$list[id]/'>&laquo; 上一页</a>" : "&laquo; 上一页") . '</span>';
	$k_1 = dbresult(dbquery("SELECT COUNT(*) FROM `gallery_photo` WHERE `id` > '$photo[id]' AND `id_gallery` = '$gallery[id]'"), 0) + 1;
	$k_2 = dbresult(dbquery("SELECT COUNT(*) FROM `gallery_photo` WHERE `id_gallery` = '$gallery[id]'"), 0);
	echo ' (第' . $k_1 . '页 共' . $k_2 . '页) ';
	if (isset($listr['id']))	echo '<span class="page">' . ($listr['id'] ? "<a href='/photo/$ank[id]/$gallery[id]/$listr[id]/'>下一页 &raquo;</a>" : "下一页 &raquo;") . '</span>';
	echo '</div>';
	/*----------------------alex-borisi---------------*/
	if (($user['abuld'] == 1 || $photo['metka'] == 0 || $photo['id_user'] == $user['id'])) {
		if (isset($user)) {
			echo '<div class="nav1">';
			echo '<img src="/style/icons/fav.gif" alt="*" /> ';
			if (dbresult(dbquery("SELECT COUNT(*) FROM `bookmarks` WHERE `id_user` = '" . $user['id'] . "' AND `id_object` = '" . $photo['id'] . "' AND `type`='fot' LIMIT 1"), 0) == 0)
				echo '<a href="?fav=1&amp;page=' . $pageEnd . '">添加到书签</a><br />';
			else
				echo '<a href="?fav=0&amp;page=' . $pageEnd . '">从书签中删除</a><br />';
			echo '在书签中 (' . dbresult(dbquery("SELECT COUNT(*) FROM `bookmarks` WHERE `id_user` = '" . $user['id'] . "' AND `id_object` = '" . $photo['id'] . "' AND `type`='photo' LIMIT 1"), 0) . ') 用户.';
			echo '</div>';
		}
		echo '<div class="main">';
		echo '类型: <b>' . $photo['ras'] . '</b>, ' . $w . 'x' . $h . ' <br />';
		if ($photo['opis'] != null)
			echo output_text($photo['opis']) . '<br />';
		echo '<img src="/style/icons/d.gif" alt="*"> <a href="/photo/photo0/' . $photo['id'] . '.' . $photo['ras'] . '" title="下载原文">';
		echo '下载';
		echo ' (' . size_file(filesize(H . 'sys/gallery/photo/' . $photo['id'] . '.jpg')) . ')';
		echo '</a><br />';
		echo '</div>';
		if (user_access('photo_photo_edit') && $ank['level'] < $user['level'] || isset($user) && $ank['id'] == $user['id'])
			include_once check_replace('inc/gallery_show_photo_form.php');
	}
	$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `gallery_komm` WHERE `id_photo` = '$photo[id]'"), 0);
	$k_page = k_page($k_post, $set['p_str']);
	$page = page($k_page);
	$start = $set['p_str'] * $page - $set['p_str'];
	echo '<div class="foot">';
	echo '评论：';
	echo '</div>';
	if ($k_post == 0) {
		echo '<div class="mess">';
		echo '目前没有评论。';
		echo '</div>';
	} else {
		/*------------сортировка по времени--------------*/
		if (isset($user)) {
			echo '<div id="comments" class="menus">';
			echo '<div class="webmenu">';
			echo '<a href="?page=' . $page . '&amp;sort=1" class="' . ($user['sort'] == 1 ? 'activ' : null) . '">在下面</a>';
			echo '</div>';
			echo '<div class="webmenu">';
			echo '<a href="?page=' . $page . '&amp;sort=0" class="' . ($user['sort'] == 0 ? 'activ' : null) . '">在顶部</a>';
			echo '</div>';
			echo '</div>';
		}
		/*---------------alex-borisi---------------------*/
	}
	$q = dbquery("SELECT * FROM `gallery_komm` WHERE `id_photo` = '$photo[id]' ORDER BY `id` $sort LIMIT $start, $set[p_str]");
	while ($post = dbassoc($q)) {
		$ank2 = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$post[id_user]' LIMIT 1"));
		// Лесенка
		echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
		$num++;
		echo user::nick($ank2['id'], 1, 1, 0);
		if (isset($user) && $user['id'] != $ank2['id']) {
			echo ' <a href="?response=' . $ank2['id'] . '&amp;page=' . $page . '">[@]</a> ';
		}
		echo ' (' . vremja($post['time']) . ')<br />';
		$postBan = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE (`razdel` = 'all' OR `razdel` = 'photo') AND `post` = '1' AND `id_user` = '$ank2[id]' AND (`time` > '$time' OR `navsegda` = '1')"), 0);
		// Блок сообщения
		if ($postBan == 0) {
			echo output_text($post['msg']);
		} else {
			echo output_text($banMess) . '<br />';
		}
		if (isset($user)) {
			echo '<div class="right">';
			if (user_access('photo_komm_del') || $ank['id'] == $user['id'])
				echo '<a rel="delete" href="?delete=' . $post['id'] . '&amp;page=' . $page . '" title="删除注释"><img src="/style/icons/delete.gif" alt="*"></a>';
			echo '</div>';
		}
		echo '</div>';
	}
	if ($k_page > 1) str('?', $k_page, $page); // 输出页数
	if (isset($user)) {
		echo '<form method="post" name="message" action="?page=' . $pageEnd . '&amp;' . REPLY . '">';
		if (test_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
			include_once check_replace(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php');
		else
			echo $tPanel . '<textarea name="msg">' . $insert . '</textarea><br />';
		echo '<input value="发送" type="submit" />';
		echo '</form>';
	}
}
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> ' . user::nick($ank['id'], 1, 0, 0) . ' | <a href="/photo/' . $ank['id'] . '/">相册</a> | ';
echo '<a href="/photo/' . $ank['id'] . '/' . $gallery['id'] . '/">' . text($gallery['name']) . '</a> | ';
echo '<b>' . text($photo['name']) . '</b>';
if ($photo['metka'] == 1) echo ' <font color=red>(18+)</font>';
echo '</div>';
include_once '../sys/inc/tfoot.php';
exit;
