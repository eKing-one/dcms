<?
function online($user = NULL)
{
	global $set, $time;
	static $users;
	
	if (!isset($users[$user]))
	{
		if (dbresult(dbquery("SELECT COUNT(id) FROM `user` WHERE `id` = '$user' AND `date_last` > '" . (time()-600) . "' LIMIT 1"),0) == 1)
		{
			if ($set['show_away'] == 0)$on = 'online';
			else
			{
				$ank = dbassoc(dbquery("SELECT `date_last` FROM `user` WHERE `id` = '$user' LIMIT 1"));
				if ((time() - $ank['date_last']) == 0)
				$on = 'online';
				else
				$on = 'away: ' . (time()-$ank['date_last']) . ' сек';
			}
			$ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$user' LIMIT 1"));

			if ($ank['browser'] == 'wap')
				$users[$user] = " <img src='/style/icons/online.gif' alt='*' /> ";
			else
				$users[$user] = " <img src='/style/icons/online_web.gif' alt='*' /> ";
		}
		else
		{
			$users[$user]=null;
		}
	}
	return $users[$user];
}
?>