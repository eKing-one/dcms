<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
only_reg();
// Размер подарков при выводе в браузер
$width = ($webbrowser == 'web' ? '100' : '70');
// Подарок
$post = dbassoc(dbquery("SELECT id,status,coment,id_gift,id_ank,id_user,time FROM `gifts_user` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"));
// Если записи нет кидаем на главную
if (!$post['id']) {
	header("Location: /index.php?");
}
// Сам Подарок 
$gift = dbassoc(dbquery("SELECT id,name FROM `gift_list` WHERE `id` = '" . $post['id_gift'] . "' LIMIT 1"));
// Кому подарили
$ank = user::get_user($post['id_user']);
// Кто подарил
$anketa = user::get_user($post['id_ank']);
// Принятие подарка
if ($post['status'] == 0 && isset($_GET['ok']) && $user['id'] == $ank['id']) {
	dbquery("UPDATE `gifts_user` SET `status` = '1' WHERE `id` = '$post[id]' LIMIT 1");
	/*
		==========================
		Уведомления
		==========================
		*/
	dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$anketa[id]', '$gift[id]', 'ok_gift', '$time')");
	// Сообщение 
	$_SESSION['message'] = '来自 ' . $anketa['nick'] . ' 通过';
	header("Location: gift.php?id=$post[id]");
	exit;
}
// Отказ от подарка
if ($post['status'] == 0 && isset($_GET['no']) && $user['id'] == $ank['id']) {
	dbquery("DELETE FROM `gifts_user` WHERE `id` = '$post[id]' LIMIT 1");
	/*
		==========================
		Уведомления
		==========================
		*/
	dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$anketa[id]', '$gift[id]', 'no_gift', '$time')");
	$_SESSION['message'] = '来自 ' . $anketa['nick'] . ' 被拒绝';
	header("Location: ?new");
	exit;
}
// Удаление подарка
if (isset($_GET['delete']) && ($ank['id'] == $user['id']  || $user['level'] > 2)) {
	// Запрос удаления
	dbquery("DELETE FROM `gifts_user` WHERE `id` = '$post[id]' LIMIT 1");
	// Сообщение 
	$_SESSION['message'] = '来自 ' . $anketa['nick'] . ' 已删除';
	header("Location: index.php");
	exit;
}
//网页标题
$set['title'] = '给 ' . $ank['nick'] . ' 的礼物：' . htmlspecialchars($gift['name']);
include_once '../../sys/inc/thead.php';
title();
aut();
/*
==================================
Вывод подарка пользователя
==================================
*/
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" /> ' . user::nick($ank['id'], 1, 0, 0) . '</a> | <a href="/user/gift/index.php?id=' . $ank['id'] . '">礼物</a> | <b>' . htmlspecialchars($gift['name']) . '</b>';
echo '</div>';
// Подарок
echo '<div class="nav2">';
echo '<img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:' . $width . 'px;" alt="*" /><br />';
echo htmlspecialchars($gift['name']) . ' :: ' . vremja($post['time']) . '<br />';
echo '</div>';
// Автор подарка
echo '<div class="nav1">';
echo user::nick($anketa['id'],1,1,0);
if ($post['coment']) echo '评论: <br />' . output_text($post['coment']);
echo '</div>';
if ($ank['id'] == $user['id']) {
	echo '<div class="nav2">';
	if ($post['status'] == 0) {
		// Новый подарок - Действие
		echo '<center><img src="/style/icons/ok.gif" alt="*" /> <a href="?id=' . $post['id'] . '&amp;ok">接受</a> ';
		echo '<img src="/style/icons/delete.gif" alt="*" /> <a href="?id=' . $post['id'] . '&amp;no">拒绝</a></center>';
	} else {
		// Удаление 
		echo '<img src="/style/icons/delete.gif" alt="*" /> <a href="/user/gift/gift.php?id=' . $post['id'] . '&amp;delete">删除</a>';
	}
	echo '</div>';
}
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" /> ' . user::nick($ank['nick'],1,0,0) . '</a> | <a href="/user/gift/index.php?id=' . $ank['id'] . '">礼物</a> | <b>' . htmlspecialchars($gift['name']) . '</b>';
echo '</div>';
include_once '../../sys/inc/tfoot.php';
