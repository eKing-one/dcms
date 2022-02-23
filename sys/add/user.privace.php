<?
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
$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));
// 是朋友状态吗？
$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE 
 (`user` = '$user[id]' AND `frend` = '$ank[id]') OR 
 (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"), 0);
// 朋友确认
$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE 
 (`user` = '$user[id]' AND `to` = '$ank[id]') OR 
 (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"), 0);
/*
* 如果你不是专辑主持人，
* 你不是专辑的主人
* 你的职位等于或小于专辑主持人的职位
* 是隐私起作用，还是被忽视
*/
if ($ank['id'] != $user['id'] && ($user['group_access'] == 0 || $user['group_access'] <= $ank['group_access'])) {
	// 如果 pp 具有私有配置，则开始输出
	if (($uSet['privat_str'] == 2 && $frend != 2) || $uSet['privat_str'] == 0) {
		if ($ank['group_access'] > 1)
			echo '<div class="err">' . $ank['group_name'] . '</div>';
		echo '<div class="nav1">'. user::nick($ank['id'],1,1,0);
		echo '</div>';
		echo '<div class="nav2">';
		echo user::avatar($ank['id']);
		echo '</div>';
	}
	if ($uSet['privat_str'] == 2 && $frend != 2) // 只要有朋友的话
	{
		echo '<div class="mess">';
		echo '只有用户的朋友才能查看用户的页面！';
		echo '</div>';
		// В друзья
		if (isset($user)) {
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
		}
		include_once H . 'sys/inc/tfoot.php';
		exit;
	}
	// 如果页面关闭
	if ($uSet['privat_str'] == 0) {
		echo '<div class="mess">';
		echo '用户完全限制了对其页面的访问!';
		echo '</div>';
		include_once H . 'sys/inc/tfoot.php';
		exit;
	}
}
