<?php
require_once '../../sys/inc/start.php';
require_once '../../sys/inc/compress.php';
require_once '../../sys/inc/sess.php';
require_once '../../sys/inc/home.php';
require_once '../../sys/inc/settings.php';
require_once '../../sys/inc/db_connect.php';
require_once '../../sys/inc/ipua.php';
require_once '../../sys/inc/fnc.php';
require_once '../../sys/inc/adm_check.php';
require_once '../../sys/inc/user.php';
$set['title'] = '加入优先展示';
require_once '../../sys/inc/thead.php';
title();

if (!isset($user)) header("location: ../../index.php?");
err();
aut();

if (isset($user)):
if (isset($_POST['stav']) && is_numeric($_POST['stav'])) {
		if (isset($_POST['msg'])) {
		$st = $_POST['stav'];
		$tm = $time + 60 * 60 * 24 * $_POST['stav'];
		$msg = my_esc($_POST['msg']);
		if ($user['money'] >= $st) {
			if (dbresult(dbquery("SELECT COUNT(*) FROM `liders` WHERE `id_user` = '$user[id]'"), 0) == 0) {
				dbquery("INSERT INTO `liders` (`id_user`, `stav`, `msg`, `time`, `time_p`) values('$user[id]', '$st', '$msg', '$tm', '$time')");
			} else {
				dbquery("UPDATE `liders` SET `time` = '$tm', `time_p` = '$time', `msg` = '$msg', `stav` = '$st' WHERE `id_user` = '$user[id]'");
			}
			dbquery("UPDATE `user` SET `money` = '" . ($user['money'] - $st) . "' WHERE `id` = '$user[id]' LIMIT 1");
			$_SESSION['message'] = '你已经成功地成为一个领导者';
			header("Location: ./index.php?ok");
			exit;
		} else {
			$err = '你没有足够的资金';
		}
	} else {
		$err = '信息字段不能为空';
	}
}
err();
?>

<div class="foot">
	<img src="../../style/icons/str2.gif" alt="S"/> <a href="./">附加服务</a> | <b>成为领导者</b>
</div>
<div class="mess">
	至少需要<b style="color:red;"> 1 </b>
	<b style="color:green;"><?php echo $sMonet[1]; ?></b>
	你的个人资料将在在线列表优先展示！
</div>
<form class="main" method="post" action="?">
	花费: 
	<select name="stav">
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
		<option value="7">7</option>
	</select> 
	<?php echo $sMonet[0]; ?>
	<br />
	留言（215 字节）
	<textarea name="msg"></textarea>
	<br />
	<input value="加入优先展示" type="submit" />
</form>

<?php endif; ?>

<div class="foot">
	<img src="../../style/icons/str2.gif" alt="S"/> <a href="./">附加服务</a>
</div>

<?php
require_once '../../sys/inc/tfoot.php';
