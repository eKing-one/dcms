<?php
//个人资料页面
include_once '../../sys//inc/start.php';
include_once '../../sys//inc/compress.php';
include_once '../../sys//inc/sess.php';
include_once '../../sys//inc/home.php';
include_once '../../sys//inc/settings.php';
include_once '../../sys//inc/db_connect.php';
include_once '../../sys//inc/ipua.php';
include_once '../../sys//inc/fnc.php';
include_once '../../sys//inc/user.php';

// 检查用户是否登录
if (isset($user)) $ank['id'] = $user['id'];
if (isset($_GET['id'])) $ank['id'] = intval($_GET['id']);
if ($ank['id'] == 0) {
	$ank = user::get_user($ank['id']);
	$set['title'] = $ank['nick'] . ' - 个人资料 '; //网页标题
	include_once '../../sys/inc/thead.php';
	title();
	aut();
	/*
	==================================
	用户的页面隐私
	禁止查看个人资料
	==================================
	*/
	$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));
	$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$ank[id]') OR (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"), 0);
	$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$ank[id]') OR (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"), 0);
	if ($ank['id'] != $user['id'] && $user['group_access'] == 0) {
		if (($uSet['privat_str'] == 2 && $frend != 2) || $uSet['privat_str'] == 0) {	// 如果页面有私人设置，则开始输出
			if ($ank['group_access'] > 1) echo "<div class='err'>$ank[group_name]</div>";
			echo "<div class='nav1'>";
			echo group($ank['id']) . " $ank[nick] ";
			echo medal($ank['id']) . " " . online($ank['id']) . " ";
			echo "</div>";
			echo "<div class='nav2'>";
			echo user::avatar($ank['id'], true, 128, 128);
			echo "<br />";
		}
		if ($uSet['privat_str'] == 2 && $frend != 2) // 如果只是为了朋友
		{
			echo '<div class="mess">';
			echo '只有他的朋友才能查看用户的页面！';
			echo '</div>';
			// В друзья
			if (isset($user)) {
				echo '<div class="nav1">';
				if ($frend_new == 0 && $frend == 0) {
					echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?add=" . $ank['id'] . "'>添加到朋友</a><br />";
				} elseif ($frend_new == 1) {
					echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?otm=$ank[id]'>拒绝申请</a><br />";
				} elseif ($frend == 2) {
					echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?del=$ank[id]'>从朋友中删除</a><br />";
				}
				echo "</div>";
			}
			include_once '../../sys/inc/tfoot.php';
			exit;
		}
		if ($uSet['privat_str'] == 0) // Если закрыта
		{
			echo '<div class="mess">';
			echo '用户已禁止查看他的页面！';
			echo '</div>';
			include_once '../../sys/inc/tfoot.php';
			exit;
		}
	}
	echo "<span class=\"err\">$ank[group_name]</span><br />";
	if ($ank['ank_o_sebe'] != NULL) echo "<span class=\"ank_n\">关于自己:</span> <span class=\"ank_d\">$ank[ank_o_sebe]</span><br />";
	if (isset($_SESSION['refer']) && $_SESSION['refer'] != NULL && otkuda($_SESSION['refer']))
		echo "<div class='foot'>&laquo;<a href='$_SESSION[refer]'>" . otkuda($_SESSION['refer']) . "</a><br /></div>";
	include_once '../../sys//inc/tfoot.php';
	exit;
}

// 检查用户是否存在
$ank = user::get_user($ank['id']);
if (!$ank) {
	header("Location: /index.php?" . SID);
	exit;
}
//----------------------//
$timediff = dbresult(dbquery("SELECT `time` FROM `user` WHERE `id` = '$ank[id]' LIMIT 1", $db), 0);
$oneMinute = 60;
$oneHour = 60 * 60;
$hourfield = floor(($timediff) / $oneHour);
$minutefield = floor(($timediff - $hourfield * $oneHour) / $oneMinute);
$secondfield = floor(($timediff - $hourfield * $oneHour - $minutefield * $oneMinute));
$sHoursLeft = $hourfield;
$sHoursText = "小时";
$nHoursLeftLength = strlen($sHoursLeft);
$h_1 = substr($sHoursLeft, -1, 1);
if (substr($sHoursLeft, -2, 1) != 1 && $nHoursLeftLength > 1) {
	if ($h_1 == 2 || $h_1 == 3 || $h_1 == 4) {
		$sHoursText = "小时";
	} elseif ($h_1 == 1) {
		$sHoursText = "小时";
	}
}
if ($nHoursLeftLength == 1) {
	if ($h_1 == 2 || $h_1 == 3 || $h_1 == 4) {
		$sHoursText = "小时";
	} elseif ($h_1 == 1) {
		$sHoursText = "小时";
	}
}
$sMinsLeft = $minutefield;
$sMinsText = "分钟";
$nMinsLeftLength = strlen($sMinsLeft);
$m_1 = substr($sMinsLeft, -1, 1);
if ($nMinsLeftLength > 1 && substr($sMinsLeft, -2, 1) != 1) {
	if ($m_1 == 2 || $m_1 == 3 || $m_1 == 4) {
		$sMinsText = "分钟";
	} else if ($m_1 == 1) {
		$sMinsText = "一分钟";
	}
}
if ($nMinsLeftLength == 1) {
	if ($m_1 == 2 || $m_1 == 3 || $m_1 == 4) {
		$sMinsText = "分钟";
	} elseif ($m_1 == "1") {
		$sMinsText = "一分钟";
	}
}
$displaystring = "" .
	$sHoursLeft . " " .
	$sHoursText . " " .
	$sMinsLeft . " " .
	$sMinsText . " ";
if ($timediff < 0) $displaystring = '日期已经到了';
$set['title'] = $ank['nick'] . ' - 个人资料 '; //网页标题
include_once '../../sys/inc/thead.php';
title();
if ((!isset($_SESSION['refer']) || $_SESSION['refer'] == NULL)
	&& isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL &&
	!preg_match('#info\.php#', $_SERVER['HTTP_REFERER'])
)
	$_SESSION['refer'] = str_replace('&', '&amp;', preg_replace('#^http://[^/]*/#', '/', $_SERVER['HTTP_REFERER']));
aut();
if (isset($user) && $ank['id'] == $user['id']) {
	$name = "<a href='/user/info/edit.php?act=ank&amp;set=name'>";
	$date = "<a href='/user/info/edit.php?act=ank&amp;set=date'>";
	$gorod = "<a href='/user/info/edit.php?act=ank&amp;set=gorod'>";
	$osebe = "<a href='/user/info/edit.php?act=ank&amp;set=osebe'>";
	$pol = "<a href='/user/info/edit.php?act=ank&amp;set=pol'>";
	$mail = "<a href='/user/info/edit.php?act=ank&amp;set=mail'>";
	$icq = "<a href='/user/info/edit.php?act=ank&amp;set=icq'>";
	$skype = "<a href='/user/info/edit.php?act=ank&amp;set=skype'>";
	$mobile = "<a href='/user/info/edit.php?act=ank&amp;set=mobile'>";
	$a = "</a>";
} else {
	$name = "<font style='color : #005ba8; padding:1px;'>";
	$date =  "<font style='color : #005ba8; padding:1px;'>";
	$gorod = "<font style='color : #005ba8; padding:1px;'>";
	$osebe =   "<font style='color : #005ba8; padding:1px;'>";
	$pol =   "<font style='color : #005ba8; padding:1px;'>";
	$mail =   "<font style='color : #005ba8; padding:1px;'>";
	$icq =   "<font style='color : #005ba8; padding:1px;'>";
	$skype =   "<font style='color : #005ba8; padding:1px;'>";
	$mobile =   "<font style='color : #005ba8; padding:1px;'>";
	$a = "</font>";
}
if ($ank['group_access'] > 1) echo "<div class='err'>$ank[group_name]</div>";
echo "<div class='nav2'>";
echo "<span class=\"ank_n\">最后登录:</span> <span class=\"ank_d\">" . vremja($ank['date_last']) . "</span><br />";
echo "</div>";
echo "<div class='nav1'>";
echo user::avatar($ank['id'], true, 128, 128);
echo "</div>";
//-------------alex-borisi---------------//
if ($ank['rating'] >= 0 && $ank['rating'] <= 100) {
	echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$ank[rating]%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
} elseif ($ank['rating'] >= 100 && $ank['rating'] <= 200) {
	$rat = $ank['rating'] - 100;
	echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
} elseif ($ank['rating'] >= 200 && $ank['rating'] <= 300) {
	$rat = $ank['rating'] - 200;
	echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
} elseif ($ank['rating'] >= 300 && $ank['rating'] <= 400) {
	$rat = $ank['rating'] - 300;
	echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
} elseif ($ank['rating'] >= 400 && $ank['rating'] <= 500) {
	$rat = $ank['rating'] - 400;
	echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
} elseif ($ank['rating'] >= 500 && $ank['rating'] <= 600) {
	$rat = $ank['rating'] - 500;
	echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
} elseif ($ank['rating'] >= 600 && $ank['rating'] <= 700) {
	$rat = $ank['rating'] - 600;
	echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
} elseif ($ank['rating'] >= 700 && $ank['rating'] <= 800) {
	$rat = $ank['rating'] - 700;
	echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
} elseif ($ank['rating'] >= 800 && $ank['rating'] <= 900) {
	$rat = $ank['rating'] - 800;
	echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
} elseif ($ank['rating'] >= 900 && $ank['rating'] <= 1000) {
	$rat = $ank['rating'] - 900;
	echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
}
//-------------alex-borisi---------------//
if (isset($user) && $user['id'] != $ank['id']) {
	echo "<div class='nav2'>";
	echo "<img src='/style/icons/pochta.gif' alt='*' /> <a href=\"/user/mail.php?id=$ank[id]\"><b>私聊</b></a>";
	echo "</div>";
}
echo "<div class='nav2'>";
echo "<img src='/style/icons/photo.png' alt='*' /> <a href='/photo/$ank[id]/'><b>相片册</b></a><br />";
echo "</div>";
//-----------------积分、金币----------------//
echo "<div class='nav2'>";
echo "<b>ID: $ank[id]</b><br /> ";
echo "积分 (<font color='green'>$ank[balls]</font>)<br /> ";
echo $sMonet[2] . ' (' . $ank['money'] . ')<br />';
echo "<img src='/style/icons/time.png' alt='*' width='14'/> ($displaystring)<br />  ";
echo "</div>";
//---------------------------------------------//
//------------------个人信息-------------------//
echo "<div class='nav1'>";
if ($ank['ank_name'] != NULL)
	echo "$name<span class=\"ank_n\">姓名:</span>$a <span class=\"ank_d\">$ank[ank_name]</span><br />";
else
	echo "$name<span class=\"ank_n\">姓名:</span>$a<br />";
echo "$pol<span class=\"ank_n\">性别:</span>$a <span class=\"ank_d\">" . (($ank['pol'] == 1) ? '男' : '女') . "</span><br />";
if ($ank['ank_city'] != NULL)
	echo "$gorod<span class=\"ank_n\">城市:</span>$a <span class=\"ank_d\">" . output_text($ank['ank_city']) . "</span><br />";
else
	echo "$gorod<span class=\"ank_n\">城市:</span>$a<br />";
if ($ank['ank_d_r'] != NULL && $ank['ank_m_r'] != NULL && $ank['ank_g_r'] != NULL) {
	if ($ank['ank_m_r'] == 1) $ank['mes'] = '1';
	elseif ($ank['ank_m_r'] == 2) $ank['mes'] = '2';
	elseif ($ank['ank_m_r'] == 3) $ank['mes'] = '3';
	elseif ($ank['ank_m_r'] == 4) $ank['mes'] = '4';
	elseif ($ank['ank_m_r'] == 5) $ank['mes'] = '5';
	elseif ($ank['ank_m_r'] == 6) $ank['mes'] = '6';
	elseif ($ank['ank_m_r'] == 7) $ank['mes'] = '7';
	elseif ($ank['ank_m_r'] == 8) $ank['mes'] = '8';
	elseif ($ank['ank_m_r'] == 9) $ank['mes'] = '9';
	elseif ($ank['ank_m_r'] == 10) $ank['mes'] = '10';
	elseif ($ank['ank_m_r'] == 11) $ank['mes'] = '11';
	else $ank['mes'] = '12';
	echo "$date<span class=\"ank_n\">出生日期:</span>$a $ank[ank_g_r]/$ank[mes]/$ank[ank_d_r] <br />";
	$ank['ank_age'] = date("Y") - $ank['ank_g_r'];
	if (date("n") < $ank['ank_m_r']) $ank['ank_age'] = $ank['ank_age'] - 1;
	elseif (date("n") == $ank['ank_m_r'] && date("j") < $ank['ank_d_r']) $ank['ank_age'] = $ank['ank_age'] - 1;
	echo "<span class=\"ank_n\">年龄:</span> $ank[ank_age] ";
} elseif ($ank['ank_d_r'] != NULL && $ank['ank_m_r'] != NULL) {
	if ($ank['ank_m_r'] == 1) $ank['mes'] = '1';
	elseif ($ank['ank_m_r'] == 2) $ank['mes'] = '2';
	elseif ($ank['ank_m_r'] == 3) $ank['mes'] = '3';
	elseif ($ank['ank_m_r'] == 4) $ank['mes'] = '4';
	elseif ($ank['ank_m_r'] == 5) $ank['mes'] = '5';
	elseif ($ank['ank_m_r'] == 6) $ank['mes'] = '6';
	elseif ($ank['ank_m_r'] == 7) $ank['mes'] = '7';
	elseif ($ank['ank_m_r'] == 8) $ank['mes'] = '8';
	elseif ($ank['ank_m_r'] == 9) $ank['mes'] = '9';
	elseif ($ank['ank_m_r'] == 10) $ank['mes'] = '10';
	elseif ($ank['ank_m_r'] == 11) $ank['mes'] = '11';
	else $ank['mes'] = '12';
	echo "$date<span class=\"ank_n\">生日:</span>$a $ank[mes] $ank[ank_d_r]";
} else {
	echo "$date<span class=\"ank_n\">出生日期:</span>$a";
}
if ($ank['ank_d_r'] >= 19 && $ank['ank_m_r'] == 1) {
	echo "| 水瓶座<br />";
} elseif ($ank['ank_d_r'] <= 19 && $ank['ank_m_r'] == 2) {
	echo "| 水瓶座<br />";
} elseif ($ank['ank_d_r'] >= 18 && $ank['ank_m_r'] == 2) {
	echo "| 双鱼座<br />";
} elseif ($ank['ank_d_r'] <= 21 && $ank['ank_m_r'] == 3) {
	echo "| 双鱼座<br />";
} elseif ($ank['ank_d_r'] >= 20 && $ank['ank_m_r'] == 3) {
	echo "| 白羊座<br />";
} elseif ($ank['ank_d_r'] <= 21 && $ank['ank_m_r'] == 4) {
	echo "| 白羊座<br />";
} elseif ($ank['ank_d_r'] >= 20 && $ank['ank_m_r'] == 4) {
	echo "| 金牛座<br />";
} elseif ($ank['ank_d_r'] <= 21 && $ank['ank_m_r'] == 5) {
	echo "| 金牛座<br />";
} elseif ($ank['ank_d_r'] >= 20 && $ank['ank_m_r'] == 5) {
	echo "| 双子座<br />";
} elseif ($ank['ank_d_r'] <= 22 && $ank['ank_m_r'] == 6) {
	echo "| 双子座<br />";
} elseif ($ank['ank_d_r'] >= 21 && $ank['ank_m_r'] == 6) {
	echo "| 巨蟹座<br />";
} elseif ($ank['ank_d_r'] <= 22 && $ank['ank_m_r'] == 7) {
	echo "| 巨蟹座<br />";
} elseif ($ank['ank_d_r'] >= 23 && $ank['ank_m_r'] == 7) {
	echo "| 狮子座<br />";
} elseif ($ank['ank_d_r'] <= 22 && $ank['ank_m_r'] == 8) {
	echo "| 狮子座<br />";
} elseif ($ank['ank_d_r'] >= 22 && $ank['ank_m_r'] == 8) {
	echo "| 处女座<br />";
} elseif ($ank['ank_d_r'] <= 23 && $ank['ank_m_r'] == 9) {
	echo "| 处女座<br />";
} elseif ($ank['ank_d_r'] >= 22 && $ank['ank_m_r'] == 9) {
	echo "| 天秤座<br />";
} elseif ($ank['ank_d_r'] <= 23 && $ank['ank_m_r'] == 10) {
	echo "| 天秤座<br />";
} elseif ($ank['ank_d_r'] >= 22 && $ank['ank_m_r'] == 10) {
	echo "| 天蝎座<br />";
} elseif ($ank['ank_d_r'] <= 22 && $ank['ank_m_r'] == 11) {
	echo "| 天蝎座<br />";
} elseif ($ank['ank_d_r'] >= 21 && $ank['ank_m_r'] == 11) {
	echo "| 射手座<br />";
} elseif ($ank['ank_d_r'] <= 22 && $ank['ank_m_r'] == 12) {
	echo "| 射手座<br />";
} elseif ($ank['ank_d_r'] >= 21 && $ank['ank_m_r'] == 12) {
	echo "| 摩羯座<br />";
} elseif ($ank['ank_d_r'] <= 20 && $ank['ank_m_r'] == 1) {
	echo "| 摩羯座<br />";
}
echo "</div>";
//--------------关于我自己--------------//
echo "<div class='nav1'>";
if ($ank['ank_o_sebe'] != NULL)
	echo "$osebe<span class=\"ank_n\">关于你自己：</span>$a <span class=\"ank_d\">" . output_text($ank['ank_o_sebe']) . "</span><br />";
else
	echo "$osebe<span class=\"ank_n\">关于你自己：</span>$a<br />";
echo "</div>";
//-------------联系方式----------------//
echo "<div class='nav2'>";
if ($ank['ank_icq'] != NULL && $ank['ank_icq'] != 0)
	echo "$icq<span class=\"ank_n\">QQ:</span>$a <span class=\"ank_d\">$ank[ank_icq]</span><br />";
else
	echo "$icq<span class=\"ank_n\">QQ:</span>$a<br />";
echo "$mail E-Mail:$a";
if ($ank['email'] != NULL && ($ank['set_show_mail'] == 1 || isset($user) && ($user['level'] > $ank['level'] || $user['level'] == 4))) {
	if ($ank['set_show_mail'] == 0) $hide_mail = ' (隐藏)';
	else $hide_mail = NULL;
	if (preg_match("#(@mail\.ru$)|(@bk\.ru$)|(@inbox\.ru$)|(@list\.ru$)#", $ank['email']))
		echo " <a href=\"mailto:$ank[email]\" title=\"写信\" class=\"ank_d\">$ank[email]</a>$hide_mail<br />";
	else
		echo " <a href=\"mailto:$ank[email]\" title=\"写信\" class=\"ank_d\">$ank[email]</a>$hide_mail<br />";
} else {
	echo "<br />";
}
if ($ank['ank_n_tel'] != NULL)
	echo "$mobile<span class=\"ank_n\">电话:</span>$a <span class=\"ank_d\">$ank[ank_n_tel]</span><br />";
else
	echo "$mobile<span class=\"ank_n\">电话:</span>$a<br />";
if ($ank['ank_skype'] != NULL)
	echo "$skype<span class=\"ank_n\">Skype:</span>$a <span class=\"ank_d\">$ank[ank_skype]</span><br />";
else
	echo "$skype<span class=\"ank_n\">Skype:</span>$a<br />";
echo "</div>";
//--------------------管理用户----------------------//
echo "<div class='nav1'>";
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$ank[id]' AND `time` > '$time'"), 0) != 0) {
	$q = dbquery("SELECT * FROM `ban` WHERE `id_user` = '$ank[id]' AND `time` > '$time' ORDER BY `time` DESC LIMIT 5");
	while ($post = dbassoc($q)) {
		echo "<span class='ank_n'>禁止到 " . vremja($post['time']) . ":</span>";
		echo "<span class='ank_d'>" . output_text($post['prich']) . "</span><br />";
	}
} else {
	$narush = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$ank[id]'"), 0);
	echo "<span class='ank_n'>黑名单:</span>" . (($narush == 0) ? " <span class='ank_d'>否</span><br />" : " <span class=\"ank_d\">$narush</span><br />");
}
echo "<span class=\"ank_n\">注册时间:</span> <span class=\"ank_d\">" . vremja($ank['date_reg']) . "</span><br />";
echo "</div>";
if ($user['level'] > $ank['level']) {
	if (isset($_GET['info'])) {
		echo "<div class='foot'>";
		echo "<img src='/style/icons/str.gif' alt='*' /> <a href='?id={$ank['id']}'>隐藏</a><br />";
		echo "</div>";
		echo "<div class='p_t'>";
		if ($ank['ip'] != NULL) {
			if (user_access('user_show_ip') && $ank['ip'] != 0) {
				echo "<span class=\"ank_n\">IP:</span> <span class=\"ank_d\">{$ank['ip']}</span>";
				if (user_access('adm_ban_ip'))
					echo " [<a href='/adm_panel/ban_ip.php?min={$ank['ip']}'>禁止</a>]";
				echo "<br />";
			}
		}
		if (user_access('user_show_ua') && $ank['ua'] != NULL)
			echo "<span class=\"ank_n\">UA:</span> <span class=\"ank_d\">$ank[ua]</span><br />";
		if (user_access('user_show_ip') && opsos($ank['ip']))
			echo "<span class=\"ank_n\">IP:</span> <span class=\"ank_d\">" . opsos($ank['ip']) . "</span><br />";
		if ($ank['show_url'] == 1) {
			if (otkuda($ank['url'])) echo "<span class=\"ank_n\">URL:</span> <span class=\"ank_d\"><a href='$ank[url]'>" . otkuda($ank['url']) . "</a></span><br />";
		}
		if (user_access('user_collisions') && $user['level'] > $ank['level']) {
			$mass[0] = $ank['id'];
			$collisions = user_collision($mass);
			if (count($collisions) > 1) {
				echo "<span class=\"ank_n\">相似账号:</span><br />";
				echo "<span class=\"ank_d\">";
				for ($i = 1; $i < count($collisions); $i++) {
					$ank_coll = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$collisions[$i]' LIMIT 1"));
					echo user::nick($ank_coll['id'], 1, 0, 0) . "<br />";
				}
				echo "</span>";
			}
		}
		if (user_access('adm_ref') && ($ank['level'] < $user['level'] || $user['id'] == $ank['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `user_ref` WHERE `id_user` = '$ank[id]'"), 0)) {
			$q = dbquery("SELECT * FROM `user_ref` WHERE `id_user` = '$ank[id]' ORDER BY `time` DESC LIMIT $set[p_str]");
			echo "访问的网站:<br />";
			while ($url = dbassoc($q)) {
				$site = htmlentities($url['url'], ENT_QUOTES, 'UTF-8');
				echo "<a" . ($set['web'] ? " target='_blank'" : null) . " href='/go.php?go=" . base64_encode("http://$site") . "'>$site</a> (" . vremja($url['time']) . ")<br />";
			}
		}
		if (user_access('user_delete')) {
			if (count(user_collision($mass, 1)) > 1)
				echo "！！！删除 (<a href='/adm_panel/delete_user.php?id=$ank[id]&amp;all'>所有账号</a>)";
			echo "<br />";
		}
		echo "</div>";
	} else {
		echo "<div class='foot'>";
		echo "<img src='/style/icons/str.gif' alt='*' /> <a href='?id=$ank[id]&amp;info'>其他信息</a><br />";
		echo "</div>";
	}
}
echo "<div class='foot'>";
if (isset($user) && $user['id'] == $ank['id']) echo "<img src='/style/icons/str.gif' alt='*' /> <a href=\"edit.php\">修改资料</a><br />";
if ($user['level'] > $ank['level']) {
	if (user_access('user_prof_edit'))
		echo "<img src='/style/icons/str.gif' alt='*' /> <a href='/adm_panel/user.php?id=$ank[id]'>编辑资料</a><br />";
	if ($user['id'] != $ank['id']) {
		if (user_access('user_ban_set') || user_access('user_ban_set_h') || user_access('user_ban_unset'))
			echo "<img src='/style/icons/str.gif' alt='*' /> <a href='/adm_panel/ban.php?id=$ank[id]'>加入黑名单</a><br />";
		if (user_access('user_delete')) {
			echo "<img src='/style/icons/str.gif' alt='*' /> <a href='/adm_panel/delete_user.php?id=$ank[id]'>删除用户</a>";
			echo "<br />";
		}
	}
}
if (user_access('adm_log_read') && $ank['level'] != 0 && ($ank['id'] == $user['id'] || $ank['level'] < $user['level']))
	echo "<img src='/style/icons/str.gif' alt='*' /> <a href='/adm_panel/adm_log.php?id=$ank[id]'>管理日志</a><br />";
echo "</div>";
include_once '../../sys//inc/tfoot.php';