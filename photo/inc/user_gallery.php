<?
// 如果没有设置用户且没有通过GET方式传递用户ID，则重定向到/photo/页面
if (!isset($user) && !isset($_GET['id_user'])) {
	header("Location: /photo/?" . SID);
	exit;
}
// 如果设置了用户，则将用户ID赋值给ank数组
if (isset($user)) $ank['id'] = $user['id'];
// 如果通过GET方式传递了用户ID，则将该ID转换为整数并赋值给ank数组
if (isset($_GET['id_user'])) $ank['id'] = intval($_GET['id_user']);
// 获取相册主人的信息
$ank = user::get_user($ank['id']);
// 如果获取用户信息失败，则重定向到/photo/页面
if (!$ank) {
	header("Location: /photo/?" . SID);
	exit;
}
// 如果用户被禁止访问相册
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'photo' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
	header('Location: /user/ban.php?' . SID);
	exit;
}
// 设置网页标题
$set['title'] = $ank['nick'] . ' - 相册';
// 包含创建新相册的相关代码
include 'inc/gallery_act.php';
include_once '../sys/inc/thead.php';
title();
aut();
err();
// 包含创建相册表单的代码
include 'inc/gallery_form.php';
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> ' . user::nick($ank['id'],1,0,0) . ' | <b>相册</b></div>';
// 如果当前登录用户是相册主人，则显示创建新相册的链接
if ($ank['id'] == $user['id'])
	echo '<div class="mess"><a href="/photo/' . $ank['id'] . '/?act=create"><img src="/style/icons/apply14.png"> 新相册</a></div>';
// 包含隐私设置的代码
include H . 'sys/add/user.privace.php';
// 获取该用户相册的数量
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `gallery` WHERE `id_user` = '$ank[id]'"), 0);
// 计算分页信息
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo '<table class="post">';
// 如果用户没有相册，则显示提示信息
if ($k_post == 0) {
	echo '<div class="mess">';
	echo '目前该用户没有相册。';
	echo '</div>';
}
// 查询相册信息并按时间降序排列
$q = dbquery("SELECT * FROM `gallery` WHERE `id_user` = '$ank[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q)) {
	// 交替显示不同的样式
	echo '<div class="' . ($num % 2 ? "nav1" : "nav2") . '">';
	$num++;
	// 获取相册中照片的数量
	$count = dbresult(dbquery("SELECT COUNT(*) FROM `gallery_photo` WHERE `id_gallery` = '$post[id]'"), 0);
	echo '<img src="/style/themes/' . $set['set_them'] . '/loads/14/' . ($post['pass'] != null || $post['privat'] != 0 ? 'lock.gif' : 'dir.png') . '" alt="*" /> ';
	echo '<a href="/photo/' . $ank['id'] . '/' . $post['id'] . '/">' . text($post['name']) . '</a> (' . $count . ' 照片) ';
	// 如果当前登录用户有权限或相册主人，则显示编辑和删除链接
	if (isset($user) && (user_access('photo_alb_del') || $user['id'] == $ank['id'])) {
		echo '[<a href="/photo/' . $ank['id'] . '/' . $post['id'] . '/?edit=rename"><img src="/style/icons/edit.gif" alt="*" /> 编辑</a>] ';
		echo '[<a href="/photo/' . $ank['id'] . '/' . $post['id'] . '/?act=delete"><img src="/style/icons/delete.gif" alt="*" /> 删除</a>]';
	}
	echo '<br />';
	// 如果相册没有描述，则显示提示信息；否则显示描述
	if ($post['opis'] == null)
		echo '没有描述<br />';
	else
		echo '<div class="text">' . output_text($post['opis']) . '</div>';
	echo '创建时间: ' . vremja($post['time_create']);
	echo '</div>';
}
echo '</table>';
// 如果有多页，则显示分页链接
if ($k_page > 1) str('?', $k_page, $page);
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> ' . user::nick($ank['id'],1,0,0) . ' | <b>相册</b>';
echo '</div>';
include_once '../sys/inc/tfoot.php';
exit;