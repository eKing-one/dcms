<?
function group($user = NULL)
{
	global $set, $time;
	if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$user' AND (`time` > '$time' OR `navsegda` = '1')"), 0) != 0)
	{
		$ban = ' <img src="/style/user/ban.png" alt="*" class="icon" id="icon_group" /> ';
		return $ban;
	}
	else 
	{
		$ank = dbarray(dbquery("SELECT group_access, pol  FROM `user` WHERE `id` = $user LIMIT 1"));
		if (isset($ank['group_access'] ) && ($ank['group_access'] > 7 && ($ank['group_access'] < 10 || $ank['group_access'] > 14)))
		{
			if ($ank['pol'] == 1) $adm = '<img src="/style/user/1.png" alt="*" class="icon" id="icon_group" /> ';
			else
			$adm = '<img src="/style/user/2.png" alt="" class="icon"/> ';
			return $adm;
		}
		elseif (isset($ank['group_access'] ) && (($ank['group_access'] > 1 && $ank['group_access'] <= 7) || ($ank['group_access'] > 10 && $ank['group_access'] <= 14)))
		{
			if ($ank['pol'] == 1)
				$mod = '<img src="/style/user/3.png" alt="*" class="icon" id="icon_group" /> ';
			else
				$mod = '<img src="/style/user/4.png" alt="*" class="icon" id="icon_group" /> ';
			return $mod;
		}
		else
		{
			if (isset($ank['pol'])&&$ank['pol'] == 1)
				$user = '<img src="/style/user/5.png" alt="" class="icon" id="icon_group" /> ';
			else
				$user = '<img src="/style/user/6.png" alt="" class="icon" id="icon_group" /> ';
			return $user;
		}
	}
}
?>