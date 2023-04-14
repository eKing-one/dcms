<?//网页标题
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
only_reg();
if (isset($user))$ank['id'] = intval($_GET['id']);
$ank=user::get_user($ank['id']);
if(!$ank || $user['id'] == $ank['id']){header("Location: /index.php?".SID);exit;}
if (isset($_GET['act']) && $_POST['money'])
{
$money=abs(intval($_POST['money']));
if ($user['money'] < $money)$err = '你没有足够的资金转帐';
if (!$err)
{
dbquery("UPDATE `user` SET `money` = '" . ($ank['money'] + $money) . "' WHERE `id` ='$ank[id]';");
dbquery("UPDATE `user` SET `money` = '" . ($user['money'] - $money) . "' WHERE `id` ='$user[id]';");
$msg = "用户 [b]".$user['nick']."[/b] 我把钱转给你了 [b] $money [/b] $sMonet[0]! [br]别忘了说谢谢！";
dbquery("INSERT INTO `mail` (`id_user`, `id_kont`, `msg`, `time`) values('0', '$ank[id]', '$msg', '$time')");
$_SESSION['message'] = '转让成功完成';
header("Location: /user/info.php?id=$ank[id]");
exit;
}
}
$set['title']='赠送'.$sMonet[0]; // заголовок страницы
include_once '../../sys/inc/thead.php';
title();
aut();
err();
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/user/info.php?id=$ank[id]'>$ank[nick]</a> | 赠送<br />";
echo "</div>";
if (isset($user) & $user['money']<=1)
{
echo '<div class="mess">';
	if ($user['pol']==0){
		echo "不好意思。。。 <b>美女,</b> ";
	} else {
		echo "<b>不好意思。。。 兄弟,</b> ";
	}
		echo "赠送$sMonet[2] 其他居民需要获得最低 <b>2</b> $sMonet[2]<br/>你的 <b>$user[money] </b>$sMonet[0]";
echo '</div>';
}
else
{
echo '<div class="mess">';
echo "你的 $sMonet[2]: <b>$user[money]</b><br />";
echo '</div>';
echo "<form class='main' action=\"?id=$ank[id]&amp;act\" method=\"post\">";
echo "数量 $sMonet[0]:<br />";
echo "<input type='text' name='money' value='1' /><br />";
echo "<input class='submit' type='submit' value='赠送' /><br />";
echo "</form>";
}
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/user/info.php?id=$ank[id]'>$ank[nick]</a> | 赠送<br />";
echo "</div>";
include_once '../../sys/inc/tfoot.php';
