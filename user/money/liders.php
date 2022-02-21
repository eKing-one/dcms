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
$set['title'] = '领导者';
include_once '../../sys/inc/thead.php';
title();
if (!isset($user))
header("location: /index.php?");
err();
aut();
if (isset($user))
{
if (isset($_POST['stav']) && isset($_POST['msg']))
{
if ($_POST['stav']==1)
{
	$st=1;
	$tm=$time+86400;
}
else if ($_POST['stav']==2)
{
	$st=2;
	$tm=$time+172800;
}
else if ($_POST['stav']==3)
{
	$st=3;
	$tm=$time+259200;
}
else if ($_POST['stav']==4)
{
	$st=4;
	$tm=$time+345600;
}
else if ($_POST['stav']==5)
{
	$st=5;
	$tm=$time+432000;
}
else if ($_POST['stav']==6)
{
	$st=6;
	$tm=$time+518400;
}
else if ($_POST['stav']==7)
{
	$st=7;
	$tm=$time+604800;
}
$msg=my_esc($_POST['msg']);
if ($user['money']>=$st)
{
if (dbresult(dbquery("SELECT COUNT(*) FROM `liders` WHERE `id_user` = '$user[id]'"), 0)==0)
{
	dbquery("INSERT INTO `liders` (`id_user`, `stav`, `msg`, `time`, `time_p`) values('$user[id]', '$st', '".$msg."', '$tm', '$time')");
}
else
{
	dbquery("UPDATE `liders` SET `time` = '$tm', `time_p` = '$time', `msg` = '$msg', `stav` = '$st' WHERE `id_user` = '$user[id]'");
}
dbquery("UPDATE `user` SET `money` = '".($user['money']-$st)."' WHERE `id` = '$user[id]' LIMIT 1");
$_SESSION['message'] = '你已经成功地成为一个领导者';
header("Location: /user/liders/index.php?ok");
exit;
}else{
$err='你没有足够的资金';
}
}else{
$err='消息字段不能为空';
}
err();
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="S"/> <a href="/user/money/">额外服务</a> | <b>成为领导者</b>';
echo '</div>';
echo '<div class="mess">';
echo '为了进入领导者，你至少需要 <b style="color:red;">1</b> <b style="color:green;">' . $sMonet[1] . '</b>, 这项服务将提供1天 
你在这上面的位置取决于数字 ' . $sMonet[0] . ' （总停留时间）！ 
此外，您的个人资料将在约会和搜索页面上旋转！';
echo '</div>';
echo '<form class="main" method="post" action="?">';
	echo '花费: <select name="stav">
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	</select> ' . $sMonet[0] . '<br />';
echo '签名(215个字符)<textarea name="msg"></textarea><br />';
echo '<input value="成为领导者" type="submit" />';
echo '</form>';
}
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="S"/> <a href="/user/money/">额外服务</a> | <b>成为领导者</b>';
echo '</div>';
include_once '../../sys/inc/tfoot.php';
?>