<?
/*
* $name описание действий объекта 
*/
if ($type == 'them' && $post['avtor'] != $user['id']) {
	$name = '创建' . ($avtor['pol'] == 1 ? null : "а") . ' 在论坛主题 ';
}
/*
* Вывод блока с содержимым 
*/
if ($type == 'them') {
	$them = dbassoc(dbquery("SELECT * FROM `forum_t` WHERE `id` = '" . $post['id_file'] . "' LIMIT 1"));
	$razdel = dbassoc(dbquery("SELECT * FROM `forum_r` WHERE `id` = '$them[id_razdel]' LIMIT 1"));
	$forum = dbassoc(dbquery("SELECT * FROM `forum_f` WHERE `id` = '$razdel[id_forum]' LIMIT 1"));
	if ($them['id']) {
		echo '<div class="nav1">';
		echo user::nick($avtor['id'],0,0,0) . ' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a> ' . $name .  $s1 . vremja($post['time']) . $s2 . '<br />';
		echo '</div>';
		echo '<div class="nav2">';
		echo ' <a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/' . $them['id'] . '/"> ' . text($them['name']) . '</a> ';
		echo '<div class="text">' . output_text($them['text']) . '<br /></div>';
	} else {
		echo '<div class="nav1">';
		echo user::nick($avtor['id'],0,0,0) . " <a href='user.settings.php?id=$avtor[id]'>[!]</a>";
		echo medal($avtor['id']) . online($avtor['id']);
		echo '</div>';
		echo '<div class="nav2">';
		echo '该主题已被删除 =( ' . $s1 . vremja($post['time']) . $s2;
	}
}
