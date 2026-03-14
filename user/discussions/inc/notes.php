<?php
/*
* 讨论标题
*/
if ($type == 'notes' && $post['avtor'] != $user['id']) {
	$name = '朋友的日记';
} else if ($type == 'notes' && $post['avtor'] == $user['id']) {
	$name = '你的日记';
}

/*
* 显示
*/
if ($type == 'notes') {
	$notes = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` = '" . $post['id_sim'] . "' LIMIT 1"));
	if (isset($notes['id']) && $notes['id']) {
		if ($notes['private'] == 0) {
			$allowViewNote = true;
		} else {
			if (isset($user)) {
				if ($notes['private'] == 1) {
					$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '{$user['id']}' AND `frend` = '{$notes['id_user']}') OR (`user` = '{$notes['id_user']}' AND `frend` = '{$user['id']}') LIMIT 1"), 0);
					if ($user['id'] == $notes['id_user'] || $frend == 2  || user_access('notes_delete')) {
						$allowViewNote = true;
					} else {
						$allowViewNote = false;
					}
				} elseif ($notes['private'] == 2 && ($user['id'] == $notes['id_user'] || user_access('notes_delete'))) {
					$allowViewNote = true;
				} else {
					$allowViewNote = false;
				}
			} else {
				$allowViewNote = false;
			}
		}
		echo "<div class='nav1'><img src='/style/icons/dnev.png' alt='*' /> <a href='/plugins/notes/list.php?id={$notes['id']}&amp;page={$pageEnd}'>$name</a>";
		if ($post['count'] > 0) {
			echo " <b><font color='red'>+{$post['count']}</font></b>";
		}
		echo ' <span class="time">' . $s1 . vremja($post['time']) . $s2 . '</span></div>';
		echo '<div class="nav2">';
		echo '<b>' . user::nick($avtor['id'], 1, 1, 0) . '</b><br />';
		if ($allowViewNote) {
			echo '<span class="text">' . output_text($notes['msg']) . '</span>';
		} elseif ($notes['private'] == 1) {
			echo '<font color="#999">[内容仅好友可见]</font>';
		} else {
			echo '<font color="#999">[内容仅作者可见]</font>';
		}
		echo '</div>';
	} else {
		echo '<div class="mess">论坛主题已被删除<span class="time">' . $s1 . vremja($post['time']) . $s2 . '</span></div>';
	}
}