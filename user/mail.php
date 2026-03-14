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
	) {
	$_SESSION['refer'] = str_replace('&', '&amp;', preg_replace('#^http://[^/]*/#', '/', $_SERVER['HTTP_REFERER']));
}

if (!isset($_GET['id'])) {
	header("Location: conts.php?" . session_id());
	exit;
}

$ank = user::get_user($_GET['id']);
if (!$ank) {
	header("Location: conts.php?" . session_id());
	exit;
}

$privateMessage = new GuGuan123\dcms\Services\PrivateMessage($set, $db);

if ($user['group_access'] < 1 && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'all' AND `id_user` = '$ank[id]' AND (`time` > '$time' OR `view` = '0')"), 0) != 0) {
	// 如果用户被封禁
	$ank = user::get_user($ank['id']);
	$set['title'] = $ank['nick'] . ' -  '; //网页标题
	include_once '../sys/inc/thead.php';
	title();
	aut();
	echo "<div class='nav2'>";
	echo "<b><font color=red>此用户被封禁！</font></b><br /> ";
	echo "</div>";
	include_once '../sys/inc/tfoot.php';
} else {
	// 将邮件标记为已读
	dbquery("UPDATE `mail` SET `read` = '1' WHERE `id_kont` = '$user[id]' AND `id_user` = '$ank[id]'");

	$set['title'] = '私信: ' . $ank['nick'];
	include_once '../sys/inc/thead.php';
	title();
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
		echo "<form class='nav1' method='post' action='?id=$ank[id]&amp;spam=$mess[id]'>";
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
	echo "<img src='../style/icons/str2.gif' alt='*'> <a href='/user?id=$ank[id]'>返回</a><br />";
	echo "</div>";
	include_once '../sys/inc/tfoot.php';
}

/*
==================================
The End
==================================
*/
// 添加到联系人
if ($user['add_konts'] == 2 && dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"), 0) == 0)
	dbquery("INSERT INTO `users_konts` (`id_user`, `id_kont`, `time`) VALUES ('$user[id]', '$ank[id]', '$time')");

// 更新联系信息
dbquery("UPDATE `users_konts` SET `new_msg` = '0' WHERE `id_kont` = '$ank[id]' AND `id_user` = '$user[id]' LIMIT 1");

if (isset($_POST['refresh'])) {
	header("Location: ?id=$ank[id]" . session_id());
	exit;
}

if (isset($_POST['msg']) && $ank['id'] != 0 && !isset($_GET['spam'])) {
	if ($user['level'] == 0 && dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_kont` = '$user[id]' AND `id_user` = '$ank[id]'"), 0) == 0) {
		if (!isset($_SESSION['captcha'])) {
			$err[] = '验证码错误';
		} if (!isset($_POST['chislo'])) {
			$err[] = '输入验证码';
		} elseif ($_POST['chislo'] == null) {
			$err[] = '输入验证码';
		} elseif ($_POST['chislo'] != $_SESSION['captcha']) {
			$err[] = '检查验证码是否输入正确';
		}
	}

	$msg = $_POST['msg'];
	if (isset($_POST['translit']) && $_POST['translit'] == 1) $msg = translit($msg);
	if (strlen2($msg) > 1024) $err[] = '信息多余1024字';
	if (strlen2($msg) < 1) $err[] = '信息不能少于1字';

	$mat = antimat($msg);
	if ($mat) $err[] = '在消息的文本中发现了一个非法字符: ' . $mat;

	$lastMailMsg = $db->query("SELECT `msg`, `time` FROM `mail` WHERE `id_user` = ? AND `id_kont` = ? ORDER BY `time` DESC LIMIT 1", [$user['id'], $ank['id']]);
	if ($lastMailMsg['msg'] == $msg && (time() - $lastMailMsg['time']) < 300) $err[] = '重复了上一条信息';

	if (!isset($err)) {
		// 发送消息
		$db->insert('INSERT INTO `mail` (`id_user`, `id_kont`, `msg`, `time`) values(?, ?, ?, ?)', [$user['id'], $ank['id'], $msg, $time]);
		// 添加到联系人
		if ($user['add_konts'] == 1 && dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"), 0) == 0)
			dbquery("INSERT INTO `users_konts` (`id_user`, `id_kont`, `time`) VALUES ('$user[id]', '$ank[id]', '$time')");
		// 更新联系信息
		dbquery("UPDATE `users_konts` SET `time` = '$time' WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' OR `id_user` = '$ank[id]' AND `id_kont` = '$user[id]'");
		$_SESSION['message'] = '信息发送成功';
		header("Location: ?id=$ank[id]");
		exit;
	}
}

if (isset($_GET['delete']) && $_GET['delete'] != 'all') {
	$mess = $privateMessage->get(intval($_GET['delete']));

	if (isset($_POST['continue']) && $_POST['continue'] == 'yes') {
		if ($mess['sender_id'] == $user['id'] || $mess['receiver_id'] == $user['id']) {
			if ($mess['unlink'] != $user['id'] && $mess['unlink'] != 0) {
				$privateMessage->delete($mess['id']);
			} else {
				$privateMessage->unlink($mess['id'], $user['id']);
			}
			$_SESSION['message'] = '已删除';
			header("Location: ?id=$ank[id]");
			exit;
		}
	} else {
		echo '<div class="mess">是否删除此消息？</div>';
		echo '<div class="nav1">';
		echo $mess['msg'];
		echo '</div>';
		echo '<form method="post" name="delete" action="?id=' . $ank['id'] . '&amp;delete=' . $mess['id'] . '">';
		echo '<input type="hidden" name="continue" value="yes">';
		echo '<input value="确认删除" type="submit">';
		echo '</form>';
		require_once '../sys/inc/tfoot.php';
	}
}

if (isset($_GET['delete']) && $_GET['delete'] == 'all') {
	if (isset($_POST['continue']) && $_POST['continue'] == 'yes') {
		$privateMessage->clear_all($user['id'], $ank['id']);
		$_SESSION['message'] = '已删除所有消息';
		header("Location: ?id=$ank[id]");
		exit;
	} else {
		echo '<div class="mess">是否清空消息？</div>';
		echo '<form method="post" name="delete" action="?id=' . $ank['id'] . '&amp;delete=all">';
		echo '<input type="hidden" name="continue" value="yes">';
		echo '<input value="确认删除" type="submit">';
		echo '</form>';
		require_once '../sys/inc/tfoot.php';
	}
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
	if ($uSet['privat_mail'] == 2 && $frend != 2) {
		// 如果只为了朋友
		echo '<div class="mess">';
		echo '只有他的朋友可以写消息给用户！';
		echo '</div>';
		echo '<div class="nav1">';
		if ($frend_new == 0 && $frend == 0) {
			echo "<img src='../style/icons/druzya.png' alt='*'/> <a href='frends/create.php?add=" . $ank['id'] . "'>添加到朋友</a><br />";
		} elseif ($frend_new == 1) {
			echo "<img src='../style/icons/druzya.png' alt='*'/> <a href='frends/create.php?otm=$ank[id]'>拒绝申请</a><br />";
		} elseif ($frend == 2) {
			echo "<img src='../style/icons/druzya.png' alt='*'/> <a href='frends/create.php?del=$ank[id]'>从朋友中删除</a><br />";
		}
		echo "</div>";
		$block = false;
	} if ($uSet['privat_mail'] == 0) {
		// 如果关闭
		echo '<div class="mess">';
		echo '用户已禁止向他写信息！';
		echo '</div>';
		$block = false;
	}
}

echo "<div class='nav2'>";
echo "与…通信 " . group($ank['id']) . " <a href='info.php?id=" . $ank['id'] . "'>" . $ank['nick'] . "</a> " . medal($ank['id']) . online($ank['id']) . " <span style='float:right;'>";

if (dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"), 0) == 1) {
	$kont = dbarray(dbquery("SELECT * FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"));
	echo "<a href='conts.php?type=$kont[type]&amp;act=del&amp;id=$ank[id]'><img src='../style/icons/cross_r.gif' alt='*'></a></span><br/></div>";
} else {
	echo "<a href='conts.php?type=common&amp;act=add&amp;id=$ank[id]'><img src='../style/icons/lj.gif' alt='*'> 添加到联系人</a></span><br/></div>";
}

$rt = time() - 600;

if ($ank['id'] != 0 && $ank['date_last'] < $rt) {
	echo "<div class='plug'>用户 {$ank['nick']} 不在在线。留下你的信息，他会稍后阅读。</div>";
}

if ($ank['id'] != 0 && $block == true) {
    if (dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"), 0) == 1) {
        $kont = dbarray(dbquery("SELECT * FROM `users_konts` WHERE `id_user` = '$user[id]' AND `id_kont` = '$ank[id]'"));
    } else {
        echo "<div class='foot'><img src='../style/icons/str.gif' alt='*'> <a href='conts.php?type=common&amp;act=add&amp;id=$ank[id]'>添加到联系人列表</a></div>";
    }
    echo "<form method='post' name='message' action='?id=$ank[id]'>";
    if ($set['web'] && is_file('../style/themes/' . $set['set_them'] . '/altername_post_form.php'))
        include_once '../style/themes/' . $set['set_them'] . '/altername_post_form.php';
    else
        echo $tPanel . "<textarea name='msg'></textarea><br />";
    if ($user['level'] == 0 && dbresult(dbquery("SELECT COUNT(*) FROM `users_konts` WHERE `id_kont` = '$user[id]' AND `id_user` = '$ank[id]'"), 0) == 0)
        echo "<img src='../captcha.php?SESS=$sess' width='100' height='30' alt='核证号码' /><br /><input name='chislo' size='5' maxlength='5' value='' type='text' /><br/>";
    echo "<input type='submit' name='send' value='发送' />";
    echo "<input type='submit' name='refresh' value='清空' />";
    echo "</form>";
}

echo "<div class='foot'><img src='../style/icons/str.gif' alt='*'> <a href='conts.php?" . (isset($kont) ? 'type=' . $kont['type'] : null) . "'>所有联系人</a></div>";

echo "<table class='post'>";
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `mail` WHERE `unlink` != '$user[id]' AND `id_user` = '$user[id]' AND `id_kont` = '$ank[id]' OR `id_user` = '$ank[id]' AND `id_kont` = '$user[id]' AND  `unlink` != '$user[id]'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
if ($k_post == 0) {
	echo '<div class="mess">没有评论</div>';
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
			echo ' <b><span style="color:green">我发送给</span></b><a href="info.php?id=' . $ank2['id'] . '"><b>' . $ank['nick'] . '</b></a>';
		} else {
			echo " " . group($ank2['id']) . " <a href=\"info.php?id=$ank2[id]\">$ank2[nick]</a>";
			echo "" . medal($ank2['id']) . " " . online($ank2['id']) . " ";
		}
	} else if ($ank2['id'] == 0) {
		echo "<b>系统</b>";
	} else {
		echo "[删除！]";
	}
	echo '<span style="float:right;color:#666;font-size:small;"> ' . vremja($post['time']) . '</span> ';
	if ($post['read'] == 0) echo "(未读)<br />";
	echo "<br/>" . output_text($post['msg']) . "";
	echo "<div style='text-align:right;'>";
	if ($ank2['id'] != $user['id']) echo "<a href=\"?id=$ank[id]&amp;page=$page&amp;spam=$post[id]\"><img src='../style/icons/blicon.gif' alt='*' title='举报'>举报</a>";
	echo "<a href=\"?id=$ank[id]&amp;page=$page&amp;delete=$post[id]\"><img src='../style/icons/delete.gif' alt='*' title='删除此消息'> 删除</a>";
	echo "   </div>";
	echo "   </div>";
}
echo "</table>";

if ($k_page > 1) str("?id=$ank[id]&amp;", $k_page, $page); // 输出页数

echo "<div class='foot'>";
echo "<img src='../style/icons/str.gif' alt='*'> <a href='?id=$ank[id]&amp;page=$page&amp;delete=all'>清空所有信息</a><br />";
echo "</div>";

include_once '../sys/inc/tfoot.php';