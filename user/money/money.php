<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/adm_check.php';
include_once '../../sys/inc/user.php';
$set['title']='积分转移';
include_once '../../sys/inc/thead.php';
title();
if (!isset($user))header("location: /index.php?");
err();
aut();
if (isset($user) && isset($_POST['title']) && $_POST['title'] > 0) {
	if ($_POST['title']==1) {
		$money="500";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==2) {
		$money="1000";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==3) {
		$money="1500";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==4) {
		$money="2000";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==5) {
		$money="2500";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==6) {
		$money="3000";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==7) {
		$money="3500";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==8) {
		$money="4000";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==9) {
		$money="4500";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==10) {
		$money="5000";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==11) {
		$money="5500";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==12) {
		$money="6000";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==13) {
		$money="6500";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==14) {
		$money="7000";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==15) {
		$money="7500";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==16) {
		$money="8000";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==17) {
		$money="8500";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==18) {
		$money="9000";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==19) {
		$money="9500";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==20) {
		$money="10000";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']>20) {
		$err='点的转移是可能的不超过20 '.$sm.' 对于一个操作';
	}
	if ($user['balls'] >= $money) {
		if (!$err) {
			dbquery("UPDATE `user` SET `balls` = '" . ($user['balls']-$money) . "' WHERE `id` = '$user[id]' LIMIT 1");
			dbquery("UPDATE `user` SET `money` = '" . ($user['money']+$m) . "' WHERE `id` = '$user[id]' LIMIT 1");
			$_SESSION['message'] = '恭喜，账户的补货已经顺利完成';
			header("Location: ?");
			exit;
		}
	} else {
		err('没有足够的积分来完成操作');
	}
}
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/user/info.php'>$user[nick]</a> | 交换$sMonet[0]<br />";
echo "</div>";
echo "<div class='mess'>";
echo "你有 <b>$user[balls]</b> 积分。";
echo "</div>";
echo "<div class='mess'>";
echo "在这项服务的帮助下，您将能够将获得的活动积分兑换成 $sMonet[2]<br />
<b>在 ".date("Y.m.d")."兑换比例 : 1 $sMonet[1] &rArr; 500 积分.</b>";
echo "</div>";
if (isset($user) && $user['balls']>=500) {
	echo "<form class='main' method=\"post\" action=\"money.php\">";
	echo "数量:<br /><select name='title'>";
	echo "<option value='1' selected='selected'><b>1 $sMonet[1]</b></option>";
	echo "<option value='2' ><b>2 $sMonet[2]</b></option>";
	echo "<option value='3' ><b>3 $sMonet[2]</b></option>";
	echo "<option value='4' ><b>4 $sMonet[2]</b></option>";
	echo "<option value='5' ><b>5 $sMonet[0]</b></option>";
	echo "<option value='6' ><b>6 $sMonet[0]</b></option>";
	echo "<option value='7' ><b>7 $sMonet[0]</b></option>";
	echo "</select><br />";
	echo "<input value=\"接收\" type=\"submit\" />";
	echo "</form>";
} else {
	echo "<div class='err'>";
	echo "没有足够的积分来完成操作";
	echo "</div>";
}
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/user/info.php'>$user[nick]</a> | 交换$sMonet[0]<br />";
echo "</div>";
include_once '../../sys/inc/tfoot.php';
?>