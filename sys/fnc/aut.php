<?php
function title() {
	global $user;	
	if (isset($user)) {
		global $set;
		if ($set['web'] == false) {
			$k_new = dbresult(dbquery("SELECT COUNT(`mail`.`id`) FROM `mail`
			LEFT JOIN `users_konts` ON `mail`.`id_user` = `users_konts`.`id_kont` AND `users_konts`.`id_user` = '$user[id]'
			WHERE `mail`.`id_kont` = '$user[id]' AND (`users_konts`.`type` IS NULL OR `users_konts`.`type` = 'common' OR `users_konts`.`type` = 'favorite') AND `mail`.`read` = '0'"),0);
		   $k_new_fav = dbresult(dbquery("SELECT COUNT(`mail`.`id`) FROM `mail`
			LEFT JOIN `users_konts` ON `mail`.`id_user` = `users_konts`.`id_kont` AND `users_konts`.`id_user` = '$user[id]'
			WHERE `mail`.`id_kont` = '$user[id]' AND (`users_konts`.`type` = 'favorite') AND `mail`.`read` = '0'"),0); // 邮件		
			$lenta = dbresult(dbquery("SELECT COUNT(`read`) FROM `tape` WHERE `id_user` = '$user[id]' AND `read` = '0' "),0); // 乐队
			$discuss = dbresult(dbquery("SELECT COUNT(`count`) FROM `discussions` WHERE `id_user` = '$user[id]' AND `count` > '0' "),0); // 讨论
			$k_frend = dbresult(dbquery("SELECT COUNT(id) FROM `frends_new` WHERE `to` = '$user[id]'"), 0); // 朋友
			$k_notif = dbresult(dbquery("SELECT COUNT(`read`) FROM `notification` WHERE `id_user` = '$user[id]' AND `read` = '0'"), 0); // 通知

			if ($lenta > 0) {
				$j2 = 'tape';
			} elseif ($discuss > 0) {
				$j2 = 'discussions';
			} elseif ($k_notif > 0) {
				$j2 = 'notification';
			} else {
				$j2 = 'tape';
			}

			// 将计数器的总和相加
			$k_l = $lenta + $k_notif + $discuss;
			?>

			<table style="width:100%" cellspacing="0" cellpadding="0"><tr>
			<td class="auts">
				<a href="/user/info.php?id=<?=$user['id']?>"><center><img src="/style/icons/nav_stranica.gif" alt="DS" /></center></a>
			</td>


			<!--------- 邮件 ---------->
			<td class="auts">
				<?php if ($k_new != 0 && $k_new_fav == 0): ?>
					<a href="/user/new_mess.php">
						<center>
							<img src="/style/icons/icon_pochta22.gif" alt="DS" />
							<font color="#ff0000">(<?=$k_new?>)</font>
						</center>
					</a>
				<? else: ?>
					<a href="/user/conts.php">
						<center>
							<img src="/style/icons/nav_pochta.gif" alt="S" />
						</center>
					</a>
				<?php endif; ?>
			</td>


			<!--------- 乐队 ---------->
			<td class='auts'>
				<a href="/user/<?=$j2?>/index.php">
					<center>
						<img src="/style/icons/nav_lenta.gif" alt="DS" />
						<?php if($k_l > 0): ?>
							<font color="#ff0000">(<?=$k_l?>)</font>
						<?php endif; ?>
					</center>
				</a>
			</td>


			<!--------- 朋友 ---------->
			<?php if ($k_frend > 0): ?>
				<td class='auts'>
					<a href="/user/frends/new.php">
						<center>
							<img src="/style/icons/icon_druzya.gif" alt="DS" />
							<font color='red'>(<?=$k_frend?>)</font>
						</center>
					</a>
				</td>
			<?php endif; ?>



			<!--------- 刷新 ---------->
			<td class='auts'>
				<a href="<?=text($_SERVER['REQUEST_URI'])?>">
					<center>
						<img src="/style/icons/nav_obnovit.gif" alt="DS" />
					</center>
				</a>
			</td>		
			</tr></table>
			<?
		}
	}
}