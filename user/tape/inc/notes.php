<?
/*
* $name описание действий объекта 
*/
if ($type == 'notes' && $post['avtor'] != $user['id']) // дневники
{
	$name = '创建' . ($avtor['pol'] == 1 ? null : "а") . ' 新日记';
}
/*
* Вывод блока с содержимым 
*/
if ($type  ==  'notes') {
	$notes = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` = '" . $post['id_file'] . "' LIMIT 1"));
	if ($notes['id']) {
		echo '<div class="nav1">';
		echo user::nick($avtor['id'],0,0,0) .' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a> ' . $name . '
		<b>' . text($notes['name']) . '</b> ' . $s1 . vremja($post['time']) . $s2 . '<br />';
		echo '</div>';
		echo '<div class="nav2" ><div class="text" >';
		echo output_text($notes['msg']) . '<br /></div>';
		echo '<a href="/plugins/notes/list.php?id=' . $notes['id'] . '"><img src="/style/icons/bbl5.png" alt="*"/> 
		(' . dbresult(dbquery("SELECT COUNT(*) FROM `notes_komm` WHERE `id_notes` = '$notes[id]'"), 0) . ')</a>';
	} else {
		echo '<div class="nav1">';
		echo user::nick($avtor['id'],0,0,0) . ' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a>';
		echo "</div>";
		echo '<div class="nav2">';
		echo "日记已被删除 =( $s1 " . vremja($post['time']) . " $s2";
	}
}
