<?
/*
=======================================
Лента друзей для Dcms-Social
Автор: Искатель
---------------------------------------
Этот скрипт распостроняется по лицензии
движка Dcms-Social. 
При использовании указывать ссылку на
оф. сайт http://dcms-social.ru
---------------------------------------
Контакты
ICQ: 587863132
http://dcms-social.ru
=======================================
*/

include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/adm_check.php';
include_once '../../sys/inc/user.php';

$my = null;
$frend = null;
$all = null;

only_reg();
	
/* Класс к статусу */

if (isset($_GET['likestatus']))
{

	// Статус пользователя
	$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id` = '".intval($_GET['likestatus'])."' LIMIT 1"));
	$ank = get_user($status['id_user']);
	if ($user['id']!=$ank['id'] && dbresult(dbquery("SELECT COUNT(*) FROM `status_like` WHERE `id_status` = '$status[id]' AND `id_user` = '$user[id]' LIMIT 1"),0)==0)
	{
		dbquery("INSERT INTO `status_like` (`id_user`, `time`, `id_status`) values('$user[id]', '$time', '$status[id]')");
		/*
		===================================
		Лента
		===================================
		*/

		$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $user['id'] . "' AND `i` = '1'");

		while ($f = dbarray($q))
		{
			$a = get_user($f['frend']);
			
			$lentaSet = dbarray(dbquery("SELECT * FROM `tape_set` WHERE `id_user` = '".$a['id']."' LIMIT 1")); // Общая настройка ленты
			if ($a['id'] != $ank['id'] && $f['lenta_status_like']==1 && $lentaSet['lenta_status_like']==1)
			dbquery("INSERT INTO `tape` (`id_user`,`ot_kogo`,  `avtor`, `type`, `time`, `id_file`) values('$a[id]', '$user[id]', '$status[id_user]', 'status_like', '$time', '$status[id]')"); 

		}

		header("Location: ?page=" . intval($_GET['page']));
		exit;
	}
}


$set['title']='Лента';
include_once '../../sys/inc/thead.php';

/*
===============================
Очищение списка непрочитанных
===============================
*/
if (isset($_GET['read']) && $_GET['read']=='all')
{
	if (isset($user))
	{
		dbquery("UPDATE `tape` SET `read` = '1' WHERE `id_user` = '$user[id]'");
		$_SESSION['message'] = 'Список непрочитанных очищен';
		header("Location: ?page=".intval($_GET['page'])."");
		exit;
	}
}


/*
===============================
Полная очистка ленты
===============================
*/
if (isset($_GET['delete']) && $_GET['delete']=='all')
{
	if (isset($user))
	{
		dbquery("DELETE FROM `tape` WHERE `id_user` = '$user[id]'");
		$_SESSION['message'] = 'Лента успешно очищена';
		header("Location: ?");
		exit;
	}
}
title();
err();
aut();

$k_notif = dbresult(dbquery("SELECT COUNT(`read`) FROM `notification` WHERE `id_user` = '$user[id]' AND `read` = '0'"), 0); // Уведомления

if ($k_notif > 0)$k_notif = '<font color=red>('.$k_notif.')</font>';
else $k_notif = null;

$discuss = dbresult(dbquery("SELECT COUNT(`count`) FROM `discussions` WHERE `id_user` = '$user[id]' AND `count` > '0' "),0); // Обсуждения

if ($discuss > 0)$discuss = '<font color=red>('.$discuss.')</font>';
else $discuss = null;

$lenta = dbresult(dbquery("SELECT COUNT(`read`) FROM `tape` WHERE `id_user` = '$user[id]' AND `read` = '0' "),0); // Лента

if ($lenta > 0)$lenta = '<font color=red>('.$lenta.')</font>';
else $lenta = null;

echo "<div id='comments' class='menus'>";
echo "<div class='webmenu'>";
echo "<a href='/user/tape/' class='activ'>Лента $lenta</a>";
echo "</div>"; 
echo "<div class='webmenu'>";
echo "<a href='/user/discussions/' >Обсуждения  $discuss</a>";
echo "</div>"; 
echo "<div class='webmenu'>";
echo "<a href='/user/notification/'>Уведомления $k_notif</a>";
echo "</div>"; 
echo "</div>";	


$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `tape`  WHERE `id_user` = '$user[id]' "),0);
$k_page = k_page($k_post,$set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page-$set['p_str'];
	


echo '<div class="foot">';
echo '<a href="?page=' . $page . '&amp;read=all"><img src="/style/icons/ok.gif"> Отметить всё как прочитанное</a>';
echo '</div>'; 


$q = dbquery("SELECT * FROM `tape` WHERE `id_user` = '$user[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");

if ($k_post == 0)
{
	echo "  <div class='mess'>";
	echo "Нет новых событий";
	echo "  </div>";
}

while ($post = dbassoc($q))
{
	$type = $post['type'];
	$avtor = get_user($post['avtor']);
	$name = null;
	
	if ($post['read'] == 0)
	{
		$s1 = "<font color='red'>";
		$s2 = "</font>";
		dbquery("UPDATE `tape` SET `read` = '1' WHERE `id` = '$post[id]'");
	}
	else
	{
		$s1 = null;
		$s2 = null;
	}

	/*
	===============================
	Помечаем сообщение прочитанным
	===============================
	*/	

	
	
	$d = opendir('inc/');

	while($dname = readdir($d))
	{
		if ($dname != '.' && $dname != '..')
		{
			include 'inc/' . $dname;
		}
	}
	
	echo '</div>';
}

if ($k_page>1)str('?',$k_page,$page); 


echo '<div class="foot">';
echo '<a href="?page=' . $page . '&amp;delete=all"><img src="/style/icons/delete.gif"> Очистить ленту</a>';
echo '</div>'; 
	
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> <a href="/info.php?id=' . $user['id'] . '">' . $user['nick'] . '</a> | ';
echo '<b>Лента</b>';
echo '</div>';

include_once '../../sys/inc/tfoot.php';
?>