<?
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
$width = ($webbrowser == 'web' ? '100' : '70'); // Размер подарков при выводе в браузер
/*
===============================
Полная очистка уведомлений
===============================
*/
if (isset($_GET['delete']) && $_GET['delete'] == 'all') {
	if (isset($user)) {
		dbquery("DELETE FROM `notification` WHERE `id_user` = '$user[id]'");
		$_SESSION['message'] = '清除通知';
		header("Location: ?");
		exit;
	}
}
if (isset($_GET['del'])) // удаление уведомления
{
	if (isset($user)) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `notification`  WHERE `id_user` = '$user[id]' AND `id` = '" . intval($_GET['del']) . "'"), 0) == 1) {
			dbquery("DELETE FROM `notification` WHERE `id_user` = '$user[id]' AND `id` = '" . intval($_GET['del']) . "' LIMIT 1");
			$_SESSION['message'] = '删除通知';
			header("Location: ?komm&" . intval($_GET['page']) . "");
			exit;
		}
	}
}
$set['title'] = '通知书';
include_once '../../sys/inc/thead.php';
title();
err();
aut();
/*
======
Панель
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
echo "<a href='/user/notification/' class='activ'>通知书 $k_notif</a>";
echo "</div>";
echo "</div>";
/*
==========
Список уведомлений
==========
*/
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `notification`  WHERE `id_user` = '$user[id]' "), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `notification` WHERE `id_user` = '$user[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");
if ($k_post == 0) //Если нет уведомлений, то...
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
	$type = $post['type']; //Тип уведомления
	$avtor = get_user($post['avtor']); //От кого уведомление
	if ($post['read'] == 0) //Если не прочитано
	{
		$s1 = "<font color='red'>";
		$s2 = "</font>";
	} else {
		$s1 = null;
		$s2 = null;
	}
	/*
===============================
Значение переменной $name для 
определенного типа сообщения
===============================
*/
	if ($type == 'ok_gift') // Принимаем подарок
	{
		$name = '已接受' . ($avtor['pol'] == 1 ? "" : "а") . ' 你的礼物 ';
	} elseif ($type == 'no_gift') // Отказ от подарка
	{
		$name = '被拒绝' . ($avtor['pol'] == 1 ? "" : "а") . ' 你的礼物 ';
	} elseif ($type == 'new_gift') // Подарки новые
	{
		$name = '有' . ($avtor['pol'] == 1 ? "" : "а") . ' 给你的礼物 ';
	} elseif ($type == 'files_komm' || $type == 'obmen_komm') // 文件
	{
		$name = '回答说' . ($avtor['pol'] == 1 ? "" : "а") . ' 在文件的注释中给你 ';
	} elseif ($type == 'news_komm') // Новости 
	{
		$name = '回答说' . ($avtor['pol'] == 1 ? "" : "а") . ' 在对新闻的评论中给你 ';
	} elseif ($type == 'status_komm') // Статусы
	{
		$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id` = '" . $post['id_object'] . "' LIMIT 1"));
		$name = '回答说' . ($avtor['pol'] == 1 ? "" : "а") . ' 给你在这个评论 ';
	} elseif ($type == 'foto_komm') // Фото 
	{
		$name = '回答说' . ($avtor['pol'] == 1 ? "" : "а") . ' 在照片的评论中给你 ';
	} elseif ($type == 'notes_komm') // Дневники
	{
		$name = '回答说' . ($avtor['pol'] == 1 ? "" : "а") . ' 在日记的评论中给你 ';
	} elseif ($type == 'them_komm') // форум
	{
		$name = '回答说' . ($avtor['pol'] == 1 ? "" : "а") . ' 你在主题 ';
	} elseif ($type == 'stena_komm') // Стена
	{
		$stena = get_user($post['id_object']);
		if ($stena['id'] == $user['id']) $sT = '你的';
		elseif ($stena['id'] == $avtor['id']) $sT = '我的';
		else {
			$sT = null;
		}
		$name = '回答说' . ($avtor['pol'] == 1 ? "" : "а") . ' 你在 ' . $sT;
	} elseif ($type == 'guest' || $type == 'adm_komm') // Гостевая, админ чат
	{
		$name = '回答说' . ($avtor['pol'] == 1 ? "" : "а") . ' 你在 ';
	} elseif ($type == 'del_frend') // Уведомления о удаленных друзьях
	{
		$name = ' 不幸的是我删除了它' . ($avtor['pol'] == 1 ? "" : "а") . ' 你来自朋友名单';
	} elseif ($type == 'no_frend') // Уведомления о отклоненных заявках в друзья
	{
		$name = ' 不幸的是我拒绝了' . ($avtor['pol'] == 1 ? "" : "а") . ' 在友谊中献给你';
	} elseif ($type == 'ok_frend') // Уведомления о принятых заявках в друзья
	{
		$name = ' 已成为' . ($avtor['pol'] == 1 ? "" : "а") . ' 你的朋友';
	} elseif ($type == 'otm_frend') // Уведомления о отмененных заявках в друзья
	{
		$name = ' 取消' . ($avtor['pol'] == 1 ? "" : "а") . ' 您的应用程序将您添加为好友';
	} elseif ($type == 'stena_komm2') {
		$name = ' 写道 ' . ($avtor['pol'] == 1 ? ' ' : 'a') . ' 在你 <a href="/user/komm.php?id=' . $post['id_object'] . '">在墙上的入口</a>';
	}
	/*
===============================
Подарки
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
			echo  group($avtor['id']) . " ";
			echo user::nick($avtor['id'], 1, 1, 1) . " " . $name;
			if ($type == 'new_gift') echo '<a href="/user/gift/gift.php?id=' . $id_gift['id'] . '"><img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:60px;" alt="*" /> ' . htmlspecialchars($gift['name']) . '</a>';
			else echo '<img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:60px;" alt="*" /> ' . htmlspecialchars($gift['name']);
			echo "  $s1 " . vremja($post['time']) . " $s2";
		}
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	/*
===============================
Друзья/Заявки
===============================
*/
	if ($type == 'no_frend' || $type == 'ok_frend' || $type == 'del_frend' || $type == 'otm_frend') {
		if ($avtor['id']) {
			echo user::avatar($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>";
			echo "  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
			echo "  $s1 " . vremja($post['time']) . " $s2";
		} else {
			echo " 这个朋友已经从网站上删除了=）  $s1 " . vremja($post['time']) . " $s2";
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
			echo user::avatar($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
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
	if ($type == 'files_komm' || $type == 'obmen_komm') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		$file = dbassoc(dbquery("SELECT * FROM `obmennik_files` WHERE `id` = '" . $post['id_object'] . "' LIMIT 1"));
		$dir = dbassoc(dbquery("SELECT * FROM `user_files` WHERE `id` = '" . $file['my_dir'] . "' LIMIT 1"));
		$ras = $file['ras'];
		if ($file['id'] && $avtor['id']) {
			echo user::avatar($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
			echo " <img src='/style/icons/d.gif' alt='*'> ";
			echo '<a href="/user/personalfiles/' . $file['id_user'] . '/' . $dir['id'] . '/?id_file=' . $file['id'] . '&amp;page=' . $pageEnd . '"><b>' . htmlspecialchars($file['name']) . '.' . $ras . '</b></a> ';
			echo "  $s1 " . vremja($post['time']) . " $s2";
		} else {
			echo " 这 " . (!$file['id'] ? "档案" : "用户") . " 已删除=(  $s1 " . vremja($post['time']) . " $s2";
		}
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	/*
===============================
Фото коментарии
===============================
*/
	if ($type == 'foto_komm') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		$foto = dbassoc(dbquery("SELECT * FROM `gallery_foto` WHERE `id` = '" . $post['id_object'] . "' LIMIT 1"));
		if ($foto['id']) {
			echo user::avatar($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
			echo " <img src='/style/icons/foto.png' alt='*'> ";
			echo " <a href='/foto/$foto[id_user]/$foto[id_gallery]/$foto[id]/?page=$pageEnd'>" . htmlspecialchars($foto['name']) . "</a> ";
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
			echo user::avatar($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
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
		echo user::avatar($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
		echo "<img src='/style/icons/stena.gif' alt='*'> <a href='/info.php?id=$stena[id]&amp;page=$pageEnd'>墙</a> " . ($sT == null ? "$stena[nick]" : "") . "  $s1 " . vremja($post['time']) . " $s2";
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	if ($type == 'stena_komm2') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		echo user::avatar($avtor['id']) . group($avtor['id']) . ' ';
		echo user::nick($avtor['id'], 1, 1, 1) . ' ' . $name . ' ';
		echo '' . $s1 . vremja($post['time']) . $s2 . ' ';
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	if ($type == 'stena') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		echo user::avatar($avtor['id']) . group($avtor['id']) . ' ';
		echo user::nick($avtor['id'], 1, 1, 1) . ' 写道' . ($avtor['pol'] == 0 ? 'a' : null) . ' 在你的墙上';
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
			$ankS = get_user($status['id_user']);
			echo user::avatar($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
			echo "<img src='/style/icons/comment.png' alt='*'> <a href='/user/status/komm.php?id=$status[id]&amp;page=$pageEnd'>状况</a>  $s1 " . vremja($post['time']) . " $s2";
		} else {
			echo '状态已被删除 =(';
		}
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	/*
===============================
Новости коментарии
===============================
*/
	if ($type == 'news_komm') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		$news = dbassoc(dbquery("SELECT * FROM `news` WHERE `id` = '" . $post['id_object'] . "' LIMIT 1"));
		echo user::avatar($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
		echo "<img src='/style/icons/news.png' alt='*'> <a href='/news/news.php?id=$news[id]&amp;page=$pageEnd'>" . htmlspecialchars($news['title']) . "</a>   $s1 " . vremja($post['time']) . " $s2";
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	/*
===============================
Гостевая коментарии
===============================
*/
	if ($type == 'guest') {
		if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
		if ($avtor['id']) {
			echo user::avatar($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
			echo "<img src='/style/icons/guest.png' alt='*'> <a href='/guest/?page=$pageEnd'>客人</a>  $s1 " . vremja($post['time']) . " $s2";
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
		echo user::avatar($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
		echo "<img src='/style/icons/chat.gif' alt='S' /> <a href='/plugins/admin/chat/?page=$pageEnd'>管理员聊天</a>  $s1 " . vremja($post['time']) . " $s2";
		echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	echo "</div>";
}
if ($k_page > 1) str('?', $k_page, $page); // 输出页数
echo '<div class="mess"><img src="/style/icons/delete.gif"> <a href="?delete=all">删除所有通知</a></div>';
echo "<div class=\"foot\">";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php?id=$user[id]'>$user[nick]</a> | ";
echo '<b>通知书</b> | <a href="settings.php">设置</a>';
echo "</div>";
include_once '../../sys/inc/tfoot.php';
