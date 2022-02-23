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
$set['title']='积分 5+';
include_once '../../sys/inc/thead.php';
title();
if (!isset($user))
header("location: /index.php?");
err();
aut();
if (isset($user))
{
if (isset($_POST['stav']))
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
if ($user['money']>=$st)
{
dbquery("UPDATE `user_set` SET `ocenka` = '$tm' WHERE `id_user` = '$user[id]'");
dbquery("UPDATE `user` SET `money` = '".($user['money']-$st)."' WHERE `id` = '$user[id]' LIMIT 1");
$_SESSION['message'] = '恭喜您，您已成功激活服务';
header("Location: index.php?");
exit;
}else{
$err='你没有足够的资金';
}
}
err();
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php'>$user[nick]</a> | 服务 \"积分 5+\"<br />";
echo "</div>";
echo"<div class='nav1'>";
echo "服务 <img src='/style/icons/6.png' alt='*'><br /> 1 $sMonet[1] = 1 使用特权的日子.";
echo"</div>";
$c2 = dbresult(dbquery("SELECT COUNT(*) FROM `user_set` WHERE `id_user` = '$user[id]' AND `ocenka` > '$time'"), 0);
if ($c2 == 0)
{
echo "<form method=\"post\" action=\"?\">";
	echo '投币: <select name="stav">
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	</select> ' . $sMonet[0] . '<br />';
echo "<input value=\"购买服务\" type=\"submit\" />";
echo "</form>";
}else{
echo"<div class='mess'>";
echo "服务已连接";
echo"</div>";
}
}
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php'>$user[nick]</a> | 服务 \"积分 5+\"<br />";
echo "</div>";
include_once '../../sys/inc/tfoot.php';
