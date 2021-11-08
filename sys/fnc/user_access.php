<?
function user_access($access, $u_id = null, $exit = false)
{

	if ($u_id == null)
		global $user;
	else 
		$user = get_user($u_id);

	if (!isset($user['group_access']) || $user['group_access'] == null)
	{
		if ($exit !== false)
		{
			header('Location: ' . $exit);
			exit;
		}
		else return false;
	}

	if ($exit !== false)
	{
		if (dbresult(dbquery("SELECT COUNT(*) FROM `user_group_access` WHERE `id_group` = '$user[group_access]' AND `id_access` = '" . my_esc($access) . "'"),0) == 0)
		{
			header("Location: $exit");
			exit;
		}
	}
	else
	return (dbresult(dbquery("SELECT COUNT(*) FROM `user_group_access` WHERE `id_group` = '$user[group_access]' AND `id_access` = '" . my_esc($access) . "'"),0) == 1 ? true : false);
}
?>