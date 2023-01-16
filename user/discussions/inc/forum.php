<?
/*
* 讨论标题
*/
if ($type == 'them' && $post['avtor'] != $user['id']) {
	$name = '论坛| 论坛主题';
} else if ($type == 'them' && $post['avtor'] == $user['id']) {
	$name = '论坛| 你的主题';
}
/*
* 显示在屏幕上
*/
if ($type == 'them') {
	$them = dbassoc(dbquery("SELECT * FROM `forum_t` WHERE `id` = '" . $post['id_sim'] . "' LIMIT 1"));
	if ($them['id']) {
?>
		<div class="nav1">
			<img src="/style/icons/forum.png" alt="*" /> <a href="/forum/<?= $them['id_forum'] ?>/<?= $them['id_razdel'] ?>/<?= $them['id'] ?>/?page=<?= $pageEnd ?>"><?= $name ?></a>
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
			<?php echo user::nick($avtor['id'], 1, 1, 0); ?>
			 &raquo; <b><?= text($them['name']) ?></b><br />
			<span class="text"><?= output_text($them['text']) ?></span>
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