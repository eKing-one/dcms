<?
/*
* $name описание действий объекта 
*/
if ($type == 'status_like' && $post['avtor'] != $user['id']) // статус like
{
	$name = 'считает классным статус';
}
else if ($type=='status_like' && $post['avtor'] == $user['id'])
{
	$name = 'считает классным ваш статус';
}
else if ($type=='status' && $post['avtor'] != $user['id'])
{
	$name = 'установил' . ($avtor['pol'] == 1 ? null : "а") . ' новый статус';
}


/*
* Вывод блока с содержимым 
*/

if ($type == 'status_like' || $type == 'status')
{
	$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id` = '" . $post['id_file'] . "' LIMIT 1"));

	$otkogo = get_user($post['ot_kogo']);

	if ($status['id'])
	{
		echo '<div class="nav1">';
		
		if ($post['ot_kogo'])
		{
			echo avatar($otkogo['id']) . group($otkogo['id']); 
			echo user::nick($otkogo['id']) . medal($otkogo['id']) . online($otkogo['id']) . '  <a href="user.settings.php?id=' . $otkogo['id'] . '">[!]</a>';
		}
		else
		{
			echo avatar($avtor['id']) . group($avtor['id']); 
			echo user::nick($avtor['id']) . medal($avtor['id']) . online($avtor['id']) . '  <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a>';
		}
		
		echo $name;
		
		if ($type != 'status')
		{
			echo avatar($avtor['id']) . group($avtor['id']); 
			echo '<a href="/info.php?id=' . $avtor['id'] . '">' . $avtor['nick'] . '</a>  ' . medal($avtor['id']) . online($avtor['id']) . ' ';
		}
		
		echo $s1 . vremja($post['time']) . $s2;
		echo '</div>';
		echo '<div class="nav2">';
		
		echo '<div class="st_1"></div>';
		echo '<div class="st_2">';
		echo output_text($status['msg']) . '<br />';
		echo '</div>';
		
		echo '<a href="/user/status/komm.php?id=' . $status['id'] . '"><img src="/style/icons/bbl4.png" alt=""/> ' . dbresult(dbquery("SELECT COUNT(*) FROM `status_komm` WHERE `id_status` = '$status[id]'"),0) . '</a>';
		
		$l = dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]'"),0);
			
		if (isset($user) && $user['id'] != $avtor['id'])
		{
			if ($user['id']!=$avtor['id'] && dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]' AND `id_user` = '$user[id]' LIMIT 1"),0)==0){
				echo ' <a href="?likestatus=' . $status['id'] . '&amp;page=$page"><img src="/style/icons/like.gif" alt=""/>Класс!</a> &bull; ';
				$like = $l;
			}else{
				echo ' <img src="/style/icons/like.gif" alt=""/> Вы и ';
				$like = $l - 1;
			}
		}
		else
		{
			echo ' <img src="/style/icons/like.gif" alt=""/> ';
			$like = $l;
		}
		
		echo '<a href="/user/status/like.php?id=' . $status['id'] . '">' . $like . ' чел.</a>';

	}
	else
	{
		echo '<div class="nav1">';
		echo avatar($avtor['id']) . group($avtor['id']) . user::nick($avtor['id']);
		echo medal($avtor['id']) . online($avtor['id']) . ' <a href="user.settings.php?id=' . $avtor['id'] . '">[!]</a><br />';
		echo 'Статус уже удален =(';

	}
}

?>