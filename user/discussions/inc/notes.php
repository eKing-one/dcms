<?
/*
* Заголовок обсуждения
*/
if ($type == 'notes' && $post['avtor'] != $user['id']) {
	$name = '朋友的日记';
} else if ($type == 'notes' && $post['avtor'] == $user['id']) {
	$name = '你的日记';
}
/*
* Выводим на экран
*/
if ($type == 'notes') {
	$notes = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` = '" . $post['id_sim'] . "' LIMIT 1"));
	if ($notes['id']) {
?>
		<div class="nav1">
			<img src="/style/icons/dnev.png" alt="*" /> <a href="/plugins/notes/list.php?id=<?= $notes['id'] ?>&amp;page=<?= $pageEnd ?>"><?= $name ?></a>
			<?
			if ($post['count'] > 0) {
			?><b>
					<font color='red'>+<?= $post['count'] ?></font>
				</b><?
				}
					?>
			<span class="time"><?= $s1 . vremja($post['time']) . $s2 ?></span>
		</div>
		<div class="nav2">
			<b>
			<?php echo user::nick($avtor['id'], 1, 1, 0); ?>
			
			<br />
			<span class="text"><?= output_text($notes['msg']) ?></span>
		</div>
	<?
	} else {
	?>
		<div class="mess">
			论坛主题已被删除
			<span class="time"><?= $s1 . vremja($post['time']) . $s2 ?></span>
		</div>
<?
	}
}
?>