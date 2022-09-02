<?
if (!isset($user) && !isset($_GET['id_user'])) {
	header("Location: /photo/?" . SID);
	exit;
}
if (isset($user)) $ank['id'] = $user['id'];
if (isset($_GET['id_user'])) $ank['id'] = intval($_GET['id_user']);
// Автор альбома
$ank = user::get_user($ank['id']);
if (!$ank) {
	header("Location: /photo/?" . SID);
	exit;
}
// Если вы в бане 
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'photo' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
	header('Location: /ban.php?' . SID);
	exit;
}
//网页标题
$set['title'] = $ank['nick'] . ' - 相片册';
// Это при создании нового альбома
include 'inc/gallery_act.php';
include_once '../sys/inc/thead.php';
title();
aut();
err();
// Создание альбомов
include 'inc/gallery_form.php';
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> ' . user::nick($ank['id'],1,0,0) . ' | <b>相册</b></div>';
if ($ank['id'] == $user['id'])
	echo '<div class="mess"><a href="/photo/' . $ank['id'] . '/?act=create"><img src="/style/icons/apply14.png"> 新专辑</a></div>';
// Подключаем приватность стр. 
include H . 'sys/add/user.privace.php';
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `gallery` WHERE `id_user` = '$ank[id]'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo '<table class="post">';
if ($k_post == 0) {
	echo '<div class="mess">';
	echo '无相册';
	echo '</div>';
}
$q = dbquery("SELECT * FROM `gallery` WHERE `id_user` = '$ank[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q)) {
	// Лесенка
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;
	// Cчетчик фотографий
	$count = dbresult(dbquery("SELECT COUNT(*) FROM `gallery_photo` WHERE `id_gallery` = '$post[id]'"), 0);
	echo '<img src="/style/themes/' . $set['set_them'] . '/loads/14/' . ($post['pass'] != null || $post['privat'] != 0 ? 'lock.gif' : 'dir.png') . '" alt="*" /> ';
	echo '<a href="/photo/' . $ank['id'] . '/' . $post['id'] . '/">' . text($post['name']) . '</a> (' . $count . ' 照片) ';
	if (isset($user) && (user_access('photo_alb_del') || $user['id'] == $ank['id'])) {
		echo '[<a href="/photo/' . $ank['id'] . '/' . $post['id'] . '/?edit=rename"><img src="/style/icons/edit.gif" alt="*" /> 编辑</a>] ';
		echo '[<a href="/photo/' . $ank['id'] . '/' . $post['id'] . '/?act=delete"><img src="/style/icons/delete.gif" alt="*" /> 删除</a>]';
	}
	echo '<br />';
	if ($post['opis'] == null)
		echo '没有描述<br />';
	else
		echo '<div class="text">' . output_text($post['opis']) . '</div>';
	echo '生成的: ' . vremja($post['time_create']);
	echo '</div>';
}
echo '</table>';
// 输出页数
if ($k_page > 1) str('?', $k_page, $page);
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> ' . user::nick($ank['id'],1,0,0) . ' | <b>专辑</b>';
echo '</div>';
include_once '../sys/inc/tfoot.php';
exit;
