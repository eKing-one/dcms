<?php
/*
* $name 对象操作的描述
*/
if ($type == 'notes' && $post['avtor'] != $user['id']) {	// 日记
	$name = '创建' . ($avtor['pol'] == 1 ? null : "а") . ' 新日记';
}

/*
* 包含内容的块的输出
*/
if ($type  ==  'notes') {
	$notes = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` = '" . $post['id_file'] . "' LIMIT 1"));
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
		echo '<div class="nav1">';
		echo user::nick($avtor['id'], 0, 0, 0) . ' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a>' . $name . ' <b>';
		if ($allowViewNote) {
			echo text($notes['name']);
		} else {
			echo '[不可见]';
		}
		echo '</b> ' . $s1 . vremja($post['time']) . $s2 . '<br />';
		echo '</div>';
		echo '<div class="nav2" ><div class="text" >';
		if ($allowViewNote) {
			echo output_text($notes['msg']);
		} elseif ($notes['private'] == 1) {
			echo '<font color="#999">[内容仅好友可见]</font>';
		} else {
			echo '<font color="#999">[内容仅作者可见]</font>';
		}
		echo '<br /></div>';
		// 评论数量按钮
		echo '<a href="../../plugins/notes/list.php?id=' . $notes['id'] . '"><img src="../../style/icons/bbl5.png" alt="*"/> (' . dbresult(dbquery("SELECT COUNT(*) FROM `notes_komm` WHERE `id_notes` = '$notes[id]'"), 0) . ')</a>';
	} else {
		echo '<div class="nav1">';
		echo user::nick($avtor['id'], 1, 0, 0) . ' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a>';
		echo "</div>";
		echo '<div class="nav2">';
		echo "日记已被删除 =( $s1 " . vremja($post['time']) . " $s2";
	}
}
