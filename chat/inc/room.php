<?
//没有评论
if (isset($_POST['msg']) && isset($user)) {
	$msg = $_POST['msg'];
	$mat = antimat($msg);
	if ($mat) $err[] = '在信息的文本中发现了一个非法字符: ' . $mat;
	if (strlen2($msg) > 512) {
		$err[] = '信息长于 512 字节。试着压缩一下？';
	} elseif (strlen2($msg) < 2) {
		$err[] = '信息短于 2 字节。试着扩充一下？';
	} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `chat_post` WHERE `id_user` = '$user[id]' AND `msg` = '" . my_esc($msg) . "' AND `time` > '" . ($time - 300) . "' LIMIT 1"), 0) != 0) {
		$err = '你的留言重复了前面的';
	} elseif (!isset($err)) {
		if (isset($_POST['privat'])) {
			$priv = abs(intval($_POST['privat']));
		} else {
			$priv = 0;
		}
		dbquery("INSERT INTO `chat_post` (`id_user`, `time`, `msg`, `room`, `privat`) values('$user[id]', '$time', '" . my_esc($msg) . "', '$room[id]', '$priv')");
		$_SESSION['message'] = '留言已成功添加';
		header("Location: /chat/room/$room[id]/" . rand(1000, 9999) . "/");
		exit;
	}
}
if ($room['umnik'] == 1) include 'inc/umnik.php';
if ($room['shutnik'] == 1) include 'inc/shutnik.php';
err();
aut(); // форма авторизации
if (isset($user)) {
	echo "<form method=\"post\" name='message' action=\"/chat/room/$room[id]/" . rand(1000, 9999) . "/\">";
	if ($set['web'] && is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
		include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
	else
		echo "$tPanel<textarea name=\"msg\"></textarea><br />";
	echo "<input value=\"发送\" type=\"submit\" />";
	echo " <a href='/chat/room/$room[id]/" . rand(1000, 9999) . "/'>刷新</a><br />";
	echo "</form>";
}
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `chat_post` WHERE `room` = '$room[id]' AND (`privat`='0'" . (isset($user) ? " OR `privat` = '$user[id]'" : null) . ")"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo "<table class='post'>";
if ($k_post == 0) {
	echo "<div class='mess'>";
	echo "目前没有信息。";
	echo "</div>";
}
$q = dbquery("SELECT * FROM `chat_post` WHERE `room` = '$room[id]' AND (`privat`='0'" . (isset($user) ? " OR `privat` = '$user[id]'" : null) . ") ORDER BY id DESC LIMIT $start, $set[p_str]");
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
	if ($post['umnik_st'] == 0 && $post['shutnik'] == 0)
		$ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
	if ($post['umnik_st'] == 0 && $post['shutnik'] == 0)
		echo group($ank['id']);
	elseif ($post['shutnik'] == 1)
		echo "<img src='/style/themes/$set[set_them]/chat/14/shutnik.png' alt='' />";
	elseif ($post['umnik_st'] != 0)
		echo "<img src='/style/themes/$set[set_them]/chat/14/umnik.png' alt='' />";
	if ($post['privat'] == $user['id']) {
		$sPrivat = '<font color="darkred">[!п]</font>';
	} else {
		$sPrivat = NULL;
	}
	if ($post['umnik_st'] == 0 && $post['shutnik'] == 0) {
		echo "<a href='/chat/room/$room[id]/" . rand(1000, 9999) . "/$ank[id]/'>$ank[nick]</a>";
		echo "" . medal($ank['id']) . " $sPrivat " . online($ank['id']) . " (" . vremja($post['time']) . ")<br />";
	} elseif ($post['umnik_st'] != 0)
		echo "$set[chat_umnik] (" . vremja($post['time']) . ")";
	elseif ($post['shutnik'] == 1)
		echo "$set[chat_shutnik] (" . vremja($post['time']) . ")";
	echo output_text($post['msg']) . '';
	echo "</div>";
}
echo "</table>";
if ($k_page > 1) str("/chat/room/$room[id]/" . rand(1000, 9999) . "/?", $k_page, $page); // 输出页数
