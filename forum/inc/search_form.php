<?php
echo "<form method='post' action='?search={$passgen}'>";
echo "文本:<br /><input type='text' name='text' value='" . htmlentities($searched['text'], ENT_QUOTES, 'UTF-8') . "' /><br />";
echo "搜寻地点:<br /><select name='in'>";
echo "<option value=''>所有位置</option>";

$q = dbquery("SELECT `id`,`name` FROM `forum_f`" . ((!isset($user) || $user['level']==0) ? " WHERE `adm` = '0'" : null) . " ORDER BY `pos` ASC");
while ($forums = dbassoc($q)) {
	echo "<option value='f{$forums['id']}'" . (($searched['in']['m'] == 'f' && $searched['in']['id'] == $forums['id']) ? " selected='selected'" : null) . ">&gt;&gt; " . htmlspecialchars($forums['name']) . "</option>";
	$q2 = dbquery("SELECT `id`,`name` FROM `forum_r` WHERE `id_forum` = '$forums[id]' ORDER BY `time` DESC");
	while ($razdels = dbassoc($q2)) {
		echo "<option value='r{$razdels['id']}'" . (($searched['in']['m'] == 'r' && $searched['in']['id'] == $razdels['id']) ? " selected='selected'" : null) . ">&gt; " . htmlspecialchars($razdels['name']) . "</option>";
	}
}
echo "</select><br />";
echo "<input type='submit' value='开始搜索' /><br />";
echo "</form>";