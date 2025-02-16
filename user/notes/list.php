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
/**
 * 别看名字是 list，其实也有日记举报页。
 * 举报页修改注解参见 ../user/info.php 被注释掉的大段代码。
**/
/* Бан пользователя */
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0')"), 0) != 0) {
	header('Location: /user/ban.php?' . session_id());
	exit;
}
$notes = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"));
$avtor = user::get_user($notes['id_user']);
if (isset($user))
	$count = dbresult(dbquery("SELECT COUNT(*) FROM `notes_count` WHERE `id_user` = '" . $user['id'] . "' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0);
// Закладки
$markinfo = dbresult(dbquery("SELECT COUNT(*) FROM `mark_notes` WHERE `id_list` = '" . $notes['id'] . "'"), 0);
if (isset($user))
	dbquery("UPDATE `notification` SET `read` = '1' WHERE `type` = 'notes_komm' AND `id_user` = '$user[id]' AND `id_object` = '$notes[id]'");
/*
================================
Модуль жалобы на пользователя
и его сообщение либо контент
в зависимости от раздела
================================
*/
if (isset($_GET['spam'])  &&  isset($user)) {
	$mess = dbassoc(dbquery("SELECT * FROM `notes_komm` WHERE `id` = '" . intval($_GET['spam']) . "' limit 1"));
	$spamer = user::get_user($mess['id_user']);
	if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'notes_komm' AND `spam` = '" . $mess['msg'] . "'"), 0) == 0) {
		if (isset($_POST['msg'])) {
			if ($mess['id_user'] != $user['id']) {
				$msg = my_esc($_POST['msg']);
				if (strlen2($msg) < 3) $err = '更详细地说明投诉的原因';
				if (strlen2($msg) > 1512) $err = '文本的长度超过512个字符的限制';
				if (isset($_POST['types'])) $types = intval($_POST['types']);
				else $types = '0';
				if (!isset($err)) {
					dbquery("INSERT INTO `spamus` (`id_object`, `id_user`, `msg`, `id_spam`, `time`, `types`, `razdel`, `spam`) values('$notes[id]', '$user[id]', '$msg', '$spamer[id]', '$time', '$types', 'notes_komm', '" . my_esc($mess['msg']) . "')");
					$_SESSION['message'] = '考虑申请已发出';
					header("Location: ?id=$notes[id]&page=" . intval($_GET['page']) . "&spam=$mess[id]");
					exit;
				}
			}
		}
	}
	$set['title'] = '日记 ' . htmlspecialchars($notes['name']) . '';
	include_once '../../sys/inc/thead.php';
	title();
	aut();
	err();
	if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'notes_komm'"), 0) == 0) {
		echo "<div class='mess'>虚假信息会导致昵称被屏蔽。 
如果你经常被一个写各种讨厌的东西的人惹恼，你可以把他加入黑名单。</div>";
		echo "<form class='nav1' method='post' action='?id=$notes[id]&amp;page=" . intval($_GET['page']) . "&amp;spam=$mess[id]'>";
		echo "<b>用户:</b> ";
		echo " " . user::avatar($spamer['id']) . "  " . user::nick($spamer['id'], 1, 1, 0) . " (" . vremja($mess['time']) . ")<br />";
		echo "<b>违规：</b> <font color='green'>" . output_text($mess['msg']) . "</font><br />";
		echo "原因：<br /><select name='types'>";
		echo "<option value='1' selected='selected'>垃圾邮件/广告</option>";
		echo "<option value='2' selected='selected'>欺诈行为</option>";
		echo "<option value='3' selected='selected'>引战</option>";
		echo "<option value='4' selected='selected'>网络暴力</option>";
		echo "<option value='0' selected='selected'>其他</option>";
		echo "</select><br />";
		echo "附加解释：$tPanel";
		echo "<textarea name=\"msg\"></textarea><br />";
		echo "<input value=\"提交投诉\" type=\"submit\" />";
		echo "</form>";
	} else {
		echo "<div class='mess'>有关 <font color='green'>$spamer[nick]</font> 的投诉管理团队将尽快处理，请耐心等待。</div>";
	}
	echo "<div class='foot'>";
	echo "<img src='/style/icons/str2.gif' alt='*'> <a href='?id=$notes[id]&amp;page=" . intval($_GET['page']) . "'>返回</a><br />";
	echo "</div>";
	include_once '../../sys/inc/tfoot.php';
	exit;
}
/*
==================================
The End
==================================
*/
// Запись просмотра
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `notes_count` WHERE `id_user` = '" . $user['id'] . "' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0) == 0) {
	dbquery("INSERT INTO `notes_count` (`id_notes`, `id_user`) VALUES ('$notes[id]', '$user[id]')");
	dbquery("UPDATE `notes` SET `count` = '" . ($notes['count'] + 1) . "' WHERE `id` = '$notes[id]' LIMIT 1");
}
/*------------очищаем счетчик этого обсуждения-------------*/
if (isset($user)) {
	dbquery("UPDATE `discussions` SET `count` = '0' WHERE `id_user` = '$user[id]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1");
}
/*---------------------------------------------------------*/
$set['title'] = '日记 - ' . htmlspecialchars($notes['name']) . '';
$set['meta_description'] = htmlspecialchars($notes['msg']);
include_once '../../sys/inc/thead.php';
if (isset($_POST['msg']) && isset($user)) {
	$msg = $_POST['msg'];
	if (strlen2($msg) > 1024) {
		$err = '信息长于 1024 字节。试着压缩一下？';
	} elseif (strlen2($msg) < 2) {
		$err = '信息短于 2 字节。试着扩充一下？';
	} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `notes_komm` WHERE `id_notes` = '" . intval($_GET['id']) . "' AND `id_user` = '$user[id]' AND `msg` = '" . my_esc($msg) . "' LIMIT 1"), 0) != 0) {
		$err = '你的留言重复了上一条';
	} elseif (!isset($err)) {
		/*
		==========================
		Уведомления об ответах
		==========================
		*/
		if (isset($user) && $respons == TRUE) {
			$notifiacation = dbassoc(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '" . $ank_otv['id'] . "' LIMIT 1"));
			if ($notifiacation['komm'] == 1 && $ank_otv['id'] != $user['id'])
				dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_otv[id]', '$notes[id]', 'notes_komm', '$time')");
		}
		/*
====================================
Обсуждения
====================================
*/
		$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $notes['id_user'] . "' AND `i` = '1'");
		while ($f = dbarray($q)) {
			$a = user::get_user($f['frend']);
			$discSet = dbarray(dbquery("SELECT * FROM `discussions_set` WHERE `id_user` = '" . $a['id'] . "' LIMIT 1")); // Общая настройка обсуждений
			if ($f['disc_notes'] == 1 && $discSet['disc_notes'] == 1) /* Фильтр рассылки */ {
				//---------друзьям автора--------------//
				if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$a[id]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1"), 0) == 0) {
					if ($notes['id_user'] != $a['id']  || $a['id'] != $user['id'])
						dbquery("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$a[id]', '$notes[id_user]', 'notes', '$time', '$notes[id]', '1')");
				} else {
					$disc = dbarray(dbquery("SELECT * FROM `discussions` WHERE `id_user` = '$a[id]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1"));
					if ($notes['id_user'] != $a['id'] || $a['id'] != $user['id'])
						dbquery("UPDATE `discussions` SET `count` = '" . ($disc['count'] + 1) . "', `time` = '$time' WHERE `id_user` = '$a[id]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1");
				}
				//-------------------------------------//
			}
		}
		//-------------отправляем автору------------//
		if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$notes[id_user]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1"), 0) == 0) {
			if ($notes['id_user'] != $user['id'])
				dbquery("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$notes[id_user]', '$notes[id_user]', 'notes', '$time', '$notes[id]', '1')");
		} else {
			$disc = dbarray(dbquery("SELECT * FROM `discussions` WHERE `id_user` = '$notes[id_user]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1"));
			if ($notes['id_user'] != $user['id'])
				dbquery("UPDATE `discussions` SET `count` = '" . ($disc['count'] + 1) . "', `time` = '$time' WHERE `id_user` = '$notes[id_user]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1");
		}
		dbquery("INSERT INTO `notes_komm` (`id_user`, `time`, `msg`, `id_notes`) values('$user[id]', '$time', '" . my_esc($msg) . "', '" . intval($_GET['id']) . "')");
		dbquery("UPDATE `user` SET `balls` = '" . ($user['balls'] + 1) . "' WHERE `id` = '$user[id]' LIMIT 1");
		$_SESSION['message'] = '信息发送成功';
		header("Location: list.php?id=$notes[id]&page=" . intval($_GET['page']) . "");
		exit;
	}
}
if (isset($user))
	$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$avtor[id]') OR (`user` = '$avtor[id]' AND `frend` = '$user[id]') LIMIT 1"), 0);
title();
aut(); // форма авторизации
err();
if ($notes['private'] == 1 && $user['id'] != $avtor['id'] && $frend != 2  && !user_access('notes_delete')) {
	msg('根据用户的隐私设置，这篇日记只允许用户的朋友查看。');
	echo "  <div class='foot'>";
	echo "<a href='index.php'>返回</a><br />";
	echo "   </div>";
	include_once '../../sys/inc/tfoot.php';
	exit;
}
if ($notes['private'] == 2 && $user['id'] != $avtor['id']  && !user_access('notes_delete')) {
	msg('根据用户的隐私设置，已禁止查看这篇日记。');
	echo "  <div class='foot'>";
	echo "<a href='index.php'>返回</a><br />";
	echo "   </div>";
	include_once '../../sys/inc/tfoot.php';
	exit;
}
if (isset($_GET['delete']) && ($user['id'] == $avtor['id'] || user_access('notes_delete'))) {
	echo "<center>";
	echo "你真的想删除日记吗 " . output_text($notes['name']) . "?<br />";
	echo "[<a href='delete.php?id=$notes[id]'><img src='/style/icons/ok.gif'> 删除</a>] [<a href='list.php?id=$notes[id]'><img src='/style/icons/delete.gif'> 取消</a>] ";
	echo "</center>";
	include_once '../../sys/inc/tfoot.php';
}
if (isset($user)) {
	//------------------------like-----------------//
	if (isset($_GET['like']) && $_GET['like'] == 1) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `id_user` = '" . $user['id'] . "' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0) == 0) {
			dbquery("INSERT INTO `notes_like` (`id_notes`, `id_user`, `like`) VALUES ('$notes[id]', '$user[id]', '1')");
			dbquery("UPDATE `notes` SET `count` = '" . ($notes['count'] + 1) . "' WHERE `id` = '$notes[id]' LIMIT 1");
			$_SESSION['message'] = '你的表态已经计入统计';
			header("Location: list.php?id=$notes[id]&page=" . intval($_GET['page']) . "");
			exit;
		}
	}
	//-----------------------------------------------//
	//------------------------dlike-----------------//
	if (isset($_GET['like']) && $_GET['like'] == 0) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `id_user` = '" . $user['id'] . "' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0) == 0) {
			dbquery("INSERT INTO `notes_like` (`id_notes`, `id_user`, `like`) VALUES ('$notes[id]', '$user[id]', '0')");
			dbquery("UPDATE `notes` SET `count` = '" . ($notes['count'] - 1) . "' WHERE `id` = '$notes[id]' LIMIT 1");
			$_SESSION['message'] = '你的表态已经计入统计';
			header("Location: list.php?id=$notes[id]&page=" . intval($_GET['page']) . "");
			exit;
		}
	}
	//-----------------------------------------------//
	//-----------------------добавляем в закладки------------//
	if (isset($_GET['fav']) && $_GET['fav'] == 1) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `mark_notes` WHERE `id_user` = '" . $user['id'] . "' AND `id_list` = '" . $notes['id'] . "' LIMIT 1"), 0) == 0) {
			dbquery("INSERT INTO `mark_notes` (`id_list`, `id_user`, `time`) VALUES ('$notes[id]', '$user[id]', '$time')");
			$_SESSION['message'] = '日记已添加到书签';
			header("Location: list.php?id=$notes[id]&page=" . intval($_GET['page']) . "");
			exit;
		}
	}
	//-------------------------------------------------------//
	//-----------------------удаляем из закладок------------//
	if (isset($_GET['fav']) && $_GET['fav'] == 0) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `mark_notes` WHERE `id_user` = '" . $user['id'] . "' AND `id_list` = '" . $notes['id'] . "' LIMIT 1"), 0) == 1) {
			dbquery("DELETE FROM `mark_notes` WHERE `id_user` = '$user[id]' AND  `id_list` = '$notes[id]' ");
			$_SESSION['message'] = '日记已从书签中删除';
			header("Location: list.php?id=$notes[id]&page=" . intval($_GET['page']) . "");
			exit;
		}
	}
	//-------------------------------------------------------//
}
echo "<div class=\"foot\">";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='index.php'>日记</a> | " . user::nick($avtor['id'], 1, 0, 0);
echo ' | <b>' . output_text($notes['name']) . '</b>';
echo "</div>";
echo "<div class=\"main\">";
echo "创建时间: (" . vremja($notes['time']) . ")";
echo "</div>";
$stat1 = $notes['msg'];
if (!$set['web']) $mn = 10;
else $mn = 30; // количество слов выводится в зависимости от браузера
$stat = explode(' ', $stat1); // деление статьи на отдельные слова
$k_page = k_page(count($stat), $set['p_str'] * $mn);
$page = page($k_page);
$start = $set['p_str'] * $mn * ($page - 1);
$stat_1 = NULL;
for ($i = $start; $i < $set['p_str'] * $mn * $page && $i < count($stat); $i++) {
	$stat_1 .= $stat[$i] . ' ';
}
echo output_text($stat_1), '<br />'; // вывод статьи со всем форматированием
if ($k_page > 1) str("?id=$notes[id]&amp;", $k_page, $page); // 输出页数
/*----------------------листинг-------------------*/
$listr = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` < '$notes[id]' ORDER BY `id` DESC LIMIT 1"));
$list = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` > '$notes[id]' ORDER BY `id`  ASC LIMIT 1"));
echo '<div class="c2" style="text-align: center;">';
echo '<span class="page">' . ($list['id'] ? '<a href="list.php?id=' . $list['id'] . '">&laquo; 上一页</a> ' : '&laquo; 上一页 ') . '</span>';
$k_1 = dbresult(dbquery("SELECT COUNT(*) FROM `notes` WHERE `id` > '$notes[id]'"), 0) + 1;
$k_2 = dbresult(dbquery("SELECT COUNT(*) FROM `notes`"), 0);
echo ' (第' . $k_1 . '页 共' . $k_2 . '页) ';
echo '<span class="page">' . ($listr['id'] ? '<a href="list.php?id=' . $listr['id'] . '">下一页 &raquo;</a>' : ' 下一页 &raquo;') . '</span>';
echo '</div>';
/*----------------------alex-borisi---------------*/
if (isset($user) && (user_access('notes_delete') || $user['id'] == $avtor['id'])) {
	echo "<div class='main2'>";
	echo "工具: ";
	echo " [<a href='edit.php?id=$notes[id]'><img src='/style/icons/edit.gif'> 编辑</a>] [<a href='?id=$notes[id]&amp;delete'><img src='/style/icons/delete.gif'> 删除</a>]";
	echo "</div>";
}
echo "<div class='main'>";
echo "<b>评级: $notes[count]</b><br />";
echo "</div>";
if (isset($user) && $user['id'] != $avtor['id']) {
	echo "<div class='main'>";
	if (dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `id_user` = '" . $user['id'] . "' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0) == 0)
		echo "[<img src='/style/icons/like.gif' alt='*' /> <a href='list.php?id=$notes[id]&amp;like=1'>喜欢</a>] [<a href='list.php?id=$notes[id]&amp;like=0'><img src='/style/icons/dlike.gif' alt='*' />不喜欢</a>]<br />";
	else
		echo "[<img src='/style/icons/like.gif' alt='*' /> " . dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `like` = '1' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0) . "] [<img src='/style/icons/dlike.gif' alt='*' /> " . dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `like` = '0' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0) . "]";
	echo "</div>";
} else {
	echo "<div class='main'>";
	echo "[<img src='/style/icons/like.gif' alt='*' /> " . dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `like` = '1' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0) . "] [<img src='/style/icons/dlike.gif' alt='*' /> " . dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `like` = '0' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0) . "]";
	echo "</div>";
}
//--------------------------В закладки-----------------------------//
if (isset($user)) {
	echo "<div class='main_seriy'>";
	echo "<div class='main'>";
	echo "<img src='/style/icons/fav.gif' alt='*' /> ";
	if (dbresult(dbquery("SELECT COUNT(*) FROM `mark_notes` WHERE `id_user` = '" . $user['id'] . "' AND `id_list` = '" . $notes['id'] . "' LIMIT 1"), 0) == 0)
		echo "<a href='list.php?id=$notes[id]&amp;fav=1'>添加到书签</a><br />";
	else
		echo "<a href='list.php?id=$notes[id]&amp;fav=0'>从书签中删除</a><br />";
	echo "在书签中 <a href='list.php?id=$notes[id]&amp;markinfo'>$markinfo</a> 用户";
	echo "</div>";
	echo "</div>";
}
//-------------------------------------------------------------//
echo "<div class='main'>";
echo '将分享:';
echo "</div>";
/*
===================================
Комментарии дневников
===================================
*/
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `notes_komm` WHERE `id_notes` = '" . intval($_GET['id']) . "'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo '<div class="foot">';
echo "评论";
echo '</div>';
if ($k_post == 0) {
	echo '<div class="mess">';
	echo "没有评论";
	echo '</div>';
} else if (isset($user)) {
	/*------------сортировка по времени--------------*/
	if (isset($user)) {
		echo "<div id='comments' class='menus'>";
		echo "<div class='webmenu'>";
		echo "<a href='list.php?id=$notes[id]&amp;page=$page&amp;sort=1' class='" . ($user['sort'] == 1 ? 'activ' : '') . "'>在下面</a>";
		echo "</div>";
		echo "<div class='webmenu'>";
		echo "<a href='list.php?id=$notes[id]&amp;page=$page&amp;sort=0' class='" . ($user['sort'] == 0 ? 'activ' : '') . "'>在顶部</a>";
		echo "</div>";
		echo "</div>";
	}
	/*---------------alex-borisi---------------------*/
}
$q = dbquery("SELECT * FROM `notes_komm` WHERE `id_notes` = '" . intval($_GET['id']) . "' ORDER BY `time` $sort LIMIT $start, $set[p_str]");
echo "<table class='post'>";
while ($post = dbassoc($q)) {
	$ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
	/*-----------代码-----------*/
	if ($num == 0) {
		echo '<div class="nav1">';
		$num = 1;
	} elseif ($num == 1) {
		echo '<div class="nav2">';
		$num = 0;
	}
	/*---------------------------*/
	echo user::nick($ank['id'], 1, 1, 0) . " ";
	if (isset($user) && $ank['id'] != $user['id']) echo "<a href='?id=$notes[id]&amp;response=$ank[id]'>[@]</a> ";
	echo " (" . vremja($post['time']) . ")<br />";
	$postBan = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE (`razdel` = 'all' OR `razdel` = 'notes') AND `post` = '1' AND `id_user` = '$ank[id]' AND (`time` > '$time' OR `navsegda` = '1')"), 0);
	if ($postBan == 0) // Блок сообщения
	{
		echo output_text($post['msg']) . "<br />";
	} else {
		echo output_text($banMess) . '<br />';
	}
	if (isset($user)) {
		echo '<div style="text-align:right;">';
		if ($ank['id'] != $user['id'])
			echo "<a href=\"?id=$notes[id]&amp;page=$page&amp;spam=$post[id]\"><img src='/style/icons/blicon.gif' alt='*'>举报</a> ";
		if (isset($user) && (user_access('notes_delete') || $user['id'] == $notes['id_user']))
			echo '<a href="delete.php?komm=' . $post['id'] . '"><img src="/style/icons/delete.gif" alt="*">删除</a>';
		echo "</div>";
	}
	echo "</div>";
}
echo "</table>";
if ($k_page > 1) str("list.php?id=" . intval($_GET['id']) . '&amp;', $k_page, $page); // 输出页数
if ($notes['private_komm'] == 1 && $user['id'] != $avtor['id'] && $frend != 2  && !user_access('notes_delete')) {
	msg('根据用户的隐私设置，这篇日记只允许用户的朋友评论。');
	echo "  <div class='foot'>";
	echo "<a href='index.php'>返回</a><br />";
	echo "   </div>";
	include_once '../../sys/inc/tfoot.php';
	exit;
}
if ($notes['private_komm'] == 2 && $user['id'] != $avtor['id'] && !user_access('notes_delete')) {
	msg('根据用户的隐私设置，已禁止评论这篇日记。');
	echo "  <div class='foot'>";
	echo "<a href='index.php'>返回</a><br />";
	echo "   </div>";
	include_once '../../sys/inc/tfoot.php';
	exit;
}
if (isset($user)) {
	echo "<form method=\"post\" name='message' action=\"?id=" . intval($_GET['id']) . "&amp;page=$page" . $go_otv . "\">";
	if ($set['web'] && is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
		include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
	else
		echo "$tPanel<textarea name=\"msg\">$otvet</textarea><br />";
	echo "<input value=\"发送\" type=\"submit\" />";
	echo "</form>";
}
echo "<div class=\"foot\">";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='index.php'>日记</a> | " . user::nick($avtor['id'], 1, 0, 0);
echo ' | <b>' . output_text($notes['name']) . '</b>';
echo "</div>";
include_once '../../sys/inc/tfoot.php';
