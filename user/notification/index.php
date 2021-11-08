<?

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
only_reg();
$width = ($webbrowser == 'web' ? '100' : '70'); // Размер подарков при выводе в браузер
/*
===============================
Полная очистка уведомлений
===============================
*/
if (isset($_GET['delete']) && $_GET['delete']=='all')
{
if (isset($user))
{
	dbquery("DELETE FROM `notification` WHERE `id_user` = '$user[id]'");
	$_SESSION['message'] = 'Уведомления очищены';
	header("Location: ?");
	exit;
}
}

if (isset($_GET['del'])) // удаление уведомления
{
if (isset($user))
{
	if (dbresult(dbquery("SELECT COUNT(*) FROM `notification`  WHERE `id_user` = '$user[id]' AND `id` = '".intval($_GET['del'])."'"),0)==1)
	{
	dbquery("DELETE FROM `notification` WHERE `id_user` = '$user[id]' AND `id` = '".intval($_GET['del'])."' LIMIT 1");
	$_SESSION['message'] = 'Уведомление удалено';
	header("Location: ?komm&".intval($_GET['page'])."");
	exit;
	}
}
}
$set['title']='Уведомления';
include_once '../../sys/inc/thead.php';
title();
err();
aut();
/*
======
Панель
======
*/
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
echo "<a href='/user/tape/'>Лента $lenta</a>";
echo "</div>"; 
echo "<div class='webmenu'>";
echo "<a href='/user/discussions/' >Обсуждения $discuss</a>";
echo "</div>"; 
echo "<div class='webmenu'>";
echo "<a href='/user/notification/' class='activ'>Уведомления $k_notif</a>";
echo "</div>"; 
echo "</div>";

/*
==========
Список уведомлений
==========
*/
	$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `notification`  WHERE `id_user` = '$user[id]' "),0);
	$k_page=k_page($k_post,$set['p_str']);
	$page=page($k_page);
	$start=$set['p_str']*$page-$set['p_str'];

	$q=dbquery("SELECT * FROM `notification` WHERE `id_user` = '$user[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");
if ($k_post==0) //Если нет уведомлений, то...
{
	echo "  <div class='mess'>\n";
		echo "Нет новых уведомлений\n";
	echo "  </div>\n";
}
//Если есть, то...
while ($post = dbassoc($q))
{
/*-----------зебра-----------*/ 
if ($num==0){
	echo '<div class="nav1">';
	$num=1;
}elseif ($num==1){
	echo '<div class="nav2">';
	$num=0;
}
/*---------------------------*/
	$type = $post['type']; //Тип уведомления
	$avtor = get_user($post['avtor']); //От кого уведомление
	
	if ($post['read']==0) //Если не прочитано
	{
	$s1 = "<font color='red'>";
	$s2 = "</font>";
	}else{
	$s1 = null;
	$s2 = null;
	}
/*
===============================
Значение переменной $name для 
определенного типа сообщения
===============================
*/
if ($type == 'ok_gift') // Принимаем подарок
{	
	$name = 'принял'.($avtor['pol'] == 1 ? "" : "а") . ' ваш подарок ';
}
elseif ($type == 'no_gift') // Отказ от подарка
{	
	$name = 'отклонил'.($avtor['pol'] == 1 ? "" : "а") . ' ваш подарок ';
}
elseif ($type == 'new_gift') // Подарки новые
{	
	$name = 'сделал'.($avtor['pol'] == 1 ? "" : "а") . ' вам подарок ';
}
elseif ($type == 'files_komm' || $type == 'obmen_komm') // Файлы
{	
	$name = 'ответил'.($avtor['pol'] == 1 ? "" : "а") . ' вам в комментариях к файлу ';
}

elseif ($type == 'news_komm') // Новости 
{	
	$name = 'ответил'.($avtor['pol'] == 1 ? "" : "а") . ' вам в комментариях к новости ';
}
elseif ($type == 'status_komm') // Статусы
{	

	$status = dbassoc(dbquery("SELECT * FROM `status` WHERE `id` = '".$post['id_object']."' LIMIT 1"));
	$name = 'ответил'.($avtor['pol'] == 1 ? "" : "а") . ' вам в комментариях этого ';
}
elseif ($type == 'foto_komm') // Фото 
{	
	$name = 'ответил'.($avtor['pol'] == 1 ? "" : "а") . ' вам в комментариях к фотографии ';
}
elseif ($type == 'notes_komm') // Дневники
{	
	$name = 'ответил'.($avtor['pol'] == 1 ? "" : "а") . ' вам в комментариях к дневнику ';
}
elseif ($type == 'them_komm') // форум
{	
	$name = 'ответил' . ($avtor['pol'] == 1 ? "" : "а") . ' вам в теме ';
}
elseif ($type == 'stena_komm') // Стена
{	
	$stena = get_user($post['id_object']);
	if ($stena['id'] == $user['id']) $sT = 'вашей';
	elseif ($stena['id'] == $avtor['id']) $sT = 'своей';
	else{ $sT = null; }
	$name = 'ответил' . ($avtor['pol'] == 1 ? "" : "а") . ' вам на '.$sT;
}
elseif ($type == 'guest' || $type == 'adm_komm') // Гостевая, админ чат
{	
	$name = 'ответил' . ($avtor['pol'] == 1 ? "" : "а").' вам в ';
}
elseif ($type == 'del_frend') // Уведомления о удаленных друзьях
{	
	$name = ' к сожалению удалил' . ($avtor['pol'] == 1 ? "" : "а").' вас из списка друзей';
}
elseif ($type == 'no_frend') // Уведомления о отклоненных заявках в друзья
{	
	$name = ' к сожалению отказал' . ($avtor['pol'] == 1 ? "" : "а").' вам в дружбе';
}

elseif ($type == 'ok_frend') // Уведомления о принятых заявках в друзья
{	
	$name = ' стал' . ($avtor['pol'] == 1 ? "" : "а").' вашим другом';
}
elseif ($type == 'otm_frend') // Уведомления о отмененных заявках в друзья
{	
	$name = ' отменил' . ($avtor['pol'] == 1 ? "" : "а").' свою заявку на добавление вас в друзья';
}elseif($type=='stena_komm2'){
$name=' написал '.($avtor['pol']==1 ? ' ' : 'a').' у Вас <a href="/user/komm.php?id='.$post['id_object'].'">в записи на стене</a>';
}
/*
===============================
Подарки
===============================
*/	
if ($type == 'new_gift' || $type == 'no_gift' || $type == 'ok_gift') 
{	
if ($type == 'new_gift'){
	$id_gift =  dbassoc(dbquery("SELECT id,id_gift FROM `gifts_user` WHERE `id` = '$post[id_object]' LIMIT 1"));
	$gift =  dbassoc(dbquery("SELECT * FROM `gift_list` WHERE `id` = '$id_gift[id_gift]' LIMIT 1"));
}else{
	$gift =  dbassoc(dbquery("SELECT * FROM `gift_list` WHERE `id` = '$post[id_object]' LIMIT 1"));
}
if ($avtor['id']){
	echo  group($avtor['id'])." ";
 echo user::nick($avtor['id'],1,1,1)." ". $name;
	if ($type == 'new_gift')echo '<a href="/user/gift/gift.php?id=' . $id_gift['id'] . '"><img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:60px;" alt="*" /> ' . htmlspecialchars($gift['name']) . '</a>';
else echo '<img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:60px;" alt="*" /> ' . htmlspecialchars($gift['name']);
echo "  $s1 ".vremja($post['time'])." $s2";
} 
if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");

echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
} 
/*
===============================
Друзья/Заявки
===============================
*/	
if ($type == 'no_frend' || $type == 'ok_frend' || $type == 'del_frend' || $type == 'otm_frend') 
{	
if ($avtor['id']){
	echo status($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>";
	echo "  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
	echo "  $s1 ".vremja($post['time'])." $s2";
} else {
	echo " Этот друг уже удален с сайта =)  $s1 ".vremja($post['time'])." $s2";
}
	echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
} 
/*
===============================
Дневники коментарии
===============================
*/	
if ($type == 'notes_komm') 
{	
if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
	$notes = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id` = '".$post['id_object']."' LIMIT 1"));
if ($notes['id']){
	echo status($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
	echo " <img src='/style/icons/zametki.gif' alt='*'> ";
	echo '<a href="/plugins/notes/list.php?id='.$notes['id'].'&amp;page='.$pageEnd.'"><b>'.htmlspecialchars($notes['name']).'</b></a> ';
	echo "  $s1 ".vremja($post['time'])." $s2";
} else {
	echo " Этот дневник уже удален =(  $s1 ".vremja($post['time'])." $s2";
}
	echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
}
/*
===============================
Файлы коментарии
===============================
*/	
if ($type == 'files_komm' || $type == 'obmen_komm') 
{	if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
	$file = dbassoc(dbquery("SELECT * FROM `obmennik_files` WHERE `id` = '".$post['id_object']."' LIMIT 1"));
	$dir=dbassoc(dbquery("SELECT * FROM `user_files` WHERE `id` = '".$file['my_dir']."' LIMIT 1"));
	$ras = $file['ras'];
if ($file['id'] && $avtor['id']){
	echo status($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
	echo " <img src='/style/icons/d.gif' alt='*'> ";
	echo '<a href="/user/personalfiles/'.$file['id_user'].'/'.$dir['id'].'/?id_file='.$file['id'].'&amp;page='.$pageEnd.'"><b>'.htmlspecialchars($file['name']).'.'.$ras.'</b></a> ';
	echo "  $s1 ".vremja($post['time'])." $s2";
} else {
	echo " Этот " . (!$file['id'] ? "файл" : "пользователь" ) . " уже удален =(  $s1 ".vremja($post['time'])." $s2";
}
	echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
}
/*
===============================
Фото коментарии
===============================
*/	
if ($type == 'foto_komm') 
{	
if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
	$foto = dbassoc(dbquery("SELECT * FROM `gallery_foto` WHERE `id` = '".$post['id_object']."' LIMIT 1"));
if ($foto['id']){
	echo status($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
	echo " <img src='/style/icons/foto.png' alt='*'> ";
	echo " <a href='/foto/$foto[id_user]/$foto[id_gallery]/$foto[id]/?page=$pageEnd'>" . htmlspecialchars($foto['name']) . "</a> ";
	echo "  $s1 ".vremja($post['time'])." $s2";
} else {
	echo " Эта фотография уже удалена =(  $s1 ".vremja($post['time'])." $s2";
}
	echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
/*
===============================
Форум коментарии
===============================
*/	
if ($type == 'them_komm') 
{	
	$them=dbassoc(dbquery("SELECT * FROM `forum_t` WHERE `id` = '".$post['id_object']."' LIMIT 1"));
if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
if ($them['id']){
	echo status($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
	echo "<img src='/style/themes/$set[set_them]/forum/14/them_$them[up]$them[close].png' alt='*' /> ";
	echo " <a href='/forum/$them[id_forum]/$them[id_razdel]/$them[id]/?page=$pageEnd'>" . htmlspecialchars($them['name']) . "</a>  $s1 ".vremja($post['time'])." $s2";
} else {
	echo " Эта тема уже удалена =(  $s1 ".vremja($post['time'])." $s2";
}
	echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
}
/*
===============================
Стена юзера
===============================
*/	
if ($type == 'stena_komm') 
{	if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
	echo status($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
	echo "<img src='/style/icons/stena.gif' alt='*'> <a href='/info.php?id=$stena[id]&amp;page=$pageEnd'>стене</a> " . ($sT == null ? "$stena[nick]" : "") . "  $s1 ".vremja($post['time'])." $s2";
	echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
	}
	if($type=='stena_komm2'){
	if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
	echo status($avtor['id']) . group($avtor['id']). ' ';
	echo user::nick($avtor['id'],1,1,1).' '.$name.' ';
	echo ''.$s1. vremja($post['time']). $s2.' ';
	echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>"; }
	if($type=='stena'){
	if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
	echo status($avtor['id']) . group($avtor['id']). ' ';
	echo user::nick($avtor['id'],1,1,1).' написал'.($avtor['pol']==0 ? 'a' : null).' у Вас на стене';
	echo ''.$s1. vremja($post['time']). $s2.' ';
	echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>"; }
/*
===============================
Стасус коментарии
===============================
*/	
if ($type == 'status_komm') 
{	if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
	if ($status['id']){
	$ankS = get_user($status['id_user']);
	echo status($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
	echo "<img src='/style/icons/comment.png' alt='*'> <a href='/user/status/komm.php?id=$status[id]&amp;page=$pageEnd'>статуса</a>  $s1 ".vremja($post['time'])." $s2";
	}else{
	echo 'Статус уже удален =(';
	}
	echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
}
/*
===============================
Новости коментарии
===============================
*/	
if ($type == 'news_komm') 
{	if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
	$news = dbassoc(dbquery("SELECT * FROM `news` WHERE `id` = '".$post['id_object']."' LIMIT 1"));
	echo status($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
	echo "<img src='/style/icons/news.png' alt='*'> <a href='/news/news.php?id=$news[id]&amp;page=$pageEnd'>" . htmlspecialchars($news['title']) . "</a>   $s1 ".vremja($post['time'])." $s2";
	echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
}
/*
===============================
Гостевая коментарии
===============================
*/	
if ($type == 'guest') 
{	if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
	if ($avtor['id'])
	{
	echo status($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
	echo "<img src='/style/icons/guest.png' alt='*'> <a href='/guest/?page=$pageEnd'>гостевой</a>  $s1 ".vremja($post['time'])." $s2";
	} else {
	echo 'Этот пользователь пользователь уже удален =(';
	}
	echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
}
/*
===============================
Админ чат
===============================
*/	
if ($type == 'adm_komm') 
{	if ($post['read'] == 0) dbquery("UPDATE `notification` SET `read` = '1' WHERE `id` = '$post[id]'");
	echo status($avtor['id']) .  group($avtor['id']) . " <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>  " . medal($avtor['id']) . " " . online($avtor['id']) . " $name ";
	echo "<img src='/style/icons/chat.gif' alt='S' /> <a href='/plugins/admin/chat/?page=$pageEnd'>админ чате</a>  $s1 ".vremja($post['time'])." $s2";
	echo "<div style='text-align:right;'><a href='?komm&amp;del=$post[id]&amp;page=$page'><img src='/style/icons/delete.gif' alt='*' /></a></div>";
}
	echo "</div>";
}


if ($k_page>1)str('?',$k_page,$page); // Вывод страниц
echo '<div class="mess"><img src="/style/icons/delete.gif"> <a href="?delete=all">Удалить все уведомления</a></div>';
echo "<div class=\"foot\">\n";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php?id=$user[id]'>$user[nick]</a> | \n";
echo '<b>Уведомления</b> | <a href="settings.php">Настройки</a>';
echo "</div>\n";
include_once '../../sys/inc/tfoot.php';
?>