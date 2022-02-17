<?
function status($ID)
{
	$avatar = dbarray(dbquery("SELECT id,id_gallery,ras FROM `gallery_foto` WHERE `id_user` = '$ID' AND `avatar` = '1' LIMIT 1"));

	if (isset($avatar['id'])&&test_file(H."sys/gallery/50/$avatar[id].$avatar[ras]"))
	{
		echo '<img class="avatar" src="/foto/foto50/' . $avatar['id'] . '.' . $avatar['ras'] . '" alt="Avatar"  width="50" />';
	}
	else
	{
		echo '<img class="avatar" src="/style/user/avatar.gif" width="50" alt="No Avatar" />';
	}
	
}
?>