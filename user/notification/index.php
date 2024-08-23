<?php
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/adm_check.php';
include_once '../../sys/inc/user.php';
only_reg();
$width = ($webbrowser == 'web' ? '100' : '70'); // 要在浏览器上显示的礼物大小
/*
===============================
删除全部通知
===============================

*/

//屏蔽 Notice 报错
error_reporting(E_ALL || ~E_NOTICE);


if (isset($_GET['delete']) && $_GET['delete'] == 'all') {
	
	if (isset($user)) {
		dbquery("DELETE FROM `notification` WHERE `id_user` = '$user[id]'");
		$_SESSION['message'] = '清除所有通知';
		header("Location: ?");
		exit;
	}
}
if (isset($_GET['del'])) // 删除通知
{
	if (isset($user)) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `notification`  WHERE `id_user` = '$user[id]' AND `id` = '" . intval($_GET['del']) . "'"), 0) == 1) {
			dbquery("DELETE FROM `notification` WHERE `id_user` = '$user[id]' AND `id` = '" . intval($_GET['del']) . "' LIMIT 1");
			$_SESSION['message'] = '清除所有通知';
			header("Location: ?komm&" . intval($_GET['page']) . "");
			exit;
		}
	}
}
$set['title'] = '我的通知';
include_once '../../sys/inc/thead.php';
title();
err();
aut();
/*
======
面板
======
*/
$k_notif = dbresult(dbquery("SELECT COUNT(`read`) FROM `notification` WHERE `id_user` = '$user[id]' AND `read` = '0'"), 0); // Уведомления
if ($k_notif > 0) $k_notif = '<font color=red>(' . $k_notif . ')</font>';
else $k_notif = null;
$discuss = dbresult(dbquery("SELECT COUNT(`count`) FROM `discussions` WHERE `id_user` = '$user[id]' AND `count` > '0' "), 0); // Обсуждения
if ($discuss > 0) $discuss = '<font color=red>(' . $discuss . ')</font>';
else $discuss = null;
$lenta = dbresult(dbquery("SELECT COUNT(`read`) FROM `tape` WHERE `id_user` = '$user[id]' AND `read` = '0' "), 0); // Лента
if ($lenta > 0) $lenta = '<font color=red>(' . $lenta . ')</font>';
else $lenta = null;
echo "<div id='comments' class='menus'>";
echo "<div class='webmenu'>";
echo "<a href='/user/tape/'>信息中心 $lenta</a>";
echo "</div>";
echo "<div class='webmenu'>";
echo "<a href='/user/discussions/' >讨论 $discuss</a>";
echo "</div>";
echo "<div class='webmenu'>";
echo "<a href='/user/notification/' class='activ'> 关于我的 $k_notif</a>";
echo "</div>";
echo "</div>";
/*
==========
通知列表
==========
*/
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `notification`  WHERE `id_user` = '$user[id]' "), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `notification` WHERE `id_user` = '$user[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");
if ($k_post == 0) //如果没有通知的话
{
	echo "  <div class='mess'>";
	echo "没有新通知";
	echo "  </div>";
}
//Если есть, то...
while ($post = dbassoc($q)) {
	/*-----------代码-----------*/
	if ($num == 0) {
		echo '<div class="nav1">';
		$num = 1;
	} elseif ($num == 1) {
		echo '<div class="nav2">';
		$num = 0;
	}
	/*---------------------------*/
	$type = $post['type']; //通知类型
	$avtor = user::get_user($post['avtor']); //谁的通知
	if ($post['read'] == 0) //未读时
	{
		$s1 = "<font color='red'>";
		$s2 = "</font>";
	} else {
		$s1 = null;
		$s2 = null;
	}
	/*
===============================
$name 变量值 
特定消息类型
===============================
*/
	if ($type == 'ok_gift') // 接受礼物
	{	
		$name = '接受了您的礼物';
	}
	elseif ($type == 'no_gift') // 拒绝礼物
	{	
		$name = '拒绝了您的礼物';
	}
	elseif ($type == 'new_gift') // 新礼物
	{	
		$name = '给您送上了新的礼物';
	}
	elseif ($type == 'files_komm' || $type == 'obmen_komm') // 文件评论
	{	
		$name = '在您的文件评论中回复了您';
	}

	elseif ($type == 'news_komm') // 新闻评论
	{	
		$name = '在您的新闻评论中回复了您';
	}
	elseif ($type == 'status_komm') // 状态评论
	{	
		$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id` = '".$post['id_object']."' LIMIT 1"));
		$name = '在这个状态的评论中回复了您';
	}
	elseif ($type == 'foto_komm') // 照片评论
	{	
		$name = '在您的照片评论中回复了您';
	}
	elseif ($type == 'notes_komm') // 日记评论
	{	
		$name = '在您的日记评论中回复了您';
	}
	elseif ($type == 'them_komm') // 论坛回复
	{	
		$name = '在您的论坛主题中回复了您';
	}
	elseif ($type == 'stena_komm') // 动态回复
	{	
		if ($stena['id'] = $user['id']) $sT = '您的';
		elseif ($stena['id'] = $avtor['id']) $sT = '他的/她的';
		else{ $sT = ['id']; }
		$name = '在'.$sT.'动态中回复了您';
	}
	elseif ($type == 'guest' || $type == 'adm_komm') // 访客留言、管理员聊天
	{	
		$name = '在您的'.$type.'中回复了您';
	}
	elseif ($type == 'del_frend') // 删除好友通知
	{	
		$name = '很遗憾，将您从好友列表中删除了';
	}
	elseif ($type == 'no_frend') // 拒绝好友请求通知
	{	
		$name = '很遗憾，拒绝了您的好友请求';
	}

	elseif ($type == 'ok_frend') // 同意好友请求通知
	{	
		$name = '成为了您的好友';
	}
	elseif ($type == 'otm_frend') // 取消好友请求通知
	{	
		$name = '取消了添加您为好友的请求';
	}elseif($type=='stena_komm2'){
		$name='在您的留言板中写了评论';
	}
	/*
===============================
送礼
===============================
*/
	if ($type == 'new_gift' || $type == 'no_gift' || $type == 'ok_gift') {
		if ($type == 'new_gift') {
			$id_gift =  dbassoc(dbquery("SELECT id,id_gift FROM `gifts_user` WHERE `id` = '$post[id_object]' LIMIT 1"));
			$gift =  dbassoc(dbquery("SELECT * FROM `gift_list` WHERE `id` = '$id_gift[id_gift]' LIMIT 1"));
		} else {
			$gift =  dbassoc(dbquery("SELECT * FROM `gift_list` WHERE `id` = '$post[id_object]' LIMIT 1"));
		}
		if ($avtor['id']) {
			echo user::nick($avtor['id'], 1, 0, 0) . " " . $name;
			if ($type == 'new_gift') echo '<a href="/user/gift/gift.php?id=' . $id_gift['id'] . '"><img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:60px;" alt="*" /> ' . htmlspecialchars($gift['name']) . '</a>';
			else echo '<img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:60px;" alt="*" /> ' . htmlspecialchars($gift['name']);
			echo "  $s1 " . vremja($post['time']) . " $s2";
		}
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	/*
===============================
朋友/申请书
===============================
*/
	if ($type == 'no_frend' || $type == 'ok_frend' || $type == 'del_frend' || $type == 'otm_frend') {
		if ($avtor['id']) {
			echo user::nick($avtor['id'], 1, 0, 0) . " $name ";
			echo "  $s1 " . vremja($post['time']) . " $s2";
		} else {
			echo "这位朋友已经从网站上删除 =) $s1 " . vremja($post['time']) . " $s2";
		}
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
		dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
	}
	/*
===============================
Дневники коментарии
===============================
*/
	if ($type == 'notes_komm') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		$notes = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` = '" . $post['id_object'] . "' LIMIT 1"));
		if ($notes['id']) {
			echo user::nick($avtor['id'], 1, 1, 0) . " $name ";
			echo " <img src='/style/icons/zametki.gif' alt='*'> ";
			echo '<a href="/plugins/notes/list.php?id=' . $notes['id'] . '&amp;page=' . $pageEnd . '"><b>' . htmlspecialchars($notes['name']) . '</b></a> ';
			echo "  $s1 " . vremja($post['time']) . " $s2";
		} else {
			echo " 这本日记已经被删除了=(  $s1 " . vremja($post['time']) . " $s2";
		}
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	/*
===============================
Файлы коментарии
===============================
*/
	if ($type == 'files_komm' || $type == 'down_komm') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		$file = dbassoc(dbquery("SELECT * FROM `downnik_files` WHERE `id` = '" . $post['id_object'] . "' LIMIT 1"));
		$dir = dbassoc(dbquery("SELECT * FROM `user_files` WHERE `id` = '" . $file['my_dir'] . "' LIMIT 1"));
		$ras = $file['ras'];
		if ($file['id'] && $avtor['id']) {
			echo user::nick($avtor['id'], 1, 1, 0) . " $name ";
			echo " <img src='/style/icons/d.gif' alt='*'> ";
			echo '<a href="/user/personalfiles/' . $file['id_user'] . '/' . $dir['id'] . '/?id_file=' . $file['id'] . '&amp;page=' . $pageEnd . '"><b>' . htmlspecialchars($file['name']) . '.' . $ras . '</b></a> ';
			echo "  $s1 " . vremja($post['time']) . " $s2";
		} else {
			echo "这个" . (!$file['id'] ? "文件" : "用户" ) . "已经被删除 =( $s1 " . vremja($post['time']) . " $s2";
		}
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	/*
===============================
Фото коментарии
===============================
*/
	if ($type == 'photo_komm') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		$photo = dbassoc(dbquery("SELECT * FROM `gallery_photo` WHERE `id` = '" . $post['id_object'] . "' LIMIT 1"));
		if ($photo['id']) {
			echo user::nick($avtor['id'], 1, 1, 0) . " $name ";
			echo " <img src='/style/icons/photo.png' alt='*'> ";
			echo " <a href='/photo/$photo[id_user]/$photo[id_gallery]/$photo[id]/?page=$pageEnd'>" . htmlspecialchars($photo['name']) . "</a> ";
			echo "  $s1 " . vremja($post['time']) . " $s2";
		} else {
			echo " 这张照片已经被删除了 =(  $s1 " . vremja($post['time']) . " $s2";
		}
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	/*
===============================
Форум коментарии
===============================
*/
	if ($type == 'them_komm') {
		$them = dbassoc(dbquery("SELECT * FROM `forum_t` WHERE `id` = '" . $post['id_object'] . "' LIMIT 1"));
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		if ($them['id']) {
			echo user::nick($avtor['id'], 1, 1, 0) . " $name ";
			echo "<img src='/style/themes/$set[set_them]/forum/14/them_$them[up]$them[close].png' alt='*' /> ";
			echo " <a href='/forum/$them[id_forum]/$them[id_razdel]/$them[id]/?page=$pageEnd'>" . htmlspecialchars($them['name']) . "</a>  $s1 " . vremja($post['time']) . " $s2";
		} else {
			echo " 此主题已被删除 =(  $s1 " . vremja($post['time']) . " $s2";
		}
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	/*
===============================
Стена юзера
===============================
*/
	if ($type == 'stena_komm') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		echo user::avatar($avtor['id']) .  user::nick($avtor['id'], 1, 1, 0) . " $name ";
		echo "<img src='/style/icons/stena.gif' alt='*'> <a href='/user/info.php?id=$stena[id]&amp;page=$pageEnd'>动态</a> " . ($sT == null ? "$stena[nick]" : "") . "  $s1 " . vremja($post['time']) . " $s2";
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	if ($type == 'stena_komm2') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		echo user::nick($avtor['id'], 1, 1, 0) . ' ' . $name . ' ';
		echo '' . $s1 . vremja($post['time']) . $s2 . ' ';
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	if ($type == 'stena') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		echo user::nick($avtor['id'], 1, 1, 0) . '在您的留言板上留言了' . ($avtor['pol'] == 0 ? 'a' : null);
		echo '' . $s1 . vremja($post['time']) . $s2 . ' ';
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	/*
===============================
Стасус коментарии
===============================
*/
	if ($type == 'status_komm') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		if ($status['id']) {
			$ankS = user::get_user($status['id_user']);
			echo user::nick($avtor['id'], 1, 1, 0) . " $name ";
			echo "<img src='/style/icons/comment.png' alt='*'> <a href='/user/status/komm.php?id=$status[id]&amp;page=$pageEnd'>状况</a>  $s1 " . vremja($post['time']) . " $s2";
		} else {
			echo '状态已被删除 =(';
		}
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	/*
===============================
新闻评论
===============================
*/
	if ($type == 'news_komm') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		$news = dbassoc(dbquery("SELECT * FROM `news` WHERE `id` = '" . $post['id_object'] . "' LIMIT 1"));
		echo user::nick($avtor['id'], 1, 1, 0) . " $name ";
		echo "<img src='/style/icons/news.png' alt='*'> <a href='/news/news.php?id=$news[id]&amp;page=$pageEnd'>" . htmlspecialchars($news['title']) . "</a>   $s1 " . vremja($post['time']) . " $s2";
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	/*
===============================
客人的评论
===============================
*/
	if ($type == 'guest') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		if ($avtor['id']) {
			echo user::nick($avtor['id'], 1, 1, 0) . " $name ";
			echo "<img src='/style/icons/guest.png' alt='*'> <a href='/guest/?page=$pageEnd'>留言板</a>  $s1 " . vremja($post['time']) . " $s2";
		} else {
			echo '此用户用户已被删除 =(';
		}
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	/*
===============================
Админ чат
===============================
*/
	if ($type == 'adm_komm') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		echo user::nick($avtor['id'], 1, 1, 0) . " $name ";
		echo "<img src='/style/icons/chat.gif' alt='S' /> <a href='/plugins/admin/chat/?page=$pageEnd'>管理员聊天</a>  $s1 " . vremja($post['time']) . " $s2";
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	echo "</div>";
}
if ($k_page > 1) str('?', $k_page, $page); // 输出页数
echo '<div class="mess"><img src="/style/icons/delete.gif"> <a href="?delete=all">删除所有通知</a></div>';
echo "<div class=\"foot\">";
echo "<img src='/style/icons/str2.gif' alt='*'> " . user::nick($user['id'], 1, 0, 0) . " | ";
echo '<b>系统通知</b> | <a href="settings.php">设置</a>';
echo "</div>";
include_once '../../sys/inc/tfoot.php';
