<?PHP
/* Онлайн пользователи */

$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_last` > '" . (time() - 600) . "'"), 0);
$q = dbquery("SELECT `id` FROM `user` WHERE `date_last` > '" . (time() - 600) . "' ORDER BY `rating` DESC LIMIT 10");
if ($k_post > 0) {
	echo "<a href='/online.php'><div class='main'>";
	echo "现在在网站上 ($k_post) 人.</div></a>";

	echo "<div class='nav3'>";
	echo '<table>';
	echo '<tr>';
	while ($ank = dbassoc($q)) {
		$ank = get_user($ank['id']);

		echo '<td style="width:60px; height:70px; vertical-align:top; border:1px solid black; text-align:center; display:inline-table; margin:2px;">';

		echo "<a href='/info.php?id=$ank[id]'>" . avatar($ank['id']) . '<br />';
		echo "<b><small>$ank[nick]</small></b></a>";

		echo '</td>';
	}
	echo '</tr>';
	echo '</table>';
	echo '</div>';
}















/* 新闻 */
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `news`"), 0);
$q = dbquery("SELECT * FROM `news` ORDER BY `id` DESC LIMIT 2");
echo "<a href='/news'><div class='my'>";
echo "<img src='/style/icons/news.png' alt='*' /> 新闻 ";
include H . 'news/count.php';
echo "</div></a>";
if ($k_post > 0) {
	echo "<div class='mess'>";
	echo '<table>';
	echo '<tr>';
	while ($post = dbassoc($q)) {
		echo '<td style="width:350px; height:70px; vertical-align:top; display:inline-table; margin:2px;">';
		echo "<a href='/news/news.php?id=$post[id]'>" . htmlspecialchars($post['title']) . "</a>";
		echo "(" . vremja($post['time']) . ")<br />";
		echo rez_text2(output_text($post['msg']));
		if ($post['link'] != NULL)	echo "<br /><a href='" . htmlentities($post['link'], ENT_QUOTES, 'UTF-8') . "'>详情 &rarr;</a><br />";
		echo "<img src='/style/icons/bbl4.png' alt='*' /> (" . dbresult(dbquery("SELECT COUNT(*) FROM `news_komm` WHERE `id_news` = '$post[id]'"), 0) . ")<br />";
		echo '</td>';
	}
	echo '</tr>';
	echo '</table>';
	echo "   </div>";
}
/* Форум */
echo "<a href='/forum'><div class='my'>";
echo "<img src='/style/icons/forum.png' alt='*' /> 论坛 ";
include H . 'forum/count.php';
echo "</div></a>";

$k_post = dbresult(dbquery("SELECT COUNT(`id`) FROM `forum_t`"), 0);
if ($k_post > 0) {
	echo "<div class='mess'>";
	$q = dbquery("SELECT * FROM `forum_t` ORDER BY `time_create` DESC LIMIT 5");
	while ($them = dbassoc($q)) {
		// Лесенка дивов
		if ($num == 0) {
			echo '<div class="nav1">';
			$num = 1;
		} elseif ($num == 1) {
			echo '<div class="nav2">';
			$num = 0;
		}

		// Иконка темы
		echo '<img src="/style/themes/' . $set['set_them'] . '/forum/14/them_' . $them['up'] . $them['close'] . '.png" alt="" /> ';
		// Ссылка на тему
		echo '<a href="/forum/' . $them['id_forum'] . '/' . $them['id_razdel'] . '/' . $them['id'] . '/"><b>' . htmlspecialchars($them['name']) . '</b></a> 
	<a href="/forum/' . $them['id_forum'] . '/' . $them['id_razdel']  . '/' . $them['id'] . '/?page=' . $pageEnd . '">
	(' . dbresult(dbquery("SELECT COUNT(`id`) FROM `forum_p` WHERE `id_forum` = '" . $them['id_forum'] . "' AND `id_razdel` = '" . $them['id_razdel'] . "' AND `id_them` = '" . $them['id'] . "'"), 0) . ')</a><br/>';
		echo rez_text($them['text'], 112) . '<br/>';
		//主题作者
		echo group($them['id_user']) . ' ';
		echo user::nick($them['id_user'], 1, 1, 1) . ' (' . vremja($them['time_create']) . ') ';

		// 最后一个岗位
		$post = dbarray(dbquery("SELECT `id`,`time`,`id_user` FROM `forum_p` WHERE `id_them` = '$them[id]' AND `id_forum` = '" . $them['id_forum'] . "' AND `id_razdel` = '" . $them['id_razdel'] . "'  ORDER BY `time` DESC LIMIT 1"));
		if (isset($post['id'])) {
			// 最后一篇文章的作者
			echo '/ ' . user::nick($post['id_user'], 1, 1, 1) . ' (' . vremja($post['time']) . ')<br />';
		}

		echo '</div>';
	}
	echo "</div>";
}

/*  Чат комнаты */
echo "<a href='/chat'><div class='my'>";
echo "<img src='/style/icons/chat.png' alt='*' /> 聊天室 ";
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

		echo "<img src='/style/themes/$set[set_them]/chat/14/room.png' alt='*' /> ";

		echo "<a href='/chat/room/$room[id]/" . rand(1000, 9999) . "/'>$room[name] (" . dbresult(dbquery("SELECT COUNT(*) FROM `chat_who` WHERE `room` = '$room[id]'"), 0) . ")</a><br />";

		if ($room['opis'] != NULL) echo esc(trim(br(bbcode(smiles(links(stripcslashes(htmlspecialchars($room['opis'])))))))) . "<br />";
		echo "</div>";
	}
	echo "</div>";
}
