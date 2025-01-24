<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
adm_check();
user_access('adm_set_sys', NULL, 'index.php?' . session_id());
$set['title'] = 'style.css';
include_once '../sys/inc/thead.php';
title();
err();
aut();

if (isset($_POST['robots'])) {
	$robots = $_POST['robots'];
} else {
	$robots = '';
}
if (isset($_POST['save'])) {
	$fs = fopen(H . "style/themes/{$set['set_them']}/style.css", "w");
	$text = fputs($fs, $robots);
	fclose($fs);
}
$text = '';
$f = file(H . "style/themes/{$set['set_them']}/style.css");
for ($i = 0; $i < count($f); $i++) {
	$text = "{$text}{$f[$i]}";
}
?>
<form method="POST">
	<textarea rows="20" cols="50" name="robots"><? echo $text; ?></textarea><br>
	<input type=submit name="save" value="保存">
</form>
<?php
if (user_access('adm_panel_show')) {
	echo "<div class='foot'>";
	echo "&laquo;<a href='/adm_panel/'> 到管理</a><br />";
	echo "</div>";
}
include_once '../sys/inc/tfoot.php';
