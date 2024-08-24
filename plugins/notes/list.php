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
/* 用户厢式货车 */
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0')"), 0) != 0) {
	header('Location: /user/ban.php?' . SID);
	exit;
}
$notes = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"));
if (!isset($notes['id'])) {
	header('Location: index.php');
	exit;
}
$avtor = user::get_user($notes['id_user']);
if (isset($user))
	$count = dbresult(dbquery("SELECT COUNT(*) FROM `notes_count` WHERE `id_user` = '" . $user['id'] . "' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0);
// 书签
$markinfo = dbresult(dbquery("SELECT COUNT(*) FROM `bookmarks` WHERE `id_object` = '" . $notes['id'] . "' AND `type`='notes'"), 0);
if (isset($user))
	dbquery("UPDATE `notification` SET `read` = '1' WHERE `type` = 'notes_komm' AND `id_user` = '$user[id]' AND `id_object` = '$notes[id]'");
/*
================================
用户投诉模块
信件或内容
因分区不同而不同
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
				if (strlen2($msg) > 1512) $err = '文本的长度超过1512个字符的限制';
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
	$set['title'] = '日记 ' . text($notes['name']) . '';
	include_once '../../sys/inc/thead.php';
	title();
	aut();
	err();
	if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'notes_komm'"), 0) == 0) {
		echo "<div class='mess'>若你认为某条言论不合适、违反了网站规则，可以举报，管理员收到后会尽快处理。
		但是，请不要瞎举报给管理添乱，若多次发出无意义的举报，将同样会按网站规则进行处罚。
		如果你真的很讨厌某位用户的言论，你可以选择将其拉黑，而不是将消息逐条举报。逐条举报会大大降低管理员处理举报的效率，甚至导致举报处理任务大量积压</div>";
		echo "<form class='nav1' method='post' action='?id=$notes[id]&amp;page=" . intval($_GET['page']) . "&amp;spam=$mess[id]'>";
		echo "<b>用户:</b> ";
		echo " " . user::nick($spamer['id'],1,1,0) . " (" . vremja($mess['time']) . ")<br />";
		echo "<b>违规：</b> <font color='green'>" . output_text($mess['msg']) . "</font><br />";
		echo "原因：<br /><select name='types'>";
		echo "<option value='1' selected='selected'>垃圾邮件/广告/日记/帖子</option>";
		echo "<option value='2' selected='selected'>诈骗行为</option>";
		echo "<option value='3' selected='selected'>引战</option>";
		echo "<option value='4' selected='selected'>网络暴力</option>";
		echo "<option value='0' selected='selected'>其他</option>";
		echo "</select><br />";
		echo "评论:$tPanel";
		echo "<textarea name=\"msg\"></textarea><br />";
		echo "<input value=\"发送\" type=\"submit\" />";
		echo "</form>";
	} else {
		echo "<div class='mess'>投诉有关<font color='green'>$spamer[nick]</font> 它将在不久的将来考虑。</div>";
	}
	echo "<div class='foot'>";
	echo "<img src='/style/icons/str2.gif' alt='*'> <a href='?id=$notes[id]&amp;page=" . intval($_GET['page']) . "'>返回</a><br />";
	echo "</div>";
	include_once '../../sys/inc/tfoot.php';
	exit;
}

// 查看记录
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `notes_count` WHERE `id_user` = '" . $user['id'] . "' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0) == 0) {
	dbquery("INSERT INTO `notes_count` (`id_notes`, `id_user`) VALUES ('$notes[id]', '$user[id]')");
	dbquery("UPDATE `notes` SET `count` = '" . ($notes['count'] + 1) . "' WHERE `id` = '$notes[id]' LIMIT 1");
}
/*------------清除此讨论的计数器-------------*/
if (isset($user)) {
	dbquery("UPDATE `discussions` SET `count` = '0' WHERE `id_user` = '$user[id]' AND `type` = 'notes' AND `id_sim` = '$notes[id]' LIMIT 1");
}
/*---------------------------------------------------------*/
$set['title'] = '日记 - ' . text($notes['name']) . '';
$set['meta_description'] = text($notes['msg']);
include_once '../../sys/inc/thead.php';
if (isset($_POST['msg']) && isset($user)) {
	$msg = $_POST['msg'];
	if (strlen2($msg) > 1024) {
		$err = '消息过长';
	} elseif (strlen2($msg) < 2) {
		$err = '短消息';
	} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `notes_komm` WHERE `id_notes` = '" . intval($_GET['id']) . "' AND `id_user` = '$user[id]' AND `msg` = '" . my_esc($msg) . "' LIMIT 1"), 0) != 0) {
		$err = '你的留言重复了上一条';
	} elseif (!isset($err)) {
		/*
		==========================
		回复通知
		==========================
		*/
		if (isset($user) && $respons == TRUE) {
			$notifiacation = dbassoc(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '" . $ank_otv['id'] . "' LIMIT 1"));
			if ($notifiacation['komm'] == 1 && $ank_otv['id'] != $user['id'])
				dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_otv[id]', '$notes[id]', 'notes_komm', '$time')");
		}
		/*
====================================
评论
====================================
*/
		$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $notes['id_user'] . "' AND `i` = '1'");
		while ($f = dbarray($q)) {
			$a = user::get_user($f['frend']);
			$discSet = dbarray(dbquery("SELECT * FROM `discussions_set` WHERE `id_user` = '" . $a['id'] . "' LIMIT 1")); // 设置全部讨论
			if ($f['disc_notes'] == 1 && $discSet['disc_notes'] == 1)
			/* 邮件列表 */ {
				//---------作者朋友--------------//
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
		//-------------发送给作者------------//
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
		$_SESSION['message'] = '消息已成功发送';
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
	msg('日记只提供给朋友');
	echo "  <div class='foot'>";
	echo "<a href='index.php'>返回</a><br />";
	echo "   </div>";
	include_once '../../sys/inc/tfoot.php';
	exit;
}
if ($notes['private'] == 2 && $user['id'] != $avtor['id']  && !user_access('notes_delete')) {
	msg('用户已禁止查看日记');
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
	if (isset($_GET['like']) && $_GET['like'] == 1) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `id_user` = '" . $user['id'] . "' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0) == 0) {
			dbquery("INSERT INTO `notes_like` (`id_notes`, `id_user`, `like`) VALUES ('$notes[id]', '$user[id]', '1')");
			dbquery("UPDATE `notes` SET `count` = '" . ($notes['count'] + 1) . "' WHERE `id` = '$notes[id]' LIMIT 1");
			$_SESSION['message'] = '你的选票被计算在内了';
			header("Location: list.php?id=$notes[id]&page=" . intval($_GET['page']) . "");
			exit;
		}
	}
	if (isset($_GET['like']) && $_GET['like'] == 0) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `id_user` = '" . $user['id'] . "' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0) == 0) {
			dbquery("INSERT INTO `notes_like` (`id_notes`, `id_user`, `like`) VALUES ('$notes[id]', '$user[id]', '0')");
			dbquery("UPDATE `notes` SET `count` = '" . ($notes['count'] - 1) . "' WHERE `id` = '$notes[id]' LIMIT 1");
			$_SESSION['message'] = '你的票被计算在内了';
			header("Location: list.php?id=$notes[id]&page=" . intval($_GET['page']) . "");
			exit;
		}
	}
	if (isset($_GET['fav']) && $_GET['fav'] == 1) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `bookmarks` WHERE `id_user` = '" . $user['id'] . "' AND `id_object` = '" . $notes['id'] . "' AND `type`='notes' LIMIT 1"), 0) == 0) {
			dbquery("INSERT INTO `bookmarks` (`type`,`id_object`, `id_user`, `time`) VALUES ('notes','$notes[id]', '$user[id]', '$time')");
			$_SESSION['message'] = '日记被添加到书签中';
			header("Location: list.php?id=$notes[id]&page=" . intval($_GET['page']) . "");
			exit;
		}
	}
	if (isset($_GET['fav']) && $_GET['fav'] == 0) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `bookmarks` WHERE `id_user` = '" . $user['id'] . "' AND `id_object` = '" . $notes['id'] . "' AND `type`='notes' LIMIT 1"), 0) == 1) {
			dbquery("DELETE FROM `bookmarks` WHERE `id_user` = '$user[id]' AND  `id_object` = '$notes[id]' AND `type`='notes' ");
			$_SESSION['message'] = '从书签中删除的日记';
			header("Location: list.php?id=$notes[id]&page=" . intval($_GET['page']) . "");
			exit;
		}
	}
}
echo "<div class=\"foot\">";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='index.php'>日记</a> | <a href='/user/info.php?id=$avtor[id]'>$avtor[nick]</a>";
echo ' | <b>' . output_text($notes['name']) . '</b>';
echo "</div>";
echo "<div class='main'>";
echo "<table style='width:110%;'><td style='width:4%;'>" . user::avatar($avtor['id']) . "</td>";
echo "<td style='width:96%;'> 作者: " . user::nick($avtor['id'], 1, 1, 0) . " ";
echo "(<img src='/style/icons/them_00.png'>  " . vremja($notes['time']) . ")<br/>";
echo "<img src='/style/icons/eye.png'> 预览: " . $notes['count'] . "</td></table></div>";
$stat1 = $notes['msg'];
if (!$set['web']) $mn = 20;
else $mn = 90; // 按浏览器显示的词数
$stat = explode(' ', $stat1); // 把报道分成一个词
$k_page = k_page(count($stat), $set['p_str'] * $mn);
$page = page($k_page);
$start = $set['p_str'] * $mn * ($page - 1);
$stat_1 = NULL;
for ($i = $start; $i < $set['p_str'] * $mn * $page && $i < count($stat); $i++) {
	$stat_1 .= $stat[$i] . ' ';
}
echo '<div class="mess">' . output_text($stat_1), ''; // 打印所有格式的文档 。
notes_share($notes['id']);
echo '</div>';
if ($k_page > 1) str("?id=$notes[id]&amp;", $k_page, $page); // 输出页数
/*----------------------листинг-------------------*/
$listr = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` < '$notes[id]' ORDER BY `id` DESC LIMIT 1"));
$list = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` > '$notes[id]' ORDER BY `id`  ASC LIMIT 1"));
echo '<div class="c2" style="text-align: center;">';
if (isset($list['id'])) echo '<span class="page">' . ($list['id'] ? '<a href="list.php?id=' . $list['id'] . '">&laquo; 上一页</a> ' : '&laquo; 上一页') . '</span>';
$k_1 = dbresult(dbquery("SELECT COUNT(*) FROM `notes` WHERE `id` > '$notes[id]'"), 0) + 1;
$k_2 = dbresult(dbquery("SELECT COUNT(*) FROM `notes`"), 0);
echo ' (第' . $k_1 . '页 共' . $k_2 . '页) ';
if (isset($listr['id'])) echo '<span class="page">' . ($listr['id'] ? '<a href="list.php?id=' . $listr['id'] . '">下一页 &raquo;</a>' : ' 下一页 &raquo;') . '</span>';
echo '</div>';
/*----------------------plugins---------------*/
echo "<div class='main2'>";
$share = dbresult(dbquery("SELECT COUNT(*)FROM `notes` WHERE `share_id`='" . $notes['id'] . "' AND `share_type`='notes'"), 0);
if (dbresult(dbquery("SELECT COUNT(*)FROM `notes` WHERE `id_user`='" . $user['id'] . "' AND `share_type`='notes' AND `share_id`='" . $notes['id'] . "' LIMIT 1"), 0) == 0 && isset($user) && $user['id'] != $notes['id_user']) {
	echo " <a href='share.php?id=" . $notes['id'] . "'><img src='/style/icons/action_share_color.gif'> 分享: (" . $share . ")</a>";
} else {
	echo "<img src='/style/icons/action_share_color.gif'> 分享:  (" . $share . ")";
}
if (isset($user) && (user_access('notes_delete') || $user['id'] == $avtor['id'])) {
	echo "<br/><a href='edit.php?id=$notes[id]'><img src='/style/icons/edit.gif'> 修改</a> <a href='?id=$notes[id]&amp;delete'><img src='/style/icons/delete.gif'> 删除</a>";
}
echo "</div><div class='main'>";
$l1 = dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `like` = '0' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0);
$l2 = dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `like` = '1' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0);
if (isset($user) && $user['id'] != $avtor['id']) {
	if (dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `id_user` = '" . $user['id'] . "' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0) == 0)
		echo "<a href='list.php?id=$notes[id]&amp;like=1'><img src='/style/icons/thumbu.png' alt='*' /> </a> (" . ($l2 - $l1) . ") <a href='list.php?id=$notes[id]&amp;like=0'><img src='/style/icons/thumbd.png' alt='*' /></a>";
	else
		echo " <img src='/style/icons/thumbu.png' alt='*' /> (" . ($l2 - $l1) . ") <img src='/style/icons/thumbd.png' alt='*' /> ";
} else {
	echo " <img src='/style/icons/thumbu.png' alt='*' />  (" . ($l2 - $l1) . ") <img src='/style/icons/thumbd.png' alt='*' /> ";
}
//--------------------------移至书签-----------------------------//
if (isset($user)) {
	echo "" . ($webbrowser ? "&bull;" : null) . " <img src='/style/icons/add_fav.gif' alt='*' /> ";
	if (dbresult(dbquery("SELECT COUNT(*) FROM `bookmarks` WHERE `id_user` = '" . $user['id'] . "' AND `id_object` = '" . $notes['id'] . "' AND `type`='notes' LIMIT 1"), 0) == 0)
		echo "<a href='list.php?id=$notes[id]&amp;fav=1'>添加到书签</a><br />";
	else
		echo "<a href='list.php?id=$notes[id]&amp;fav=0'>删除书签</a><br />";
	echo "<img src='/style/icons/add_fav.gif' alt='*' />  <a href='fav.php?id=" . $notes['id'] . "'>谁将它添加到书签?</a> (" . $markinfo . ")";
}
echo '</div>';

/*
===================================
日记评论
===================================
*/
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `notes_komm` WHERE `id_notes` = '" . intval($_GET['id']) . "'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo '<div class="foot">';
echo "<b>评论</b>: (" . dbresult(dbquery("SELECT COUNT(`id`)FROM `notes_komm` WHERE `id_notes`='" . $notes['id'] . "'"), 0) . ")";
echo '</div>';
if ($k_post == 0) {
	echo '<div class="mess">';
	echo "没有评论";
	echo '</div>';
} else if (isset($user)) {
	/*------------按时间排列--------------*/
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
	/*-----------------------------------*/
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
	echo user::nick($ank['id'], 1, 1, 0);
	if (isset($user) && $ank['id'] != $user['id']) echo "<a href='?id=$notes[id]&amp;response=$ank[id]'>[@]</a> ";
	echo " (" . vremja($post['time']) . ")<br />";
	$postBan = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE (`razdel` = 'all' OR `razdel` = 'notes') AND `post` = '1' AND `id_user` = '$ank[id]' AND (`time` > '$time' OR `navsegda` = '1')"), 0);
	if ($postBan == 0) // 消息块
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
	msg('只有朋友才能评论');
	echo "  <div class='foot'>";
	echo "<a href='index.php'>返回</a><br />";
	echo "   </div>";
	include_once '../../sys/inc/tfoot.php';
	exit;
}
if ($notes['private_komm'] == 2 && $user['id'] != $avtor['id'] && !user_access('notes_delete')) {
	msg('评论区已关闭');
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
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='index.php'>日记</a> | ". user::nick($avtor['id'], 1, 0, 0);
echo ' | <b>' . output_text($notes['name']) . '</b>';
echo "</div>";
include_once '../../sys/inc/tfoot.php';
