<?php
include_once 'sys/inc/start.php';
include_once 'sys/inc/compress.php';
include_once 'sys/inc/sess.php';
include_once 'sys/inc/home.php';
include_once 'sys/inc/settings.php';
include_once 'sys/inc/db_connect.php';
include_once 'sys/inc/ipua.php';
include_once 'sys/inc/fnc.php';
$banpage=true;
include_once 'sys/inc/user.php';
only_reg();
$set['title']='禁止';
include_once 'sys/inc/thead.php';
title();
err();
aut();
if (!isset($user)){header("Location: /index.php?".SID);exit;}
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0')"), 0)==0)
{
header('Location: /index.php?'.SID);exit;
}
dbquery("UPDATE `ban` SET `view` = '1' WHERE `id_user` = '$user[id]'"); // увидел причину бана
$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$user[id]'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
echo "<table class='post'>";
$q=dbquery("SELECT * FROM `ban` WHERE `id_user` = '$user[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q))
{
$ank=get_user($post['id_ban']);
/*-----------зебра-----------*/
if ($num==0){echo "  <div class='nav1'>";$num=1;}elseif ($num==1){echo "  <div class='nav2'>";$num=0;}
/*---------------------------*/
echo "发出禁令".($ank['pol']==0?"а":"")." $ank[nick]: ";
	if ($post['navsegda']==1){		echo " бан <font color=red><b>永遠！</b></font><br />";}	else {		echo " до " . vremja($post['time']) . "<br />";	}
echo '<b>原因:</b> '.$pBan[$post['pochemu']].'<br />';echo '<b>章:</b> '.$rBan[$post['razdel']].'<br />';echo '<b>评论:</b> '.esc(trim(br(bbcode(smiles(links(stripcslashes(htmlspecialchars($post['prich']))))))))."<br />";if ($post['time']>$time)echo "<font color=red><b>Активен</b></font><br />";
echo "   </div>";
}
echo "</table>";
if ($k_page>1)str('?',$k_page,$page); // Вывод страниц
echo "为了避免这种情况，我们建议您学习 <a href=\"/rules.php\">规则</a>我们的网站<br />";
include_once 'sys/inc/tfoot.php';
?>