<?php
/*
==================================
个人信息保护政策
用于其他模块时
定义变数 $ank
$ank = user::get_user(object);
include H.'sys/add/user.privace.php';
==================================
*/

// 用户设置
$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '{$ank['id']}'  LIMIT 1"));

if (isset($user)) {

}

/*
* 如果用户设置了私有设置
*/
if (isset($user)) {
	// 是朋友状态吗？
	$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '{$user['id']}' AND `frend` = '{$ank['id']}') OR (`user` = '{$ank['id']}' AND `frend` = '{$user['id']}') LIMIT 1"), 0);

	// 朋友确认
	$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '{$user['id']}' AND `to` = '{$ank['id']}') OR (`user` = '{$ank['id']}' AND `to` = '{$user['id']}') LIMIT 1"), 0);
}


if ($uSet['privat_str'] == 2) {
 	if (!isset($user)) {
		if ($ank['group_access'] > 1) echo '<div class="err">' . $ank['group_name'] . '</div>';
		echo '<div class="nav1">'. user::nick($ank['id'], 1, 1, 0);
		echo '</div>';
		echo '<div class="nav2">';
		echo user::avatar($ank['id']);
		echo '</div>';
		echo '<div class="mess">';
		echo '只有用户的朋友才能查看用户的页面！';
		echo '</div>';
		include_once H . 'sys/inc/tfoot.php';
	} elseif ($ank['id'] != $user['id'] && $frend != 2 && $user['group_access'] <= 1 && $user['group_access'] <= $ank['group_access']) {
		if ($ank['group_access'] > 1) echo '<div class="err">' . $ank['group_name'] . '</div>';
		echo '<div class="nav1">'. user::nick($ank['id'], 1, 1, 0);
		echo '</div>';
		echo '<div class="nav2">';
		echo user::avatar($ank['id']);
		echo '</div>';
		echo '<div class="mess">';
		echo '只有用户的朋友才能查看用户的页面！';
		echo '</div>';
		// 朋友
		echo '<div class="nav1">';
		echo '<img src="/style/icons/druzya.png" alt="*"/>';
		if ($frend_new == 0 && $frend == 0) {
			echo '<a href="/user/frends/create.php?add=' . $ank['id'] . '">添加为好友</a><br />';
		} elseif ($frend_new == 1) {
			echo '<a href="/user/frends/create.php?otm=' . $ank['id'] . '">拒绝申请</a><br />';
		} elseif ($frend == 2) {
			echo '<a href="/user/frends/create.php?del=' . $ank['id'] . '">把...从朋友中除名</a><br />';
		}
		echo '</div>';
		include_once H . 'sys/inc/tfoot.php';
	}
}

if ($uSet['privat_str'] == 0) {
	if (!isset($user)) {
		if ($ank['group_access'] > 1) echo '<div class="err">' . $ank['group_name'] . '</div>';
		echo '<div class="nav1">'. user::nick($ank['id'], 1, 1, 0);
		echo '</div>';
		echo '<div class="nav2">';
		echo user::avatar($ank['id']);
		echo '</div>';
		echo '<div class="mess">';
		echo '用户完全限制了对其页面的访问!';
		echo '</div>';
		include_once H . 'sys/inc/tfoot.php';
	} elseif ($ank['id'] != $user['id']) {
		if ($user['group_access'] <= 1 || $user['group_access'] <= $ank['group_access']) {
			if ($ank['group_access'] > 1) echo '<div class="err">' . $ank['group_name'] . '</div>';
			echo '<div class="nav1">'. user::nick($ank['id'], 1, 1, 0);
			echo '</div>';
			echo '<div class="nav2">';
			echo user::avatar($ank['id']);
			echo '</div>';
			echo '<div class="mess">';
			echo '用户完全限制了对其页面的访问!';
			echo '</div>';
			include_once H . 'sys/inc/tfoot.php';
		}
	}
}