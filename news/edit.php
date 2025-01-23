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
user_access('adm_news',null,'index.php?'.SID);

if (!isset($_GET['id']) && !is_numeric($_GET['id'])) {
	header("Location: index.php?".SID);
	exit;
}
if (dbresult(dbquery("SELECT COUNT(*) FROM `news` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1",$db), 0) == 0) {
	header("Location: index.php?".SID);
	exit;
}

$news = dbassoc(dbquery("SELECT * FROM `news` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));
if (isset($_POST['view'])) {
	$news['title'] = $_POST['title'];
	$news['msg'] = $_POST['msg'];
	$news['link'] = $_POST['link'];
	$news['id_user'] = $user['id'];
}

if (isset($_POST['title']) && isset($_POST['msg']) && isset($_POST['link']) && isset($_POST['ok'])) {
	$title = esc($_POST['title'],1);
	$link = esc($_POST['link'],1);
	if ($link != NULL && !preg_match('#^https?://#',$link) && !preg_match('#^/#i',$link))
	$link='/'.$link;
	$msg = esc($_POST['msg']);
	if (strlen2($title) > 50) $err = '新闻标题太长';
	if (strlen2($title) < 3) $err = '标题太短了(三个字以上)';
	$mat = antimat($title);
	if ($mat) $err[] = '新闻标题中包含特殊字符: ' . $mat;
	if (strlen2($msg) > 10024) $err = '新闻内容太多(超过10024字)';
	if (strlen2($msg) < 2) $err = '新闻的内容太少了';
	$mat = antimat($msg);
	if ($mat)$err[] = '在内容里发现一个禁止的字符 ' . $mat;
	$title = my_esc($_POST['title']);
	$msg = my_esc($_POST['msg']);
	if (!isset($err)) {
		$ch = intval($_POST['ch']);
		$mn = intval($_POST['mn']);
		$main_time = time() + $ch * $mn * 60 * 60 * 24;
		if ($main_time <= time())
		$main_time = 0;
		dbquery("UPDATE `news` SET `title` = '$title', `msg` = '$msg', `link` = '$link', `main_time` = '$main_time', `time` = '$time' WHERE `id` = '$news[id]' LIMIT 1");
		dbquery("UPDATE `user` SET `news_read` = '0'");
		$_SESSION['message'] = '成功更改';
		header("Location: news.php?id={$news['id']}");
		exit;
	}
}

$set['title'] = '新闻编辑';
include_once '../sys/inc/thead.php';
title();
err();
aut(); // 授权形式

if (isset($_POST['view']) && !isset($err)) {
	echo '<div class="main2">';
	echo text($news['title']);
	echo '</div>';
	echo '<div class="mess">';
	echo output_text($news['msg']) . '<br />';
	echo '</div>';
	if ($news['link'] != NULL) {
		echo '<div class="main">';
		echo '<a href="' . htmlentities($news['link'], ENT_QUOTES, 'UTF-8') . '">查看详情 &rarr;</a><br />';
		echo '</div>';
	}
}

echo '<form class="mess" method="post" name="message" action="?id=' . $news['id'] . '">';
echo '新闻标题:<br /><input name="title" size="16" maxlength="32" value="' . text($news['title']) . '" type="text" /><br />';

$insert = text($news['msg']);
if (is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php')) {
	include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
} else {
	echo '信息:' . $tPanel . '<textarea name="msg">' . $insert . '</textarea><br />';
}
echo '链接:<br /><input name="link" size="16" maxlength="64" value="' . text($news['link']) . '" type="text" /><br />';
echo '在主页显示时间:<br />';
echo '<input type="text" name="ch" size="3" value="' . (isset($_POST['ch']) ? intval($_POST['ch']) : "1") . '" />';
echo '<select name="mn">';
echo '  <option value="0" ' . (isset($_POST['mn']) && $_POST['mn'] == 0 ? "selected='selected'" : null) . '>   </option>';
echo '  <option value="1" ' . (isset($_POST['mn']) && $_POST['mn'] == 1 ? "selected='selected'" : null) . '>天</option>';
echo '  <option value="7" ' . (isset($_POST['mn']) && $_POST['mn'] == 7 ? "selected='selected'" : null) . '>星期</option>';
echo '  <option value="31" ' . (isset($_POST['mn']) && $_POST['mn'] == 31 ? "selected='selected'" : null) . '>个月</option>';
echo '</select><br />';
echo '<input value="预览" type="submit" name="view"/> ';
echo '<input value="完成" type="submit" name="ok"/>';
echo '</form>';
echo'<div class="foot">';
echo '<img src="/style/icons/str.gif" alt="*"> <a href="index.php">新闻中心</a> | <a href="news.php?id=' . $news['id'] . '">' . text($news['title']) . '</a><br />';
echo '</div>';
include_once '../sys/inc/tfoot.php';
