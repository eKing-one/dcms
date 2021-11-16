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

/* Бан пользователя */ 
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
header('Location: /ban.php?'.SID);exit;
}

$set['title']='日记';
include_once '../../sys/inc/thead.php';
title();
aut(); // форма авторизации
/*** Поле поиска ****/
echo "<div class='foot'><form method=\"post\" action=\"search.php?go\">";
echo "<table><td><input style='width:95%;' type=\"text\" name=\"usearch\" maxlength=\"16\" /></td><td> ";
echo "<input type=\"submit\" value=\"搜索\" /></td></table>";
echo "</form></div>";

/**** Панель навигации ****/
echo "<div id='comments' class='menus'>";
echo "<div class='webmenu'>";
echo "<a href='index.php' class='activ'>日记</a>";
echo "</div>"; 
echo "<div class='webmenu last'>";
echo "<a href='dir.php'>类别：</a>";
echo "</div>"; 
        if(isset($user)){
echo "<div class='webmenu last'>";
echo "<a href='user.php?id=".$user['id']."'>我的</a>";
echo "</div>"; 
}
echo "</div>";

/**** Сортировка*****/
$sortir = isset($_GET['sort']) ? $_GET['sort'] : NULL;
switch($sortir)
{
case 't':
$order='order by `time` desc';
echo"<div class='foot'><b>新的</b> | <a href='?sort=c'>流行的</a></div>";
break;
case 'c':
$order='order by `count` desc';
echo"<div class='foot'><a href='?sort=t'>新的</a> | <b>Популярные</b></div>";
/* Сортировка популярных дневников по времени */
echo "<div class='nav2'>";
if(isset($_GET['new']) && $_GET['new']=='t'){
echo"<b>新的</b> | <a href='?sort=c&new=m'>每月</a> | <a href='?sort=c&new=v'>一直如此</a>";
$new=" AND `time`>'".(time()-600)."' ";
}elseif(isset($_GET['new']) && $_GET['new']=='m'){
echo"<a href='?sort=c&new=t'>新的</a> | <b>每月</b> | <a href='?sort=c&new=v'>一直如此</a>";
$new=" AND `time`>'".(time()-2592000)."' ";
}elseif(isset($_GET['new']) && $_GET['new']=='v'){
echo"<a href='?sort=c&new=t'>新的</a> | <a href='?sort=c&new=m'>每月</a> | <b>一直如此</b>";
$new=null;
}elseif(isset($_GET['sort']) && $_GET['sort']=='c'){
 echo"<b>新的</b> | <a href='?sort=c&new=m'>每月</a> | <a href='?sort=c&new=v'>一直如此</a>";
$new=" AND `time`>'".(time()-600)."' ";
}else{ $new=null; }
echo "</div>";
/* Сортировка популярных дневников по времени */
break;
default:
$order='order by `time` desc';
 echo "<div class='foot'><b>新的</b> | <a href='?sort=c'>流行的</a></div>";
}

if(!isset($_GET['sort']) OR $_GET['sort']!='c'){
$new=null; }
$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `notes` WHERE `private`='0'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];

$q=dbquery("SELECT * FROM `notes` $order LIMIT $start, $set[p_str]");

echo "<table class='post'>";

if ($k_post==0)
{
echo "  <div class='mess'>";
echo "没有记录";
echo "  </div>";
}

while ($post = dbassoc($q))
{

/*-----------зебра-----------*/
if ($num==0)
{echo "  <div class='nav1'>";
$num=1;
}elseif ($num==1)
{echo "  <div class='nav2'>";
$num=0;}
/*---------------------------*/
echo group($post['id_user'])." ";
echo user::nick($post['id_user'],1,1,1)." : <a href='/plugins/notes/list.php?id=".$post['id']."'>".text($post['name'])."</a>";
echo '<span style="float:right;color:#666;">'.vremja($post['time']).'</span><br/>';
echo rez_text($post['msg'],80)." <br/>";
notes_sh($post['id']);
echo "<br/><img src='/style/icons/uv.png'> <font color=#666>(".dbresult(dbquery("SELECT COUNT(`id`)FROM `notes_komm` WHERE `id_notes`='$post[id]'"),0).") &bull;";
echo " <a href='fav.php?id=".$post['id']."'><img src='/style/icons/add_fav.gif'> (".dbresult(dbquery("SELECT COUNT(`id`)FROM `bookmarks` WHERE `id_object`='".$post['id']."' AND `type`='notes'"),0).")</a> &bull; ";
echo " <img src='/style/icons/action_share_color.gif'> (".dbresult(dbquery("SELECT COUNT(`id`)FROM `notes` WHERE `share_id`='".$post['id']."' AND `share_type`='notes'"),0).") </font>";
echo "  </div>";

}
echo "</table>";

if (isset($_GET['sort'])) $dop="sort=".my_esc($_GET['sort'])."&amp;";
else $dop='';
if ($k_page>1)str('?'.$dop.'',$k_page,$page); // Вывод страниц
if(isset($user))echo "<div class='foot'><a href='add.php'> 创建记录</a></div>";
include_once '../../sys/inc/tfoot.php';
?>