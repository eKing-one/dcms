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
dbquery("DELETE FROM `bookmarks` WHERE `id` = '" . intval($_GET['delete']) . "' AND `id_user` = '$user[id]' AND `type`='foto' LIMIT 1");
	$_SESSION['message'] = '删除书签';
	header("Location: ?page=" . intval($_GET['page']) . "" . SID);exit;
	exit;
}
if( !$ank ){ header("Location: /index.php?".SID); exit; }
$set['title']='书签 - 照片 - ' . $ank['nick']; //网页标题
include_once '../../sys/inc/thead.php';
title();
err();
aut(); // форма авторизации
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" /> <a href="/user/bookmark/index.php?id=' . $ank['id'] . '">书签</a> | <b>照片</b>';
echo '</div>';
$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `bookmarks` WHERE `id_user` = '$ank[id]' AND `type`='foto' "),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
echo '<table class="post">';
if ($k_post == 0)
{
	echo '<div class="mess">';
	echo '书签中没有照片';
	echo '</div>';
}
$q=dbquery("SELECT * FROM `bookmarks`  WHERE `id_user` = '$ank[id]' AND `type`='foto' ORDER BY id DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q))
{
/*-----------зебра-----------*/ 
if ($num==0){
	echo '<div class="nav1">';
	$num=1;
}
elseif ($num==1){
	echo '<div class="nav2">';
	$num=0;
}
/*---------------------------*/
$f=$post['id_object'];
$foto = dbassoc(dbquery("SELECT * FROM `gallery_foto` WHERE `id` = '" . $f . "'  LIMIT 1"));
$gallery = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id`='" . $foto['id_gallery'] . "'  LIMIT 1"));
$ank_p=get_user($gallery['id_user']);
echo '<a href="/foto/' . $ank_p['id'] . '/' . $gallery['id'] . '/' . $foto['id'] . '/" title="转到照片"><img style=" padding: 2px; height: 45px; width: 45px;" src="/foto/foto48/' . $foto['id'] . '.' . $foto['ras'] . '" alt="*" /> ' . htmlspecialchars($foto['name']) . '</a>  (' . vremja($post['time']) . ')';
if ($ank['id'] == $user['id'])
echo '<div style="text-align:right;"><a href="?delete=' . $post['id'] . '&amp;page=' . $page . '"><img src="/style/icons/delete.gif" alt="*" /></a></div>';
echo '</div>';
}
echo '</table>';
if ($k_page>1)str('?',$k_page,$page); // Вывод страниц
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" /> <a href="/user/bookmark/index.php?id=' . $ank['id'] . '">书签</a> | <b>照片</b>';
echo '</div>';
include_once '../../sys/inc/tfoot.php';
?>
