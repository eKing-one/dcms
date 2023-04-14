<?php
/*-----------------------状况表格-----------------------*/
if (isset($user) && isset($_GET['status'])) {
	if ($user['id'] == $ank['id']) {
		echo '<div class="main">状态[512个字符]</div>';
		echo '<form action="/user/info.php?id=' . $ank['id'] . '" method="post">';
		echo "$tPanel<textarea type=\"text\" style='' name=\"status\" value=\"\"/></textarea><br /> ";
		echo "<input class=\"submit\" style='' type=\"submit\" value=\"安装\" />";
		echo " <a href='/user/info.php?id=$ank[id]'>取消</a><br />";
		echo "</form>";
		include_once '../sys/inc/tfoot.php';
		exit;
	}
}
/*-----------------------------------------------------------*/
if ($ank['group_access'] > 1) echo "<div class='err'>$ank[group_name]</div>";
echo "<div class='nav1'>";
echo user::nick($ank['id'], 0, 1, 1);
if ((user_access('user_ban_set') || user_access('user_ban_set_h') || user_access('user_ban_unset')) && $ank['id'] != $user['id'])
	echo "<a href='/adm_panel/ban.php?id=$ank[id]'><font color=red>[禁止]</font></a>";
echo "</div>";
// Аватар
echo "<div class='nav2'>";
echo user::avatar($ank['id']);
echo "<br />";
if (isset($user) && isset($_GET['like']) && $user['id'] != $ank['id'] && dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]' AND `id_user` = '$user[id]' LIMIT 1"), 0) == 0) {
	dbquery("INSERT INTO `status_like` (`id_user`, `id_status`) values('$user[id]', '$status[id]')");
}
if (isset($user) || $user['id'] == $ank['id']) {

	if (isset($status['id'])) {
		if ($status['msg']!=null){
				echo "<div class='st_1'></div>";
				echo "<div class='st_2'>";
				echo output_text($status['msg']) . ' <font style="font-size:11px; color:gray;">' . vremja($status['time']) . '</font>';
				
				if ($ank['id'] == $user['id']) echo " [<a href='?id=$ank[id]&amp;status'><img src='/style/icons/edit.gif' alt='*'> 编辑</a>]";
				echo "</div>";
		}
		
	}
	
	// 如果已设置
	if (isset($status['id'])) {
		echo " <a href='/user/status/komm.php?id=$status[id]'><img src='/style/icons/bbl4.png' alt=''/> " . dbresult(dbquery("SELECT COUNT(*) FROM `status_komm` WHERE `id_status` = '$status[id]'"), 0) . " </a> ";
		$l = dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]'"), 0);
		if (isset($user) && $user['id'] != $ank['id'] && dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]' AND `id_user` = '$user[id]' LIMIT 1"), 0) == 0) {
			echo " <a href='/user/info.php?id=$ank[id]&amp;like'><img src='/style/icons/like.gif' alt='*'/> 点赞!</a> • ";
			$like = $l;
		} else if (isset($user) && $user['id'] != $ank['id']) {
			echo " <img src='/style/icons/like.gif' alt=''/>你和 ";
			$like = $l - 1;
		} else {
			echo " <img src='/style/icons/like.gif' alt=''/> ";
			$like = $l;
		}
		echo "<a href='/user/status/like.php?id=$status[id]'> $like 个用户</a>觉得很赞！";
	}
	/* Общее колличество статусов */
	$st = dbresult(dbquery("SELECT COUNT(*) FROM `status` WHERE `id_user` = '$ank[id]'"), 0);
	if ($st > 0) {
		echo "<br /> &rarr; <a href='/user/status/index.php?id=$ank[id]'>所有状态</a> (" . $st . ")";
	}
}
echo "</div>";
/*
========================================
Подарки
========================================
*/
$k_p = dbresult(dbquery("SELECT COUNT(id) FROM `gifts_user` WHERE `id_user` = '$ank[id]' AND `status` = '1'"), 0);
$width = ($webbrowser == 'web' ? '60' : '45'); // Размер подарков при выводе в браузер
if ($k_p > 0) {
	$q = dbquery("SELECT id,id_gift,status FROM `gifts_user` WHERE `id_user` = '$ank[id]' AND `status` = '1' ORDER BY `id` DESC LIMIT 5");
	echo '<div class="nav2">';
	while ($post = dbassoc($q)) {
		$gift = dbassoc(dbquery("SELECT id FROM `gift_list` WHERE `id` = '$post[id_gift]' LIMIT 1"));
		echo '<a href="/user/gift/gift.php?id=' . $post['id'] . '"><img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:' . $width . 'px;" alt="Подарок" /></a> ';
	}
	echo '</div>';
	echo '<div class="nav2">';
	echo '&rarr; <a href="/user/gift/index.php?id=' . $ank['id'] . '">所有礼品</a> (' . $k_p . ')';
	echo '</div>';
}
/*
========================================
Анкета
========================================
*/
echo "<div class='nav1'>";
echo "<img src='/style/icons/anketa.gif' alt='*' /> <a href='/user/info/anketa.php?id=$ank[id]'>个人资料</a> ";
if (isset($user) && $user['id'] == $ank['id']) {
	echo "[<img src='/style/icons/edit.gif' alt='*' /> <a href='/user/info/edit.php'>编辑</a>]";
}
echo "</div>";
/*
========================================
谁来看我？
========================================
*/
if (isset($user) && $user['id'] == $ank['id']) {
	echo '<div class="nav2">';
	$new_g = dbresult(dbquery("SELECT COUNT(*) FROM `my_guests` WHERE `id_ank` = '$user[id]' AND `read`='1'"), 0);
	echo '<img src="/style/icons/guests.gif" alt="*" /> ';
	if ($new_g != 0) {
		echo "<a href='/user/myguest/index.php'><font color='red'>谁来看我？ +$new_g</font></a> ";
	} else {
		echo "<a href='/user/myguest/index.php'>谁来看我？</a> ";
	}
	echo ' | ';
	$ocenky = dbresult(dbquery("SELECT COUNT(*) FROM `gallery_rating` WHERE `avtor` = '$ank[id]'  AND `read`='1'"), 0);
	if ($ocenky != 0) {
		echo "<a href='/user/info/ocenky.php'><font color='red'>评价 +$ocenky</font></a> ";
	} else {
		echo "<a href='/user/info/ocenky.php'>评价</a> ";
	}
	echo "</div>";
}
/*
========================================
朋友
========================================
*/
$k_f = dbresult(dbquery("SELECT COUNT(id) FROM `frends_new` WHERE `to` = '$ank[id]' LIMIT 1"), 0);
$k_fr = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE `user` = '$ank[id]' AND `i` = '1'"), 0);
$res = dbquery("select `frend` from `frends` WHERE `user` = '$ank[id]' AND `i` = '1'");
echo '<div class="nav2">';
echo '<img src="/style/icons/druzya.png" alt="*" /> ';
echo '<a href="/user/frends/?id=' . $ank['id'] . '">朋友</a> (' . $k_fr . '</b>/';
$i = 0;
while ($k_fr = dbarray($res)) {
	if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$k_fr[frend]' && `date_last` > '" . (time() - 600) . "'"), 0) != 0)
		$i++;
}
echo "<span style='color:green'><a href='/user/frends/online.php?id=" . $ank['id'] . "'>$i</a></span>)";
if ($k_f > 0 && $ank['id'] == $user['id']) echo " <a href='/user/frends/new.php'><font color='red'>+$k_f</font></a>";
echo "</div>";
if (isset($user) && $user['id'] == $ank['id']) {
	echo "<div class='nav2'>";
	/*
========================================
Уведомления
========================================
*/
	if (isset($user) && $user['id'] == $ank['id']) {
		$k_notif = dbresult(dbquery("SELECT COUNT(`read`) FROM `notification` WHERE `id_user` = '$user[id]' AND `read` = '0'"), 0); // Уведомления
		if ($k_notif > 0) {
			echo "<img src='/style/icons/notif.png' alt='*' /> ";
			echo "<a href='/user/notification/index.php'><font color='red'>通知书</font></a> ";
			echo "<font color=\"red\">+$k_notif</font> ";
			echo "<br />";
		}
	}
	/*
========================================
Обсуждения
========================================
*/
	if (isset($user) && $user['id'] == $ank['id']) {
		echo "<img src='/style/icons/chat.gif' alt='*' /> ";
		$new_g = dbresult(dbquery("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$user[id]' AND `count` > '0'"), 0);
		if ($new_g != 0) {
			echo "<a href='/user/discussions/index.php'><font color='red'>讨论情况</font></a> ";
			echo "<font color=\"red\">+$new_g</font> ";
		} else {
			echo "<a href='/user/discussions/index.php'>讨论情况</a> ";
		}
		echo "<br />";
	}
	/*
========================================
Лента
========================================
*/
	if ($user['id'] == $ank['id']) {
		$k_l = dbresult(dbquery("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$user[id]'  AND  `read` = '0'"), 0);
		if ($k_l != 0) {
			$color = "<font color='red'>";
			$color2 = "</font>";
		} else {
			$color = null;
			$color2 = null;
		}
		echo "<img src='/style/icons/lenta.gif' alt='*' /> <a href='/user/tape/'>" . $color . "信息中心" . $color2 . "</a> ";
		if ($k_l != 0) echo "<font color=\"red\">+$k_l</font>";
		echo "<br />";
	}
	echo "</div>";
}
echo "<div class='nav1'>";
/*
========================================
Фото
========================================
*/
echo "<img src='/style/icons/photo.png' alt='*' /> ";
echo "<a href='/photo/$ank[id]/'>照片</a> ";
echo "(" . dbresult(dbquery("SELECT COUNT(*) FROM `gallery_photo` WHERE `id_user` = '$ank[id]'"), 0) . ")<br />";
/*
========================================
档案
========================================
*/
if (dbresult(dbquery("SELECT COUNT(*) FROM `user_files` WHERE `id_user` = '$ank[id]' AND `osn` = '1'"), 0) == 0) {
	dbquery("INSERT INTO `user_files` (`id_user`, `name`,  `osn`) values('$ank[id]', '档案', '1')");
}
$dir_osn = dbassoc(dbquery("SELECT * FROM `user_files` WHERE `id_user` = '$ank[id]' AND `osn` = '1' LIMIT 1"));
echo "<img src='/style/icons/files.gif' alt='*' /> ";
if (isset($dir_osn['id'])) echo "<a href='/user/personalfiles/$ank[id]/$dir_osn[id]/'>档案</a> ";
echo "(" . dbresult(dbquery("SELECT COUNT(*) FROM `user_files` WHERE `id_user` = '$ank[id]' AND `osn` > '1'"), 0) . "/" . dbresult(dbquery("SELECT COUNT(*) FROM `downnik_files` WHERE `id_user` = '$ank[id]'"), 0) . ")<br />";
/*
========================================
Музыка
========================================
*/
$k_music = dbresult(dbquery("SELECT COUNT(*) FROM `user_music` WHERE `id_user` = '$ank[id]'"), 0);
echo "<img src='/style/icons/play.png' alt='*' width='16'/> ";
echo "<a href='/user/music/index.php?id=$ank[id]'>音乐</a> ";
echo "(" . $k_music . ")";
echo "</div>";
/*
========================================
Темы и комментарии
========================================
*/
echo "<div class='nav2'><img src='/style/icons/blogi.png' alt='*' width='16'/> ";
echo "<a href='/user/info/them_p.php?id=" . $ank['id'] . "'>帖子与评论</a> ";
echo "</div>";
/*
========================================
Дневники
========================================
*/
echo "<div class='nav2'>";
$kol_dnev = dbresult(dbquery("SELECT COUNT(*) FROM `notes` WHERE `id_user` = '" . $ank['id'] . "'"), 0);
echo "<img src='/style/icons/zametki.gif' alt='*' /> ";
echo "<a href='/plugins/notes/user.php?id=$ank[id]'>日记</a> ($kol_dnev)<br />";
/*
========================================
Закладки
========================================
*/
$zakladki = dbresult(dbquery("SELECT COUNT(`id`)FROM `bookmarks` WHERE `id_user`='" . $ank['id'] . "'"), 0);;
echo "<img src='/style/icons/fav.gif' alt='*' /> ";
echo "<a href='/user/bookmark/index.php?id=$ank[id]'>书签</a> ($zakladki)<br />";
/*
========================================
Отзывы
========================================
*/
echo "<img src='/style/my_menu/who_rating.png' alt='*' /> <a href='/user/info/who_rating.php?id=$ank[id]'>评价</a>
 (" . dbresult(dbquery("SELECT COUNT(*) FROM `user_voice2` WHERE `id_kont` = '" . $ank['id'] . "'"), 0) . ")<br />";
echo "</div>";
/*
========================================
Сообщение
========================================
*/
if (isset($user) && $ank['id'] != $user['id']) {
	echo "<div class='nav1'>";
	echo " <a href=\"/user/mail.php?id=$ank[id]\"><img src='/style/icons/pochta.gif' alt='*' /> 信息</a><br />";/*
========================================
В друзья
========================================
*/
	if ($frend_new == 0 && $frend == 0) {
		echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?add=" . $ank['id'] . "'>添加到朋友</a><br />";
	} elseif ($frend_new == 1) {
		echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?otm=$ank[id]'>拒绝申请</a><br />";
	} elseif ($frend == 2) {
		echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?del=$ank[id]'>从朋友中删除</a><br />";
	}
	/*
========================================
В закладки
========================================
*/
	echo '<img src="/style/icons/fav.gif" alt="*" /> ';
	if (dbresult(dbquery("SELECT COUNT(*) FROM `mark_people` WHERE `id_user` = '" . $user['id'] . "' AND `id_people` = '" . $ank['id'] . "' LIMIT 1"), 0) == 0)
		echo '<a href="?id=' . $ank['id'] . '&amp;fav=1">书签</a><br />';
	else
		echo '<a href="?id=' . $ank['id'] . '&amp;fav=0">从书签中删除</a><br />';
	echo "</div>";
	echo "<div class='nav2'>";
	/*
========================================
Монеты перевод
========================================
*/
	echo "<img src='/style/icons/uslugi.gif' alt='*' /> <a href=\"/user/money/translate.php?id=$ank[id]\">赠送$sMonet[0]</a><br />";
	/*
========================================
Сделать подарок
========================================
*/
	echo "<img src='/style/icons/present.gif' alt='*' /> <a href=\"/user/gift/categories.php?id=$ank[id]\">送礼物</a><br />";
	echo "</div>";
}
/*
========================================
Настройки
========================================
*/
if (isset($user) && $ank['id'] == $user['id']) {
	echo "<div class='main'>";
	echo "<img src='/style/icons/uslugi.gif' alt='*' /> <a href=\"/user/money/index.php\">额外服务</a><br /> ";
	echo "<img src='/style/icons/settings.png' alt='*' /> <a href=\"/user/info/settings.php\">我的设置</a> | <a href=\"/user/my_aut.php\">菜单</a>";
	echo "</div>";
}
/*
========================================
Стена
========================================
*/
echo "<div class='foot'>";
echo "<img src='/style/icons/stena.gif' alt='*' /> ";
if (isset($user) && $user['wall'] == 0)
	echo "<a href='/user/info.php?id=$ank[id]&amp;wall=1'>动态</a>";
elseif (isset($user))
	echo "<a href='/user/info.php?id=$ank[id]&amp;wall=0'>动态</a>";
else
	echo "动态";
echo "</div>";
if ($user['wall'] == 0) {
	include_once H . 'user/stena/index.php';
}
/*
========================================
The End
========================================
*/
