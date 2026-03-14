<?php
require_once '../../sys/inc/start.php';
require_once '../../sys/inc/compress.php';
require_once '../../sys/inc/sess.php';
require_once '../../sys/inc/home.php';
require_once '../../sys/inc/settings.php';
require_once '../../sys/inc/db_connect.php';
require_once '../../sys/inc/ipua.php';
require_once '../../sys/inc/fnc.php';
require_once '../../sys/inc/user.php';

$note_list = $db->queryAll("SELECT * FROM `notes` WHERE `private` = 0 ORDER BY `id` DESC LIMIT {$set['p_str']}");

header("Content-type: application/rss+xml; charset=utf-8");
?>
<rss version="2.0">
	<!-- 日记频道 -->
	<channel>
		<title>日记</title>
		<link><?php echo get_http_type() . "://" . htmlentities($_SERVER['SERVER_NAME']); ?>/plugins/notes/</link>
		<language>zh-cn</language>
		<lastBuildDate><?php echo date("r", $db->queryColumn("SELECT MAX(time) FROM `notes`")); ?></lastBuildDate>
		<?php foreach ($note_list as $note_post): ?>
			<item>
				<title><?php echo $note_post['name']; ?></title>
				<link><?php echo htmlentities(get_http_type() . "://{$_SERVER['SERVER_NAME']}/plugins/notes/list.php?id={$note_post['id']}", ENT_QUOTES, 'UTF-8'); ?></link>
				<description><![CDATA[<?php echo output_text($note_post['msg'], true, true, false); ?>]]></description>
				<pubDate><?php echo date("r", $note_post['time']); ?></pubDate>
			</item>
		<?php endforeach; ?>
	</channel>
</rss>