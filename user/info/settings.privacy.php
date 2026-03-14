<?php
require_once '../../sys/inc/start.php';
require_once '../../sys/inc/compress.php';
require_once '../../sys/inc/sess.php';
require_once '../../sys/inc/home.php';
require_once '../../sys/inc/settings.php';
require_once '../../sys/inc/db_connect.php';
require_once '../../sys/inc/ipua.php';
require_once '../../sys/inc/fnc.php';
require_once '../../sys/inc/user.php';
only_reg();
$set['title'] = '设置-隐私保护';
require_once '../../sys/inc/thead.php';
title();

$userSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '" . $user['id'] . "' LIMIT 1"));
if (isset($_POST['save'])) {
	// 个人页面
	if (isset($_POST['privat_str']) && ($_POST['privat_str'] == 0 || $_POST['privat_str'] == 1 || $_POST['privat_str'] == 2)) {
		$db->update('UPDATE `user_set` SET `privat_str` = ? WHERE `id_user` = ?', [intval($_POST['privat_str']), $user['id']]);
	}
	// 私信
	if (isset($_POST['privat_mail']) && ($_POST['privat_mail'] == 0 || $_POST['privat_mail'] == 1 || $_POST['privat_mail'] == 2)) {
		$db->update('UPDATE `user_set` SET `privat_mail` = ? WHERE `id_user` = ?', [intval($_POST['privat_mail']), $user['id']]);
	}
	msg('更改成功');
}

err();
aut();

?>
<div id='comments' class='menus'>
	<div class='webmenu'>
		<a href='/user/info/settings.php'>通用</a>
	</div>
	<div class='webmenu last'>
		<a href='/user/tape/settings.php'>通知消息</a>
	</div>
	<div class='webmenu last'>
		<a href='/user/discussions/settings.php'>讨论</a>
	</div>
	<div class='webmenu last'>
		<a href='/user/notification/settings.php'>关于我的</a>
	</div>
	<div class='webmenu last'>
		<a href='/user/info/settings.privacy.php' class='activ'>隐私保护</a>
	</div>
	<div class='webmenu last'>
		<a href='/user/info/secure.php' >更改密码</a>
	</div>
</div>

<form action='?' method="post">
	<div class='mess'>	<!-- 查看页面 -->
		查看我的主页
	</div>
	<div class='nav1'>
		<input name='privat_str' type='radio' <?php echo ($userSet['privat_str'] == 1 ? ' checked="checked"' : null); ?> value='1' /> 全部
		<input name='privat_str' type='radio' <?php echo ($userSet['privat_str'] == 2 ? ' checked="checked"' : null); ?> value='2' /> 仅好友
		<input name='privat_str' type='radio' <?php echo ($userSet['privat_str'] == 0 ? ' checked="checked"' : null); ?> value='0' /> 只有我
	</div>
	<div class='mess'>	<!-- 消息 -->
		给我发送私信
	</div>
	<div class='nav1'>
		<input name='privat_mail' type='radio' <?php echo ($userSet['privat_mail'] == 1 ? ' checked="checked"' : null); ?> value='1' /> 全部
		<input name='privat_mail' type='radio' <?php echo ($userSet['privat_mail'] == 2 ? ' checked="checked"' : null); ?> value='2' /> 仅好友
		<input name='privat_mail' type='radio' <?php echo ($userSet['privat_mail'] == 0 ? ' checked="checked"' : null); ?> value='0' /> 只有我
	</div>
	<div class='main'>
		<input type='submit' name='save' value='保存' />
	</div>
</form>

<div class="foot">
	<img src='/style/icons/str2.gif' alt='*'> <?php echo user::nick($user['id'],1,0,0); ?> | 
	<b>隐私保护</b>
</div>

<?php require_once '../../sys/inc/tfoot.php';
