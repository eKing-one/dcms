<?
/*
=======================================
Дневники для Dcms-Social
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
/* Бан пользователя */ 
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
header('Location: /ban.php?'.SID);exit;
}
$set['title']='类别';
include_once '../../sys/inc/thead.php';
title();


if (isset($_POST['title']) && user_access('notes_edit'))
{
$title=my_esc($_POST['title'],1);
$msg=my_esc($_POST['msg']);


if (strlen2($title)>32){$err='标题不能超过 32 个字符';}
if (strlen2($title)<3){$err='短标题';}

if (strlen2($msg)>10024){$err='内容不能超过 10024 个字符';}
if (strlen2($msg)<2){$err='内容太短';}

if (!isset($err)){
dbquery("INSERT INTO `notes_dir` (`msg`, `name`) values('$msg', '$title')");
dbquery("OPTIMIZE TABLE `notes_dir`");

$_SESSION['message']='成功创建类别';
header("Location: dir.php?".SID);
exit;
}
}

err();
aut();
echo "<div id='comments' class='menus'>";

echo "<div class='webmenu'>";
echo "<a href='index.php'>日记</a>";
echo "</div>"; 

        
echo "<div class='webmenu last'>";
echo "<a href='dir.php' class='activ'>类别</a>";
echo "</div>"; 
        
echo "<div class='webmenu last'>";
echo "<a href='search.php'>搜索</a>";
echo "</div>"; 

echo "</div>";

/*
==================================
Дневники
==================================
*/

if (isset($_GET['id']))
{
$id_dir=intval($_GET['id']);
$kount=dbresult(dbquery("SELECT COUNT(*) FROM `notes_dir` WHERE `id` = '$id_dir' "),0);
}
if (isset($_GET['id']) && $kount==1)
{
if (isset($_GET['sort']) && $_GET['sort'] =='t')$order='order by `time` desc';
elseif (isset($_GET['sort']) && $_GET['sort'] =='c') $order='order by `count` desc';
else $order='order by `time` desc';
if(isset($user))
{
echo'<div class="foot">';
echo "<a href=\"user.php\">我的日记。</a> | ";
echo "<a href=\"add.php?id_dir=$id_dir\">创建日记</a>";
echo '</div>';
}
if (isset($_GET['sort']) && $_GET['sort'] =='t'){
echo'<div class="foot">';
echo"<b>Новые</b> | <a href='?id=$id_dir&amp;sort=c'>流行的</a>";
echo '</div>';
}elseif (isset($_GET['sort']) && $_GET['sort'] =='c'){
echo'<div class="foot">';
echo"<a href='?id=$id_dir&amp;sort=t'>新</a> | <b>流行的</b>";
echo '</div>';
}else{
echo'<div class="foot">';
echo"<b>新</b> | <a href='?id=$id_dir&amp;sort=c'>流行的</a>";
echo '</div>';
}
$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `notes`  WHERE `id_dir` = '$id_dir'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
$q=dbquery("SELECT * FROM `notes` WHERE `id_dir` = '$id_dir' $order LIMIT $start, $set[p_str]");

if ($k_post==0)
{

echo "  <div class='mess'>";
echo "没有记录。";
echo "  </div>";

}
$num=0;
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


echo "<img src='/style/icons/dnev.png' alt='*'> ";

echo "<a href='list.php?id=$post[id]&amp;dir=$post[id_dir]'>" . htmlspecialchars($post['name']) . "</a> ";

echo " <span style='time'>(".vremja($post['time']).")</span>";

$k_n= dbresult(dbquery("SELECT COUNT(*) FROM `notes` WHERE `id` = $post[id] AND `time` > '".$ftime."'",$db), 0);
if ($k_n!=0)echo " <img src='/style/icons/new.gif' alt='*'>";


echo "   </div>";
}

if (isset($_GET['sort'])) $dop="sort=" . my_esc($_GET['sort']) . "&amp;";
else $dop='';
if ($k_page>1)str('?id='.$id_dir.'&'.$dop.'',$k_page,$page); // Вывод страниц

include_once '../../sys/inc/tfoot.php';
exit;
}


/*
==================================
Категории
==================================
*/
$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `notes_dir` "),0);
$q=dbquery("SELECT * FROM `notes_dir` ORDER BY `id` DESC");
echo "<table class='post'>";
if ($k_post==0)
{
echo "  <div class='mess'>";
echo "无类别";
echo "  </div>";
}
$num=0;
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

echo "<img src='/style/themes/$set[set_them]/loads/14/dir.png' alt='*'> ";
$k_pp=dbresult(dbquery("SELECT COUNT(*) FROM `notes`  WHERE `id_dir` = '$post[id]'"),0);
$k_nn=dbresult(dbquery("SELECT COUNT(*) FROM `notes`  WHERE `id_dir` = '$post[id]' AND `time` > '$ftime'"),0);
if ($k_nn>0)
$k_nn="<font color='red'>+$k_nn</font>";
else
$k_nn=NULL;

echo "<a href='dir.php?id=$post[id]'>" . output_text($post['name']) . "</a> ($k_pp) $k_nn";


if (isset($user) && ($user['level']>3))
echo "<a href='delete.php?dir=$post[id]'><img src='/style/icons/delete.gif' alt='*'></a><br />";
//$k_n= dbresult(dbquery("SELECT COUNT(*) FROM `notes` WHERE `id_dir` = $post[id] AND `time` > '".$ftime."'",$db), 0);

echo output_text($post['msg'])."<br />";

echo "   </div>";
}
echo "</table>";


if (isset($user) && user_access('notes_edit')){
if (isset($_GET['create'])){
echo "<form method=\"post\" action=\"dir.php\">";
echo "标题:<br /><input name=\"title\" size=\"16\" maxlength=\"32\" value=\"\" type=\"text\" /><br />";
echo "说明:<br /><textarea name=\"msg\" ></textarea><br />";

echo "<input value=\"创造\" type=\"submit\" />";
echo "</form>";
}else{
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='dir.php?create'>添加类别</a><br />";
echo "</div>";
}
}

echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='index.php'>所有日记</a><br />";
echo "</div>";

include_once '../../sys/inc/tfoot.php';
?>