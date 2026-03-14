<?php
// 获取在线用户数
$k_post = $db->queryColumn("SELECT COUNT(DISTINCT ul.id_user) AS online_users FROM `user_log` ul WHERE ul.last_online > NOW() - INTERVAL 10 MINUTE AND ul.ban = 0 AND ul.last_online = (SELECT MAX(last_online) FROM `user_log` ul2 WHERE ul2.id_user = ul.id_user AND ul2.last_online > NOW() - INTERVAL 10 MINUTE AND ul2.ban = 0)");
// 获取在线用户列表
$q = dbquery("SELECT ul.id, ul.id_user, ul.last_online, ul.url
              FROM `user_log` ul
              WHERE ul.last_online > NOW() - INTERVAL 10 MINUTE
                  AND ul.ban = 0
                  AND ul.last_online = (
                      SELECT MAX(last_online)
                      FROM `user_log` ul2
                      WHERE ul2.id_user = ul.id_user
                        AND ul2.last_online > NOW() - INTERVAL 10 MINUTE
                        AND ul2.ban = 0
                  )
              ORDER BY ul.last_online DESC
			  LIMIT 20;");
if ($k_post > 0) {
	echo '<a href="user/online.php"><div class="main">';
	echo "当前网站在线 ($k_post) 人</div></a>";

	echo "<div class='nav3'>";
	echo '<table>';
	echo '<tr>';
	while ($ank = dbassoc($q)) {
		$ank = user::get_user($ank['id_user']);

		if (isset($ank['id'])) {
			echo '<td class="oline_user">';

			echo '<a href="user/info.php?id=' . $ank['id'] . '">' . user::avatar($ank['id']) . '<br />';
			echo "<b><small>$ank[nick]</small></b></a>";

			echo '</td>';
		}
	}
	echo '</tr>';
	echo '</table>';
	echo '</div>';
}

/* 新闻 */
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `news`"), 0);
$q = dbquery("SELECT * FROM `news` ORDER BY `id` DESC LIMIT 2");
echo "<a href='news/'><div class='my'>";
echo "<img src='/style/icons/news.png' alt='*' /> 新闻 ";
include H . 'news/count.php';
echo "</div></a>";
if ($k_post > 0) {
	echo "<div class='mess'>";
	echo '<table>';
	echo '<tr>';
	while ($post = dbassoc($q)) {
		echo '<td style="width:350px; height:70px; vertical-align:top; display:inline-table; margin:2px;">';
		echo "<a href='news/news.php?id=$post[id]'>" . htmlspecialchars($post['title']) . "</a>";
		echo " (" . vremja($post['time']) . ")<br />";
		echo rez_text2($post['msg']);
		if ($post['link'] != NULL)	echo "<br /><a href='" . htmlentities($post['link'], ENT_QUOTES, 'UTF-8') . "'>详情 &rarr;</a><br />";
		echo "<img src='style/icons/bbl4.png' alt='*' /> (" . dbresult(dbquery("SELECT COUNT(*) FROM `news_komm` WHERE `id_news` = '$post[id]'"), 0) . ")<br />";
		echo '</td>';
	}
	echo '</tr>';
	echo '</table>';
	echo "   </div>";
}

/* 论坛 */
echo "<a href='forum/'><div class='my'>";
echo "<img src='style/icons/forum.png' alt='*' /> 论坛 ";
include H . 'forum/count.php';
echo "</div></a>";

$k_post = dbresult(dbquery("SELECT COUNT(`id`) FROM `forum_t`"), 0);
if ($k_post > 0) {
	echo "<div class='mess'>";
	$q = dbquery("SELECT * FROM `forum_t` ORDER BY `time_create` DESC LIMIT 5");
	while ($them = dbassoc($q)) {
		if ($num == 0) {
			echo '<div class="nav1">';
			$num = 1;
		} elseif ($num == 1) {
			echo '<div class="nav2">';
			$num = 0;
		}

		// 帖子图标
		echo '<img src="style/themes/' . $set['set_them'] . '/forum/14/them_' . $them['up'] . $them['close'] . '.png" alt="" /> ';
		// 帖子链接及评论总数
		echo '<a href="forum/' . $them['id_forum'] . '/' . $them['id_razdel'] . '/' . $them['id'] . '/"><b>' . htmlspecialchars($them['name']) . '</b></a> 
		      <a href="forum/' . $them['id_forum'] . '/' . $them['id_razdel']  . '/' . $them['id'] . '/?page=' . $pageEnd . '">
		      (' . dbresult(dbquery("SELECT COUNT(`id`) FROM `forum_p` WHERE `id_forum` = '" . $them['id_forum'] . "' AND `id_razdel` = '" . $them['id_razdel'] . "' AND `id_them` = '" . $them['id'] . "'"), 0) . ')</a><br/>';
		echo rez_text($them['text'], 25) . '<br/>';
		// 帖子作者昵称和创建时间
		echo user::nick($them['id_user'], 1, 1, 0) . ' (' . vremja($them['time_create']) . ') ';

		$post = dbarray(dbquery("SELECT `id`,`time`,`id_user` FROM `forum_p` WHERE `id_them` = '$them[id]' AND `id_forum` = '" . $them['id_forum'] . "' AND `id_razdel` = '" . $them['id_razdel'] . "'  ORDER BY `time` DESC LIMIT 1"));
		if (isset($post['id'])) {
			// 最后评论的用户和时间
			echo '/ ' . user::nick($post['id_user'], 1, 1, 0) . ' (' . vremja($post['time']) . ')<br />';
		}
		echo '</div>';
	}
	echo "</div>";
}

/* 日记 */
$notes_plus = dbresult(dbquery("SELECT COUNT(`id`)FROM `notes` WHERE `time`>'" . (time() - 86000) . "'"), 0);
$notes_count = dbresult(dbquery("SELECT COUNT(`id`)FROM `notes`"), 0);
if ($notes_plus > 0) {
	$notes_e = $notes_count . " + " . $notes_plus;
} else {
	$notes_e = $notes_count;
}

echo '<a href="plugins/notes/"><div class="my">';
echo '<img src="style/icons/dnev.png" alt="*" /> 日记 (' . $notes_e . ')';
echo '</div></a>';

$q = dbquery("SELECT * FROM `notes` ORDER BY `time` DESC LIMIT 5");
if (dbrows($q) == 0) {
	echo '<div class="nav2 mess main_no_notes_nav2">没有记录</div>';
} else {
	echo '<div class="mess">';
	while ($post = dbassoc($q)) {
		if ($post['private'] == 0) {
			$allowViewNote = true;
		} else {
			if (isset($user)) {
				if ($post['private'] == 1) {
					$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '{$user['id']}' AND `frend` = '{$post['id_user']}') OR (`user` = '{$post['id_user']}' AND `frend` = '{$user['id']}') LIMIT 1"), 0);
					if ($user['id'] == $post['id_user'] || $frend == 2  || user_access('notes_delete')) {
						$allowViewNote = true;
					} else {
						$allowViewNote = false;
					}
				} elseif ($post['private'] == 2 && ($user['id'] == $post['id_user'] || user_access('notes_delete'))) {
					$allowViewNote = true;
				} else {
					$allowViewNote = false;
				}
			} else {
				$allowViewNote = false;
			}
		}

		$count_comm = dbresult(dbquery("SELECT COUNT(`id`) FROM `notes_komm` WHERE `id_notes`='" . $post['id'] . "'"), 0);

		echo '<div class="nav2">';
		echo user::nick($post['id_user'], 1, 1, 0);
		echo ' : <a href="plugins/notes/list.php?id=' . $post['id'] . '"><span style="color:#06f">';
		if ($allowViewNote) {
			echo text($post['name']);
		} else {
			echo '[不可见]';
		}
		echo '</span></a><br />';
		if ($allowViewNote) {
			echo rez_text($post['msg'], 80);
			echo '<br />';
			echo ($post['share'] == 1 ? "(!) <i>转发</i><br/>" : null);
			echo '<img src="style/icons/comm_num_gray.png">' . $count_comm . '<span style="float:right;color:#666;"><small>';
			echo vremja($post['time']);
		} elseif ($post['private'] == 1) {
			echo '[内容仅好友可见]';
		} else {
			echo '[内容仅作者可见]';
		}
		echo '</small></div>';
	}
	echo '</div>';
}

/*  聊天室 */
echo "<a href='chat/'><div class='my'>";
echo "<img src='style/icons/chat.png' alt='*' /> 聊天室 ";
include H . 'chat/count.php';
echo "</div></a>";
$q = dbquery("SELECT * FROM `chat_rooms` ORDER BY `pos` ASC");
if (dbrows($q) != 0) {
	echo "<div class='mess'>";
	while ($room = dbassoc($q)) {
		/*-----------代码-----------*/
		if ($num == 0) {
			echo "  <div class='nav1'>";
			$num = 1;
		} elseif ($num == 1) {
			echo "  <div class='nav2'>";
			$num = 0;
		}
		/*---------------------------*/

		echo "<img src='style/themes/$set[set_them]/chat/14/room.png' alt='*' /> ";

		echo "<a href='chat/room/$room[id]/" . rand(1000, 9999) . "/'>$room[name] (" . dbresult(dbquery("SELECT COUNT(*) FROM `chat_who` WHERE `room` = '$room[id]'"), 0) . ")</a><br />";

		if ($room['opis'] != NULL) echo esc(trim(br(bbcode(smiles(links(stripcslashes(htmlspecialchars($room['opis'])))))))) . "<br />";
		echo "</div>";
	}
	echo "</div>";
}
