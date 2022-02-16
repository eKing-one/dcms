<?php
/*
=======================================
Модуль "Поделиться дневником" от PluginS
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
include_once '../../sys/inc/user.php';
/* Ban user */
if (isset($user) && dbresult(dbquery("SELECT COUNT(`id`) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
header('Location: /ban.php?'.SID);exit;
}

$set['title']='分享';
include_once '../../sys/inc/thead.php';
title();
aut(); only_reg();
$not=dbquery("SELECT * FROM `notes` WHERE `id`='".intval($_GET['id'])."' LIMIT 1");
if(dbrows($not)==0){
echo "<div class='err'>没有这样的记录</div>";
}elseif(dbresult(dbquery("SELECT COUNT(`share_id_user`)FROM `notes` WHERE `id_user`='".$user['id']."' AND `share_id`='".intval($_GET['id'])."' AND `share_type`='notes' LIMIT 1"),0)==1){
echo "<div class='error'>你已经共享了此条目</div>";
}else{
$notes=dbassoc($not);
if($notes['id_user']!=$user['id']){
$avtor=get_user($notes['id_user']);
if(isset($_POST['ok'])){
dbquery("INSERT INTO `notes`(`id_user`,`name`,`msg`,`share`,`share_text`,`share_id`,`share_id_user`,`share_name`,`time`) values('".$user['id']."','".text($notes['name'])."','".my_esc($_POST['share_text'])."','1','".my_esc($notes['msg'])."','".$notes['id']."','".$notes['id_user']."','".my_esc($notes['name'])."','".$time."')");
$id=mysql_insert_id();
msg('Ок всё крч');
header('Location: list.php?id='.$id);
exit;
}

?>
<div class='nav2'><div class="friends_access_list attach_block mt_0 grey"> <? echo group($avtor['id'])." ";?> <a href="/info.php?id=<?=$notes['id_user']?>"><span style="color:#79358c"><b><? echo " ".$avtor['nick']." ";?> </b></span></a> : <a href="list.php?id=<?=$notes['id']?>">
<span style="color:#06F;"><? echo $notes['name']; ?></span></a></div>
<?
echo "<form method='post' action='share.php?id=".intval($_GET['id'])."'>";
echo $tPanel;
echo "<textarea name='share_text'></textarea>";
echo "<input type='submit' name='ok' value='分享'>";
echo "</form></div>";
}else{
echo "<div class='err'>你不能转发你的笔记</div>";
}
}
include_once '../../sys/inc/tfoot.php';
?>