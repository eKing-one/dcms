<?
/**
 * & CMS Name :: DCMS-Social
 * & Author   :: Alexandr Andrushkin
 * & Contacts :: ICQ 587863132
 * & Site     :: http://dcms-social.ru
 */

include_once 'sys/inc/start.php';
include_once 'sys/inc/compress.php';
include_once 'sys/inc/sess.php';
include_once 'sys/inc/home.php';
include_once 'sys/inc/settings.php';
include_once 'sys/inc/db_connect.php';
include_once 'sys/inc/ipua.php';
include_once 'sys/inc/fnc.php';
include_once 'sys/inc/user.php';

only_reg();
$set['title'] = '个人中心';
include_once 'sys/inc/thead.php';
title();
aut();


if (isset($_GET['login']) && isset($_GET['pass']))
{
	echo '<div class="mess">';
	echo '如果您的浏览器不支持Cookie，您可以创建一个自动登录书签<br />';
	echo '<input type="text" value="http://' . text($_SERVER['SERVER_NAME']) . '/login.php?id=' . $user['id'] . '&amp;pass=' . text($_GET['pass']) . '" /><br />';
	echo '</div>';	
}

?>
<div class="main2" id="umenu_razd">我的个人资料</div>

<div class="main" id="umenu">
<img src='/style/my_menu/ank.png' alt='' /> <a href='/info.php'>我的页面</a><br />
</div>

<div class="main" id="umenu">
<img src='/style/my_menu/ank.png' alt='' /> <a href='/user/info/anketa.php'>个人资料</a> [<a href='user/info/edit.php'>编辑.</a>]<br />
</div>

<div class="main" id="umenu">
<img src='/style/my_menu/avatar.png' alt='' /> <a href='/avatar.php'>我的头像</a><br />
</div>

<?
//从文件夹加载其余插件 "sys/add/umenu"
$opdirbase = opendir(H.'sys/add/umenu');

while ($filebase = readdir($opdirbase))
{
	if (preg_match('#\.php$#i', $filebase))
	{
		echo '<div class="main" id="umenu">';
		include_once(H.'sys/add/umenu/' . $filebase);
		echo '</div>';
	}
}
?>

<div class="main2" id="umenu_razd">我的设置</div>

<div class="main" id="umenu">
<img src="/style/my_menu/set.png" alt="" /> <a href="/user/info/settings.php">常规设置</a><br />
</div>

<div class="main" id="umenu">
<img src="/style/my_menu/secure.png" alt="" /> <a href="/secure.php">更改密码</a><br />
</div>

<div class="main" id="umenu">
<img src="/style/my_menu/rules.png" alt="" /> <a href="/rules.php">规则</a><br />
</div>
<?

// 管理权限
if (user_access('adm_panel_show'))
{
	echo '<div class="main2" id="umenu_razd">管理面板</div>';
	
	echo '<div class="main" id="umenu">';
	echo '<img src="/style/my_menu/adm_panel.png" alt="" /> <a href="/adm_panel/">管理面板</a><br />';
	echo '</div>';
}

// 仅适用于wap
if ($set['web'] == false)
{
	echo '<div class="main" id="umenu">';
	echo '<a href="/exit.php"><img src="/style/icons/delete.gif" /> 退出登录 ' . user::nick($user['id'], 0) . '</a><br />';
	echo '</div>';
}


include_once 'sys/inc/tfoot.php';
exit;
?>