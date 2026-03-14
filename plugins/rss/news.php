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

$new_list = $db->queryAll("SELECT * FROM `news` ORDER BY `id` DESC LIMIT {$set['p_str']}");

header("Content-type: application/rss+xml; charset=utf-8");
?>
<rss version="2.0">
	<!-- 新闻频道 -->
	<channel>
		<title>新闻 <?php echo htmlentities($_SERVER['SERVER_NAME']); ?></title>
		<link><?php echo get_http_type() . "://" . htmlentities($_SERVER['SERVER_NAME']); ?>/news/</link>
		<description>新闻 <?php echo htmlentities($_SERVER['SERVER_NAME']); ?></description>
		<language>zh-cn</language>
		<copyright>© <?php echo htmlspecialchars($_SERVER['HTTP_HOST']); ?> - <?php echo date('Y'); ?></copyright>
		<webMaster>j4fyfxqwn@mozmail.com</webMaster>
		<lastBuildDate><?php echo date("r", dbresult(dbquery("SELECT MAX(time) FROM `news`"), 0)); ?></lastBuildDate>
		<?php foreach ($new_list as $new_post): ?>
			<item>
				<title><?php echo $new_post['title']; ?></title>
				<?php if ($new_post['link'] != NULL): ?>
					<?php if (!preg_match('#^https?://#', $new_post['link'])): ?>
						<link><?php echo htmlentities(get_http_type() . "://{$_SERVER['SERVER_NAME']}{$new_post['link']}", ENT_QUOTES, 'UTF-8'); ?></link>
					<?php else: ?>
						<link><?php echo htmlentities($new_post['link'], ENT_QUOTES, 'UTF-8'); ?></link>
					<?php endif; ?>
				<?php endif; ?>
				<description><![CDATA[<?php echo output_text($new_post['msg'], true, true, false); ?>]]></description>
				<pubDate><?php echo date("r", $new_post['time']); ?></pubDate>
			</item>
		<?php endforeach; ?>
	</channel>
</rss>