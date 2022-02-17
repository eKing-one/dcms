<?php
include_once '../../sys//inc/start.php';
include_once '../../sys//inc/compress.php';
include_once '../../sys//inc/sess.php';
include_once '../../sys//inc/home.php';
include_once '../../sys//inc/settings.php';
include_once '../../sys//inc/db_connect.php';
include_once '../../sys//inc/ipua.php';
include_once '../../sys//inc/fnc.php';
include_once '../../sys//inc/user.php';

if (isset($user)) $ank['id'] = $user['id'];
if (isset($_GET['id'])) $ank['id'] = intval($_GET['id']);
if ($ank['id'] == 0) {
	$ank = get_user($ank['id']);
	$set['title'] = $ank['nick'] . ' - 个人资料 '; //网页标题
	include_once '../../sys/inc/thead.php';
	title();
	aut();/*
==================================
Приватность станички пользователя
Запрещаем просмотр анкеты
==================================
*/

	$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));
	$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$ank[id]') OR (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"), 0);
	$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$ank[id]') OR (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"), 0);

	if ($ank['id'] != $user['id'] && $user['group_access'] == 0) {

		if (($uSet['privat_str'] == 2 && $frend != 2) || $uSet['privat_str'] == 0) // Начинаем вывод если стр имеет приват настройки
		{
			if ($ank['group_access'] > 1) echo "<div class='err'>$ank[group_name]</div>";
			echo "<div class='nav1'>";
			echo group($ank['id']) . " $ank[nick] ";
			echo medal($ank['id']) . " " . online($ank['id']) . " ";
			echo "</div>";

			echo "<div class='nav2'>";
			echo avatar($ank['id'], true, 128, 128);
			echo "<br />";
		}


		if ($uSet['privat_str'] == 2 && $frend != 2) // Если только для друзей
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

	if ($ank['ank_o_sebe'] != NULL) echo "<span class=\"ank_n\">关于自己。:</span> <span class=\"ank_d\">$ank[ank_o_sebe]</span><br />";

	if (isset($_SESSION['refer']) && $_SESSION['refer'] != NULL && otkuda($_SESSION['refer']))
		echo "<div class='foot'>&laquo;<a href='$_SESSION[refer]'>" . otkuda($_SESSION['refer']) . "</a><br /></div>";

	include_once '../../sys//inc/tfoot.php';
	exit;
}

$ank = get_user($ank['id']);
if (!$ank) {
	header("Location: /index.php?" . SID);
	exit;
}
$timediff = dbresult(dbquery("SELECT `time` FROM `user` WHERE `id` = '$ank[id]' LIMIT 1", $db), 0);

$oneMinute = 60;
$oneHour = 60 * 60;
$hourfield = floor(($timediff) / $oneHour);
$minutefield = floor(($timediff - $hourfield * $oneHour) / $oneMinute);
$secondfield = floor(($timediff - $hourfield * $oneHour - $minutefield * $oneMinute));

$sHoursLeft = $hourfield;
$sHoursText = "表";
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

	$orien = "<a href='/user/info/edit.php?act=ank&amp;set=orien'>";
	$loves = "<a href='/user/info/edit.php?act=ank&amp;set=loves'>";
	$opar = "<a href='/user/info/edit.php?act=ank&amp;set=opar'>";

	$volos = "<a href='/user/info/edit.php?act=ank&amp;set=volos'>";
	$ves = "<a href='/user/info/edit.php?act=ank&amp;set=ves'>";
	$glaza = "<a href='/user/info/edit.php?act=ank&amp;set=glaza'>";
	$rost = "<a href='/user/info/edit.php?act=ank&amp;set=rost'>";
	$osebe = "<a href='/user/info/edit.php?act=ank&amp;set=osebe'>";
	$pol = "<a href='/user/info/edit.php?act=ank&amp;set=pol'>";
	$telo = "<a href='/user/info/edit.php?act=ank&amp;set=telo'>";

	$avto = "<a href='/user/info/edit.php?act=ank&amp;set=avto'>";
	$baby = "<a href='/user/info/edit.php?act=ank&amp;set=baby'>";
	$proj = "<a href='/user/info/edit.php?act=ank&amp;set=proj'>";
	$zan = "<a href='/user/info/edit.php?act=ank&amp;set=zan'>";
	$smok = "<a href='/user/info/edit.php?act=ank&amp;set=smok'>";
	$mat_pol = "<a href='/user/info/edit.php?act=ank&amp;set=mat_pol'>";

	$mail = "<a href='/user/info/edit.php?act=ank&amp;set=mail'>";
	$icq = "<a href='/user/info/edit.php?act=ank&amp;set=icq'>";
	$skype = "<a href='/user/info/edit.php?act=ank&amp;set=skype'>";
	$mobile = "<a href='/user/info/edit.php?act=ank&amp;set=mobile'>";

	$a = "</a>";
} else {
	$name = "<font style='color : #005ba8; padding:1px;'>";
	$date =  "<font style='color : #005ba8; padding:1px;'>";
	$gorod =  "<font style='color : #005ba8; padding:1px;'>";

	$orien = "<font style='color : #005ba8; padding:1px;'>";
	$loves = "<font style='color : #005ba8; padding:1px;'>";
	$opar = "<font style='color : #005ba8; padding:1px;'>";

	$avto = "<font style='color : #005ba8; padding:1px;'>";
	$baby =  "<font style='color : #005ba8; padding:1px;'>";
	$zan = "<font style='color : #005ba8; padding:1px;'>";
	$smok = "<font style='color : #005ba8; padding:1px;'>";
	$mat_pol =  "<font style='color : #005ba8; padding:1px;'>";
	$proj =  "<font style='color : #005ba8; padding:1px;'>";

	$telo =  "<font style='color : #005ba8; padding:1px;'>";
	$volos = "<font style='color : #005ba8; padding:1px;'>";
	$ves =  "<font style='color : #005ba8; padding:1px;'>";
	$glaza =  "<font style='color : #005ba8; padding:1px;'>";
	$rost =  "<font style='color : #005ba8; padding:1px;'>";
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
echo avatar($ank['id'], true, 128, 128);
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
	echo "<img src='/style/icons/pochta.gif' alt='*' /> <a href=\"/mail.php?id=$ank[id]\"><b>私下写</b></a>";
	echo "</div>";
}

echo "<div class='nav2'>";
echo "<img src='/style/icons/foto.png' alt='*' /> <a href='/foto/$ank[id]/'><b>相片册</b></a><br />";
echo "</div>";

//-----------------инфо----------------//
echo "<div class='nav2'>";
echo "<b>ID: $ank[id]</b><br /> ";
echo "分数 (";
echo "<font color='green'>$ank[balls]</font>)<br /> ";
echo $sMonet[2] . ' (' . $ank['money'] . ')<br />';
echo "<img src='/style/icons/time.png' alt='*' width='14'/> ($displaystring)<br />  ";
echo "</div>";

//-------------------------------------------------------//

//------------------основное-------------------//
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
	if ($ank['ank_m_r'] == 1) $ank['mes'] = '一月';
	elseif ($ank['ank_m_r'] == 2) $ank['mes'] = '二月';
	elseif ($ank['ank_m_r'] == 3) $ank['mes'] = '三月';
	elseif ($ank['ank_m_r'] == 4) $ank['mes'] = '四月';
	elseif ($ank['ank_m_r'] == 5) $ank['mes'] = '五月';
	elseif ($ank['ank_m_r'] == 6) $ank['mes'] = '六月';
	elseif ($ank['ank_m_r'] == 7) $ank['mes'] = '七月';
	elseif ($ank['ank_m_r'] == 8) $ank['mes'] = '八月';
	elseif ($ank['ank_m_r'] == 9) $ank['mes'] = '九月';
	elseif ($ank['ank_m_r'] == 10) $ank['mes'] = '十月';
	elseif ($ank['ank_m_r'] == 11) $ank['mes'] = '十一月';
	else $ank['mes'] = '十二月';
	echo "$date<span class=\"ank_n\">出生日期:</span>$a $ank[ank_d_r] $ank[mes] $ank[ank_g_r]г. <br />";
	$ank['ank_age'] = date("Y") - $ank['ank_g_r'];
	if (date("n") < $ank['ank_m_r']) $ank['ank_age'] = $ank['ank_age'] - 1;
	elseif (date("n") == $ank['ank_m_r'] && date("j") < $ank['ank_d_r']) $ank['ank_age'] = $ank['ank_age'] - 1;
	echo "<span class=\"ank_n\">年龄:</span> $ank[ank_age] ";
} elseif ($ank['ank_d_r'] != NULL && $ank['ank_m_r'] != NULL) {
	if ($ank['ank_m_r'] == 1) $ank['mes'] = '一月';
	elseif ($ank['ank_m_r'] == 2) $ank['mes'] = '二月';
	elseif ($ank['ank_m_r'] == 3) $ank['mes'] = '三月';
	elseif ($ank['ank_m_r'] == 4) $ank['mes'] = '四月';
	elseif ($ank['ank_m_r'] == 5) $ank['mes'] = '五月';
	elseif ($ank['ank_m_r'] == 6) $ank['mes'] = '六月';
	elseif ($ank['ank_m_r'] == 7) $ank['mes'] = '七月';
	elseif ($ank['ank_m_r'] == 8) $ank['mes'] = '八月';
	elseif ($ank['ank_m_r'] == 9) $ank['mes'] = '九月';
	elseif ($ank['ank_m_r'] == 10) $ank['mes'] = '十月';
	elseif ($ank['ank_m_r'] == 11) $ank['mes'] = '十一月';
	else $ank['mes'] = '十二月';
	echo "$date<span class=\"ank_n\">生日:</span>$a $ank[ank_d_r] $ank[mes] ";
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
	echo "| 双子胎<br />";
} elseif ($ank['ank_d_r'] <= 22 && $ank['ank_m_r'] == 6) {
	echo "| 双子胎<br />";
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
//--------------------------------------------------//


//--------------внешность---------------//
echo "<div class='nav2'>";
if ($ank['ank_rost'] != NULL)
	echo "$rost<span class=\"ank_n\">身高:</span>$a <span class=\"ank_d\">$ank[ank_rost]</span><br />";
else
	echo "$rost<span class=\"ank_n\">身高:</span>$a<br />";
if ($ank['ank_ves'] != NULL)
	echo "$ves<span class=\"ank_n\">重量:</span>$a <span class=\"ank_d\">$ank[ank_ves]</span><br />";
else
	echo "$ves<span class=\"ank_n\">重量:</span>$a<br />";

if ($ank['ank_cvet_glas'] != NULL)
	echo "$glaza<span class=\"ank_n\">眼睛颜色:</span>$a <span class=\"ank_d\">$ank[ank_cvet_glas]</span><br />";
else
	echo "$glaza<span class=\"ank_n\">眼睛颜色:</span>$a<br />";
if ($ank['ank_volos'] != NULL)
	echo "$volos<span class=\"ank_n\">头发:</span>$a <span class=\"ank_d\">$ank[ank_volos]</span><br />";
else
	echo "$volos<span class=\"ank_n\">头发:</span>$a<br />";

echo "$telo<span class=\"ank_n\">身体类型:</span>$a";
if ($ank['ank_telosl'] == 1)
	echo " <span class=\"ank_d\">没有人回答</span><br />";
if ($ank['ank_telosl'] == 2)
	echo " <span class=\"ank_d\">瘦骨嶙峋</span><br />";
if ($ank['ank_telosl'] == 3)
	echo " <span class=\"ank_d\">平常的</span><br />";
if ($ank['ank_telosl'] == 4)
	echo " <span class=\"ank_d\">运动项目</span><br />";
if ($ank['ank_telosl'] == 5)
	echo " <span class=\"ank_d\">肌肉发达</span><br />";
if ($ank['ank_telosl'] == 6)
	echo " <span class=\"ank_d\">密密麻麻</span><br />";
if ($ank['ank_telosl'] == 7)
	echo " <span class=\"ank_d\">全</span><br />";
if ($ank['ank_telosl'] == 0)
	echo "<br />";
echo "</div>";
//-----------------------------------------------------//


//--------------Знакомства---------------//
echo "<div class='nav1'>";

echo "$orien<span class=\"ank_n\">方向感:</span>$a";
if ($ank['ank_orien'] == 0)
	echo "<br />";
if ($ank['ank_orien'] == 1)
	echo " <span class=\"ank_d\">杂种</span><br />";
if ($ank['ank_orien'] == 2)
	echo " <span class=\"ank_d\">毕</span><br />";
if ($ank['ank_orien'] == 3)
	echo " <span class=\"ank_d\">同性恋/女同性恋</span><br />";

echo "$loves<span class=\"ank_n\">约会目标:</span>$a<br />";

if ($ank['ank_lov_1'] == 1) echo "<img src='/style/icons/str.gif' alt='*' />  友谊与沟通<br />";
if ($ank['ank_lov_2'] == 1) echo "<img src='/style/icons/str.gif' alt='*' />  通信<br />";
if ($ank['ank_lov_3'] == 1) echo "<img src='/style/icons/str.gif' alt='*' />  爱情，关系<br />";
if ($ank['ank_lov_4'] == 1) echo "<img src='/style/icons/str.gif' alt='*' />  两人有规律的性生活<br />";
if ($ank['ank_lov_5'] == 1) echo "<img src='/style/icons/str.gif' alt='*' />  一两次性爱<br />";
if ($ank['ank_lov_6'] == 1) echo "<img src='/style/icons/str.gif' alt='*' />  分组秒с<br />";
if ($ank['ank_lov_7'] == 1) echo "<img src='/style/icons/str.gif' alt='*' />  虚拟性爱<br />";
if ($ank['ank_lov_8'] == 1) echo "<img src='/style/icons/str.gif' alt='*' />  为了钱提供亲密<br />";
if ($ank['ank_lov_9'] == 1) echo "<img src='/style/icons/str.gif' alt='*' />  为了钱寻找亲密关系 <br />";
if ($ank['ank_lov_10'] == 1) echo "<img src='/style/icons/str.gif' alt='*' />  婚姻、建立家庭<br />";
if ($ank['ank_lov_11'] == 1) echo "<img src='/style/icons/str.gif' alt='*' />  出生、养育子女<br />";
if ($ank['ank_lov_12'] == 1) echo "<img src='/style/icons/str.gif' alt='*' />  为 V 结婚是的<br />";
if ($ank['ank_lov_13'] == 1) echo "<img src='/style/icons/str.gif' alt='*' />  合租住房<br />";
if ($ank['ank_lov_14'] == 1) echo "<img src='/style/icons/str.gif' alt='*' />  体育活动<br />";
if ($ank['ank_o_par'] != NULL)
	echo "$opar<span class=\"ank_n\">关于合作伙伴：</span>$a <span class=\"ank_d\">" . output_text($ank['ank_o_par']) . "</span><br />";
else
	echo "$opar<span class=\"ank_n\">关于合作伙伴：</span>$a<br />";

if ($ank['ank_o_sebe'] != NULL)
	echo "$osebe<span class=\"ank_n\">关于你自己：</span>$a <span class=\"ank_d\">" . output_text($ank['ank_o_sebe']) . "</span><br />";
else
	echo "$osebe<span class=\"ank_n\">关于你自己：</span>$a<br />";
echo "</div>";
//-----------------------------------------------------//


//--------------о себе------------------//
echo "<div class='nav2'>";
if ($ank['ank_zan'] != NULL)
	echo "$zan<span class=\"ank_n\">我在做什么：</span>$a <span class=\"ank_d\">" . output_text($ank['ank_zan']) . "</span><br />";
else
	echo "$zan<span class=\"ank_n\">我在做什么？:</span>$a<br />";

echo "$smok<span class=\"ank_n\"> 吸烟：</span>$a";
if ($ank['ank_smok'] == 1)
	echo " <span class=\"ank_d\">不吸烟。</span><br />";
if ($ank['ank_smok'] == 2)
	echo " <span class=\"ank_d\">吸烟</span><br />";
if ($ank['ank_smok'] == 3)
	echo " <span class=\"ank_d\">很少</span><br />";
if ($ank['ank_smok'] == 4)
	echo " <span class=\"ank_d\">投球</span><br />";
if ($ank['ank_smok'] == 5)
	echo " <span class=\"ank_d\">成功退出</span><br />";
if ($ank['ank_smok'] == 0)
	echo "<br />";
echo "$mat_pol<span class=\"ank_n\">物质状况:</span>$a";
if ($ank['ank_mat_pol'] == 1)
	echo " <span class=\"ank_d\">非固定收入</span><br />";
if ($ank['ank_mat_pol'] == 2)
	echo " <span class=\"ank_d\">固定的少量收入</span><br />";
if ($ank['ank_mat_pol'] == 3)
	echo " <span class=\"ank_d\">稳定平均收入</span><br />";
if ($ank['ank_mat_pol'] == 4)
	echo " <span class=\"ank_d\">收入不错/有保障</span><br />";
if ($ank['ank_mat_pol'] == 5)
	echo " <span class=\"ank_d\">不赚钱。</span><br />";
if ($ank['ank_mat_pol'] == 0)
	echo "<br />";

echo "$avto<span class=\"ank_n\">有车情况：</span>$a";
if ($ank['ank_avto_n'] == 1)
	echo " <span class=\"ank_d\">有</span><br />";
if ($ank['ank_avto_n'] == 2)
	echo " <span class=\"ank_d\">取消</span><br />";
if ($ank['ank_avto_n'] == 3)
	echo " <span class=\"ank_d\">想买。</span><br />";
if ($ank['ank_avto_n'] == 0)
	echo "<br />";
if ($ank['ank_avto'] && $ank['ank_avto_n'] != 2 && $ank['ank_avto_n'] != 0)
	echo "<img src='/style/icons/str.gif' alt='*' />  <span class=\"ank_d\">" . output_text($ank['ank_avto']) . "</span><br />";
echo "$proj<span class=\"ank_n\">居住:</span>$a";
if ($ank['ank_proj'] == 1)
	echo " <span class=\"ank_d\">单独的公寓（出租或自有）</span><br />";
if ($ank['ank_proj'] == 2)
	echo " <span class=\"ank_d\">宿舍房间、公用设施</span><br />";
if ($ank['ank_proj'] == 3)
	echo " <span class=\"ank_d\">和父母住在一起</span><br />";
if ($ank['ank_proj'] == 4)
	echo " <span class=\"ank_d\">和一个朋友/女朋友住在一起</span><br />";
if ($ank['ank_proj'] == 5)
	echo " <span class=\"ank_d\">与伴侣或配偶生活在一起</span><br />";
if ($ank['ank_proj'] == 6)
	echo " <span class=\"ank_d\">没有永久住所</span><br />";
if ($ank['ank_proj'] == 0)
	echo "<br />";
echo "$baby<span class=\"ank_n\">是否有儿童：</span>$a";
if ($ank['ank_baby'] == 1)
	echo " <span class=\"ank_d\">没有</span><br />";
if ($ank['ank_baby'] == 2)
	echo " <span class=\"ank_d\">不，但我希望我能。</span><br />";
if ($ank['ank_baby'] == 3)
	echo " <span class=\"ank_d\">吃，住在一起</span><br />";
if ($ank['ank_baby'] == 4)
	echo " <span class=\"ank_d\">是的，我们分开生活</span><br />";
if ($ank['ank_baby'] == 0)
	echo "<br />";
echo "</div>";
//-------------------------------------------//

if (isset($user) && $ank['id'] == $user['id']) {
	$alko = "<a href='/user/info/edit.php?act=ank&amp;set=alko'>";
	$nark = "<a href='/user/info/edit.php?act=ank&amp;set=nark'>";
} else {

	$alko = null;
	$nark = null;
}

//---------------------дополнительно--------------------//

echo "<div class='nav1'>";

echo "$alko<span class=\"ank_n\">酒精:</span>$a";
if ($ank['ank_alko_n'] == 1)
	echo " <span class=\"ank_d\">是的，我在喝酒。</span><br />";
if ($ank['ank_alko_n'] == 2)
	echo " <span class=\"ank_d\">很少，在节假日</span><br />";
if ($ank['ank_alko_n'] == 3)
	echo " <span class=\"ank_d\">不，我绝对不能接受</span><br />";
if ($ank['ank_alko_n'] == 0)
	echo "<br />";
if ($ank['ank_alko'] && $ank['ank_alko_n'] != 3 && $ank['ank_alko_n'] != 0) echo "<img src='/style/icons/str.gif' alt='*' />  <span class=\"ank_d\">" . output_text($ank['ank_alko']) . "</span>";
echo "</div>";
//----------------------------------------------------------//

//-------------контакты----------------//
echo "<div class='nav2'>";
if ($ank['ank_icq'] != NULL && $ank['ank_icq'] != 0)
	echo "$icq<span class=\"ank_n\">ICQ:</span>$a <span class=\"ank_d\">$ank[ank_icq]</span><br />";
else
	echo "$icq<span class=\"ank_n\">ICQ:</span>$a<br />";

echo "$mail E-Mail:$a";
if ($ank['ank_mail'] != NULL && ($ank['set_show_mail'] == 1 || isset($user) && ($user['level'] > $ank['level'] || $user['level'] == 4))) {
	if ($ank['set_show_mail'] == 0) $hide_mail = ' (隐藏)';
	else $hide_mail = NULL;
	if (preg_match("#(@mail\.ru$)|(@bk\.ru$)|(@inbox\.ru$)|(@list\.ru$)#", $ank['ank_mail']))
		echo " <a href=\"mailto:$ank[ank_mail]\" title=\"写信\" class=\"ank_d\">$ank[ank_mail]</a>$hide_mail<br />";
	else
		echo " <a href=\"mailto:$ank[ank_mail]\" title=\"写信\" class=\"ank_d\">$ank[ank_mail]</a>$hide_mail<br />";
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
//------------------------------------------//

echo "<div class='nav1'>";
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$ank[id]' AND `time` > '$time'"), 0) != 0) {
	$q = dbquery("SELECT * FROM `ban` WHERE `id_user` = '$ank[id]' AND `time` > '$time' ORDER BY `time` DESC LIMIT 5");
	while ($post = dbassoc($q)) {
		echo "<span class='ank_n'>禁止直到 " . vremja($post['time']) . ":</span>";
		echo "<span class='ank_d'>" . output_text($post['prich']) . "</span><br />";
	}
} else {
	$narush = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$ank[id]'"), 0);
	echo "<span class='ank_n'>侵犯行为:</span>" . (($narush == 0) ? " <span class='ank_d'>取消</span><br />" : " <span class=\"ank_d\">$narush</span><br />");
}
echo "<span class=\"ank_n\">登记:</span> <span class=\"ank_d\">" . vremja($ank['date_reg']) . "</span><br />";

echo "</div>";

if ($user['level'] > $ank['level']) {
	if (isset($_GET['info'])) {
		echo "<div class='foot'>";
		echo "<img src='/style/icons/str.gif' alt='*' /> <a href='?id=$ank[id]'>隐藏</a><br />";
		echo "</div>";

		echo "<div class='p_t'>";
		if ($ank['ip'] != NULL) {
			if (user_access('user_show_ip') && $ank['ip'] != 0) {
				echo "<span class=\"ank_n\">IP:</span> <span class=\"ank_d\">" . long2ip($ank['ip']) . "</span>";
				if (user_access('adm_ban_ip'))
					echo " [<a href='/adm_panel/ban_ip.php?min=$ank[ip]'>禁止</a>]";
				echo "<br />";
			}
		}
		if ($ank['ip_cl'] != NULL) {
			if (user_access('user_show_ip') && $ank['ip_cl'] != 0) {
				echo "<span class=\"ank_n\">IP (CLIENT):</span> <span class=\"ank_d\">" . long2ip($ank['ip_cl']) . "</span>";
				if (user_access('adm_ban_ip'))
					echo " [<a href='/adm_panel/ban_ip.php?min=$ank[ip_cl]'>禁止</a>]";
				echo "<br />";
			}
		}

		if ($ank['ip_xff'] != NULL) {
			if (user_access('user_show_ip') && $ank['ip_xff'] != 0) {
				echo "<span class=\"ank_n\">IP (XFF):</span> <span class=\"ank_d\">" . long2ip($ank['ip_xff']) . "</span>";
				if (user_access('adm_ban_ip'))
					echo " [<a href='/adm_panel/ban_ip.php?min=$ank[ip_xff]'>禁止</a>]";
				echo "<br />";
			}
		}

		if (user_access('user_show_ua') && $ank['ua'] != NULL)
			echo "<span class=\"ank_n\">UA:</span> <span class=\"ank_d\">$ank[ua]</span><br />";
		if (user_access('user_show_ip') && opsos($ank['ip']))
			echo "<span class=\"ank_n\">省:</span> <span class=\"ank_d\">" . opsos($ank['ip']) . "</span><br />";
		if (user_access('user_show_ip') && opsos($ank['ip_cl']))
			echo "<span class=\"ank_n\">省 (CL):</span> <span class=\"ank_d\">" . opsos($ank['ip_cl']) . "</span><br />";
		if (user_access('user_show_ip') && opsos($ank['ip_xff']))
			echo "<span class=\"ank_n\">省 (XFF):</span> <span class=\"ank_d\">" . opsos($ank['ip_xff']) . "</span><br />";

		if ($ank['show_url'] == 1) {
			if (otkuda($ank['url'])) echo "<span class=\"ank_n\">URL:</span> <span class=\"ank_d\"><a href='$ank[url]'>" . otkuda($ank['url']) . "</a></span><br />";
		}
		if (user_access('user_collisions') && $user['level'] > $ank['level']) {
			$mass[0] = $ank['id'];
			$collisions = user_collision($mass);
			if (count($collisions) > 1) {
				echo "<span class=\"ank_n\">可能的尼克:</span><br />";
				echo "<span class=\"ank_d\">";

				for ($i = 1; $i < count($collisions); $i++) {
					$ank_coll = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$collisions[$i]' LIMIT 1"));
					echo "\"<a href='/info.php?id=$ank_coll[id]'>$ank_coll[nick]</a>\"<br />";
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
				echo "清除 (<a href='/adm_panel/delete_user.php?id=$ank[id]&amp;all'>所有的尼基</a>)";
			echo "<br />";
		}
		echo "</div>";
	} else {
		echo "<div class='foot'>";
		echo "<img src='/style/icons/str.gif' alt='*' /> <a href='?id=$ank[id]&amp;info'>补充信息</a><br />";
		echo "</div>";
	}
}

echo "<div class='foot'>";

if (isset($user) && $user['id'] == $ank['id']) echo "<img src='/style/icons/str.gif' alt='*' /> <a href=\"edit.php\">修改问卷</a><br />";
if ($user['level'] > $ank['level']) {
	if (user_access('user_prof_edit'))
		echo "<img src='/style/icons/str.gif' alt='*' /> <a href='/adm_panel/user.php?id=$ank[id]'>编辑配置文件</a><br />";
	if ($user['id'] != $ank['id']) {
		if (user_access('user_ban_set') || user_access('user_ban_set_h') || user_access('user_ban_unset'))
			echo "<img src='/style/icons/str.gif' alt='*' /> <a href='/adm_panel/ban.php?id=$ank[id]'>违反行为（禁止酷刑）</a><br />";

		if (user_access('user_delete')) {

			echo "<img src='/style/icons/str.gif' alt='*' /> <a href='/adm_panel/delete_user.php?id=$ank[id]'>删除用户</a>";
			echo "<br />";
		}
	}
}

if (user_access('adm_log_read') && $ank['level'] != 0 && ($ank['id'] == $user['id'] || $ank['level'] < $user['level']))
	echo "<img src='/style/icons/str.gif' alt='*' /> <a href='/adm_panel/adm_log.php?id=$ank[id]'>管理报告</a><br />";

echo "</div>";
include_once '../../sys//inc/tfoot.php';
