<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('user_delete', null, 'index.php?' . SID);
adm_check();
if (isset($_GET['id'])) $ank['id'] = intval($_GET['id']);
else {
	header("Location: /index.php?" . SID);
	exit;
}
if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$ank[id]' LIMIT 1"), 0) == 0) {
	header("Location: /index.php?" . SID);
	exit;
}
$ank = user::get_user($ank['id']);
if ($user['level'] <= $ank['level']) {
	header("Location: /index.php?" . SID);
	exit;
}
$set['title'] = '删除用户 "' . $ank['nick'] . '"';
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['delete'])) {
	if (function_exists('set_time_limit')) @set_time_limit(600);
	$mass[0] = $ank['id'];
	$collisions = user_collision($mass, 1);
	dbquery("DELETE FROM `user` WHERE `id` = '$ank[id]' LIMIT 1");
	dbquery("DELETE FROM `chat_post` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `gifts_user` WHERE `id_user` = '$ank[id]' OR `id_ank` = '$ank[id]'");
	dbquery("DELETE FROM `frends` WHERE `user` = '$ank[id]' OR `frend` = '$ank[id]'");
	dbquery("DELETE FROM `frends_new` WHERE `user` = '$ank[id]' OR `to` = '$ank[id]'");
	dbquery("DELETE FROM `stena` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `stena_like` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `status_like` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `status` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `status_komm` WHERE `id_user` = '$ank[id]'");
	$q5 = dbquery("SELECT * FROM `forum_t` WHERE `id_user` = '$ank[id]'");
	while ($post5 = dbassoc($q5)) {
		dbquery("DELETE FROM `forum_p` WHERE `id_them` = '$post5[id]'");
	}
	dbquery("DELETE FROM `forum_t` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `user_set` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `notification` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `notification_set` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `discussions` WHERE `id_user` = '$ank[id]' OR `id_user` = '$ank[id]' OR `ot_kogo` = '$ank[id]'");
	dbquery("DELETE FROM `discussions_set` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `forum_p` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `forum_zakl` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `guest` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `loads_komm` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `news_komm` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `user_files` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `user_music` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `like_object` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `status` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `status_like` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `status_komm` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `status_count` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `mark_notes` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `mark_files` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `mark_people` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `mark_photo` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `tape_set` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `tape` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `tape` WHERE `avtor` = '$ank[id]'");
	dbquery("DELETE FROM `tape` WHERE `id_file` = '$ank[id]' AND `type` = 'frend'");
	$opdirbase = @opendir(H . 'sys/add/delete_user_act');
	while ($filebase = @readdir($opdirbase))
		if (preg_match('#\.php$#i', $filebase))
			include_once(check_replace(H . 'sys/add/delete_user_act/' . $filebase));
	$q5 = dbquery("SELECT * FROM `downnik_files` WHERE `id_user` = '$ank[id]'");
	while ($post5 = dbassoc($q5)) {
		unlink(H . 'sys/down/files/' . $post5['id'] . '.dat');
	}
	dbquery("DELETE FROM `downnik_files` WHERE `id_user` = '$ank[id]'");
	dbquery("DELETE FROM `users_konts` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'");
	dbquery("DELETE FROM `mail` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'");
	dbquery("DELETE FROM `user_voice` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'");
	dbquery("DELETE FROM `user_collision` WHERE `id_user` = '$ank[id]' OR `id_user2` = '$ank[id]'");
	dbquery("DELETE FROM `votes_user` WHERE `u_id` = '$ank[id]'");
	if (count($collisions) > 1 && isset($_GET['all'])) {
		for ($i = 1; $i < count($collisions); $i++) {
			dbquery("DELETE FROM `user` WHERE `id` = '$collisions[$i]' LIMIT 1");
			dbquery("DELETE FROM `chat_post` WHERE `id_user` = '$collisions[$i]'");
			dbquery("DELETE FROM `forum_t` WHERE `id_user` = '$collisions[$i]'");
			$q5 = dbquery("SELECT * FROM `forum_t` WHERE `id_user` = '$collisions[$i]'");
			while ($post5 = dbassoc($q5)) {
				dbquery("DELETE FROM `forum_p` WHERE `id_them` = '$post5[id]'");
			}
			dbquery("DELETE FROM `forum_p` WHERE `id_user` = '$collisions[$i]'");
			dbquery("DELETE FROM `forum_zakl` WHERE `id_user` = '$collisions[$i]'");
			dbquery("DELETE FROM `guest` WHERE `id_user` = '$collisions[$i]'");
			dbquery("DELETE FROM `loads_komm` WHERE `id_user` = '$collisions[$i]'");
			dbquery("DELETE FROM `news_komm` WHERE `id_user` = '$collisions[$i]'");
			$q5 = dbquery("SELECT * FROM `downnik_files` WHERE `id_user` = '$collisions[$i]'");
			while ($post5 = dbassoc($q5)) {
				unlink(H . 'sys/down/files/' . $post5['id'] . '.dat');
			}
			dbquery("DELETE FROM `downnik_files` WHERE `id_user` = '$collisions[$i]'");
			dbquery("DELETE FROM `users_konts` WHERE `id_user` = '$collisions[$i]' OR `id_kont` = '$collisions[$i]'");
			dbquery("DELETE FROM `mail` WHERE `id_user` = '$collisions[$i]' OR `id_kont` = '$collisions[$i]'");
			dbquery("DELETE FROM `user_voice` WHERE `id_user` = '$collisions[$i]' OR `id_kont` = '$collisions[$i]'");
			dbquery("DELETE FROM `user_collision` WHERE `id_user` = '$collisions[$i]' OR `id_user2` = '$collisions[$i]'");
			dbquery("DELETE FROM `votes_user` WHERE `u_id` = '$collisions[$i]'");
		}
		admin_log('用户', '删除', "删除用户组 '$ank[nick]' (id#" . implode(',id#', $collisions) . ")");
		msg('所有用户数据已被删除');
	} else {
		admin_log('用户', '删除', "删除用户 '$ank[nick]' (id#$ank[id])");
		msg("所有用户数据 $ank[nick] 已删除");
	}
	$tab = dbquery("SHOW TABLES");

	while ($name = mysqli_fetch_array($tab)) {
  	  //就是table 名字，接下去就用mysqi 的写法写下去就是了
		dbquery("OPTIMIZE TABLE `" . $name[0] . "`");
	}
	// for ($i = 0; $i < dbrows($tab); $i++) {
	// 	dbquery("OPTIMIZE TABLE `" . mysql_tablename($tab, $i) . "`");
	// }
	echo "<div class='foot'>";
	echo "&laquo;<a href='/users.php'>用户</a><br />";
	echo "</div>";
	include_once '../sys/inc/tfoot.php';
}
$mass[0] = $ank['id'];
$collisions = user_collision($mass, 1);
$chat_post = dbresult(dbquery("SELECT COUNT(*) FROM `chat_post` WHERE `id_user` = '$ank[id]'"), 0);
if (count($collisions) > 1 && isset($_GET['all'])) {
	$chat_post_coll = 0;
	for ($i = 1; $i < count($collisions); $i++) {
		$chat_post_coll += dbresult(dbquery("SELECT COUNT(*) FROM `chat_post` WHERE `id_user` = '$collisions[$i]'"), 0);
	}
	if ($chat_post_coll != 0)
		$chat_post = "$chat_post +$chat_post_coll*";
}
echo "<span class=\"ank_n\">聊天信息:</span> <span class=\"ank_d\">$chat_post</span><br />";
$k_them = dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `id_user` = '$ank[id]'"), 0);
if (count($collisions) > 1 && isset($_GET['all'])) {
	$k_them_coll = 0;
	for ($i = 1; $i < count($collisions); $i++) {
		$k_them_coll += dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `id_user` = '$collisions[$i]'"), 0);
	}
	if ($k_them_coll != 0)
		$k_them = "$k_them +$k_them_coll*";
}
echo "<span class=\"ank_n\">论坛的主题:</span> <span class=\"ank_d\">$k_them</span><br />";
$k_p_forum = dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id_user` = '$ank[id]'"), 0);
if (count($collisions) > 1 && isset($_GET['all'])) {
	$k_p_forum_coll = 0;
	for ($i = 1; $i < count($collisions); $i++) {
		$k_p_forum_coll += dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id_user` = '$collisions[$i]'"), 0);
	}
	if ($k_p_forum_coll != 0)
		$k_p_forum = "$k_p_forum +$k_p_forum_coll*";
}
echo "<span class=\"ank_n\">论坛内的帖子:</span> <span class=\"ank_d\">$k_p_forum</span><br />";
$zakl = dbresult(dbquery("SELECT COUNT(*) FROM `forum_zakl` WHERE `id_user` = '$ank[id]'"), 0);
if (count($collisions) > 1 && isset($_GET['all'])) {
	$zakl_coll = 0;
	for ($i = 1; $i < count($collisions); $i++) {
		$zakl_coll += dbresult(dbquery("SELECT COUNT(*) FROM `forum_zakl` WHERE `id_user` = '$collisions[$i]'"), 0);
	}
	if ($zakl_coll != 0)
		$zakl = "$zakl +$zakl_coll*";
}
echo "<span class=\"ank_n\">书签:</span> <span class=\"ank_d\">$zakl</span><br />";
$guest = dbresult(dbquery("SELECT COUNT(*) FROM `guest` WHERE `id_user` = '$ank[id]'"), 0);
if (count($collisions) > 1 && isset($_GET['all'])) {
	$guest_coll = 0;
	for ($i = 1; $i < count($collisions); $i++) {
		$guest_coll += dbresult(dbquery("SELECT COUNT(*) FROM `guest` WHERE `id_user` = '$collisions[$i]'"), 0);
	}
	if ($guest_coll != 0)
		$guest = "$guest +$guest_coll*";
}
echo "<span class=\"ank_n\">客人:</span> <span class=\"ank_d\">$guest</span><br />";
$konts = dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'"), 0);
if (count($collisions) > 1 && isset($_GET['all'])) {
	$konts_coll = 0;
	for ($i = 1; $i < count($collisions); $i++) {
		$konts_coll += dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$collisions[$i]' OR `id_kont` = '$collisions[$i]'"), 0);
	}
	if ($konts_coll != 0)
		$konts = "$konts +$konts_coll*";
}
echo "<span class=\"ank_n\">联系人:</span> <span class=\"ank_d\">$konts</span><br />";
$mail = dbresult(dbquery("SELECT COUNT(*) FROM `mail` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'"), 0);
if (count($collisions) > 1 && isset($_GET['all'])) {
	$mail_coll = 0;
	for ($i = 1; $i < count($collisions); $i++) {
		$mail_coll += dbresult(dbquery("SELECT COUNT(*) FROM `mail` WHERE `id_user` = '$collisions[$i]' OR `id_kont` = '$collisions[$i]'"), 0);
	}
	if ($mail_coll != 0)
		$mail = "$mail +$mail_coll*";
}
echo "<span class=\"ank_n\">私人讯息:</span> <span class=\"ank_d\">$mail</span><br />";
$komm_loads = dbresult(dbquery("SELECT COUNT(*) FROM `loads_komm` WHERE `id_user` = '$ank[id]'"), 0);
if (count($collisions) > 1 && isset($_GET['all'])) {
	$komm_loads_coll = 0;
	for ($i = 1; $i < count($collisions); $i++) {
		$komm_loads_coll += dbresult(dbquery("SELECT COUNT(*) FROM `loads_komm` WHERE `id_user` = '$collisions[$i]'"), 0);
	}
	if ($komm_loads_coll != 0)
		$komm_loads = "$komm_loads +$komm_loads_coll*";
}
echo "<span class=\"ank_n\">下载中的评论:</span> <span class=\"ank_d\">$komm_loads</span><br />";
$news_komm = dbresult(dbquery("SELECT COUNT(*) FROM `news_komm` WHERE `id_user` = '$ank[id]'"), 0);
if (count($collisions) > 1 && isset($_GET['all'])) {
	$news_komm_coll = 0;
	for ($i = 1; $i < count($collisions); $i++) {
		$news_komm_coll += dbresult(dbquery("SELECT COUNT(*) FROM `news_komm` WHERE `id_user` = '$collisions[$i]'"), 0);
	}
	if ($news_komm_coll != 0)
		$news_komm = "$news_komm +$news_komm_coll*";
}
echo "<span class=\"ank_n\">新闻评论:</span> <span class=\"ank_d\">$news_komm</span><br />";
$user_voice = dbresult(dbquery("SELECT COUNT(*) FROM `user_voice2` WHERE `id_user` = '$ank[id]' OR `id_kont` = '$ank[id]'"), 0);
if (count($collisions) > 1 && isset($_GET['all'])) {
	$user_voice_coll = 0;
	for ($i = 1; $i < count($collisions); $i++) {
		$user_voice_coll += dbresult(dbquery("SELECT COUNT(*) FROM `user_voice2` WHERE `id_user` = '$collisions[$i]' OR `id_kont` = '$collisions[$i]'"), 0);
	}
	if ($user_voice_coll != 0)
		$user_voice = "$user_voice +$user_voice_coll*";
}
echo "<span class=\"ank_n\">评级结果:</span> <span class=\"ank_d\">$user_voice</span><br />";
$downnik = dbresult(dbquery("SELECT COUNT(*) FROM `downnik_files` WHERE `id_user` = '$ank[id]'"), 0);
if (count($collisions) > 1 && isset($_GET['all'])) {
	$downnik_coll = 0;
	for ($i = 1; $i < count($collisions); $i++) {
		$downnik_coll += dbresult(dbquery("SELECT COUNT(*) FROM `downnik_files` WHERE `id_user` = '$collisions[$i]'"), 0);
	}
	if ($downnik_coll != 0)
		$downnik = "$downnik +$downnik_coll*";
}
echo "<span class=\"ank_n\">下载中心中的文件:</span> <span class=\"ank_d\">$downnik</span><br />";
$opdirbase = @opendir(H . 'sys/add/delete_user_info');
while ($filebase = @readdir($opdirbase))
	if (preg_match('#\.php$#i', $filebase))
		include_once(check_replace(H . 'sys/add/delete_user_info/' . $filebase));
echo "<form method=\"post\" action=\"\">";
echo "<input value=\"删除\" type=\"submit\" name='delete' />";
echo "</form>";
if (count($collisions) > 1 && isset($_GET['all'])) {
	echo "* 也会被删除用户:";
	for ($i = 1; $i < count($collisions); $i++) {
		$ank_coll = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$collisions[$i]'"));
		echo "$ank_coll[nick]";
		if ($i == count($collisions) - 1)
			echo '.';
		else echo '; ';
	}
	echo "<br />";
}
echo "无法恢复已删除的数据<br />";
echo "<div class='foot'>";
echo "&laquo;<a href='/user/info.php?id=$ank[id]'>返回资料</a><br />";
echo "&laquo;<a href='/users.php'>用户</a><br />";
echo "</div>";
include_once '../sys/inc/tfoot.php';
