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
$set['title'] = '设置-讨论';
require_once '../../sys/inc/thead.php';
title();

if (isset($_POST['save'])) {
	// 关于照片讨论
	if (isset($_POST['disc_photo']) && ($_POST['disc_photo'] == 0 || $_POST['disc_photo'] == 1)) {
		$disc = (int) $_POST['disc_photo'];
		dbquery("UPDATE `discussions_set` SET `disc_photo` = '" . $disc . "' WHERE `id_user` = '$user[id]'");
	}
	// 关于文件讨论
	if (isset($_POST['disc_files']) && ($_POST['disc_files'] == 0 || $_POST['disc_files'] == 1)) {
		$disc = (int) $_POST['disc_files'];
		dbquery("UPDATE `discussions_set` SET `disc_files` = '" . $disc . "' WHERE `id_user` = '$user[id]'");
	}
	// 关于状态讨论
	if (isset($_POST['disc_status']) && ($_POST['disc_status'] == 0 || $_POST['disc_status'] == 1)) {
		$disc = (int) $_POST['disc_status'];
		dbquery("UPDATE `discussions_set` SET `disc_status` = '" . $disc . "' WHERE `id_user` = '$user[id]'");
	}
	// 关于日记讨论
	if (isset($_POST['disc_notes']) && ($_POST['disc_notes'] == 0 || $_POST['disc_notes'] == 1)) {
		$disc = (int) $_POST['disc_notes'];
		dbquery("UPDATE `discussions_set` SET `disc_notes` = '" . $disc . "' WHERE `id_user` = '$user[id]'");
	}
	// 关于帖子讨论
	if (isset($_POST['disc_forum']) && ($_POST['disc_forum'] == 0 || $_POST['disc_forum'] == 1)) {
		$disc = (int) $_POST['disc_forum'];
		dbquery("UPDATE `discussions_set` SET `disc_forum` = '" . $disc . "' WHERE `id_user` = '$user[id]'");
	}
	msg('更改成功');
}

$discSet = dbassoc(dbquery("SELECT * FROM `discussions_set` WHERE `id_user` = '" . $user['id'] . "' LIMIT 1"));

err();
aut();
?>
<div id="comments" class="menus">
	<div class="webmenu">
		<a href="/user/info/settings.php">通用</a>
	</div>
	<div class="webmenu">
		<a href="/user/tape/settings.php">通知消息</a>
	</div>
	<div class="webmenu">
		<a href="/user/discussions/settings.php" class="activ">讨论</a>
	</div>
	<div class="webmenu">
		<a href="/user/notification/settings.php">关于我的</a>
	</div>
	<div class="webmenu">
		<a href="/user/info/settings.privacy.php">隐私保护</a>
	</div>
	<div class="webmenu">
		<a href="/user/info/secure.php">更改密码</a>
	</div>
</div>
<form action="?" method="post">
	<div class="mess">
		关于日记讨论的通知
	</div>
	<div class="nav1">
		<input name="disc_notes" type="radio" <?= ($discSet['disc_notes'] == 1 ? ' checked="checked"' : null) ?> value="1" /> 开启
		<input name="disc_notes" type="radio" <?= ($discSet['disc_notes'] == 0 ? ' checked="checked"' : null) ?> value="0" /> 关闭
	</div>
	<div class="mess">
		关于论坛帖子讨论的通知
	</div>
	<div class="nav1">
		<input name="disc_forum" type="radio" <?= ($discSet['disc_forum'] == 1 ? ' checked="checked"' : null) ?> value="1" /> 开启
		<input name="disc_forum" type="radio" <?= ($discSet['disc_forum'] == 0 ? ' checked="checked"' : null) ?> value="0" /> 关闭
	</div>
	<div class="mess">
		关于照片中讨论的通知
	</div>
	<div class="nav1">
		<input name="disc_photo" type="radio" <?= ($discSet['disc_photo'] == 1 ? ' checked="checked"' : null) ?> value="1" /> 开启
		<input name="disc_photo" type="radio" <?= ($discSet['disc_photo'] == 0 ? ' checked="checked"' : null) ?> value="0" /> 关闭
	</div>
	<div class="mess">
		关于文件中讨论的通知
	</div>
	<div class="nav1">
		<input name="disc_files" type="radio" <?= ($discSet['disc_files'] == 1 ? ' checked="checked"' : null) ?> value="1" /> 开启
		<input name="disc_files" type="radio" <?= ($discSet['disc_files'] == 0 ? ' checked="checked"' : null) ?> value="0" /> 关闭
	</div>
	<div class="mess">
		关于状态讨论的通知
	</div>
	<div class="nav1">
		<input name="disc_status" type="radio" <?= ($discSet['disc_status'] == 1 ? ' checked="checked"' : null) ?> value="1" /> 开启
		<input name="disc_status" type="radio" <?= ($discSet['disc_status'] == 0 ? ' checked="checked"' : null) ?> value="0" /> 关闭
	</div>
	<div class="main">
		<input type="submit" name="save" value="保存" />
	</div>
</form>
<div class="foot">
	<img src="/style/icons/str2.gif" alt="*"> <?= user::nick($user['id'],1,0,0) ?></a> | <b>讨论</b>
</div>

<?php require_once '../../sys/inc/tfoot.php';
