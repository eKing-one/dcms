<?//返回管理面板
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
user_access('adm_set_chat',null,'index.php?'.session_id());
adm_check();
$set['title']='聊天问题';
include_once '../sys/inc/thead.php';
title();

if (isset($_GET['act']) && isset($_POST['sep']) && isset($_FILES['file']['tmp_name'])) {
	$sep = $_POST['sep'];
	if (isset($_POST['replace'])) dbquery('TRUNCATE `chat_vopros`');
	$k_add = 0;
	$list = file($_FILES['file']['tmp_name']);
	for($i=0; $i<count($list); $i++) {
		if (substr_count($list[$i], $sep) == 0) continue;
		list($vopr, $otv) = explode($sep, trim($list[$i]));
		if (strlen2($vopr) < 10 || strlen2($otv) < 2) continue;
		dbquery("INSERT INTO `chat_vopros` (`vopros`, `otvet`) VALUES ('" . my_esc($vopr) . "', '" . my_esc($otv) . "')");
		$k_add++;
	}
	admin_log('聊天', '增编', "添加 {$k_add} 问题");
	msg("成功添加 {$k_add} 从 {$i} 问题");
}
err();
aut();

echo "数据库中的总问题: " . dbresult(dbquery("SELECT COUNT(*) FROM `chat_vopros`"), 0) . "<br />";
echo "<form method='post' action='?act={$passgen}' enctype='multipart/form-data'>";
echo "<input type='file' name='file' /><br />";
echo "分离器:<br /><input value='|' name='sep' type='text' /><br />";
echo "仅支持UTF-8编码的文本文件。<br />每个问题-答案应该在一个单独的行。<br />";
echo "<input value='更换/更换' name='replace' type='submit' /><br />";
echo "<input value='添加' name='add' type='submit' /><br />";
echo "</form>";
echo "<div class='foot'>";
echo "&raquo;<a href='/adm_panel/settings_chat.php'>聊天设置</a><br />";
echo "&raquo;<a href='/adm_panel/chat_shut.php'>笑话</a><br />";
if (user_access('adm_panel_show')) echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
echo "</div>";
include_once '../sys/inc/tfoot.php';
