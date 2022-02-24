<?php

/**
 * 联系人
 */
include_once 'sys/inc/start.php';
include_once 'sys/inc/compress.php';
include_once 'sys/inc/sess.php';
include_once 'sys/inc/home.php';
include_once 'sys/inc/settings.php';
include_once 'sys/inc/db_connect.php';
include_once 'sys/inc/ipua.php';
include_once 'sys/inc/fnc.php';
include_once 'sys/inc/user.php';
only_reg();
$kont = dbquery("SELECT `id_kont` FROM `users_konts` WHERE `type`='deleted' AND `id_user`='" . $user['id'] . "' AND `time`>='" . $_SERVER['REQUEST_TIME'] . "'");
if (dbrows($kont) > 0) {
	while ($konts = dbassoc($kont)) {
		dbquery("DELETE FROM `user_konts` WHERE `id_kont`='" . $konts['id_kont'] . "'");
		dbquery("DELETE FROM `mail` WHERE `id_user`='" . $user['id'] . "' AND `id_kont`='" . $konts['id_kont'] . "'");
	}
}
switch (@$_GET['type']) {
	case 'favorite':
		$type = 'favorite';
		$type_name = '特别关注';
		break;
	case 'ignor':
		$type = 'ignor';
		$type_name = '忽略';
		break;
	case 'deleted':
		$type = 'deleted';
		$type_name = '垃圾箱';
		break;
	default:
		$type = 'common';
		$type_name = '活动';
		break;
}
$set['title'] = $type_name . '中的联系人';
include_once 'sys/inc/thead.php';
title();
if (isset($_GET['id'])) {
	$ank = user::get_user($_GET['id']);
	if ($ank) {
		if (isset($_GET['act'])) {
			switch ($_GET['act']) {
				case 'add':
					if (dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"), 0) == 1)
						$err[] = '此用户已在您的联系人列表中';
					else {
						dbquery("INSERT INTO `users_konts` (`id_user`, `id_kont`, `time`) VALUES ('$user[id]', '$ank[id]', '$time')");
						$_SESSION['message'] = '成功添加联系人';
						header("Location: ?");
						exit;
					}
					break;
				case 'del':
					if (dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"), 0) == 0)
						$warn[] = '此用户不在您的联系人列表中';
					else {
						dbquery("UPDATE `users_konts` SET `type` = 'deleted', `time` = '" . ($time + 2592000) . "' WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' LIMIT 1");
						$_SESSION['message'] = '联系人已移至垃圾箱';
						header("Location: ?");
						exit;
						$type = 'deleted';
					}
					break;
			}
		}
	} else
		$err[] = '未找到用户';
}
if (isset($_GET['act']) && $_GET['act'] == 'edit_ok' && isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"), 0) == 1) {
	$ank = user::get_user(intval($_GET['id']));
	if (dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"), 0) == 1) {
		$kont = dbarray(dbquery("SELECT * FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"));
		if (isset($_POST['name']) && $_POST['name'] != ($kont['name'] != null ? $kont['name'] : $ank['nick'])) {
			if (preg_match('#[^A-z0-9\-_\.,\[\]\(\) ]#i', $_POST['name'])) $err[] = '联系人的名称包含禁止的字符';
			if (strlen($_POST['name']) > 64) $err[] = '联系人姓名的长度超过64个字符';
			if (!isset($err)) {
				dbquery("UPDATE `users_konts` SET `name` = '" . my_esc(htmlspecialchars($_POST['name'])) . "' WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' LIMIT 1");
				$_SESSION['message'] = '联系人成功重命名';
				header("Location: ?");
				exit;
			}
		}
		if (isset($_POST['type']) && preg_match('#^(common|ignor|favorite|deleted)$#', $_POST['type']) && $_POST['type'] != $type) {
			if ($_POST['type'] == 'deleted') {
				$lol = $time + 2592000;
			} else {
				$lol = $time;
			}
			dbquery("UPDATE `users_konts` SET `type` = '$_POST[type]', `time` = '$lol' WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' LIMIT 1");
			$_SESSION['message'] = '联系人成功转移';
			header("Location: ?");
			exit;
		}
	} else
		$err[] = '未找到联系人';
}
aut();
/*========================================标记========================================*/
if (is_array($_POST)) {
	foreach ($_POST as $key => $value) {
		if (preg_match('#^post_([0-9]*)$#', $key, $postnum) && $value = '1') {
			$delpost[] = $postnum[1];
		}
	}
}
// игнор 
if (isset($_POST['ignor'])) {
	if (isset($delpost) && is_array($delpost)) {
		echo '<div class="mess">联系我们: ';
		for ($q = 0; $q <= count($delpost) - 1; $q++) {
			if (dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$delpost[$q]'"), 0) == 0)
				$warn[] = '此用户不在您的联系人列表中';
			else {
				dbquery("UPDATE `users_konts` SET `type` = 'ignor', `time` = '$time' WHERE `id_user` = '$user[id]' AND `id_kont` = '$delpost[$q]' LIMIT 1");
			}
			$ank_del = user::get_user($delpost[$q]);
			echo '<font color="#395aff"><b>' . $ank_del['nick'] . '</b></font>, ';
		}
		echo ' 已加入黑名单</div>';
	} else {
		$err[] = '没有一个联系人突出显示';
	}
}
// активные 
if (isset($_POST['common'])) {
	if (isset($delpost) && is_array($delpost)) {
		echo '<div class="mess">联系人: ';
		for ($q = 0; $q <= count($delpost) - 1; $q++) {
			if (dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$delpost[$q]'"), 0) == 0)
				$warn[] = '此用户不在您的联系人列表中';
			else {
				dbquery("UPDATE `users_konts` SET `type` = 'common', `time` = '$time' WHERE `id_user` = '$user[id]' AND `id_kont` = '$delpost[$q]' LIMIT 1");
			}
			$ank_del = user::get_user($delpost[$q]);
			echo '<font color="#395aff"><b>' . $ank_del['nick'] . '</b></font>, ';
		}
		echo ' 成功转移到活动联系人</div>';
	} else {
		$err[] = '没有一个联系人突出显示';
	}
}
// избранное
if (isset($_POST['favorite'])) {
	if (isset($delpost) && is_array($delpost)) {
		echo '<div class="mess">联系人:';
		for ($q = 0; $q <= count($delpost) - 1; $q++) {
			if (dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$delpost[$q]'"), 0) == 0)
				$warn[] = '此用户不在您的联系人列表中';
			else {
				dbquery("UPDATE `users_konts` SET `type` = 'favorite', `time` = '$time' WHERE `id_user` = '$user[id]' AND `id_kont` = '$delpost[$q]' LIMIT 1");
			}
			$ank_del = user::get_user($delpost[$q]);
			echo '<font color="#395aff"><b>' . $ank_del['nick'] . '</b></font>, ';
		}
		echo ' 成功移动到收藏夹</div>';
	} else {
		$err[] = '没有一个联系人突出显示';
	}
}
// удаляем
if (isset($_POST['deleted'])) {
	if (isset($delpost) && is_array($delpost)) {
		echo '<div class="mess">联系人';
		for ($q = 0; $q <= count($delpost) - 1; $q++) {
			if (dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$delpost[$q]'"), 0) == 0)
				$warn[] = '此用户不在您的联系人列表中';
			else {
				dbquery("UPDATE `users_konts` SET `type` = 'deleted', `time` = '$time' WHERE `id_user` = '$user[id]' AND `id_kont` = '$delpost[$q]' LIMIT 1");
			}
			$ank_del = user::get_user($delpost[$q]);
			echo '<font color="#395aff"><b>' . $ank_del['nick'] . '</b></font>, ';
		}
		echo ' 成功转移至垃圾桶</div>';
	} else {
		$err[] = '没有一个联系人突出显示';
	}
}
err();
echo "<div class='nav2'><span style='float:right;'><a href='/mails.php'><img src='/style/icons/mails.png'> 写一封信</a></span><br/></div>";
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `type` = '$type'"), 0);
if ($k_post) {
	$k_page = k_page($k_post, $set['p_str']);
	$page = page($k_page);
	$start = $set['p_str'] * $page - $set['p_str'];
	echo '<table class="post">';
	$q = dbquery("SELECT * FROM `users_konts` WHERE `id_user` = '$user[id]' AND `type` = '$type' ORDER BY `time` DESC, `new_msg` DESC LIMIT $start, $set[p_str]");
	echo '<form method="post" action="">';
	while ($post = dbarray($q)) {
		$ank_kont = user::get_user($post['id_kont']);
		$k_mess = dbresult(dbquery("SELECT COUNT(*) FROM `mail` WHERE `unlink` != '$user[id]' AND `id_user` = '$ank_kont[id]' AND `id_kont` = '$user[id]'"), 0);
		$k_mess2 = dbresult(dbquery("SELECT COUNT(*) FROM `mail` WHERE `unlink` != '$user[id]' AND `id_user` = '$user[id]' AND `id_kont` = '$ank_kont[id]'"), 0);
		$k_mess_to = dbresult(dbquery("SELECT COUNT(*) FROM `mail` WHERE `unlink` != '$user[id]' AND `id_user` = '$user[id]' AND `id_kont` = '$ank_kont[id]' AND `read` = '0'"), 0);
		$k_new_mess = dbresult(dbquery("SELECT COUNT(*) FROM `mail` WHERE `id_user` = '$ank_kont[id]' AND `id_kont` = '$user[id]' AND `read` = '0'"), 0);
		if ($k_mess_to > 0)		$k_mess_to = ' <font color=red><b>&uarr;</b></font> [<font color=red>' . $k_mess_to . '</font>]';
		else		$k_mess_to = null;
		/*-----------代码-----------*/
		if ($num == 0) {
			echo "  <div class='nav1'>";
			$num = 1;
		} elseif ($num == 1) {
			echo "  <div class='nav2'>";
			$num = 0;
		}
		/*---------------------------*/
		echo user::nick($ank['id'], 1, 1, 0); //输出用户名
		echo '<input type="checkbox" name="post_' . $post['id_kont'] . '" value="1" />';
		echo ($k_new_mess != 0 ? '<img src="/style/icons/new_mess.gif" alt="*" /> ' : '<img src="/style/icons/msg.gif" alt="*" /> ') . '<a href="/mail.php?id=' . $ank_kont['id'] . '">' . ($post['name'] != null ? $post['name'] : '信息') . '</a> ';
		echo ($k_new_mess != 0 ? '<font color="red">' : null) . ($k_new_mess != 0 ? '+' . $k_new_mess : '(' . $k_mess . '/' . $k_mess2 . ')' . $k_mess_to) . ($k_new_mess != 0 ? '</font> ' : null);
		echo '</div>';
	}
	echo '<div class="nav2">';
	if ($type != 'deleted') echo '<input value="垃圾箱" type="submit" name="deleted" /> ';
	if ($type != 'common') echo '<input value="忽略" type="submit" name="common" /> ';
	if ($type != 'favorite') echo '<input value="特别关注" type="submit" name="favorite" /> ';
	if ($type != 'ignor') echo '<input value="不经常联系" type="submit" name="ignor" /> ';
	echo '</form>';
	echo '</div>';
	if ($k_page > 1) str("?type=$type&amp;", $k_page, $page); // 输出页数
} else {
	echo '<div class="mess">';
	echo '您的联系人列表为空';
	echo '</div>';
}
if ($type == 'deleted') echo '<div class="mess">注意。联系人存储在垃圾箱里不超过1个月。<br />之后，它们被完全移除。</div>';
if ($type == 'ignor') echo '<div class="mess">关于来自这些联系人的消息的通知不会出现</div>';
if ($type == 'favorite') echo '<div class="mess">来自这些联系人的消息通知将突出显示</div>';
echo '<div class="main">';
echo ($type == 'common' ? '<b>' : null) . '<img style="padding:2px;" src="/style/icons/activ.gif" alt="*" /> <a href="?type=common">正常联系</a>' . ($type == 'common' ? '</b>' : null) . ' (' . dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `type` = 'common'"), 0) . ')<br />';
echo ($type == 'favorite' ? '<b>' : null) . '<img style="padding:2px;" src="/style/icons/star_fav.gif" alt="*" /> <a href="?type=favorite">特别关注</a>' . ($type == 'favorite' ? '</b>' : null) . ' (' . dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `type` = 'favorite'"), 0) . ')<br />';
echo ($type == 'ignor' ? '<b>' : null) . '<img style="padding:2px;" src="/style/icons/spam.gif" alt="*" /> <a href="?type=ignor">忽略</a>' . ($type == 'ignor' ? '</b>' : null) . ' (' . dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `type` = 'ignor'"), 0) . ')<br />';
echo ($type == 'deleted' ? '<b>' : null) . '<img style="padding:2px;" src="/style/icons/trash.gif" alt="*" /> <a href="?type=deleted">垃圾箱</a>' . ($type == 'deleted' ? '</b>' : null) . ' (' . dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `type` = 'deleted'"), 0) . ')<br />';
echo '</div>';
include_once 'sys/inc/tfoot.php';
