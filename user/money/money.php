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
$set['title']='积分兑换';
include_once '../../sys/inc/thead.php';
title();
if (!isset($user))header("location: /index.php?");
err();
aut();
if (isset($user) && isset($_POST['title']) && $_POST['title'] > 0) {
	if ($_POST['title']==1) {
		$money="50";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==2) {
		$money="100";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==3) {
		$money="150";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==4) {
		$money="200";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==5) {
		$money="250";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==6) {
		$money="300";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==7) {
		$money="350";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==8) {
		$money="400";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==9) {
		$money="450";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==10) {
		$money="500";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==11) {
		$money="550";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==12) {
		$money="600";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==13) {
		$money="650";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==14) {
		$money="700";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==15) {
		$money="750";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==16) {
		$money="800";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==17) {
		$money="850";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==18) {
		$money="900";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==19) {
		$money="950";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']==20) {
		$money="1000";
		$m="".intval($_POST['title'])."";
	} elseif ($_POST['title']>20) {
		$err='不超过20硬币 '.$sm.' 在一次兑换中';
	}
	if ($user['balls'] >= $money) {
		if (!$err) {
			dbquery("UPDATE `user` SET `balls` = '" . ($user['balls']-$money) . "' WHERE `id` = '$user[id]' LIMIT 1");
			dbquery("UPDATE `user` SET `money` = '" . ($user['money']+$m) . "' WHERE `id` = '$user[id]' LIMIT 1");
			$_SESSION['message'] = '恭喜，积分已顺利兑换';
			header("Location: ?");
			exit;
		}
	} else {
		err('没有足够的积分来兑换');
	}
}
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/user/info.php'>$user[nick]</a> | 交换$sMonet[0]<br />";
echo "</div>";
echo "<div class='mess'>";
echo "你拥有 <b>$user[balls]</b> 积分";
echo "</div>";
echo "<div class='mess'>";
echo "您将能够将获得的活动积分兑换成硬币 $sMonet[2]<br />
<b>在 ".date("Y.m.d")."兑换比例 : 1 $sMonet[1] &rArr; 50 积分.</b>";
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
	echo "<input value=\"兑换\" type=\"submit\" />";
	echo "</form>";
} else {
	echo "<div class='err'>";
	echo "没有足够的积分来兑换";
	echo "</div>";
}
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/user/info.php'>$user[nick]</a> | 交换$sMonet[0]<br />";
echo "</div>";
include_once '../../sys/inc/tfoot.php';
?>