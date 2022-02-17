<?php
function avatar($ID, $link = false, $dir = '50', $w = '50')
{
	/**
	* 
	* @var / 头像，为了方便代码修改了功能
	* 
	*/
	$avatar = dbarray(dbquery("SELECT id,id_gallery,ras FROM `gallery_foto` WHERE `id_user` = '$ID' AND `avatar` = '1' LIMIT 1"));

	if (isset($avatar['id']) && isset($avatar['ras']) && test_file(H."sys/gallery/$dir/$avatar[id].$avatar[ras]"))
	{
		return ($link == true ? '<a href="/foto/' . $ID . '/' . $avatar['id_gallery'] . '/' . $avatar['id'] . '/">' : false) . '
	<img class="avatar" src="/foto/foto' . $dir . '/' . $avatar['id'] . '.' . $avatar['ras'] . '" alt="Avatar"  width="' . $w . '" />' . ($link == true ? '</a>' : false);
	}
	else
	{
		return '<img class="avatar" src="/style/user/avatar.gif" width="' . $w . '" alt="No Avatar" />';
	}
	
}
?>