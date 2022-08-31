<?
/*
* 讨论题目
*/
if ($type == 'down' && $post['avtor'] != $user['id']) // обмен
{
	$name = '档案 | 朋友档案';
} else if ($type == 'down' && $post['avtor'] == $user['id']) {
	$name = '档案 | 你的档案';
}
/*
* Выводим на экран
*/
if ($type == 'down') {
	$file = dbassoc(dbquery("SELECT * FROM `downnik_files` WHERE `id` = '" . $post['id_sim'] . "' LIMIT 1"));
	if ($file['id']) {
?>
		<div class="nav1">
			<img src="/style/icons/disk.png" alt="*" />
			<a href="/user/personalfiles/<?= $file['id_user'] ?>/<?= $file['my_dir'] ?>/?id_file=<?= $file['id'] ?>&amp;page=<?= $pageEnd ?>"><?= $name ?></a>
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
				<font color='green'><?= $avtor['nick'] ?></font>
			</b>
			<?= ($avtor['id'] != $user['id'] ? '<a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a>' : '') ?>
			<?= medal($avtor['id']) ?> <?= online($avtor['id']) ?> &raquo; <b><?= text($foto['name']) ?></b><br />
			<span class="text"><?= output_text($file['opis']) ?></span>
		</div>
	<?
	} else {
	?>
		<div class="mess">
			该文件已被删除
			<span class="time"><?= $s1 . vremja($post['time']) . $s2 ?></span>
		</div>
<?
	}
}
?>