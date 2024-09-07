<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';

$set['title'] = '领袖'; //网页标题
include_once '../../sys/inc/thead.php';
title();
aut();
err();
echo '<div class="foot">';
echo '<img src="/style/icons/lider.gif" alt="S"/> <a href="/user/money/liders.php">成为领袖</a>';
echo '</div>';
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `liders` WHERE `time` > '$time'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo '<table class="post">';
if ($k_post == 0) {
	echo '<div class="mess">';
	echo '目前没有领袖';
	echo '</div>';
}
$q = dbquery("SELECT * FROM `liders` WHERE `time` > '$time' ORDER BY stav DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q)) {
	$ank = user::get_user($post['id_user']);
	/*-----------代码-----------*/
	if ($num == 0) {
		echo '<div class="nav1">';
		$num = 1;
	} elseif ($num == 1) {
		echo '<div class="nav2">';
		$num = 0;
	}
	/*---------------------------*/
	echo user::avatar($ank['id']); // Аватарка
	echo user::nick($ank['id'],1,1,0) . ' (' . vremja($post['time']) . ')<br />';
	echo '花费: <b style="color:red;">' . $post['stav'] . '</b> <b style="color:green;">' . $sMonet[0] . '</b><br />';
	echo output_text($post['msg']) . '<br />';
	if (isset($user) && $user['level'] > 2)
		echo '<div style="text-align:right;"><a href="delete.php?id=' . $post['id_user'] . '"><img src="/style/icons/delete.gif" alt="*"/></a></div>';
	echo '</div>';
}
echo '</table>';
if ($k_page > 1) str('?', $k_page, $page); // 输出页数
echo '<div class="foot">';
echo '<img src="/style/icons/lider.gif" alt="S"/> <a href="/user/money/liders.php">成为领袖</a>';
echo '</div>';
include_once '../../sys/inc/tfoot.php';
