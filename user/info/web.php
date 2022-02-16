<?php

/*--------------------в друзья-------------------*/
$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE (`user` = '$user[id]' AND `to` = '$ank[id]') OR (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"), 0);
$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '$user[id]' AND `frend` = '$ank[id]') OR (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"), 0);
$not_user = dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$ank[id]' LIMIT 1"), 0) == 0;
if (isset($user) && $user['id'] != $ank['id']) {
	if (isset($_GET['fok'])) {
		echo '<center>';
		echo "<div class='foot'><form action='/info.php?id=" . $ank['id'] . "' method=\"post\">";
		echo "<input class=\"submit\" type=\"submit\" value=\"关闭\" />";
		echo "</form></div>";
		echo '</center>';
	}
}
if (isset($user) && isset($_GET['frends'])  && $frend_new == 0 && $frend == 0) {
	if ($user['id'] != $ank['id']) {
		echo '<center>';
		echo "<div class='err'>用户将需要确认你是朋友。</div><div class='foot'><form action='/user/frends/create.php?add=" . $ank['id'] . "' method=\"post\">";
		echo "<input class=\"submit\" type=\"submit\" value=\"邀请\" />";
		echo " <a href='/info.php?id=$ank[id]'>取消</a><br />";
		echo "</form></div>";
		echo '</center>';
	}
}
/*---------------------------------------------------------*/
// Должность на сайте
if ($ank['group_access'] > 1) {
	echo "<div class='err'>$ank[group_name]</div>";
} ?>
<table class='table_info' cellspacing="0" cellpadding="0">
	<tr>
		<td class='block_menu'>
			<?

			// Аватар 
			echo "<div class='mains'>";
			echo avatar($ank['id'], false, 640, 200);
			echo "</div>";

			// Рейтинг
			echo "<div class='main'>";
			if ($ank['rating'] >= 0 && $ank['rating'] <= 100) {
				echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$ank[rating]%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
			} elseif ($ank['rating'] >= 100 && $ank['rating'] <= 200) {
				$rat = $ank['rating'] - 100;
				echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
			} elseif ($ank['rating'] >= 200 && $ank['rating'] <= 300) {
				$rat = $ank['rating'] - 200;
				echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
			} elseif ($ank['rating'] >= 300 && $ank['rating'] <= 400) {
				$rat = $ank['rating'] - 300;
				echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
			} elseif ($ank['rating'] >= 400 && $ank['rating'] <= 500) {
				$rat = $ank['rating'] - 400;
				echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
			} elseif ($ank['rating'] >= 500 && $ank['rating'] <= 600) {
				$rat = $ank['rating'] - 500;
				echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
			} elseif ($ank['rating'] >= 600 && $ank['rating'] <= 700) {
				$rat = $ank['rating'] - 600;
				echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
			} elseif ($ank['rating'] >= 700 && $ank['rating'] <= 800) {
				$rat = $ank['rating'] - 700;
				echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
			} elseif ($ank['rating'] >= 800 && $ank['rating'] <= 900) {
				$rat = $ank['rating'] - 800;
				echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
			} elseif ($ank['rating'] >= 900 && $ank['rating'] <= 1000) {
				$rat = $ank['rating'] - 900;
				echo "<div style='background-color: #73a8c7; width: 200px; height: 17px;'>
<div style=' background-color: #064a91; height:17px; width:$rat%;'></div>
<span style='position:relative; top:-17px; left:45%; right:57%; color:#ffffff;'>$ank[rating]%</span>
</div>";
			}
			echo "</div>";
			echo "<div class='main'>";
			echo "<b>ID 编号: $ank[id]</b>";
			echo "</div>";
			/*---------------个人资料-------------------*/
			echo "<div class='main2'>";
			echo "<img src='/style/icons/anketa.gif' alt='*' /> <a href='/user/info/anketa.php?id=$ank[id]'>个人资料</a> ";
			if (isset($user) && $user['id'] == $ank['id']) {
				echo "[<img src='/style/icons/edit.gif' alt='*' /> <a href='/user/info/edit.php'>编辑</a>]";
			}
			echo "</div>";
			/*---------------------------------------*/

			/*------------------------客人---------------------------*/
			if (isset($user) && $user['id'] == $ank['id']) {
				echo "<div class='main'>";
				$new_g = dbresult(dbquery("SELECT COUNT(*) FROM `my_guests` WHERE `id_ank` = '$user[id]' AND `read`='1'"), 0);
				echo "<img src='/style/icons/guests.gif' alt='*' /> ";
				if ($new_g != 0) {
					$color = "<font color='red'>";
					$color2 = "</font>";
				} else {
					$color = null;
					$color2 = null;
				}
				echo "<a href='/user/myguest/index.php'>" . $color . "客人" . $color2 . "</a> ";
				if ($new_g != 0) echo "<font color=\"red\">+$new_g</font>";
				echo "</div>";
			}
			/*-------------------------------------------------------*/

			/*-----------------лента-----------------*/
			if (isset($user) && $user['id'] == $ank['id']) {
				echo "<div class='main'>";

				/*
========================================
Уведомления
========================================
*/
				$k_notif = dbresult(dbquery("SELECT COUNT(`read`) FROM `notification` WHERE `id_user` = '$user[id]' AND `read` = '0'"), 0); // Уведомления

				if ($k_notif > 0) {
					echo "<img src='/style/icons/notif.png' alt='*' /> ";
					echo "<a href='/user/notification/index.php'><font color='red'>通知</font></a> ";
					echo "<font color=\"red\">+$k_notif</font> ";
					echo "<br />";
				}

				/*
========================================
Обсуждения
========================================
*/
				echo "<img src='/style/icons/chat.gif' alt='*' /> ";
				$new_g = dbresult(dbquery("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$user[id]' AND `count` > '0'"), 0);
				if ($new_g != 0) {
					echo "<a href='/user/discussions/index.php'><font color='red'>讨论情况</font></a> ";
					echo "<font color=\"red\">+$new_g</font> ";
				} else {
					echo "<a href='/user/discussions/index.php'>讨论情况</a> ";
				}
				echo "<br />";
				$k_l = dbresult(dbquery("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$user[id]'  AND  `read` = '0'"), 0);
				if ($k_l != 0) {
					$color = "<font color='red'>";
					$color2 = "</font>";
				} else {
					$color = null;
					$color2 = null;
				}
				echo "<img src='/style/icons/lenta.gif' alt='*' /> <a href='/user/tape/'>" . $color . "消息" . $color2 . "</a> ";
				if ($k_l != 0) echo "<font color=\"red\">+$k_l</font>";
				echo "</div>";
			}
			/*---------------------------------------*/

			echo "<div class='main2'>";
			echo "<img src='/style/my_menu/who_rating.png' alt='*' /> <a href='/user/info/who_rating.php?id=$ank[id]'><b>反馈意见</b></a> (" . dbresult(dbquery("SELECT COUNT(*) FROM `user_voice2` WHERE `id_kont` = '" . $ank['id'] . "'"), 0) . ")<br />";
			echo "</div>";

			/*-----------------------------в друзья-------------------------*/
			if (isset($user) && $user['id'] != $ank['id']) {
				echo "<div class='main'>";
				if ($frend_new == 0 && $frend == 0) {
					echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/info.php?id=$ank[id]&amp;frends'>添加为好友</a><br />";
				} elseif ($frend_new == 1) {
					echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?otm=$ank[id]'>拒绝申请</a><br />";
				} elseif ($frend == 2) {
					echo "<img src='/style/icons/druzya.png' alt='*'/> <a href='/user/frends/create.php?del=$ank[id]'>把...从朋友中除名</a><br />";
				}
				echo "</div>";
				/*-------------------------------------------------------------*/


				/*--------------------Сообщение-----------------------------------*/
				echo "<div class='main'>";
				echo " <a href=\"/mail.php?id=$ank[id]\"><img src='/style/icons/pochta.gif' alt='*' /> 通信</a> ";
				echo "</div>";
				/*----------------------------------------------------------------*/


				/*
========================================
Монеты перевод
========================================
*/
				echo "<div class='main2'>";
				echo "<img src='/style/icons/many.gif' alt='*' /> <a href=\"/user/money/translate.php?id=$ank[id]\">兑换 $sMonet[0]</a> ";
				echo "</div>";

				/*
========================================
Сделать подарок
========================================
*/
				echo "<div class='main2'>";
				echo "<img src='/style/icons/present.gif' alt='*' /> <a href=\"/user/gift/categories.php?id=$ank[id]\">送礼物</a><br />";
				echo "</div>";
			}



			/*-----------------------------настройки-----------------------*/
			if (isset($user) && $ank['id'] == $user['id']) {
				echo "<div class='main2'>";
				echo "<img src='/style/icons/uslugi.gif' alt='*' /> <a href=\"/user/money/index.php\">附加服务</a><br /> ";
				echo "<img src='/style/icons/settings.png' alt='*' /> <a href=\"/user/info/settings.php\">我的设置。</a> | <a href=\"/umenu.php\">菜单</a>";
				echo "</div>";
			}
			/*-------------------------------------------------------------*/



			/*--------------------------在线好友----------------------*/
			$set['p_str'] = 20;
			$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `frends` INNER JOIN `user` ON `frends`.`frend`=`user`.`id` WHERE `frends`.`user` = '$ank[id]' AND `frends`.`i` = '1' AND `user`.`date_last`>'" . (time() - 600) . "'"), 0);
			$k_page = k_page($k_post, $set['p_str']);
			$page = page($k_page);
			$start = $set['p_str'] * $page - $set['p_str'];
			$q = dbquery("SELECT * FROM `frends` INNER JOIN `user` ON `frends`.`frend`=`user`.`id` WHERE `frends`.`user` = '$ank[id]' AND `frends`.`i` = '1' AND `user`.`date_last`>'" . (time() - 600) . "' ORDER BY `user`.`date_last` DESC LIMIT $start, $set[p_str]");
			if ($k_post > 0) {
				echo "<div class='foot'>在线好友 ($k_post)</div>";
			}
			while ($post3 = dbassoc($q)) {
				$ank3 = get_user($post3['frend']);

				/*---------斑马---------*/
				if ($num == 0) {
					echo "  <div class='nav1'>";
					$num = 1;
				} elseif ($num == 1) {
					echo "  <div class='nav2'>";
					$num = 0;
				}


				/*-----------------------*/
				echo avatar($ank3['id']);
				echo ' <a href="/info.php?id=' . $ank3['id'] . '">' . $ank3['nick'] . '</a>' . medal($ank3['id']) . ' ' . online($ank3['id']) . ' (' . (($ank3['pol'] == 1) ? '男' : '女') . ')<br />';

				echo '<a href="/mail.php?id=' . $ank3['id'] . '"><img src="/style/icons/pochta.gif" alt="*" /> 通信</a> ';
				echo "</div>";
			}
			/*---------------------the end--------------------------*/
			?>
		</td>
		<td class='block_info'>
			<?

			echo '<table>';

			/*---------------------------朋友-----------------------------*/
			$k_f = dbresult(dbquery("SELECT COUNT(id) FROM `frends_new` WHERE `to` = '$ank[id]' LIMIT 1"), 0);
			$k_fr = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE `user` = '$ank[id]' AND `i` = '1'"), 0);
			$res = dbquery("select `frend` from `frends` WHERE `user` = '$ank[id]' AND `i` = '1'");

			echo '<a class="top_nav" href="/user/frends/?id=' . $ank['id'] . '">朋友 (' . $k_fr . '</b>/';
			$i = 0;
			while ($k_fr = dbarray($res)) {
				if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$k_fr[frend]' && `date_last` > '" . (time() - 800) . "'"), 0) != 0) $i++;
			}
			echo $i;
			if ($k_f > 0 && $ank['id'] == $user['id']) echo " +" . $k_f . "";
			echo "</a>";

			/*--------------------------------------------------------------*/


			/*------------------------相片册---------------------------*/

			echo "<a class='top_nav' href='/foto/$ank[id]/'>照片 ";
			echo "(" . dbresult(dbquery("SELECT COUNT(*) FROM `gallery_foto` WHERE `id_user` = '$ank[id]'"), 0) . ")</a>";

			/*--------------------------------------------------------------*/
			/*-------------------------个人档案---------------------------*/
			if (dbresult(dbquery("SELECT COUNT(*) FROM `user_files` WHERE `id_user` = '$ank[id]' AND `osn` = '1'"), 0) == 0) {
				dbquery("INSERT INTO `user_files` (`id_user`, `name`,  `osn`) values('$ank[id]', '文件', '1')");
			}
			$dir_osn = dbassoc(dbquery("SELECT * FROM `user_files` WHERE `id_user` = '$ank[id]' AND `osn` = '1' LIMIT 1"));

			if (isset($dir_osn['id'])) echo "<a class='top_nav' href='/user/personalfiles/$ank[id]/$dir_osn[id]/'>文件";
			echo "(" . dbresult(dbquery("SELECT COUNT(*) FROM `user_files` WHERE `id_user` = '$ank[id]' AND `osn` > '1'"), 0) . "/" . dbresult(dbquery("SELECT COUNT(*) FROM `obmennik_files` WHERE `id_user` = '$ank[id]'"), 0) . ")";
			echo "</a>";
			/*----------------------------------------------------------------*/

			echo "<a class='top_nav' href='/user/info/them_p.php?id=" . $ank['id'] . "'>专题和评论</a> ";

			/*-------------------------音乐---------------------------------*/
			$k_music = dbresult(dbquery("SELECT COUNT(*) FROM `user_music` WHERE `id_user` = '$ank[id]'"), 0);

			echo "<a class='top_nav' href='/user/music/index.php?id=$ank[id]'>音乐 ";
			echo "(" . $k_music . ")";
			echo "</a>";
			/*----------------------------------------------------------------*/

			/*---------------------------日记------------------------------*/
			echo "<div>";
			$kol_dnev = dbresult(dbquery("SELECT COUNT(*) FROM `notes` WHERE `id_user` = '" . $ank['id'] . "'"), 0);
			echo "<a class='top_nav' href='/plugins/notes/user.php?id=$ank[id]'>日记 ($kol_dnev)";
			echo "</a>";
			/*----------------------------------------------------------------*/




			/*
========================================
书签
========================================
*/
			$zakladki = dbresult(dbquery("SELECT COUNT(id) FROM `bookmarks` WHERE `id_user` = '" . $ank['id'] . "'"), 0);
			echo "<a class='top_nav' href='/user/bookmark/index.php?id=$ank[id]'>书签($zakladki)";
			echo "</a><br />";

			echo '</table>';

			/*
调查问卷、照片和墙壁的输出
*/
			echo "<div class='accordion-group'>
<div class='accordion-heading'>";
			echo " " . group($ank['id']) . " ";
			echo user::nick($ank['id'], 1, 1, 1) . " <span style='float:right;color:#666;'>进来了" . ($ank['pol'] == 0 ? 'a' : null) . " " . vremja($ank['date_last']) . "</span> ";
			if ((user_access('user_ban_set') || user_access('user_ban_set_h') || user_access('user_ban_unset')) && $ank['id'] != $user['id'])
				echo "<a href='/adm_panel/ban.php?id=$ank[id]'><font color=red>[禁止]</font></a>";
			echo "</div></div>";




			//-------------статус вывод------------//

			if (isset($status['id']) || $ank['id'] == $user['id']) {
				echo '<div class="st_1"></div>';
				echo '<div class="st_2">';
				if (isset($user) && $user['id'] == $ank['id']) {
					echo "<form style='border:none;' action='?id=" . $ank['id'] . "' method=\"post\">";
					echo "<input type=\"text\" style='width:80%;' placeholder=''你有什么新鲜事？?' name=\"status\" value=\"\"/> ";
					echo "<input class=\"submit\" style=' width:15%;' type=\"submit\" value=\"+\" />";
					echo "</form>";
				}
				if (isset($status['id'])) echo output_text($status['msg']) . ' <font style="font-size:10px; color:gray;">' . vremja($status['time']) . '</font>';
				echo "</div>";
				if (isset($status['id'])) {
					echo " <a href='/user/status/komm.php?id=$status[id]'><img src='/style/icons/bbl4.png' alt=''/> " . dbresult(dbquery("SELECT COUNT(*) FROM `status_komm` WHERE `id_status` = '$status[id]'"), 0) . " </a> ";
					$l = dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]'"), 0);
					if (isset($user) && $user['id'] != $ank['id'] && dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]' AND `id_user` = '$user[id]' LIMIT 1"), 0) == 0) {
						echo " <a href='/info.php?id=$ank[id]&amp;like'><img src='/style/icons/like.gif' alt='*'/> 等级!</a> • ";
						$like = $l;
					} else if (isset($user) && $user['id'] != $ank['id']) {
						echo " <img src='/style/icons/like.gif' alt=''/> 你和。 ";
						$like = $l - 1;
					} else {
						echo " <img src='/style/icons/like.gif' alt=''/> ";
						$like = $l;
					}
					echo "<a href='/user/status/like.php?id=$status[id]'> $like 人. </a>";
					echo '</div>';
				}

				/* Общее колличество статусов */
				$st = dbresult(dbquery("SELECT COUNT(*) FROM `status` WHERE `id_user` = '$ank[id]'"), 0);
				if ($st > 0) {
					echo "<div class='main2'>"; // пишем свой див
					echo " &rarr; <a href='/user/status/index.php?id=$ank[id]'>所有状态</a> (" . $st . ")";
					echo "</div>";
				}
			}
/*
===============================
最近添加的照片
===============================
*/
			$sql = dbquery("SELECT * FROM `gallery_foto` WHERE `id_user` = '$ank[id]' ORDER BY `id` DESC LIMIT 8");
			$coll = dbresult(dbquery("SELECT COUNT(*) FROM `gallery_foto` WHERE `id_user` = '$ank[id]' ORDER BY `id` DESC"), 0);
			if ($coll > 0) {
				echo "<div class='slim_header'>";
				echo "<img src='/style/icons/pht2.png' alt='*' /> ";
				echo "<a href='/foto/$ank[id]/'><b>照片</b></a> ";
				echo " <span class='mm_counter'>" . dbresult(dbquery("SELECT COUNT(*) FROM `gallery_foto` WHERE `id_user` = '$ank[id]'"), 0) . "</span>";
				echo "</div>";
				echo "<div class='nav3'>";

				while ($photo = dbassoc($sql)) {
					echo "<a href='/foto/$ank[id]/$photo[id_gallery]/$photo[id]/'><img class='sto500' style='width:103px; height:103px; background-image:url(/foto/foto0/$photo[id].$photo[ras]);' src=''/></a>";
				}
				echo "</div>";
			}
			/*
=====================================
用户问卷，如果作者
然后输出编辑链接
字段，如果不是，则不是 =）
=====================================
*/
			if (isset($user) && $ank['id'] == $user['id']) {
				$name = "<a href='/user/info/edit.php?act=ank_web&amp;set=name'>";
				$date = "<a href='/user/info/edit.php?act=ank_web&amp;set=date'>";
				$gorod = "<a href='/user/info/edit.php?act=ank_web&amp;set=gorod'>";
				$pol = "<a href='/user/info/edit.php?act=ank_web&amp;set=pol'>";
				$a = "</a>";
			} else {
				$name = "<font style='padding:1px; color : #005ba8; padding:1px;'>";
				$date =  "<font style='padding:1px; color : #005ba8; padding:1px;'>";
				$gorod =  "<font style='padding:1px; color : #005ba8; padding:1px;'>";
				$pol =   "<font style='padding:1px; color : #005ba8; padding:1px;'>";
				$a = "</font>";
			}


			/*
=====================================
Основное
=====================================
*/
			echo "<div class='nav1'>";
			if ($ank['ank_name'] != NULL)
				echo "$name<span class=\"ank_n\">姓名:</span>$a <span class=\"ank_d\">$ank[ank_name]</span><br />";
			else
				echo "$name<span class=\"ank_n\">姓名:</span>$a<br />";
			echo "$pol<span class=\"ank_n\">Пол:</span>$a <span class=\"ank_d\">" . (($ank['pol'] == 1) ? '男' : '女') . "</span><br />";
			if ($ank['ank_city'] != NULL)
				echo "$gorod<span class=\"ank_n\">城市:</span>$a <span class=\"ank_d\">" . output_text($ank['ank_city']) . "</span><br />";
			else
				echo "$gorod<span class=\"ank_n\">城市:</span>$a<br />";
			if ($ank['ank_d_r'] != NULL && $ank['ank_m_r'] != NULL && $ank['ank_g_r'] != NULL) {
				if ($ank['ank_m_r'] == 1) $ank['mes'] = '1 月';
				elseif ($ank['ank_m_r'] == 2) $ank['mes'] = '2 月';
				elseif ($ank['ank_m_r'] == 3) $ank['mes'] = '玛尔塔';
				elseif ($ank['ank_m_r'] == 4) $ank['mes'] = '4 月';
				elseif ($ank['ank_m_r'] == 5) $ank['mes'] = '五月';
				elseif ($ank['ank_m_r'] == 6) $ank['mes'] = '6 月';
				elseif ($ank['ank_m_r'] == 7) $ank['mes'] = 'Июля';
				elseif ($ank['ank_m_r'] == 8) $ank['mes'] = 'Августа';
				elseif ($ank['ank_m_r'] == 9) $ank['mes'] = 'Сентября';
				elseif ($ank['ank_m_r'] == 10) $ank['mes'] = 'Октября';
				elseif ($ank['ank_m_r'] == 11) $ank['mes'] = 'Ноября';
				else $ank['mes'] = '12 月';
				echo "$date<span class=\"ank_n\">出生日期:</span>$a $ank[ank_d_r] $ank[mes] $ank[ank_g_r]г. <br />";
				$ank['ank_age'] = date("Y") - $ank['ank_g_r'];
				if (date("n") < $ank['ank_m_r']) $ank['ank_age'] = $ank['ank_age'] - 1;
				elseif (date("n") == $ank['ank_m_r'] && date("j") < $ank['ank_d_r']) $ank['ank_age'] = $ank['ank_age'] - 1;
				echo "<span class=\"ank_n\">年龄:</span> $ank[ank_age] ";
			} elseif ($ank['ank_d_r'] != NULL && $ank['ank_m_r'] != NULL) {
				if ($ank['ank_m_r'] == 1) $ank['mes'] = '1 月';
				elseif ($ank['ank_m_r'] == 2) $ank['mes'] = '2 月';
				elseif ($ank['ank_m_r'] == 3) $ank['mes'] = '玛尔塔';
				elseif ($ank['ank_m_r'] == 4) $ank['mes'] = '4 月';
				elseif ($ank['ank_m_r'] == 5) $ank['mes'] = 'Мая';
				elseif ($ank['ank_m_r'] == 6) $ank['mes'] = 'Июня';
				elseif ($ank['ank_m_r'] == 7) $ank['mes'] = 'Июля';
				elseif ($ank['ank_m_r'] == 8) $ank['mes'] = 'Августа';
				elseif ($ank['ank_m_r'] == 9) $ank['mes'] = 'Сентября';
				elseif ($ank['ank_m_r'] == 10) $ank['mes'] = 'Октября';
				elseif ($ank['ank_m_r'] == 11) $ank['mes'] = 'Ноября';
				else $ank['mes'] = '12 月';
				echo "$date<span class=\"ank_n\">生日。:</span>$a $ank[ank_d_r] $ank[mes] ";
			}
			if ($ank['ank_d_r'] >= 19 && $ank['ank_m_r'] == 1) {
				echo "| 水瓶座<br />";
			} elseif ($ank['ank_d_r'] <= 19 && $ank['ank_m_r'] == 2) {
				echo "| Водолей<br />";
			} elseif ($ank['ank_d_r'] >= 18 && $ank['ank_m_r'] == 2) {
				echo "| Рыбы<br />";
			} elseif ($ank['ank_d_r'] <= 21 && $ank['ank_m_r'] == 3) {
				echo "| Рыбы<br />";
			} elseif ($ank['ank_d_r'] >= 20 && $ank['ank_m_r'] == 3) {
				echo "| Овен<br />";
			} elseif ($ank['ank_d_r'] <= 21 && $ank['ank_m_r'] == 4) {
				echo "| Овен<br />";
			} elseif ($ank['ank_d_r'] >= 20 && $ank['ank_m_r'] == 4) {
				echo "| Телец<br />";
			} elseif ($ank['ank_d_r'] <= 21 && $ank['ank_m_r'] == 5) {
				echo "| Телец<br />";
			} elseif ($ank['ank_d_r'] >= 20 && $ank['ank_m_r'] == 5) {
				echo "| Близнецы<br />";
			} elseif ($ank['ank_d_r'] <= 22 && $ank['ank_m_r'] == 6) {
				echo "| Близнецы<br />";
			} elseif ($ank['ank_d_r'] >= 21 && $ank['ank_m_r'] == 6) {
				echo "| Рак<br />";
			} elseif ($ank['ank_d_r'] <= 22 && $ank['ank_m_r'] == 7) {
				echo "| Рак<br />";
			} elseif ($ank['ank_d_r'] >= 23 && $ank['ank_m_r'] == 7) {
				echo "| Лев<br />";
			} elseif ($ank['ank_d_r'] <= 22 && $ank['ank_m_r'] == 8) {
				echo "| Лев<br />";
			} elseif ($ank['ank_d_r'] >= 22 && $ank['ank_m_r'] == 8) {
				echo "| Дева<br />";
			} elseif ($ank['ank_d_r'] <= 23 && $ank['ank_m_r'] == 9) {
				echo "| Дева<br />";
			} elseif ($ank['ank_d_r'] >= 22 && $ank['ank_m_r'] == 9) {
				echo "| Весы<br />";
			} elseif ($ank['ank_d_r'] <= 23 && $ank['ank_m_r'] == 10) {
				echo "| Весы<br />";
			} elseif ($ank['ank_d_r'] >= 22 && $ank['ank_m_r'] == 10) {
				echo "| Скорпион<br />";
			} elseif ($ank['ank_d_r'] <= 22 && $ank['ank_m_r'] == 11) {
				echo "| Скорпион<br />";
			} elseif ($ank['ank_d_r'] >= 21 && $ank['ank_m_r'] == 11) {
				echo "| Стрелец<br />";
			} elseif ($ank['ank_d_r'] <= 22 && $ank['ank_m_r'] == 12) {
				echo "| Стрелец<br />";
			} elseif ($ank['ank_d_r'] >= 21 && $ank['ank_m_r'] == 12) {
				echo "| Козерог<br />";
			} elseif ($ank['ank_d_r'] <= 20 && $ank['ank_m_r'] == 1) {
				echo "| Козерог<br />";
			}
			echo "</div>";
			echo '<form action="someplace.html" method="post" name="myForm"><div id="formResponse">';
			echo ' <a onclick="anketa.submit()" name="myForm"><div class="form_info">显示详细信息</div></a>';
			echo '</div></form>';
			echo "<script type='text/javascript'>	
	var anketa = new DHTMLSuite.form({ formRef:'myForm',action:'/ajax/php/anketa.php?id=$ank[id]',responseEl:'formResponse'});	
	var anketaClose = new DHTMLSuite.form({ formRef:'myForm',action:'/ajax/php/anketa.php',responseEl:'formResponse'});
		</script>";


/*
========================================
礼物
========================================
*/
			$k_p = dbresult(dbquery("SELECT COUNT(id) FROM `gifts_user` WHERE `id_user` = '$ank[id]' AND `status` = '1'"), 0);
			$width = ($webbrowser == 'web' ? '60' : '45'); // Размер подарков при выводе в браузер

			if ($k_p > 0) {
				echo '<div class="foot">';
				echo '&rarr; <a href="/user/gift/index.php?id=' . $ank['id'] . '">所有的礼物</a> (' . $k_p . ')';
				echo '</div>';


				$q = dbquery("SELECT id,id_gift,status FROM `gifts_user` WHERE `id_user` = '$ank[id]' AND `status` = '1' ORDER BY `id` DESC LIMIT 7");
				echo '<div class="nav2">';
				while ($post = dbassoc($q)) {
					$gift = dbassoc(dbquery("SELECT id FROM `gift_list` WHERE `id` = '$post[id_gift]' LIMIT 1"));
					echo '<a href="/user/gift/gift.php?id=' . $post['id'] . '"><img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:' . $width . 'px;" alt="礼物" /></a> ';
				}
				echo '</div>';
			}

/*
=====================================
尤兹韦尔墙
=====================================
*/
			if (isset($user)) {
				echo "<div class='accordion-group'>
<div class='accordion-heading'>";
				if ($user['wall'] == 1) {
					echo '<a class="accordion-toggle decoration-none collapsed" href="/info.php?id=' . $ank['id'] . '&amp;wall=0"><img src="/style/icons/stena.gif" alt="*" /> 墙</a>';
					include_once 'user/stena/index.php';
				} else {
					echo '<a class="accordion-toggle decoration-none collapsed" href="/info.php?id=' . $ank['id'] . '&amp;wall=1"><img src="/style/icons/stena.gif" alt="*" /> 墙</a>';
				}
				echo '</div></div>';
			}
			/*--------------------------------------------------------------*/
			?>
		</td>
	</tr>
</table>