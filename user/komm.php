<?php

include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';

if (isset($_GET['id']) && is_numeric($_GET['id']) && dbresult(dbquery("SELECT COUNT(`id`) FROM `stena` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"),0)!=0)
{
$id=abs(intval($_GET['id']));
$post=dbassoc(dbquery("SELECT * FROM `stena` WHERE `id`='$id' LIMIT 1"));
$set['title']=' 对该帖子的评论';
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['msg']) && isset($user))
{
$msg = esc(stripslashes(htmlspecialchars($_POST['msg'])));
$mat = antimat($msg);
     if ($mat)$err[] = '在消息的文本中发现了一个将死者: ' . $mat;

     if (strlen2($msg) > 1024)
     {
     $err[] = '信息太长了';
     }
     elseif (strlen2($msg) < 2)
     {
     $err[] = '短消息';
     }
     elseif (dbresult(dbquery("SELECT COUNT(`id`) FROM `stena_komm` WHERE `id_user` = '" . $user['id'] . "' AND `msg` = '" . my_esc($msg) . "' AND `id_stena`='$id' LIMIT 1"), 0) != 0)
     {
     $err[] = '您的消息重复前一个';
     }
     elseif(!isset($err))
     {
     dbquery("INSERT INTO `stena_komm` (`id_user`, `time`, `msg`,`id_stena`) values('" . $user['id'] . "', '" . $time . "', '" . my_esc($msg) . "','".$id."')");
     
     /*
     =====
     Отправляем автору комма
     =====
     */
if (isset($user)){
		$notifiacation=dbassoc(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '".$post['id_user']."' LIMIT 1"));
			
			if ($notifiacation['komm'] == 1 && $post['id_user'] != $user['id'])
			dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$post[id_user]', '$post[id]', 'stena_komm2', '$time')");
		
		}
    $_SESSION['message']='消息已成功添加';
header('Location: /user/komm.php?id='.$id);
     }
}
elseif (isset($_GET['del']) && dbresult(dbquery("SELECT COUNT(*) FROM `stena_komm` WHERE `id` = '".intval($_GET['del'])."' AND `id_stena` = '$post[id]'"),0))
{
if (isset($user) && ($user['level']>=3 || $user['id']=$stena['id_user']))
{
dbquery("DELETE FROM `stena_komm` WHERE `id` = '".intval($_GET['del'])."' LIMIT 1");
$_SESSION['message']='评论成功删除';
header('Location: /user/komm.php?id='.$id);

}
}
err();
aut(); 
////////////////////////////////////////////////////////



$post=dbassoc(dbquery("SELECT * FROM `stena` WHERE `id` = '".abs(intval($_GET['id']))."' LIMIT 1"));
echo "  <div class='nav2'>";
echo "<table><td style='width:15%;vertical-align:top;'>"; echo avatar($post['id_user']);
echo "</td><td style='vertical-align:top;'>";
echo  group($post['id_user'])." ";
echo user::nick($post['id_user'],1,1,1);
echo " <span style='color:#666'>".vremja($post['time'])."</span><br/>";
stena($post['id_user'],$post['id']);
echo output_text($post['msg'])."<br />";
echo "</td></table></div>";



$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `stena_komm` WHERE `id_stena` = '$id'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
$q=dbquery("SELECT * FROM `stena_komm` WHERE `id_stena` = '".intval($_GET['id'])."' ORDER BY `id` DESC LIMIT $start, $set[p_str]");


echo "<div class='main'><b>评论：</b> (".$k_post.")</div>";


if ($k_post==0)
{

echo'<div class="mess">';
echo'<font color=grey>还没有人对录音发表评论。</font>';
echo'</div>';

}



while ($komm = dbassoc($q))
{
echo'<div class="nav1">';
echo group($komm['id_user']).' ' ;
echo user::nick($komm['id_user'],1,1,1);
echo ' ('.vremja($komm['time']).')';
echo "<br />";
echo output_text($komm['msg'])."<br />";
if (isset($user) && ($user['level']>=3 || $user['id'] == $post['id_user']))
echo'<a href="?id='.$post['id'].'&del='.$komm['id'].'">删除</a><br />';
echo'</div>';

}


if ($k_page>1)str("komm.php?id=$post[id]&",$k_page,$page); // Вывод страниц
if (!isset($_POST['msg']) && isset($user) ) 
{
echo'<div class="main_menu"><form method="post" name="message" action="?id='.$post['id'].'">';
echo $tPanel;
echo'<textarea name="msg"></textarea><br />';
echo'<input value="发送" type="submit" />';
echo'</form></div>';

}}else{
header('Location: /index.php');
}
include_once '../sys/inc/tfoot.php';
?>