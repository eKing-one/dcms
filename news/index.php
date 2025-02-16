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
// 标题
$set['title'] = '新闻中心';
include_once '../sys/inc/thead.php';
title();
aut();

// 新闻数量
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `news`"),0);
$k_page = k_page($k_post,$set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
// 新闻精选
$q = dbquery("SELECT * FROM `news` ORDER BY `id` DESC LIMIT $start, $set[p_str]");

echo '<table class="post">';

if ($set['daily_news'] == '1') {
	echo '<div class="mess">';
	echo '<a href="daily_news.php">每日新闻</a>';
	echo '</div>';
}

// 如果没有新闻
if ($k_post == 0) {
	echo '<div class="mess">';
	echo '没有消息';
	echo '</div>';
}

// 循环输出新闻
while ($post = dbassoc($q)) {
	// 阶梯
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;
	// 新闻标题
	echo '<a id="link_menu" href="news.php?id=' . $post['id'] . '"><img src="/style/icons/rss.png" alt="*" /> ' . text($post['title']) . '</a> ';
	// 评论数量
	echo '(' . dbresult(dbquery("SELECT COUNT(*) FROM `news_komm` WHERE `id_news` = '$post[id]'"),0) . ')<br />';
	// 部分文本
	echo '<div class="text">' . output_text($post['msg']) . '</div>';
	echo '<a href="news.php?id=' . $post['id'] . '">阅读更多&gt;&gt;&gt;</a>';
	echo '</div>';
}
echo '</table>';

// 输出页数
if ($k_page>1) str('index.php?',$k_page,$page); 
if (user_access('adm_news')) {	
	echo '<div class="foot">';
	echo '<img src="/style/icons/ok.gif" alt="*" />  <a href="add.php">创建新闻项目</a><br />';	
	echo '</div>';
}

include_once '../sys/inc/tfoot.php';
