<?php
require_once '../../sys/inc/start.php';
require_once '../../sys/inc/compress.php';
require_once '../../sys/inc/sess.php';
require_once '../../sys/inc/home.php';
require_once '../../sys/inc/settings.php';
require_once '../../sys/inc/db_connect.php';
require_once '../../sys/inc/ipua.php';
require_once '../../sys/inc/fnc.php';
require_once '../../sys/inc/user.php';

/* 屏蔽封禁用户 */
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0')"), 0) != 0) {
	header('Location: ../../user/ban.php?' . session_id());
	exit;
}

$notes = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"));
if (!isset($notes['id'])) {
	header('Location: index.php');
	exit;
}

if ($notes['id_user'] !== NULL) $query_result = dbquery("SELECT id FROM `user` WHERE id = {$notes['id_user']} LIMIT 1");
if (isset($query_result) && dbrows($query_result) > 0) $avtor = user::get_user($notes['id_user']);

// 书签
$markinfo = dbresult(dbquery("SELECT COUNT(*) FROM `bookmarks` WHERE `id_object` = '" . $notes['id'] . "' AND `type`='notes'"), 0);

if (isset($user)) {
	$count = dbresult(dbquery("SELECT COUNT(*) FROM `notes_count` WHERE `id_user` = '" . $user['id'] . "' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0);
	dbquery("UPDATE `notification` SET `read` = '1' WHERE `type` = 'notes_komm' AND `id_user` = '$user[id]' AND `id_object` = '$notes[id]'");
}


/*
================================
用户举报模块
信件或内容
因分区不同而不同
================================
*/
if (isset($_GET['spam']) && isset($user)) {
	$mess = dbassoc(dbquery("SELECT * FROM `notes_komm` WHERE `id` = '" . intval($_GET['spam']) . "' limit 1"));
	if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$mess[id_user]' AND `razdel` = 'notes_komm' AND `spam` = '" . $mess['msg'] . "'"), 0) == 0) {
		if (isset($_POST['msg'])) {
			if ($mess['id_user'] != $user['id']) {
				$msg = my_esc($_POST['msg']);
				if (strlen2($msg) < 3) $err = '更加详细地说明举报的原因';
				if (strlen2($msg) > 1512) $err = '文本长度超过1512个字';
				if (isset($_POST['types'])) {
					$types = intval($_POST['types']);
				} else {
					$types = '0';
				}
				if (!isset($err)) {
					$db->query('INSERT INTO `spamus` (`id_object`, `id_user`, `msg`, `id_spam`, `time`, `types`, `razdel`, `spam`) values(:notes_id, :user_id, :msg, :spam_id, :time, :types, :razdel, :spam)', [
						':notes_id' => $notes['id'],
						':user_id' => $user['id'],
						':msg' => $msg,
						':spam_id' => $mess['id_user'],
						':time' => $time,
						':types' => $types,
						':razdel' => 'notes_komm',
						':spam' => $mess['msg']
					]);
					$_SESSION['message'] = '举报成功,管理员将火速处理';
					header("Location: ?id=$notes[id]&page=" . intval($_GET['page']) . "&spam=$mess[id]");
					exit;
				}
			}
		}
	}
	$set['title'] = '日记 ' . text($notes['name']) . '';
	require_once '../../sys/inc/thead.php';
	title();
	aut();
	err();
	if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$mess[id_user]' AND `razdel` = 'notes_komm'"), 0) == 0) {
		echo "<div class='mess'>若你认为某条言论不合适、违反了网站规则，可以举报，管理员收到后会尽快处理。
		但是，请不要瞎举报给管理添乱，若多次发出无意义的举报，将同样会按网站规则进行处罚。
		如果你真的很讨厌某位用户的言论，你可以选择将其拉黑，而不是将消息逐条举报。逐条举报会大大降低管理员处理举报的效率，甚至导致举报处理任务大量积压</div>";
		echo "<form class='nav1' method='post' action='?id=$notes[id]&amp;page=" . intval($_GET['page']) . "&amp;spam=$mess[id]'>";
		echo "<b>用户:</b> ";
		echo " " . user::nick($mess['id_user'], 1, 1, 0) . " (" . vremja($mess['time']) . ")<br />";
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
		$spamer = user::get_user($mess['id_user']);
		echo "<div class='mess'>举报有关<font color='green'>" . (isset($spamer['nick']) ? $spamer['nick'] : "[已删除]") . "</font> 它将在不久的将来考虑。</div>";
	}
	echo "<div class='foot'>";
	echo "<img src='../../style/icons/str2.gif' alt='*'> <a href='?id=$notes[id]&amp;page=" . intval($_GET['page']) . "'>返回</a><br />";
	echo "</div>";
	require_once '../../sys/inc/tfoot.php';
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

if ($notes['private'] == 1 && (empty($user) || ($user['id'] != $notes['id_user'] && $frend != 2  && !user_access('notes_delete')))) {
	$set['title'] = '[不可见]';
} elseif ($notes['private'] == 2 && (empty($user) || ($user['id'] != $notes['id_user']  && !user_access('notes_delete')))) {
	$set['title'] = '[不可见]';
} else {
	$set['title'] = '日记 - ' . text($notes['name']) . '';
}

$set['meta_description'] = text($notes['msg']);
require_once '../../sys/inc/thead.php';

if (isset($_POST['msg']) && isset($user)) {
	$msg = $_POST['msg'];
	
	// 验证消息长度
	if (strlen2($msg) > 1024) {
		$err = '消息过长';
	} elseif (strlen2($msg) < 2) {
		$err = '短消息';
	} else {
		// 检查是否重复留言
		$checkSql = "SELECT COUNT(*) as count FROM `notes_komm` WHERE `id_notes` = :id_notes AND `id_user` = :id_user AND `msg` = :msg LIMIT 1";
		$checkResult = $db->query($checkSql, [
			':id_notes' => intval($_GET['id']),
			':id_user' => $user['id'],
			':msg' => $msg
		]);
		
		if ($checkResult['count'] != 0) {
			$err = '你的留言重复了上一条';
		} elseif (!isset($err)) {
			/*
			==========================
			回复通知部分
			==========================
			*/
			if (isset($user) && $respons == TRUE) {
				// 获取目标用户的通知设置
				$notification = $db->query(
					"SELECT * FROM `notification_set` WHERE `id_user` = :id_user LIMIT 1",
					[':id_user' => $ank_otv['id']]
				);
				
				// 如果用户开启了评论通知且不是自己回复自己
				if ($notification['komm'] == 1 && $ank_otv['id'] != $user['id']) {
					// 插入通知记录
					$db->insert(
						"INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES (:avtor, :id_user, :id_object, 'notes_komm', :time)", [
							':avtor' => $user['id'],
							':id_user' => $ank_otv['id'],
							':id_object' => $notes['id'],
							':time' => $time
						]
					);
				}
			}

			/*
			====================================
			评论和讨论处理部分
			====================================
			*/
			// 获取笔记作者的好友列表
			$friends = $db->queryAll(
				"SELECT * FROM `frends` WHERE `user` = :user AND `i` = '1'",
				[':user' => $notes['id_user']]
			);

			// 遍历所有好友
			foreach ($friends as $f) {
				$a = user::get_user($f['frend']);
				// 获取好友的讨论设置
				$discSet = $db->query(
					"SELECT * FROM `discussions_set` WHERE `id_user` = :id_user LIMIT 1",
					[':id_user' => $a['id']]
				);

				// 检查好友是否订阅了笔记讨论
				if ($f['disc_notes'] == 1 && $discSet['disc_notes'] == 1) {
					// 检查是否已有讨论记录
					$discCount = $db->query(
						"SELECT COUNT(*) as count FROM `discussions` WHERE `id_user` = :id_user AND `type` = 'notes' AND `id_sim` = :id_sim LIMIT 1", [
							':id_user' => $a['id'],
							':id_sim' => $notes['id']
						]
					)['count'];

					if ($discCount == 0) {
						// 如果不是作者本人或当前用户，创建新讨论记录
						if ($notes['id_user'] != $a['id'] || $a['id'] != $user['id']) {
							$db->insert(
								"INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) VALUES (:id_user, :avtor, 'notes', :time, :id_sim, '1')", [
									':id_user' => $a['id'],
									':avtor' => $notes['id_user'],
									':time' => $time,
									':id_sim' => $notes['id']
								]
							);
						}
					} else {
						// 获取现有讨论记录
						$disc = $db->query(
							"SELECT * FROM `discussions` WHERE `id_user` = :id_user AND `type` = 'notes' AND `id_sim` = :id_sim LIMIT 1", [
								':id_user' => $a['id'],
								':id_sim' => $notes['id']
							]
						);
						
						// 更新讨论计数和时间
						if ($notes['id_user'] != $a['id'] || $a['id'] != $user['id']) {
							$db->update(
								"UPDATE `discussions` SET `count` = :count, `time` = :time WHERE `id_user` = :id_user AND `type` = 'notes' AND `id_sim` = :id_sim LIMIT 1", [
									':count' => $disc['count'] + 1,
									':time' => $time,
									':id_user' => $a['id'],
									':id_sim' => $notes['id']
								]
							);
						}
					}
				}
			}

			// 处理作者的讨论记录
			$authorDiscCount = $db->query(
				"SELECT COUNT(*) as count FROM `discussions` WHERE `id_user` = :id_user AND `type` = 'notes' AND `id_sim` = :id_sim LIMIT 1", [
					':id_user' => $notes['id_user'],
					':id_sim' => $notes['id']
				]
			)['count'];

			if (!empty($notes['id_user']) && $authorDiscCount == 0) {
				// 如果不是自己评论自己，创建作者的讨论记录
				if ($notes['id_user'] != $user['id']) {
					$db->insert(
						"INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) VALUES (:id_user, :avtor, 'notes', :time, :id_sim, '1')", [
							':id_user' => $notes['id_user'],
							':avtor' => $notes['id_user'],
							':time' => $time,
							':id_sim' => $notes['id']
						]
					);
				}
			} else {
				// 获取作者现有讨论记录
				$disc = $db->query(
					"SELECT * FROM `discussions` WHERE `id_user` = :id_user AND `type` = 'notes' AND `id_sim` = :id_sim LIMIT 1", [
						':id_user' => $notes['id_user'],
						':id_sim' => $notes['id']
					]
				);

				// 更新作者的讨论计数和时间
				if ($notes['id_user'] != $user['id']) {
					$db->update(
						"UPDATE `discussions` SET `count` = :count, `time` = :time WHERE `id_user` = :id_user AND `type` = 'notes' AND `id_sim` = :id_sim LIMIT 1", [
							':count' => $disc['count'] + 1,
							':time' => $time,
							':id_user' => $notes['id_user'],
							':id_sim' => $notes['id']
						]
					);
				}
			}

			// 插入新评论
			$db->insert(
				"INSERT INTO `notes_komm` (`id_user`, `time`, `msg`, `id_notes`) VALUES (:id_user, :time, :msg, :id_notes)", [
					':id_user' => $user['id'],
					':time' => $time,
					':msg' => $msg,
					':id_notes' => intval($_GET['id'])
				]
			);

			// 更新用户积分
			$db->update(
				"UPDATE `user` SET `balls` = :balls WHERE `id` = :id LIMIT 1", [
					':balls' => $user['balls'] + 1,
					':id' => $user['id']
				]
			);

			// 设置成功消息并重定向
			$_SESSION['message'] = '消息已成功发送';
			header("Location: list.php?id={$notes['id']}&page=" . intval($_GET['page']));
			exit;
		}
	}
}

if (isset($user) && isset($avtor['id'])) $frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$avtor[id]') OR (`user` = '$avtor[id]' AND `frend` = '$user[id]') LIMIT 1"), 0);
title();
aut(); // 授权表格
err();

if ($notes['private'] == 1 && (empty($user) || ($user['id'] != $notes['id_user'] && $frend != 2  && !user_access('notes_delete')))) {
	msg('日记只提供给朋友');
	echo "  <div class='foot'><a href='index.php'>返回</a><br /></div>";
	require_once '../../sys/inc/tfoot.php';
}
if ($notes['private'] == 2 && (empty($user) || ($user['id'] != $notes['id_user']  && !user_access('notes_delete')))) {
	msg('用户已禁止查看日记');
	echo "  <div class='foot'>";
	echo "<a href='index.php'>返回</a><br />";
	echo "   </div>";
	require_once '../../sys/inc/tfoot.php';
}

if (isset($user) && isset($_GET['delete']) && $_GET['delete'] == 'note' && ($user['id'] == $notes['id_user'] || user_access('notes_delete'))) {
	echo "<center>";
	echo "你真的想删除日记吗 " . output_text($notes['name']) . "?<br />";
	echo "[<a href='delete.php?id={$notes['id']}'><img src='../../style/icons/ok.gif'> 删除</a>] [<a href='list.php?id={$notes['id']}'><img src='../../style/icons/delete.gif'> 取消</a>] ";
	echo "</center>";
	require_once '../../sys/inc/tfoot.php';
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
echo "<img src='../../style/icons/str2.gif' alt='*'> <a href='index.php'>日记</a> | ";
if ($notes['id_user'] === NULL) {
	echo '???';
} else {
	echo user::nick($notes['id_user'], 1, 0, 0);
}
echo ' | <b>' . output_text($notes['name']) . '</b>';
echo "</div>";

echo "<div class='main'>";
echo "<table style='width:110%;'><td style='width:4%;'>" . (empty($avtor['id']) ? '<img class="avatar" src="../../style/user/avatar.gif" height="50" width="50" alt="No Avatar">' : user::avatar($avtor['id'])) . "</td>";
echo "<td style='width:96%;'> 作者: ";
if ($notes['id_user'] === NULL) {
	echo '???';
} else {
	echo user::nick($notes['id_user'], 1, 1, 0);
}
echo " (<img src='../../style/icons/them_00.png'>  " . vremja($notes['time']) . ")<br/>";
echo "<img src='../../style/icons/eye.png'> 阅读量: " . $notes['count'] . "</td></table></div>";

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
if (isset($user)) {
	$share = dbresult(dbquery("SELECT COUNT(*)FROM `notes` WHERE `share_id`='" . $notes['id'] . "' AND `share_type`='notes'"), 0);
	if (dbresult(dbquery("SELECT COUNT(*)FROM `notes` WHERE `id_user`='" . $user['id'] . "' AND `share_type`='notes' AND `share_id`='" . $notes['id'] . "' LIMIT 1"), 0) == 0 && isset($user) && $user['id'] != $notes['id_user']) {
		echo " <a href='share.php?id=" . $notes['id'] . "'><img src='../../style/icons/action_share_color.gif'> 分享: (" . $share . ")</a>";
	} else {
		echo "<img src='../../style/icons/action_share_color.gif'> 分享:  (" . $share . ")";
	}
}
if (isset($user) && (user_access('notes_delete') || $user['id'] == $notes['id_user'])) {
	echo "<br/><a href='edit.php?id=$notes[id]'><img src='../../style/icons/edit.gif'> 修改</a> <a href='?id=$notes[id]&amp;delete=note'><img src='../../style/icons/delete.gif'> 删除</a>";
}
echo "</div><div class='main'>";
$l1 = dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `like` = '0' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0);
$l2 = dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `like` = '1' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0);
if (isset($user) && isset($avtor['id']) && $user['id'] != $avtor['id']) {
	if (dbresult(dbquery("SELECT COUNT(*) FROM `notes_like` WHERE `id_user` = '" . $user['id'] . "' AND `id_notes` = '" . $notes['id'] . "' LIMIT 1"), 0) == 0)
		echo "<a href='list.php?id=$notes[id]&amp;like=1'><img src='../../style/icons/thumbu.png' alt='*' /> </a> (" . ($l2 - $l1) . ") <a href='list.php?id=$notes[id]&amp;like=0'><img src='../../style/icons/thumbd.png' alt='*' /></a>";
	else
		echo " <img src='../../style/icons/thumbu.png' alt='*' /> (" . ($l2 - $l1) . ") <img src='../../style/icons/thumbd.png' alt='*' /> ";
} else {
	echo " <img src='../../style/icons/thumbu.png' alt='*' />  (" . ($l2 - $l1) . ") <img src='../../style/icons/thumbd.png' alt='*' /> ";
}
//--------------------------移至书签-----------------------------//
if (isset($user)) {
	echo "" . ($webbrowser ? "&bull;" : null) . " <img src='../../style/icons/add_fav.gif' alt='*' /> ";
	if (dbresult(dbquery("SELECT COUNT(*) FROM `bookmarks` WHERE `id_user` = '" . $user['id'] . "' AND `id_object` = '" . $notes['id'] . "' AND `type`='notes' LIMIT 1"), 0) == 0) {
		echo "<a href='list.php?id=$notes[id]&amp;fav=1'>添加到书签</a><br />";
	} else {
		echo "<a href='list.php?id=$notes[id]&amp;fav=0'>删除书签</a><br />";
	}
	echo "<img src='../../style/icons/add_fav.gif' alt='*' />  <a href='fav.php?id=" . $notes['id'] . "'>查看收藏者</a> (" . $markinfo . ")";
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
echo "<b>评论</b>: ({$k_post})";
echo '</div>';

if ($k_post == 0) {
	echo '<div class="mess">没有评论</div>';
} else {
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
	$q = dbquery("SELECT * FROM `notes_komm` WHERE `id_notes` = '" . intval($_GET['id']) . "' ORDER BY `time` $sort LIMIT $start, $set[p_str]");
	echo "<table class='post'>";
	while ($post = dbassoc($q)) {
		// 意义不明的查询
		//$ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));

		if ($num == 0) {
			echo '<div class="nav1">';
			$num = 1;
		} elseif ($num == 1) {
			echo '<div class="nav2">';
			$num = 0;
		}

		echo user::nick($post['id_user'], 1, 1, 0);
		if (isset($user) && $post['id_user'] != $user['id']) echo "<a href='?id={$notes['id']}&amp;response={$post['id_user']}'>[@]</a> ";
		echo " (" . vremja($post['time']) . ")<br />";
		$postBan = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE (`razdel` = 'all' OR `razdel` = 'notes') AND `post` = '1' AND `id_user` = '{$post['id_user']}' AND (`time` > '{$time}' OR `navsegda` = '1')"), 0);
		if ($postBan == 0) {	// 消息块
			echo output_text($post['msg']) . "<br />";
		} else {
			echo output_text($banMess) . '<br />';
		}
		if (isset($user)) {
			echo '<div style="text-align:right;">';
			if ($post['id_user'] != $user['id']) echo "<a href=\"?id=$notes[id]&amp;page=$page&amp;spam=$post[id]\"><img src='/style/icons/blicon.gif' alt='*'>举报</a> ";
			if (isset($user) && ((user_access('notes_delete') || $user['id'] == $notes['id_user']) || $user['id'] == $post['id_user'])) echo '<a href="delete.php?komm=' . $post['id'] . '"><img src="../../style/icons/delete.gif" alt="*">删除</a>';
			echo "</div>";
		}
		echo "</div>";
	}
	echo "</table>";
}

if ($k_page > 1) str("list.php?id=" . intval($_GET['id']) . '&amp;', $k_page, $page); // 输出页数

if ($notes['private_komm'] == 1 && $user['id'] != $avtor['id'] && $frend != 2  && !user_access('notes_delete')) {
	msg('只有朋友才能评论');
	echo "  <div class='foot'>";
	echo "<a href='index.php'>返回</a><br />";
	echo "   </div>";
	require_once '../../sys/inc/tfoot.php';
}

if ($notes['private_komm'] == 2 && $user['id'] != $avtor['id'] && !user_access('notes_delete')) {
	msg('评论区已关闭');
	echo "  <div class='foot'>";
	echo "<a href='index.php'>返回</a><br />";
	echo "   </div>";
	require_once '../../sys/inc/tfoot.php';
}

// 发送评论表单
if (isset($user)) {
	echo "<form method=\"post\" name='message' action=\"?id=" . intval($_GET['id']) . "&amp;page=$page" . $go_otv . "\">";
	if ($set['web'] && is_file('../../style/themes/' . $set['set_them'] . '/altername_post_form.php')) {
		require_once '../../style/themes/' . $set['set_them'] . '/altername_post_form.php';
	} else {
		echo "$tPanel<textarea name=\"msg\">$otvet</textarea><br />";
	}
	echo "<input value=\"发送\" type=\"submit\" />";
	echo "</form>";
}

echo '<div class="foot">';
echo "<img src='../../style/icons/str2.gif' alt='*'> <a href='index.php'>日记</a> | ". user::nick($notes['id_user'], 1, 0, 0);
echo ' | <b>' . output_text($notes['name']) . '</b>';
echo "</div>";

require_once '../../sys/inc/tfoot.php';
