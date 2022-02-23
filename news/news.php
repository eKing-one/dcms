<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
// Если нет id шлем на главную
if (!isset($_GET['id']) && !is_numeric($_GET['id'])) {
	header("Location: index.php?" . SID);
	exit;
}
// Cуществование новости
if (dbresult(dbquery("SELECT COUNT(*) FROM `news` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1", $db), 0) == 0) {
	header("Location: index.php?" . SID);
	exit;
}
// Определение записи новости
$news = dbassoc(dbquery("SELECT * FROM `news` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"));
// Автор новости
$author = user::get_user($news['id_user']);
// Отмечаем уведомления
if (isset($user))
	dbquery("UPDATE `notification` SET `read` = '1' WHERE `type` = 'news_komm' AND `id_user` = '$user[id]' AND `id_object` = '$news[id]'");
/*------------------------Мне нравится------------------------*/
if (
	isset($user) && isset($_GET['like']) && ($_GET['like'] == 1 || $_GET['like'] == 0)
	&& dbresult(dbquery("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$news[id]' AND `type` = 'news' AND `id_user` = '$user[id]'"), 0) == 0
) {
	dbquery("INSERT INTO `like_object` (`id_user`, `id_object`, `type`, `like`) VALUES ('$user[id]', '$news[id]', 'news', '" . abs(intval($_GET['like'])) . "')");
	// Начисление баллов за активность
	include_once H . 'sys/add/user.active.php';
}
/*------------------------------------------------------------*/
// Комментарий 
if (isset($_POST['msg']) && isset($user)) {
	$msg = $_POST['msg'];
	$mat = antimat($msg);
	if ($mat) $err[] = '在消息的文本中发现了一个将死者: ' . $mat;
	if (strlen2($msg) > 1024) {
		$err = '信息太长了';
	} elseif (strlen2($msg) < 2) {
		$err = '短消息';
	} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `news_komm` WHERE `id_news` = '" . intval($_GET['id']) . "' AND `id_user` = '$user[id]' AND `msg` = '" . my_esc($msg) . "' LIMIT 1"), 0) != 0) {
		$err = 'Ваше сообщение повторяет предыдущее';
	} elseif (!isset($err)) {
		dbquery("INSERT INTO `news_komm` (`id_user`, `time`, `msg`, `id_news`) values('$user[id]', '$time', '" . my_esc($msg) . "', '" . intval($_GET['id']) . "')");
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
				dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_reply[id]', '$news[id]', 'news_komm', '$time')");
		}
		$_SESSION['message'] = '您的评论已被成功接受';
		header('Location: ?id=' . intval($_GET['id']) . '&page=' . intval($_GET['page']));
		exit;
	}
}
$set['title'] = '新闻 - ' . text($news['title']);
include_once '../sys/inc/thead.php';
title();
aut();
err();
// Название
echo '<div class="nav1" id="news_title">';
echo '<img src="/style/icons/news.png" alt="*" /> ' . text($news['title']);
echo '</div>';
// Текст новости
echo '<div class="nav2" id="news_content">';
echo output_text($news['msg']);
echo "</div>";
// Мне нравится и автор
echo '<div class="nav2" id="like">';
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$news[id]' AND `type` = 'news' AND `id_user` = '$user[id]'"), 0) == 0) {
	echo '[<img src="/style/icons/like.gif" alt="*"> <a href="?id=' . $news['id'] . '&amp;like=1">我喜欢</a>] ';
	echo '[<a href="?id=' . $news['id'] . '&amp;like=0"><img src="/style/icons/dlike.gif" alt="*"></a>]';
} else {
	echo '[<img src="/style/icons/like.gif" alt="*"> ' . dbresult(dbquery("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$news[id]' AND `type` = 'news' AND `like` = '1'"), 0) . '] ';
	echo '[<img src="/style/icons/dlike.gif" alt="*"> ' . dbresult(dbquery("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$news[id]' AND `type` = 'news' AND `like` = '0'"), 0) . ']';
}
echo '<br />';
// Автор 
echo '作者: '. user::nick($author['id'],1,1,0).'</div>';
// Кнопки соц сетей
echo '<div class="nav2" id="news_share">';
echo '分享:';
echo '</div>';
// Панелька управления
if (user_access('adm_news')) {
	echo '<div class="nav1" id="news_edit">';
	echo '[<img src="/style/icons/edit.gif" alt="*"> <a href="edit.php?id=' . $news['id'] . '">编辑</a>] ';
	echo '[<img src="/style/icons/delete.gif" alt="*"> <a href="delete.php?news_id=' . $news['id'] . '">删除</a>] ';
	echo '</div>';
}
/*----------------------листинг-------------------*/
$listr = dbassoc(dbquery("SELECT * FROM `news` WHERE `id` < '$news[id]' ORDER BY `id` DESC LIMIT 1"));
$list = dbassoc(dbquery("SELECT * FROM `news` WHERE `id` > '$news[id]' ORDER BY `id`  ASC LIMIT 1"));
echo '<div class="c2" style="text-align: center;">';
if (isset($list['id'])) echo '<span class="page">' . ($list['id'] ? '<a href="?id=' . $list['id'] . '">&laquo; 上一页</a> ' : '&laquo; 上一页 ') . '</span>';
$k_1 = dbresult(dbquery("SELECT COUNT(*) FROM `news` WHERE `id` > '$news[id]'"), 0) + 1;
$k_2 = dbresult(dbquery("SELECT COUNT(*) FROM `news`"), 0);
echo ' (第' . $k_1 . '页 共' . $k_2 . '页) ';
if (isset($list['id'])) echo '<span class="page">' . ($listr['id'] ? '<a href="?id=' . $listr['id'] . '">下一页 &raquo;</a>' : ' 下一页 &raquo;') . '</span>';
echo '</div>';
/*----------------------alex-borisi---------------*/
echo '<div class="foot" id="news_komm">';
echo '评论：';
echo '</div>';
// Колличество комментариев
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `news_komm` WHERE `id_news` = '" . intval($_GET['id']) . "' "), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
// Выборка постов
$q = dbquery("SELECT * FROM `news_komm` WHERE `id_news` = '" . intval($_GET['id']) . "' ORDER BY `id` $sort LIMIT $start, $set[p_str]");
echo '<table class="post">';
if ($k_post == 0) {
	echo '<div class="mess" id="no_object">';
	echo '没有留言';
	echo '</div>';
} else {
	/*------------сортировка по времени--------------*/
	if (isset($user)) {
		echo '<div id="comments" class="menus">';
		echo '<div class="webmenu">';
		echo '<a href="?id=' . $news['id'] . '&amp;page=' . $page . '&amp;sort=1" class="' . ($user['sort'] == 1 ? 'activ' : null) . '">在下面</a>';
		echo '</div>';
		echo '<div class="webmenu">';
		echo '<a href="?id=' . $news['id'] . '&amp;page=' . $page . '&amp;sort=0" class="' . ($user['sort'] == 0 ? 'activ' : null) . '">在顶部</a>';
		echo '</div>';
		echo '</div>';
	}
	/*---------------alex-borisi---------------------*/
}
while ($post = dbassoc($q)) {
	$ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
	// Лесенка
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;
	echo user::nick($ank['id'],1,1,0);
	if (isset($user) && $user['id'] != $ank['id']){
		echo ' <a href="?id=' . $news['id'] . '&amp;page=' . $page . '&amp;response=' . $ank['id'] . '">[*]</a> ';
	}
	echo '(' . vremja($post['time']) . ')<br />';
	echo output_text($post['msg']) . '<br />';
	if (isset($user)) {
		echo '<div class="right">';
		if (isset($user) && ($user['level'] > $ank['level'] || $user['level'] != 0 && $user['id'] == $ank['id']))
			echo '<a href="delete.php?id=' . $post['id'] . '"><img src="/style/icons/delete.gif" alt="*"></a>';
		echo '</div>';
	}
	echo '</div>';
}
echo '</table>';
// 输出页数
if ($k_page > 1) str("news.php?id=" . intval($_GET['id']) . '&amp;', $k_page, $page);
// Форма для комментариев
if (isset($user)) {
	echo '<form method="post" name="message" action="?id=' . intval($_GET['id']) . '&amp;page=' . $page . REPLY . '">';
	if (is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
		include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
	else
		echo $tPanel . '<textarea name="msg">' . $insert . '</textarea><br />';
	echo '<input value="发送" type="submit" />';
	echo '</form>';
}
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> <a href="index.php">新闻报道</a><br />';
echo '</div>';
include_once '../sys/inc/tfoot.php';
