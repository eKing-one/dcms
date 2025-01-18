<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
if (dbresult(dbquery("SELECT COUNT(*) FROM `news` LIMIT 1", $db), 0) == 0) exit;
header("Content-type: application/rss+xml");
echo "<rss version=\"2.0\">";
echo "<channel>";
echo "<title>新闻 " . htmlentities($_SERVER['SERVER_NAME']) . "</title>";
echo "<link>http://" . htmlentities($_SERVER['SERVER_NAME']) . "</link>";
echo "<description>新闻 " . htmlentities($_SERVER['SERVER_NAME']) . "</description>";
echo "<language>zh-CN</language>";
//echo "<webMaster>$set[adm_mail]</webMaster>";
echo "<lastBuildDate>" . date("r", dbresult(dbquery("SELECT MAX(time) FROM `news`", $db), 0)) . "</lastBuildDate>";
$q = dbquery("SELECT * FROM `news` ORDER BY `id` DESC LIMIT {$set['p_str']}");
while ($post = dbassoc($q)) {
	echo "<item>";
	echo "<title>{$post['title']}</title>";
	if ($post['link'] != NULL) {
		if (!preg_match('#^https?://#', $post['link'])) {
			echo "<link>" . htmlentities("http://{$_SERVER['SERVER_NAME']}{$post['link']}", ENT_QUOTES, 'UTF-8') . "</link>";
		} else {
			echo "<link>" . htmlentities($post['link'], ENT_QUOTES, 'UTF-8') . "</link>";
		}
	}
	echo "<description><![CDATA[";
	echo output_text($post['msg'], true, true, false) . "";
	echo "]]></description>";
	echo "<pubDate>" . date("r", $post['time']) . "</pubDate>";
	echo "</item>";
}
echo "</channel>";
echo "</rss>";