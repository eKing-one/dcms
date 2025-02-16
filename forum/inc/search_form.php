<form method='get' action='search.php'>
文本:<br />
<input type='text' name='search' value='<?php echo htmlentities(($_GET['search'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>'/><br />
搜寻地点:<br />
<select name='in'>
<option value='all'>所有位置</option>
<?php
$q = dbquery("SELECT `id`,`name` FROM `forum_f`" . ((!isset($user) || $user['level']==0) ? " WHERE `adm` = '0'" : null) . " ORDER BY `pos` ASC");
while ($forums = dbassoc($q)) {
	echo "<option value='f{$forums['id']}'" . (($searched['in']['m'] == 'f' && $searched['in']['id'] == $forums['id']) ? " selected='selected'" : null) . ">&gt;&gt; " . htmlspecialchars($forums['name']) . "</option>";
	$q2 = dbquery("SELECT `id`,`name` FROM `forum_r` WHERE `id_forum` = '$forums[id]' ORDER BY `time` DESC");
	while ($razdels = dbassoc($q2)) {
		echo "<option value='r{$razdels['id']}'" . (($searched['in']['m'] == 'r' && $searched['in']['id'] == $razdels['id']) ? " selected='selected'" : null) . ">&gt; " . htmlspecialchars($razdels['name']) . "</option>";
	}
}
?>
</select><br />
<input type='submit' value='开始搜索' /><br />
</form>