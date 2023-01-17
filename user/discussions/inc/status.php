<?
/*
* 讨论标题
*/
if ($type == 'status' && $post['avtor'] != $user['id']) {
	$name = '好友状态';
} else if ($type == 'status' && $post['avtor'] == $user['id']) {
	$name = '您的状态';
}
/*
* Выводим на экран
*/
if ($type == 'status') {
	$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id` = '" . $post['id_sim'] . "' LIMIT 1"));
	if ($status['id']) {
?>
		<div class="nav1">
			<img src="/style/icons/comment.png" alt="*" /> <a href="/user/status/komm.php?id=<?= $status['id'] ?>"><?= $name ?></a>
			
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
			 <br />
			<div class="st_1"></div>
			<div class="st_2">
				<span class="text"><?= output_text($status['msg']) ?></span><br />
			</div>
			<a href="/user/status/komm.php?id=<?= $status['id'] ?>"><img src="/style/icons/bbl4.png" alt="*" />
				<?= dbresult(dbquery("SELECT COUNT(*) FROM `status_komm` WHERE `id_status` = '$status[id]'"), 0) ?></a>
			<?
			$l = dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]'"), 0);
			if (isset($user) && $user['id'] != $avtor['id']) {
				if (
					$user['id'] != $avtor['id'] &&
					dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]' AND `id_user` = '$user[id]' LIMIT 1"), 0) == 0
				) {
			?><a href="?likestatus=<?= $status['id'] ?>&amp;page=<?= $page ?>"><img src="/style/icons/like.gif" alt="*" />赞!</a> &bull; <?
				$like = $l;
			} else {
				?><img src="/style/icons/like.gif" alt="*" /> 你和 <?
				$like = $l - 1;
			}
		} else {
?><img src="/style/icons/like.gif" alt="*" /> <?
			$like = $l;
		}
												?>
			<a href="/user/status/like.php?id=<?= $status['id'] ?>"><?= $like ?> 个用户</a>觉得很赞！
		</div>
	<?
	} else {
	?>
		<div class="mess">
			状态已被删除
			<span class="time"><?= $s1 . vremja($post['time']) . $s2 ?></span>
		</div>
<?
	}
}
?>