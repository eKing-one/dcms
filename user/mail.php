<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
only_reg();
if ((!isset($_SESSION['refer']) || $_SESSION['refer'] == NULL)
	&& isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL &&
	!preg_match('#mail\.php#', $_SERVER['HTTP_REFERER'])
)
	$_SESSION['refer'] = str_replace('&', '&amp;', preg_replace('#^http://[^/]*/#', '/', $_SERVER['HTTP_REFERER']));
if (!isset($_GET['id'])) {
	header("Location: /user/conts.php?" . SID);
	exit;
}
$ank = user::get_user($_GET['id']);
if (!$ank) {
	header("Location: /user/conts.php?" . SID);
	exit;
}
// помечаем сообщения как прочитанные
dbquery("UPDATE `mail` SET `read` = '1' WHERE `id_kont` = '$user[id]' AND `id_user` = '$ank[id]'");
$set['title'] = '邮箱: ' . $ank['nick'];
include_once '../sys/inc/thead.php';
title();
/* Бан пользователя */
if ($user['group_access'] < 1 && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'all' AND `id_user` = '$ank[id]' AND (`time` > '$time' OR `view` = '0')"), 0) != 0) {
	$ank = user::get_user($ank['id']);
	$set['title'] = $ank['nick'] . ' -  '; //网页标题
	include_once '../sys/inc/thead.php';
	title();
	aut();
	echo "<div class='nav2'>";
	echo "<b><font color=red>此用户被阻止！</font></b><br /> ";
	echo "</div>";
	include_once '../sys/inc/tfoot.php';
	exit;
}
/*
================================
用户投诉模块
信件或内容
因分区不同而不同
================================
*/
if (isset($_GET['spam'])  &&  $ank['id'] != 0) {
	$mess = dbassoc(dbquery("SELECT * FROM `mail` WHERE `id` = '" . intval($_GET['spam']) . "' limit 1"));
	$spamer = user::get_user($mess['id_user']);
	if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'mail'"), 0) == 0) {
		if (isset($_POST['msg'])) {
			if ($mess['id_kont'] == $user['id']) {
				$msg = my_esc($_POST['msg']);
				if (strlen2($msg) < 3) $err = '更详细地说明投诉的原因';
				if (strlen2($msg) > 1512) $err = '文本的长度超过512个字符的限制';
				if (isset($_POST['types'])) $types = intval($_POST['types']);
				else $types = '0';
				if (!isset($err)) {
					dbquery("INSERT INTO `spamus` (`id_user`, `msg`, `id_spam`, `time`, `types`, `razdel`, `spam`) values('$user[id]', '$msg', '$spamer[id]', '$time', '$types', 'mail', '" . my_esc($mess['msg']) . "')");
					$_SESSION['message'] = '申请已发出';
					header("Location: ?id=$ank[id]&spam=$mess[id]");
					exit;
				}
			}
		}
	}
	aut();
	err();
	if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'mail'"), 0) == 0) {
		echo "<div class='mess'>若你认为某条言论不合适、违反了网站规则，可以举报，管理员收到后会尽快处理。
		但是，请不要瞎举报给管理添乱，若多次发出无意义的举报，将同样会按网站规则进行处罚。
		如果你真的很讨厌某位用户的言论，你可以选择将其拉黑，而不是将消息逐条举报。逐条举报会大大降低管理员处理举报的效率，甚至导致举报处理任务大量积压。</div>";
		echo "<form class='nav1' method='post' action='/user/mail.php?id=$ank[id]&amp;spam=$mess[id]'>";
		echo "<b>用户:</b> ". user::nick($spamer['id'],1,0,0);
		echo "" . medal($spamer['id']) . " " . online($spamer['id']) . " (" . vremja($mess['time']) . ")<br />";
		echo "<b>违规：</b> <font color='green'>" . output_text($mess['msg']) . "</font><br />";
		echo "原因：<br /><select name='types'>";
		echo "<option value='1' selected='selected'>垃圾邮件/广告/日记/帖子</option>";
		echo "<option value='2' selected='selected'>欺诈行为</option>";
		echo "<option value='3' selected='selected'>引战</option>";
		echo "<option value='4' selected='selected'>网络暴力</option>";
		echo "<option value='0' selected='selected'>其他</option>";
		echo "</select><br />";
		echo "评论:";
		echo $tPanel . "<textarea name=\"msg\"></textarea><br />";
		echo "<input value=\"发送\" type=\"submit\" />";
		echo "</form>";
	} else {
		echo "<div class='mess'>投诉有关<font color='green'>$spamer[nick]</font> 它将在不久的将来考虑。</div>";
	}
	echo "<div class='foot'>";
	echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/user/mail.php?id=$ank[id]'>返回</a><br />";
	echo "</div>";
	include_once '../sys/inc/tfoot.php';
}
/*
==================================
The End
==================================
*/
// добавляем в контакты
if ($user['add_konts'] == 2 && dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"), 0) == 0)
	dbquery("INSERT INTO `users_konts` (`id_user`, `id_kont`, `time`) VALUES ('$user[id]', '$ank[id]', '$time')");
// обновление сведений о контакте
dbquery("UPDATE `users_konts` SET `new_msg` = '0' WHERE `id_kont` = '$ank[id]' AND `id_user` = '$user[id]' LIMIT 1");
if (isset($_POST['refresh'])) {
	header("Location: /user/mail.php?id=$ank[id]" . SID);
	exit;
}
if (isset($_POST['msg']) && $ank['id'] != 0 && !isset($_GET['spam'])) {
	if ($user['level'] == 0 && dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_kont` = '$user[id]' AND `id_user` = '$ank[id]'"), 0) == 0) {
		if (!isset($_SESSION['captcha'])) $err[] = '验证码错误';
		if (!isset($_POST['chislo'])) $err[] = '输入验证码';
		elseif ($_POST['chislo'] == null) $err[] = '输入验证码';
		elseif ($_POST['chislo'] != $_SESSION['captcha']) $err[] = '检查验证码是否输入正确';
	}
	$msg = $_POST['msg'];
	if (isset($_POST['translit']) && $_POST['translit'] == 1) $msg = translit($msg);
	if (strlen2($msg) > 1024) $err[] = '信息多余1024字';
	if (strlen2($msg) < 1) $err[] = '信息不能少于1字';
	$mat = antimat($msg);
	if ($mat) $err[] = '在消息的文本中发现了一个非法字符: ' . $mat;
	if (!isset($err) && dbresult(dbquery("SELECT COUNT(*) FROM `mail` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' AND `time` > '" . ($time - 360) . "' AND `msg` = '" . my_esc($msg) . "'"), 0) == 0) {
		// отправка сообщения
		dbquery("INSERT INTO `mail` (`id_user`, `id_kont`, `msg`, `time`) values('$user[id]', '$ank[id]', '" . my_esc($msg) . "', '$time')");
		// добавляем в контакты
		if ($user['add_konts'] == 1 && dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"), 0) == 0)
			dbquery("INSERT INTO `users_konts` (`id_user`, `id_kont`, `time`) VALUES ('$user[id]', '$ank[id]', '$time')");
		// обновление сведений о контакте
		dbquery("UPDATE `users_konts` SET `time` = '$time' WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' OR `id_user` = '$ank[id]' AND `id_kont` = '$user[id]'");
		$_SESSION['message'] = '信息发送成功';
		header("Location: ?id=$ank[id]");
		exit;
	}
}
if (isset($_GET['delete'])  && $_GET['delete'] != 'add') {
	$mess = dbassoc(dbquery("SELECT * FROM `mail` WHERE `id` = '" . intval($_GET['delete']) . "' limit 1"));
	if ($mess['id_user'] == $user['id'] || $mess['id_kont'] == $user['id']) {
		if ($mess['unlink'] != $user['id'] && $mess['unlink'] != 0)
			dbquery("DELETE FROM `mail` WHERE `id` = '" . $mess['id'] . "'");
		else
			dbquery("UPDATE `mail` SET `unlink` = '$user[id]' WHERE `id` = '$mess[id]' LIMIT 1");
		$_SESSION['message'] = '邮件删除';
		header("Location: ?id=$ank[id]");
		exit;
	}
}
if (isset($_GET['delete']) && $_GET['delete'] == 'add') {
	dbquery("DELETE FROM `mail` WHERE `unlink` = '$ank[id]'  AND `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' OR `id_user` = '$ank[id]' AND `id_kont` = '$user[id]' AND `unlink` = '$ank[id]'  ");
	dbquery("UPDATE `mail` SET `unlink` = '$user[id]' WHERE  `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' OR `id_user` = '$ank[id]' AND `id_kont` = '$user[id]'");
	$_SESSION['message'] = '已删除的邮件';
	header("Location: ?id=$ank[id]");
	exit;
}
aut();
err();
/*
==================================
保护用户的电子邮件个人信息
==================================
*/
$block = true;
$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));
$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$ank[id]') OR (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"), 0);
$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$ank[id]') OR (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"), 0);
if ($user['group_access'] == 0) {
	if ($uSet['privat_mail'] == 2 && $frend != 2) // Если только для друзей
	{
		echo '<div class="mess">';
		echo '只有他的朋友可以写消息给用户！';
		echo '</div>';
		echo '<div class="nav1">';
		if ($frend_new == 0 && $frend == 0) {
			echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?add=" . $ank['id'] . "'>添加到朋友</a><br />";
		} elseif ($frend_new == 1) {
			echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?otm=$ank[id]'>拒绝申请</a><br />";
		} elseif ($frend == 2) {
			echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?del=$ank[id]'>从朋友中删除</a><br />";
		}
		echo "</div>";
		$block = false;
	}
	if ($uSet['privat_mail'] == 0) // Если закрыта
	{
		echo '<div class="mess">';
		echo '用户已禁止向他写信息！';
		echo '</div>';
		$block = false;
	}
}
echo "<div class='nav2'>";
echo "与…通信 " . group($ank['id']) . "
 <a href='/user/info.php?id=" . $ank['id'] . "'>" . $ank['nick'] . "</a> " . medal($ank['id']) . online($ank['id']) . " <span style='float:right;'>";
if (dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"), 0) == 1) {
	$kont = dbarray(dbquery("SELECT * FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"));
	echo "<a href='/user/conts.php?type=$kont[type]&amp;act=del&amp;id=$ank[id]'><img src='/style/icons/cross_r.gif' alt='*'></a></span><br/></div>";
} else {
	echo "<a href='/user/conts.php?type=common&amp;act=add&amp;id=$ank[id]'><img src='/style/icons/lj.gif' alt='*'> 添加到联系人</a></span><br/></div>";
}
$rt = time() - 600;
if ($ank['id'] != 0 && $ank['date_last'] < $rt) {
	echo "<div class='plug'>";
	echo "用户 " . $ank['nick'] . " 不在在线。留下你的信息，他会稍后阅读。";
	echo "</div>";
}
if ($ank['id'] != 0 && $block == true) {
    if (dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"), 0) == 1) {
        $kont = dbarray(dbquery("SELECT * FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"));
    } else {
        echo "<div class='foot'><img src='/style/icons/str.gif' alt='*'> 
        <a href='/user/conts.php?type=common&amp;act=add&amp;id=$ank[id]'>添加到联系人列表</a></div>";
    }
    echo "<form method='post' name='message' action='/user/mail.php?id=$ank[id]'>";
    if ($set['web'] && is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
        include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
    else
        echo $tPanel . "<textarea name='msg'></textarea><br />";
    if ($user['level'] == 0 && dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_kont` = '$user[id]' AND `id_user` = '$ank[id]'"), 0) == 0)
        echo "<img src='/captcha.php?SESS=$sess' width='100' height='30' alt='核证号码' /><br /><input name='chislo' size='5' maxlength='5' value='' type='text' /><br/>";
    echo "<input type='submit' name='send' value='发送' />";
    echo "<input type='submit' name='refresh' value='清空' />";
    echo "</form>";
}

echo "<div class='foot'><img src='/style/icons/str.gif' alt='*'> 
	<a href='/user/conts.php?" . (isset($kont) ? 'type=' . $kont['type'] : null) . "'>所有联系人</a></div>";
echo "<table class='post'>";
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `mail` WHERE `unlink` != '$user[id]' AND `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' OR `id_user` = '$ank[id]' AND `id_kont` = '$user[id]' AND  `unlink` != '$user[id]'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
if ($k_post == 0) {
	echo "  <div class='mess'>";
	echo "没有评论";
	echo "  </div>";
}
$num = 0;
$q = dbquery("SELECT * FROM `mail` WHERE `unlink` != '$user[id]' AND `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' OR `id_user` = '$ank[id]' AND `id_kont` = '$user[id]' AND `unlink` != '$user[id]' ORDER BY id DESC LIMIT $start, $set[p_str]");
while ($post = dbarray($q)) {
	/*-----------代码-----------*/
	if ($num == 0) {
		echo "  <div class='nav1'>";
		$num = 1;
	} elseif ($num == 1) {
		echo "  <div class='nav2'>";
		$num = 0;
	}
	/*---------------------------*/
	$ank2 = user::get_user($post['id_user']);
	if ($set['set_show_icon'] == 2) {
		user::avatar($ank2['id']);
	} elseif ($set['set_show_icon'] == 1) {
		//echo "".user::avatar($ank2['id'])."";
	}
	if ($ank2 && $ank2['id']) {
		if ($ank2['id'] == $user['id']) {
			echo ' <b><span style="color:green">我发送给</span></b><a href="/user/info.php?id=' . $ank2['id'] . '"><b>' . $ank['nick'] . '</b></a>';
		} else {
			echo " " . group($ank2['id']) . " <a href=\"/user/info.php?id=$ank2[id]\">$ank2[nick]</a>";
			echo "" . medal($ank2['id']) . " " . online($ank2['id']) . " ";
		}
	} else if ($ank2['id'] == 0) {
		echo "<b>系统</b>";
	} else {
		echo "[Удален!]";
	}
	echo '<span style="float:right;color:#666;font-size:small;"> ' . vremja($post['time']) . '</span> ';
	if ($post['read'] == 0) echo "(未读)<br />";
	echo "<br/>" . output_text($post['msg']) . "";
	echo "<div style='text-align:right;'>";
	if ($ank2['id'] != $user['id']) echo "<a href=\"mail.php?id=$ank[id]&amp;page=$page&amp;spam=$post[id]\"><img src='/style/icons/blicon.gif' alt='*' title='举报'>举报</a>";
	echo "<a href=\"mail.php?id=$ank[id]&amp;page=$page&amp;delete=$post[id]\"><img src='/style/icons/delete.gif' alt='*' title='删除此消息'> 删除</a>";
	echo "   </div>";
	echo "   </div>";
}
echo "</table>";
if ($k_page > 1) str("mail.php?id=$ank[id]&amp;", $k_page, $page); // 输出页数
echo "<div class='foot'>";
echo "<img src='/style/icons/str.gif' alt='*'> <a href='mail.php?id=$ank[id]&amp;page=$page&amp;delete=add'>清除邮件</a><br />";
echo "</div>";
include_once '../sys/inc/tfoot.php';
?>
