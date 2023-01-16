<?
if (user_access('forum_for_edit') && isset($_GET['act']) && isset($_GET['ok']) && $_GET['act'] == 'set' && isset($_POST['name']) && isset($_POST['opis']) && isset($_POST['pos'])) {
	$name = esc(stripcslashes(htmlspecialchars($_POST['name'])));
	if (strlen2($name) < 3) $err = '名字太短了';
	if (strlen2($name) > 32) $err = '名字太低了';
	$name = my_esc($name);
	if (!isset($_POST['icon']) || $_POST['icon'] == null)
		$FIcon = 'default';
	else
		$FIcon = preg_replace('#[^a-z0-9 _\-\.]#i', null, $_POST['icon']);
	$opis = esc(stripcslashes(htmlspecialchars($_POST['opis'])));
	if (isset($_POST['translit2']) && $_POST['translit2'] == 1) $opis = translit($opis);
	if (strlen2($opis) > 512) $err = '描述太长';
	$opis = my_esc($opis);
	$pos = intval($_POST['pos']);
	if (!isset($err)) {
		if ($user['level'] >= 3) {
			if (isset($_POST['adm']) && $_POST['adm'] == 1) {
				admin_log('论坛', '子论坛', "[医]子 '" . htmlspecialchars($forum['name']) . "' 仅用于管理");
				$adm = 1;
			} else $adm = 0;
			dbquery("UPDATE `forum_f` SET `adm` = '$adm' WHERE `id` = '$forum[id]' LIMIT 1");
		}
		if ($forum['name'] != $name) admin_log('论坛', '子论坛', "子论坛 '" . htmlspecialchars($forum['name']) . "'重命名为'$name'");
		if ($forum['opis'] != $opis) admin_log('论坛', '子论坛', "更改了子论坛的描述'$name'");
		dbquery("UPDATE `forum_f` SET `name` = '$name', `opis` = '$opis',`icon`='$FIcon', `pos` = '$pos' WHERE `id` = '$forum[id]' LIMIT 1");
		$forum = dbassoc(dbquery("SELECT * FROM `forum_f` WHERE `id` = '$forum[id]' LIMIT 1"));
		msg('更改已成功接受');
	}
}
if (isset($_GET['act']) && isset($_GET['ok']) && $_GET['act'] == 'delete' && user_access('forum_for_delete')) {
	dbquery("DELETE FROM `forum_f` WHERE `id` = '$forum[id]'");
	dbquery("DELETE FROM `forum_r` WHERE `id_forum` = '$forum[id]'");
	dbquery("DELETE FROM `forum_t` WHERE `id_forum` = '$forum[id]'");
	dbquery("DELETE FROM `forum_p` WHERE `id_forum` = '$forum[id]'");
	admin_log('论坛', '子论坛', "删除子论坛'" . htmlspecialchars($forum['name']) . "'");
	msg('子论坛已成功删除');
	err();
	aut();
	echo "<a href=\"/forum/\">到论坛</a><br />";
	include_once '../sys/inc/tfoot.php';
}
if (user_access('forum_razd_create') && (isset($_GET['act']) && isset($_GET['ok']) && $_GET['act'] == 'new' && isset($_POST['name']))) {
	$name = esc(stripcslashes(htmlspecialchars($_POST['name'])));
	if (strlen2($name) < 2) $err = '名字太短了';
	if (strlen2($name) > 32) $err = '名字太低了';
	if (!isset($err)) {
		admin_log('论坛', '部分', "创建一个部分 $name 在子论坛 $forum[name]");
		dbquery("INSERT INTO `forum_r` (`id_forum`, `opis`,`name`, `time`) values ('$forum[id]', '" . my_esc($_POST['opis']) . "','" . my_esc($name) . "', '$time')");
		msg('该部分已成功创建');
	}
}
