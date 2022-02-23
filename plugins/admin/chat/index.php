<?
include_once '../../../sys/inc/start.php';
include_once '../../../sys/inc/compress.php';
include_once '../../../sys/inc/sess.php';
include_once '../../../sys/inc/home.php';
include_once '../../../sys/inc/settings.php';
include_once '../../../sys/inc/db_connect.php';
include_once '../../../sys/inc/ipua.php';
include_once '../../../sys/inc/fnc.php';
include_once '../../../sys/inc/user.php';
if (user_access('adm_panel_show')) {
	$set['title'] = '管理员聊天'; //网页标题
	include_once '../../../sys/inc/thead.php';
	title();
	/*
===============================
Помечаем уведомление прочитанным
===============================
*/
	dbquery("UPDATE `notification` SET `read` = '1' WHERE `type` = 'adm_komm' AND `id_user` = '$user[id]'");
	include 'inc/admin_act.php';
	if (isset($_POST['msg']) && isset($user)) {
		$msg = $_POST['msg'];
		if (isset($_POST['translit']) && $_POST['translit'] == 1) $msg = translit($msg);
		$mat = antimat($msg);
		if ($mat) $err[] = 'В тексте сообщения обнаружен мат: ' . $mat;
		if (strlen2($msg) > 1024) {
			$err[] = 'Сообщение слишком длинное';
		} elseif (strlen2($msg) < 2) {
			$err[] = 'Короткое сообщение';
		} elseif (dbresult(dbquery("SELECT COUNT(*) FROM `adm_chat` WHERE `id_user` = '$user[id]' AND `msg` = '" . my_esc($msg) . "' LIMIT 1"), 0) != 0) {
			$err = 'Ваше сообщение повторяет предыдущее';
		} elseif (!isset($err)) {
			/*
		==========================
		Уведомления об ответах
		==========================
		*/
			if (isset($user) && $respons == TRUE) {
				$notifiacation = dbassoc(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '" . $ank_otv['id'] . "' LIMIT 1"));
				if ($notifiacation['komm'] == 1 && $ank_otv['id'] != $user['id'])
					dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `type`, `time`) VALUES ('$user[id]', '$ank_otv[id]', 'adm_komm', '$time')");
			}
			dbquery("INSERT INTO `adm_chat` (id_user, time, msg) values('$user[id]', '$time', '" . my_esc($msg) . "')");
			dbquery("UPDATE `user` SET `balls` = '" . ($user['balls'] + 1) . "' WHERE `id` = '$user[id]' LIMIT 1");
			$_SESSION['message'] = '消息已成功添加';
			header("Location: ?");
			exit;
		}
	}
	err();
	aut(); // форма авторизации
	$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `adm_chat`"), 0);
	$k_page = k_page($k_post, $set['p_str']);
	$page = page($k_page);
	$start = $set['p_str'] * $page - $set['p_str'];
	echo "<table class='post'>";
	if ($k_post == 0) {
		echo "  <div class='mess'>";
		echo "没有留言。";
		echo "  </div>";
	}
	$num = 0;
	$q = dbquery("SELECT * FROM `adm_chat` ORDER BY id DESC LIMIT $start, $set[p_str]");
	while ($post = dbassoc($q)) {
		/*-----------代码-----------*/
		if ($num == 0) {
			echo "  <div class='nav1'>";
			$num = 1;
		} elseif ($num == 1) {
			echo "  <div class='nav2'>";
			$num = 0;
		}
		/*---------------------------*/
		$ank = get_user($post['id_user']);
		if ($set['set_show_icon'] == 2) {
			user::avatar($ank['id']);
		}
		echo " " . group($ank['id']) . " <a href='/info.php?id=$ank[id]'>$ank[nick]</a> <a href='?response=$ank[id]'>[*]</a>";
		echo " " . medal($ank['id']) . " " . online($ank['id']) . " (" . vremja($post['time']) . ")<br />";
		echo output_text($post['msg']) . "<br />";
		if (user_access('guest_delete'))
			echo '<div style="float:right;"><a href="delete.php?id=' . $post['id'] . '"><img src="/style/icons/delete.gif" alt="*"></a></div><br />';
		echo "   </div>";
	}
	echo "</table>";
	if ($k_page > 1) str('?', $k_page, $page); // 输出页数
	if (isset($user) || (isset($set['write_guest']) && $set['write_guest'] == 1 && (!isset($_SESSION['antiflood']) || $_SESSION['antiflood'] < $time - 300))) {
		echo "<form method=\"post\" name='message' action=\"?$go_otv\">";
		if ($set['web'] && is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
			include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
		else
			echo "$tPanel<textarea name=\"msg\">$otvet</textarea><br />";
		echo "<input value=\"发送\" type=\"submit\" />";
		echo "</form>";
	}
	echo "<div class='foot'>";
	echo "<img src='/style/icons/str.gif' alt='*' /> <a href='who.php'>谁来了？</a><br />";
	echo "</div>";
	include 'inc/admin_form.php';
	echo "<div class='foot'>";
	echo "<img src='/style/icons/str2.gif' alt='*' /> <a href='/plugins/admin/'>管理</a><br />";
	echo "</div>";
}
include_once '../../../sys/inc/tfoot.php';
