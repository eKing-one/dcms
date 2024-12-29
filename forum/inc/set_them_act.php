<?
/* PluginS Dcms-Social.Ru */
/*==== 移动主题*****/
if (
	isset($_GET['act']) && isset($_GET['ok']) && $_GET['act'] == 'mesto' && isset($_POST['razdel']) && is_numeric($_POST['razdel'])
	&& (dbresult(dbquery("SELECT COUNT(`id`) FROM `forum_r` WHERE `id` = '" . intval($_POST['razdel']) . "'"), 0) == 1 && user_access('forum_them_edit')
		|| dbresult(dbquery("SELECT COUNT(`id`) FROM `forum_r` WHERE `id` = '" . intval($_POST['razdel']) . "' WHERE `id_forum` = '$forum[id]'"), 0) == 1 && $ank2['id'] == $user['id'])
) {
	$razdel_new = dbassoc(dbquery("SELECT * FROM `forum_r` WHERE `id` = '" . intval($_POST['razdel']) . "' LIMIT 1"));
	dbquery("UPDATE `forum_p` SET `id_forum` = '$razdel_new[id_forum]', `id_razdel` = '$razdel_new[id]' WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]' AND `id_them` = '$them[id]'");
	dbquery("UPDATE `forum_t` SET `id_forum` = '$razdel_new[id_forum]', `id_razdel` = '$razdel_new[id]' WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]' AND `id` = '$them[id]'");
	$old_razdel = $razdel;
	$forum = dbassoc(dbquery("SELECT * FROM `forum_f` WHERE `id` = '$razdel_new[id_forum]' LIMIT 1"));
	$razdel = dbassoc(dbquery("SELECT * FROM `forum_r` WHERE `id` = '$razdel_new[id]' LIMIT 1"));
	$them = dbassoc(dbquery("SELECT * FROM `forum_t` WHERE `id_razdel` = '$razdel[id]' AND `id` = '$them[id]' LIMIT 1"));
	/* PluginS Dcms-Social.Ru */
	$msgg = '[red]转移话题 ' . $user['group_name'] . ' ' . $user['nick'] . ' 从节 ' . $old_razdel['name'] . ' 至该组 ' . $razdel['name'] . '[/red]';
	dbquery("INSERT INTO `forum_p` (`id_forum`, `id_razdel`, `id_them`, `id_user`, `msg`, `time`) values('$forum[id]', '$razdel[id]', '$them[id]', '0', '" . my_esc($msgg) . "', '$time')");
	/*тут конец*/
	if ($ank2['id'] != $user['id'])
		admin_log('论坛', '移动主题', "移动主题 '[url=/forum/$forum[id]/$razdel[id]/$them[id]/]$them[name][/url]' 从节'[url=/forum/$forum[id]/$old_razdel[id]/]$old_razdel[name][/url]' в раздел '[url=/forum/$forum[id]/$old_razdel[id]/]$razdel[name][/url]'");
	$_SESSION['message'] = '主题已成功移动';
	header("Location: /forum/$forum[id]/$razdel[id]/$them[id]/");
	exit;
}
/**** 删除主题 ****/
if ((user_access('forum_them_del') || $ank2['id'] == $user['id']) &&  isset($_GET['act']) && isset($_GET['ok']) && $_GET['act'] == 'delete') {
	/*
	* 删除主题文件
	*/
	$qf = dbquery("SELECT * FROM `forum_p` WHERE `id_them` = '$them[id]'");
	while ($postf = dbassoc($qf)) {
		if (dbresult(dbquery("SELECT COUNT(*) FROM `forum_files` WHERE `id_post` = '$postf[id]'"), 0) > 0) {
			$qS = dbquery("SELECT * FROM `forum_files` WHERE `id_post` = '$postf[id]'");
			while ($postS = dbassoc($qS)) {
				dbquery("DELETE FROM `forum_files` WHERE `id` = '$postS[id]'");
				@unlink(H . 'sys/forum/files/' . $postS['id'] . '.frf');
			}
		}
	}
	dbquery("DELETE FROM `forum_t` WHERE `id` = '$them[id]'");
	dbquery("DELETE FROM `forum_p` WHERE `id_them` = '$them[id]'");
	if ($ank2['id'] != $user['id']) admin_log('论坛', '删除主题', "删除主题 '$them[name]' (作者 '[url=/user/info.php?id=$ank2[id]]$ank2[nick][/url]')");
	$_SESSION['message'] = '主题已成功删除';
	header("Location: /forum/$forum[id]/$razdel[id]/$them[id]/");
	exit;
}
/**** 改变主题 ****/
if (isset($_GET['act']) && isset($_GET['ok']) && $_GET['act'] == 'set' && isset($_POST['name']) && (user_access('forum_them_edit') || $ank2['id'] == $user['id'])) {
	$name = esc(stripslashes(htmlspecialchars($_POST['name'])));
	$msg = esc(stripslashes(htmlspecialchars($_POST['msg'])));
	if (strlen2($name) < 3) $err = '名字太短了';
	if (strlen2($name) > 32) $err = '名字太长了';
	$name = my_esc($_POST['name']);
	$msg = my_esc($_POST['msg']);
	if ($user['level'] > 0) {
		if (isset($_POST['up']) && $_POST['up'] == 1 and $them['up'] != 1) {
			if ($ank2['id'] != $user['id']) admin_log('论坛', '主题参数', "固定主题'[url=/forum/$forum[id]/$razdel[id]/$them[id]/]$them[name][/url]' (作者 '[url=/user/info.php?id=$ank2[id]]$ank2[nick][/url]', раздел '$razdel[name]')");
			$up = 1;
			/* PluginS Dcms-Social.Ru */
			$msgg = '[red]主题已固定 ' . $user['group_name'] . ' ' . $user['nick'] . '[/red]';
			dbquery("INSERT INTO `forum_p` (`id_forum`, `id_razdel`, `id_them`, `id_user`, `msg`, `time`) values('$forum[id]', '$razdel[id]', '$them[id]', '0', '" . my_esc($msgg) . "', '$time')");
			/*结束了*/
		} else $up = 0;
		$add_q = " `up` = '$up',";
	} else $add_q = NULL;
	if (isset($_POST['close']) && $_POST['close'] == 1 && $them['close'] == 0) {
		$close = 1;
		if ($ank2['id'] != $user['id']) admin_log('论坛', '主题参数', "结束主题 '[url=/forum/$forum[id]/$razdel[id]/$them[id]]$them[name][/url]' (作者 '[url=/user/info.php?id=$ank2[id]]$ank2[nick][/url]')");
		/* PluginS Dcms-Social.Ru */
		$msgg = '[red]关闭主题 ' . $user['group_name'] . ' ' . $user['nick'] . '[/red]';
		dbquery("INSERT INTO `forum_p` (`id_forum`, `id_razdel`, `id_them`, `id_user`, `msg`, `time`) values('$forum[id]', '$razdel[id]', '$them[id]', '0', '" . my_esc($msgg) . "', '$time')");
		/*结束了*/
	} elseif ($them['close'] == 1 && (!isset($_POST['close']) || $_POST['close'] == 0)) {
		$close = 0;
		if ($ank2['id'] != $user['id']) admin_log('论坛', '主题参数', "打开主题'[url=/forum/$forum[id]/$razdel[id]/$them[id]]$them[name][/url]' (作者 '[url=/user/info.php?id=$ank2[id]]$ank2[nick][/url]')");
		$msgg = '[red]打开话题 ' . $user['group_name'] . ' ' . $user['nick'] . '[/red]';
		dbquery("INSERT INTO `forum_p` (`id_forum`, `id_razdel`, `id_them`, `id_user`, `msg`, `time`) values('$forum[id]', '$razdel[id]', '$them[id]', '0', '" . my_esc($msgg) . "', '$time')");
		/*结束了*/
	} else $close = $them['close'];
	if (isset($_POST['autor']) && $_POST['autor'] == 1) $autor = $user['id'];
	else $autor = $ank2['id'];
	if (!isset($err)) {
		if ($_POST['close'] == 1 and $them['close'] == 0) {
			$cl = ",`id_close`='" . $user['id'] . "' ";
		} elseif ($_POST['close'] == 0 and $them['close'] == 1) {
			$cl = null;
		} else {
			$cl = null;
		}
		dbquery("UPDATE `forum_t` SET `name` = '$name', `text` = '$msg', `id_user` = '$autor'," . $add_q . " `close` = '$close',`id_edit`='" . $user['id'] . "',`time_edit`='" . $time . "' " . $cl . " WHERE `id` = '$them[id]' LIMIT 1");
		$them = dbassoc(dbquery("SELECT * FROM `forum_t` WHERE `id` = '$them[id]' LIMIT 1"));
		$ank2 = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$them[id_user]' LIMIT 1"));
		$_SESSION['message'] = '更改已成功接受';
		header("Location: /forum/$forum[id]/$razdel[id]/$them[id]/");
		exit;
	}
}
/***** 清除标记的石材 ****/
if ((user_access('forum_post_ed') || isset($user) && $ank2['id'] == $user['id']) && isset($_GET['act']) && $_GET['act'] == 'post_delete' && isset($_GET['ok'])) {
	foreach ($_POST as $key => $value) {
		if (preg_match('#^post_([0-9]*)$#', $key, $postnum) && $value = '1') {
			$delpost[] = $postnum[1];
		}
	}
	if (isset($delpost) && is_array($delpost)) {
		dbquery("DELETE FROM `forum_p` WHERE `id_them` = '$them[id]' AND (`id` = '" . implode("'" . ' OR `id` = ' . "'", $delpost) . "') LIMIT " . count($delpost));
		if ($ank2['id'] != $user['id'])
			admin_log('论坛', '清除主题', "清除主题 '[url=/forum/$forum[id]/$razdel[id]/$them[id]/]$them[name][/url]' (作者 '[url=/user/info.php?id=$ank2[id]]$ank2[nick][/url]', 已删除 '" . count($delpost) . "' 职位)");
		$msgg = '[red]我清理了话题 ' . $user['group_name'] . ' ' . $user['nick'] . '[/red]';
		dbquery("INSERT INTO `forum_p` (`id_forum`, `id_razdel`, `id_them`, `id_user`, `msg`, `time`) values('$forum[id]', '$razdel[id]', '$them[id]', '0', '" . my_esc($msgg) . "', '$time')");
		$_SESSION['message'] = '成功删除 ' . count($delpost) . ' 职位';
		header("Location: /forum/$forum[id]/$razdel[id]/$them[id]/");
		exit;
	}
}
if (isset($_GET['act']) && $_GET['act'] == 'post_delete' && (user_access('forum_post_ed') || isset($user) && $ank2['id'] == $user['id'])) {
	echo "<form method='post' action='/forum/$forum[id]/$razdel[id]/$them[id]/?act=post_delete&amp;ok'>";
}
