<?
/*
* $name 个体操作描述 
*/
if ($type=='avatar' && $post['avtor'] != $user['id']) // аватар
{
	if ($post['avatar'])
	$name = '修改了' . ($avtor['pol'] == 1 ? null : "а") . ' 主页上的照片';
	else
	$name = '已安装' . ($avtor['pol'] == 1 ? null : "а") . ' 主页上的照片';	
}
/*
* 内容块输出 
*/
if ($type == 'avatar')
{
	$foto = dbassoc(dbquery("SELECT * FROM `gallery_foto` WHERE `id` = '".$post['id_file']."' LIMIT 1"));
	$avatar = dbassoc(dbquery("SELECT * FROM `gallery_foto` WHERE `id` = '".$post['avatar']."' LIMIT 1"));
	$gallery = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '".$foto['id_gallery']."' LIMIT 1"));
	$gallery2 = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = '".$avatar['id_gallery']."' LIMIT 1"));
	echo '<div class="nav1">';
	echo  user::nick($avtor['id'],1,1,0);
	echo medal($avtor['id']) . online($avtor['id']) . ' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a> ' . $name;
	echo $s1 . vremja($post['time']) . $s2;
	echo '</div>';
	echo '<div class="nav2">';
	if ($foto['id'])echo '<b>' . text($foto['name']) . '</b>';
	if ($avatar['id'])echo ' &raquo; <b>' . text($avatar['name']) . '</b>';
	if ($avatar['id'] || $foto['id'])echo '<br />';
	if ($foto['id'])echo '<a href="/foto/' . $avtor['id'] . '/' . $gallery['id'] . '/' . $foto['id'] . '/">';
	echo '<img style=" max-width:50px; margin:3px;" src="/foto/foto50/' . $post['id_file'] . '.jpg" alt="*" />';
	if ($foto['id'])echo '</a>';
	if ($post['avatar'])
	{
		echo ' <img src="/style/icons/arRt2.png" alt="*"/> ';
		if ($avatar['id'])echo '<a href="/foto/' . $avtor['id'] . '/' . $gallery2['id'] . '/' . $avatar['id'] . '/">';
		echo '<img style="max-width:50px; margin:3px;" src="/foto/foto50/' . $post['avatar'] . '.jpg" alt="*" />';
		if ($avatar['id'])echo '</a>';
	}
	echo '<br />';
	if ($foto['id'])
	echo '<a href="/foto/' . $avtor['id'] . '/' . $gallery['id'] . '/' . $foto['id'] . '/"><img src="/style/icons/bbl5.png" alt="*"/> (' . dbresult(dbquery("SELECT COUNT(*) FROM `gallery_komm` WHERE `id_foto` = '$foto[id]'"),0) . ')</a> ';
}
