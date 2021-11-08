<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
$set['title'] = '领导者'; //网页标题
include_once '../../sys/inc/thead.php';
title();
aut();
err();
echo '<div class="foot">';
echo '<img src="/style/icons/lider.gif" alt="S"/> <a href="/user/money/liders.php">成为领导者</a>';
echo '</div>';
$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `liders` WHERE `time` > '$time'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
echo '<table class="post">';
if ($k_post == 0)
{
echo '<div class="mess">';
echo '没有领导者';
echo '</div>';
}
$q=dbquery("SELECT * FROM `liders` WHERE `time` > '$time' ORDER BY stav DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q))
{
$ank=get_user($post['id_user']);
/*-----------зебра-----------*/ 
	if ($num==0){
		echo '<div class="nav1">';
		$num=1;
	}
	elseif ($num==1){
		echo '<div class="nav2">';
		$num=0;
	}
/*---------------------------*/
echo status($ank['id']); // Аватарка
echo group($ank['id']) , ' <a href="/info.php?id=' . $ank['id'] . '">' . $ank['nick'] . '</a> ';
echo medal($ank['id']) , online($ank['id']) . ' (' . vremja($post['time']) . ')<br />';
echo 'Ставка: <b style="color:red;">' . $post['stav'] . '</b> <b style="color:green;">' . $sMonet[0] . '</b><br />';
echo output_text($post['msg']) . '<br />';
if (isset($user) && $user['level'] > 2)
echo '<div style="text-align:right;"><a href="delete.php?id=' . $post['id_user'] . '"><img src="/style/icons/delete.gif" alt="*"/></a></div>';
echo '</div>';
}
echo '</table>';
if ($k_page > 1)str('?' , $k_page , $page); // Вывод страниц
echo '<div class="foot">';
echo '<img src="/style/icons/lider.gif" alt="S"/> <a href="/user/money/liders.php">成为领导者</a>';
echo '</div>';
include_once '../../sys/inc/tfoot.php';
?>