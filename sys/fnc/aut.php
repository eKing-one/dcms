<?
function title()
{
	global $user;	
	if (isset($user))
	{
		global $set;		
		if ($set['web'] == false)
		{			
			?><table style="width:100%" cellspacing="0" cellpadding="0"><tr><?
			$k_new = dbresult(dbquery("SELECT COUNT(`mail`.`id`) FROM `mail`
			 LEFT JOIN `users_konts` ON `mail`.`id_user` = `users_konts`.`id_kont` AND `users_konts`.`id_user` = '$user[id]'
			 WHERE `mail`.`id_kont` = '$user[id]' AND (`users_konts`.`type` IS NULL OR `users_konts`.`type` = 'common' OR `users_konts`.`type` = 'favorite') AND `mail`.`read` = '0'"),0);
			$k_new_fav = dbresult(dbquery("SELECT COUNT(`mail`.`id`) FROM `mail`
			 LEFT JOIN `users_konts` ON `mail`.`id_user` = `users_konts`.`id_kont` AND `users_konts`.`id_user` = '$user[id]'
			 WHERE `mail`.`id_kont` = '$user[id]' AND (`users_konts`.`type` = 'favorite') AND `mail`.`read` = '0'"),0); // Почта			
			 $lenta = dbresult(dbquery("SELECT COUNT(`read`) FROM `tape` WHERE `id_user` = '$user[id]' AND `read` = '0' "),0); // Лента
			 $discuss = dbresult(dbquery("SELECT COUNT(`count`) FROM `discussions` WHERE `id_user` = '$user[id]' AND `count` > '0' "),0); // Обсуждения
			 $k_frend = dbresult(dbquery("SELECT COUNT(id) FROM `frends_new` WHERE `to` = '$user[id]'"), 0); // Друзья
			 $k_notif = dbresult(dbquery("SELECT COUNT(`read`) FROM `notification` WHERE `id_user` = '$user[id]' AND `read` = '0'"), 0); // Уведомления
			/*
			=================================
			=================================
			*/			
			?>
			<td class="auts">
			<a href="/user/info.php?id=<?=$user['id']?>"><center><img src="/style/icons/nav_stranica.gif" alt="DS" /></center></a>
			</td>
			<?
			/*
			=================================
			Почта
			=================================
			*/			
			?><td class="auts"><?
			if ($k_new != 0 && $k_new_fav == 0)
			{				
				?><a href="/user/new_mess.php"><center><img src="/style/icons/icon_pochta22.gif" alt="DS" /><font color="#ff0000">(<?=$k_new?>)</font></center></a><?
			}
			else
			{
				?><a href="/user/comm.php"><center><img src="/style/icons/nav_pochta.gif" alt="S" /></center></a><?
			}
			?></td><?
			/*
			=================================
			Лента
			=================================
			*/
			if ($lenta > 0)
			{
				$j2 = 'tape';
			}
			elseif ($discuss > 0)
			{
				$j2 = 'discussions';
			}
			elseif ($k_notif > 0)
			{
				$j2 = 'notification';
			}
			else
			{
				$j2 = 'tape';
			}
			?>
			<td class='auts'>
			<a href="/user/<?=$j2?>/index.php"><center><img src="/style/icons/nav_lenta.gif" alt="DS" />
			<?
			// Cкладываем сумму счетчиков
			$k_l = $lenta + $k_notif + $discuss;
			if($k_l > 0)
			{
				?>
				<font color="#ff0000">(<?=$k_l?>)</font>
				<?
			}
			?>
			</center></a>
			</td>
			<?
			/*
			=================================
			Друзья
			=================================
			*/			 
			if ($k_frend > 0)
			{
				?>
				<td class='auts'>
				<a href="/user/frends/new.php"><center><img src="/style/icons/icon_druzya.gif" alt="DS" /><font color='red'>(<?=$k_frend?>)</font></center></a>
				</td>
				<?
			}			
			/*
			=================================
			Обновить
			=================================
			*/
			?>
			<td class='auts'>
			<a href="<?=text($_SERVER['REQUEST_URI'])?>"><center><img src="/style/icons/nav_obnovit.gif" alt="DS" /></center></a>
			</td>		
			</tr></table>
			<?
		}
	}
}
?>