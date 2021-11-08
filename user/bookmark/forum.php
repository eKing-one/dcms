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

if (isset($user))$ank['id'] = $user['id'];
if (isset($_GET['id']))$ank['id'] = intval($_GET['id']);
$ank = get_user($ank['id']);

if ($ank['id'] == 0)
{
	header("Location: /index.php?" . SID);exit;
	exit;
}

if (isset($user) && isset($_GET['delete']) && $user['id'] == $ank['id'])
{
dbquery("DELETE FROM `bookmarks` WHERE `id_object` = '" . intval($_GET['delete']) . "' AND `id_user` = '$user[id]' AND `type`='forum' LIMIT 1");
	
	$_SESSION['message'] = 'Закладка удалена';
	header("Location: ?page=" . intval($_GET['page']) . "" . SID);exit;
	exit;
}

if( !$ank ){ header("Location: /index.php?".SID); exit; }
$set['title'] = 'Закладки - 论坛';
include_once '../../sys/inc/thead.php';
title();
aut(); // форма авторизации

echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" /> <a href="/user/bookmark/index.php?id=' . $ank['id'] . '">Закладки</a> | <b>Форум</b>';
echo '</div>';

$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `bookmarks` WHERE `id_user` = '$ank[id]' AND `type`='forum' "),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
echo '<table class="post">';

if ($k_post == 0)
{
	echo '<div class="mess">';
	echo 'Нет тем в закладках';
	echo '</div>';
}

$q=dbquery("SELECT * FROM `bookmarks` WHERE `id_user` = '$ank[id]' AND `type`='forum' ORDER BY `time` DESC LIMIT $start, $set[p_str]");

while ($zakl = dbassoc($q))
{
	$them = dbassoc(dbquery("SELECT * FROM `forum_t` WHERE `id` = '$zakl[id_object]' LIMIT 1"));
	
	// Определение подфорума
	$forum = dbarray(dbquery("SELECT * FROM `forum_f` WHERE `id` = '$them[id_forum]' LIMIT 1"));
	
	// Определение раздела
	$razdel = dbarray(dbquery("SELECT * FROM `forum_r` WHERE `id` = '$them[id_razdel]' LIMIT 1"));
	
	// Лесенка дивов
	if ($num == 0)
	{
		echo '<div class="nav1">';
		$num = 1;
	}
	elseif ($num == 1)
	{
		echo '<div class="nav2">';
		$num = 0;
	}
	
	// Иконка темы
	echo '<img src="/style/themes/' . $set['set_them'] . '/forum/14/them_' . $them['up'] . $them['close'] . '.png" alt="" /> ';
	
	// Ссылка на тему
	echo '<a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/' . $them['id'] . '/">' . htmlspecialchars($them['name']) . '</a> 
	<a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/' . $them['id'] . '/?page=' . $pageEnd . '">
	(' . dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]' AND `id_them` = '$them[id]'"),0) . ')</a><br/>';
	
	// Подфорум и раздел
	echo '<a href="/forum/' . $forum['id'] . '/">' . htmlspecialchars($forum['name']) . '</a> &gt; <a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/">' . htmlspecialchars($razdel['name']) . '</a><br />';
	
	// Автор темы
	$ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $them[id_user] LIMIT 1"));
	echo 'Автор: <a href="/info.php?id=' . $ank['id'] . '">' . $ank['nick'] . '</a> (' . vremja($them['time_create']) . ')<br />';

	// Последний пост 
	$post = dbarray(dbquery("SELECT * FROM `forum_p` WHERE `id_them` = '$them[id]' AND `id_razdel` = '$razdel[id]' AND `id_forum` = '$forum[id]' ORDER BY `time` DESC LIMIT 1"));
	
	// Автор последнего поста
	$ank2 = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
	if ($ank2['id'])echo 'Посл.: <a href="/info.php?id=' . $ank2['id'] . '">' . $ank2['nick'] . '</a> (' . vremja($post['time']) . ')<br />';
	
	
	echo '</div>';
}
echo '</table>';echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" /> <a href="/user/bookmark/index.php?id=' . $ank['id'] . '">Закладки</a> | <b>Форум</b>';
echo '</div>';include_once '../../sys/inc/tfoot.php';
?>
