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
$set['title'] = '设置-关于我的';
require_once '../../sys/inc/thead.php';
title();

if (isset($_POST['save'])) {
	// 评论
	if (isset($_POST['komm']) && ($_POST['komm'] == 0 || $_POST['komm'] == 1)) {
		$db->update('UPDATE `notification_set` SET `komm` = ? WHERE `id_user` = ?', [intval($_POST['komm']), $user['id']]);
	}
	msg('更改成功');
}

$notSet = $db->queryAll('SELECT * FROM `notification_set` WHERE `id_user` = ? LIMIT 1', [$user['id']]);

err();
aut();
?>

<div id="comments" class="menus">
	<div class="webmenu">
		<a href="../info/settings.php">通用</a>
	</div>
	<div class="webmenu last">
		<a href="../tape/settings.php">通知消息</a>
	</div>
	<div class="webmenu last">
		<a href="../discussions/settings.php">讨论</a>
	</div>
	<div class="webmenu last">
		<a href="../notification/settings.php" class="activ">关于我的</a>
	</div>
	<div class="webmenu last">
		<a href="../info/settings.privacy.php" >隐私保护</a>
	</div>
	<div class="webmenu last">
		<a href="../info/secure.php" >更改密码</a>
	</div>
</div>

<form action="?" method="post">
	<!-- Лента фото -->
	<div class="mess">关于评论中的回复的通知</div>
	<div class="nav1">
		<input name="komm" type="radio" <?php echo ($notSet['komm'] == 1 ? ' checked="checked"' : null); ?> value="1" /> 开启 
		<input name="komm" type="radio" <?php echo ($notSet['komm'] == 0 ? ' checked="checked"' : null); ?> value="0" /> 关闭 
	</div>
	<div class="main">
		<input type="submit" name="save" value="保存" />
	</div>
</form>

<div class="foot">
	<img src="../../style/icons/str2.gif" alt="*" /> <a href="index.php">通知书</a> | <b>设置</b><br />
</div>

<?php require_once '../../sys/inc/tfoot.php';
