<?
/* Бан пользователя */ 
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'foto' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
	header('Location: /ban.php?'.SID);
	exit;
}

$set['title'] = '相片册'; //网页标题

include_once '../sys/inc/thead.php';
title();
aut();

$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `gallery`"),0);
$k_page = k_page($k_post,$set['p_str']);
$page = page($k_page);
$start = $set['p_str']*$page-$set['p_str'];


echo '<table class="post">';

if ($k_post == 0)
{
	echo '<div class="mess">';
	echo '无相册';
	echo '</div>';
}

$q = dbquery("SELECT * FROM `gallery` ORDER BY `time` DESC LIMIT $start, $set[p_str]");

while ($post = dbassoc($q))
{
	$ank = get_user($post['id_user']);

	// Лесенка
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;

	echo '<img src="/style/themes/' . $set['set_them'] . '/loads/14/' . ($post['pass'] != null || $post['privat'] != 0 ? 'lock.gif' : 'dir.png') . '" alt="*" /> ';

	echo '<a href="/foto/' . $ank['id'] . '/' . $post['id'] . '/">' . text($post['name']) . '</a> (' . dbresult(dbquery("SELECT COUNT(*) FROM `gallery_foto` WHERE `id_gallery` = '$post[id]'"),0) . ' 照片)<br />';

	if ($post['opis'] == null)
	echo '无描述<br />';
	else 
	echo output_text($post['opis']) . '<br />';

	echo '已创建: ' . vremja($post['time_create']) . '<br />';

	echo '作者: ';
	echo user::avatar($ank['id'], 2) . user::nick($ank['id'], 1, 1, 1);

	echo '</div>';
}

echo '</table>';

if ($k_page>1)str('?',$k_page,$page); // Вывод страниц

if (isset($user))
{
	echo '<div class="foot">';
	echo '<img src="/style/icons/str.gif" alt="*"> <a href="/foto/' . $user['id'] . '/">我的相册</a><br />';
	echo '</div>';
}

include_once '../sys/inc/tfoot.php';
exit;
?>