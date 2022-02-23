<?
/*
==================================
Приватность станички пользователя
При использовании в других модулях
определяйте переменную $ank
::
$ank = get_user(object);
include H.'sys/add/user.privace.php';
==================================
*/	
// Настройки юзера
$uSet = dbarray(dbquery("SELECT * FROM `user_set` WHERE `id_user` = '$ank[id]'  LIMIT 1"));
// Статус друг ли вы
$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE 
 (`user` = '$user[id]' AND `frend` = '$ank[id]') OR 
 (`user` = '$ank[id]' AND `frend` = '$user[id]') LIMIT 1"),0);
// Проверка завки в друзья
$frend_new = dbresult(dbquery("SELECT COUNT(*) FROM `frends_new` WHERE 
 (`user` = '$user[id]' AND `to` = '$ank[id]') OR 
 (`user` = '$ank[id]' AND `to` = '$user[id]') LIMIT 1"),0);
/*
* Если вы не выше по должности хозяина альбома, 
* и вы не являетесь хозяином альбома
* и ваша должность равна или меньше должности хозяина альбома
* то приватность работает, либо она игнорируется
*/
if ($ank['id'] != $user['id'] && ($user['group_access'] == 0 || $user['group_access'] <= $ank['group_access']))
{	
	// Начинаем вывод если стр имеет приват настройки
	if (($uSet['privat_str'] == 2 && $frend != 2) || $uSet['privat_str'] == 0) 
	{
		if ($ank['group_access'] > 1)
		echo '<div class="err">' . $ank['group_name'] . '</div>';
		echo '<div class="nav1">';
		echo group($ank['id']) . user::nick($ank['id'], 0) . medal($ank['id']) . online($ank['id']);
		echo '</div>';		
		echo '<div class="nav2">';
		echo user::avatar($ank['id']);
		echo '</div>';	
	}
	if ($uSet['privat_str'] == 2 && $frend != 2) // Если только для друзей
	{
		echo '<div class="mess">';
		echo '只有用户的朋友才能查看用户的页面！';
		echo '</div>';
		// В друзья
		if (isset($user))
		{
			echo '<div class="nav1">';
			echo '<img src="/style/icons/druzya.png" alt="*"/>';
			if ($frend_new == 0 && $frend==0)
			{
				echo '<a href="/user/frends/create.php?add=' . $ank['id'] . '">添加为好友</a><br />';
			}
			elseif ($frend_new == 1)
			{
				echo '<a href="/user/frends/create.php?otm=' . $ank['id'] . '">拒绝申请</a><br />';
			}
			elseif ($frend == 2)
			{
				echo '<a href="/user/frends/create.php?del=' . $ank['id'] . '">把...从朋友中除名</a><br />';
			}
			echo '</div>';
		}
		include_once H.'sys/inc/tfoot.php';
		exit;
	}
	// Если cтраница закрыта
	if ($uSet['privat_str'] == 0) 
	{
		echo '<div class="mess">';
		echo '用户完全限制了对其页面的访问!';
		echo '</div>';
		include_once H.'sys/inc/tfoot.php';
		exit;
	}
}
