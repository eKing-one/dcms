<?
function user_collision($massive, $im = 0)
{
	global $user;
	$new = false;
		for ($i=0; $i < count($massive); $i++)
		{
			$collision_q = dbquery("SELECT * FROM `user_collision` WHERE `id_user` = '" . $massive[$i] . "' OR `id_user2` = '" . $massive[$i] . "'");
			while ($collision = dbassoc($collision_q))
			{
				if ($collision['id_user'] == $massive[$i])
					$coll = $collision['id_user2'];
				else 
					$coll = $collision['id_user'];
				$ank_coll2 = get_user($coll);
				if (!in_array ($coll, $massive) && ($user['level'] > $ank_coll2['level']) && ($im == 0 || $user['id'] != $ank_coll2['id'])) 
				{
					$massive[] = $coll;
					$new = true;
				}
			}
		}
	if ($new)
		$massive = user_collision($massive);
	return $massive;
}
if(isset($_GET))
{	
	foreach($_GET as $key => $value)
	{
		$_GET[$key] = fiera($value);
	}
}
?>