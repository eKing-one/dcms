<?
function medal($user = 0)
{
	$ank = dbarray(dbquery("SELECT `rating` FROM `user` WHERE `id` = $user LIMIT 1"));
	$img = 0;
	if ($ank['rating'] >= 6 && $ank['rating'] <= 11)
		$img = 1;
	elseif ($ank['rating'] >= 12 && $ank['rating'] <= 19)
		$img = 2;
	elseif ($ank['rating'] >= 20 && $ank['rating'] <= 27)
		$img = 3;
	elseif ($ank['rating'] >= 28 && $ank['rating'] <= 37)
		$img = 4;
	elseif ($ank['rating'] >= 38 && $ank['rating'] <= 47)
		$img = 5;
	elseif ($ank['rating'] >= 48 && $ank['rating'] <= 59)
		$img = 6;
	elseif ($ank['rating'] >= 60 && $ank['rating'] <= 9999999)
		$img = 7;
	if ($img != 0)
	{
		return ' <img src="/style/medal/' . $img . '.png" alt="DS" />';
	}
}
?>
