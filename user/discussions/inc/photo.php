<?php
/*
* Заголовок обсуждения
*/
if ($type == 'photo' && $post['avtor'] != $user['id']) {
	$name = '朋友的照片';
} else if ($type == 'photo' && $post['avtor'] == $user['id']) {
	$name = '你的照片';
}
/*
* Выводим на экран
*/
if ($type == 'photo') {
	$photo = dbassoc(dbquery("SELECT * FROM `gallery_photo` WHERE `id` = '" . $post['id_sim'] . "' LIMIT 1"));
	if ($photo['id']) {

		echo '<div class="nav1">
			<img src="/style/icons/camera.png" alt="*" /> <a href="/photo/'.$avtor['id'].'/'.$photo['id_gallery'].'/'.$photo['id'].'/?page='.$pageEnd.'">'.$name.'</a>';
			if ($post['count'] > 0) {
			echo '<b>
					<font color="red">+'.$post['count'].' </font>
				</b>';
				}
					?>
			<span class="time"><?= $s1 . vremja($post['time']) . $s2 ?></span>
		</div>
		<div class="nav2">
			<b>
				<font color='green'><?= $avtor['nick'] ?></font>
			</b>
			<?= ($avtor['id'] != $user['id'] ? '<a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a>' : '') ?>
			<?= medal($avtor['id']) ?> <?= online($avtor['id']) ?> &raquo; <b><?= text($photo['name']) ?></b><br />
			<img src="/photo/photo50/<?= $photo['id'] ?>.<?= $photo['ras'] ?>" alt="Image" />
		</div>
	<?
	} else {
	?>
		<div class="mess">
			照片已被删除
			<span class="time"><?= $s1 . vremja($post['time']) . $s2 ?></span>
		</div>
<?
	}
}
?>