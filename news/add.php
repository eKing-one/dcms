<?
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
// Переменные по умолчанию
if (isset($_POST['view']))
{
	$news['title'] = $_POST['title'];
	$news['msg'] = $_POST['msg'];
	$news['link'] = $_POST['link'];
	$news['id_user'] = $user['id'];
}else{
	$news['title'] = null;
	$news['msg'] = null;
	$news['link'] = null;
	$news['id_user'] = null;
}
if (isset($_POST['title']) && isset($_POST['msg']) && isset($_POST['link']) && isset($_POST['ok']))
{
	$title = esc($_POST['title'],1);
	$link = esc($_POST['link'],1);
	$msg = esc($_POST['msg'],1);
	if ($link != NULL && !preg_match('#^https?://#',$link) && !preg_match('#^/#i',$link))$link='/'.$link;
	 if (strlen2($title)>50){$err = '新闻标题太大了';}
	if (strlen2($title)<3){$err = '短标题';}
	$mat = antimat($title);
	if ($mat)$err[] = '在新闻标题中发现了一个禁止字符: '.$mat;
	if (strlen2($msg)>10024){$err='新闻的内容太大了';}
	if (strlen2($msg)<2){$err='新闻的内容太小了';}
	$mat = antimat($msg);
	if ($mat)$err[]='在内容里发现一个禁止的字符 '.$mat;
	$title = my_esc($_POST['title']);
	$msg = my_esc($_POST['msg']);if (!isset($err)){
	$ch = intval($_POST['ch']);
	$mn = intval($_POST['mn']);
	$main_time = time()+$ch*$mn*60*60*24;if ($main_time<=time())
	$main_time = 0;
	dbquery("INSERT INTO `news` (`id_user`,`time`, `msg`, `title`, `main_time`, `link`) values('".$user['id']."','$time', '".$msg."', '$title', '$main_time', '$link')");
	dbquery("update `user` set `news_read` = '0'");
	$news['id'] = mysql_insert_id();
	dbquery("OPTIMIZE TABLE `news`");
	dbquery("UPDATE `user` SET `news_read` = '0'");
	if (isset($_POST['mail'])) // Расслылка новостей на майл
	{
		$q = dbquery("SELECT `ank_mail` FROM `user` WHERE `set_news_to_mail` = '1' AND `ank_mail` <> ''");
		while ($ank = dbassoc($q))
		{
			dbquery("INSERT INTO `mail_to_send` (`mail`, `them`, `msg`) values('$ank[ank_mail]', '新闻', '".trim(br(bbcode(links(stripcslashes(htmlspecialchars($msg))))))."')");
		}
	}
	$_SESSION['message'] = '新闻创建成功';
	header("Location: news.php?id=$news[id]");
	exit;
	}
}
$set['title'] = '创建新闻';
include_once '../sys/inc/thead.php';
title();
err();
aut(); // форма авторизации
if (isset($_POST['view']) && !isset($err))
{
	echo '<div class="main2">';
	echo text($news['title']);
	echo '</div>';
	echo'<div class="mess">';
	echo output_text($news['msg']) . '<br />';
	echo '</div>';
	if ($news['link'] != NULL)
	{
		echo '<div class="main">';
		echo '<a href="' . htmlentities($news['link'], ENT_QUOTES, 'UTF-8') . '">详情 &rarr;</a><br />';
		echo '</div>';
	}
}
echo '<form class="mess" method="post" name="message" action="add.php">';
echo '新闻标题:<br /><input name="title" size="16" maxlength="32" value="' . text($news['title']) . '" type="text" /><br />';
$msg2 = text($news['msg']);
if (is_file(H.'style/themes/'.$set['set_them'].'/altername_post_form.php'))
{
	include_once H.'style/themes/'.$set['set_them'].'/altername_post_form.php';
}else{
	echo '信息:' . $tPanel . '<textarea name="msg">' . $msg2 . '</textarea><br />';
}
echo '链接:<br /><input name="link" size="16" maxlength="64" value="' . text($news['link']) . '" type="text" /><br />';
echo '在主页显示时间:<br />';
echo '<input type="text" name="ch" size="3" value="'.(isset($_POST['ch'])?"".intval($_POST['ch'])."":"1").'" />';
echo '<select name="mn">';
echo '  <option value="0" ' . (isset($_POST['mn']) && $_POST['mn'] == 0 ? "selected='selected'" : null) . '>   </option>';
echo '  <option value="1" ' . (isset($_POST['mn']) && $_POST['mn'] == 1 ? "selected='selected'" : null) . '>天数</option>';
echo '  <option value="7" ' . (isset($_POST['mn']) && $_POST['mn'] == 7 ? "selected='selected'" : null) . '>星期</option>';
echo '  <option value="31" ' . (isset($_POST['mn']) && $_POST['mn'] == 31 ? "selected='selected'" : null).'>个月</option>';
echo '</select><br />';
echo '<input value="查看" type="submit" name="view" /> ';
if (isset($_POST['view']))echo '<input value="完成" type="submit" name="ok" />';
echo '</form>';
echo '<div class="foot">';
echo '<img src="/style/icons/str.gif" alt="*"> <a href="index.php">新闻中心</a><br />';
echo '</div>';
include_once '../sys/inc/tfoot.php';
?>