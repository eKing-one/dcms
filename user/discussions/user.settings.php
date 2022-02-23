<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
if (isset($user)) $ank['id'] = $user['id'];
if (isset($_GET['id'])) $ank['id'] = intval($_GET['id']);
$ank = user::get_user($ank['id']);
if (!$ank || $ank['id'] == 0) {
	header('Location: /index.php?' . SID);
	exit;
}
only_reg();
$frend = dbassoc(dbquery("SELECT * FROM `frends` WHERE `user` = '" . $user['id'] . "' AND `frend` = '$ank[id]' AND `i` = '1'", $db));
if (!isset($frend['user'])) {
	header('Location: index.php?' . SID);
	exit;
}
if (isset($_POST['save'])) {
	// Обсуждения фото
	if (isset($_POST['disc_foto']) && ($_POST['disc_foto'] == 0 || $_POST['disc_foto'] == 1)) {
		$disc = (int) $_POST['disc_foto'];
		dbquery("UPDATE `frends` SET `disc_foto` = '" . $disc . "' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
	}
	// Обсуждения файлов
	if (isset($_POST['disc_obmen']) && ($_POST['disc_obmen'] == 0 || $_POST['disc_obmen'] == 1)) {
		$disc = (int) $_POST['disc_obmen'];
		dbquery("UPDATE `frends` SET `disc_obmen` = '" . $disc . "' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
	}
	// Обсуждения статусов
	if (isset($_POST['disc_status']) && ($_POST['disc_status'] == 0 || $_POST['disc_status'] == 1)) {
		$disc = (int) $_POST['disc_status'];
		dbquery("UPDATE `frends` SET `disc_status` = '" . $disc . "' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
	}
	// Обсуждения дневников
	if (isset($_POST['disc_notes']) && ($_POST['disc_notes'] == 0 || $_POST['disc_notes'] == 1)) {
		$disc = (int) $_POST['disc_notes'];
		dbquery("UPDATE `frends` SET `disc_notes` = '" . $disc . "' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
	}
	// Обсуждения форум
	if (isset($_POST['disc_forum']) && ($_POST['disc_forum'] == 0 || $_POST['disc_forum'] == 1)) {
		$disc = (int) $_POST['disc_forum'];
		dbquery("UPDATE `frends` SET `disc_forum` = '" . $disc . "' WHERE `user` = '$user[id]' AND `frend` = '$ank[id]'");
	}
	$_SESSION['message'] = '更改已成功接受';
	header('Location: index.php');
	exit;
}
$set['title'] = '设置供稿 ' . $ank['nick'];
include_once '../../sys/inc/thead.php';
title();
err();
aut();
?>
<div id="comments" class="menus">
	<div class="webmenu">
		<a href="index.php">讨论</a>
	</div>
	<div class="webmenu">
		<a href="settings.php">设置</a>
	</div>
</div>
<form action="?id=<?= $ank['id'] ?>" method="post">
	<div class="mess">
		关于日记讨论的通知 <?= $ank['nick'] ?>.
	</div>
	<div class="nav1">
		<input name="disc_notes" type="radio" <?= ($frend['disc_notes'] == 1 ? ' checked="checked"' : null) ?> value="1" /> 是的
		<input name="disc_notes" type="radio" <?= ($frend['disc_notes'] == 0 ? ' checked="checked"' : null) ?> value="0" /> 否定
	</div>
	<div class="mess">
		关于主题讨论的通知 <?= $ank['nick'] ?> 在论坛.
	</div>
	<div class="nav1">
		<input name="disc_forum" type="radio" <?= ($frend['disc_forum'] == 1 ? ' checked="checked"' : null) ?> value="1" /> 是的
		<input name="disc_forum" type="radio" <?= ($frend['disc_forum'] == 0 ? ' checked="checked"' : null) ?> value="0" /> 否定
	</div>
	<div class="mess">
		关于照片中讨论的通知 <?= $ank['nick'] ?>.
	</div>
	<div class="nav1">
		<input name="disc_foto" type="radio" <?= ($frend['disc_foto'] == 1 ? ' checked="checked"' : null) ?> value="1" /> 是的
		<input name="disc_foto" type="radio" <?= ($frend['disc_foto'] == 0 ? ' checked="checked"' : null) ?> value="0" /> 否定
	</div>
	<div class="mess">
		关于文件中讨论的通知 <?= $ank['nick'] ?>.
	</div>
	<div class="nav1">
		<input name="disc_obmen" type="radio" <?= ($frend['disc_obmen'] == 1 ? ' checked="checked"' : null) ?> value="1" /> 是的
		<input name="disc_obmen" type="radio" <?= ($frend['disc_obmen'] == 0 ? ' checked="checked"' : null) ?> value="0" /> 否定
	</div>
	<div class="mess">
		关于状态讨论的通知 <?= $ank['nick'] ?>.
	</div>
	<div class="nav1">
		<input name="disc_status" type="radio" <?= ($frend['disc_status'] == 1 ? ' checked="checked"' : null) ?> value="1" /> 是的
		<input name="disc_status" type="radio" <?= ($frend['disc_status'] == 0 ? ' checked="checked"' : null) ?> value="0" /> 否定
	</div>
	<div class="main">
		<input type="submit" name="save" value="保存" />
	</div>
</form>
<?
include_once '../../sys/inc/tfoot.php';
?>