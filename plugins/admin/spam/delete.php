<?php
//投诉有关投诉有关
include_once '../../../sys/inc/start.php';
include_once '../../../sys/inc/compress.php';
include_once '../../../sys/inc/sess.php';
include_once '../../../sys/inc/home.php';
include_once '../../../sys/inc/settings.php';
include_once '../../../sys/inc/db_connect.php';
include_once '../../../sys/inc/ipua.php';
include_once '../../../sys/inc/fnc.php';
include_once '../../../sys/inc/user.php';
if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id` = '".intval($_GET['id'])."'"),0)==1)
{
$post=dbassoc(dbquery("SELECT * FROM `spamus` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));
$spamer = user::get_user($post['id_spam']);
$ank=user::get_user($post['id_user']);
if ($user['group_access'] == 2)
$adm = '聊天室版主';
elseif ($user['group_access'] == 3)
$adm = '论坛版主';
elseif ($user['group_access'] == 4)
$adm = '下载中心版主';
elseif ($user['group_access'] == 5)
$adm = '库版主';
elseif ($user['group_access'] == 6)
$adm = '摄影版主';
elseif ($user['group_access'] == 7)
$adm = '版主';
elseif ($user['group_access'] == 8)
$adm = '管理员';
elseif ($user['group_access'] == 9)
$adm = '最高管理者';
elseif ($user['group_access'] == 11)
$adm = '日志版主';
elseif ($user['group_access'] == 12)
$adm = '嘉宾版主';
elseif ($user['group_access'] == 15)
$adm = '站长';
if ($user['group_access']==2)
{
$types = "chat";
}
elseif ($user['group_access']==3)
{
$types ="forum";
}
elseif ($user['group_access']==4)
{
$types = "obmen_komm";
}
elseif ($user['group_access']==5)
{
$types = "lib_komm";
}
elseif ($user['group_access']==6)
{
$types = "foto_komm";
}
elseif ($user['group_access']==11)
{
$types = "notes_komm' ";
}
elseif ($user['group_access']==12)
{
$types = "guest";
}
elseif (($user['group_access']>6 && $user['group_access']<10) || $user['group_access']==15)
{
$types = true;
}
else
{
$types = false;
}
if ($types == $post['types'] || $types == true)
{
admin_log('不满事项','消除投诉',"消除投诉 $ank[nick] 关于 $spamer[nick]");
// отправка сообщения
if (isset($_GET['otkl']))
$msg = "用户投诉事项 [b]$spamer[nick][/b] 被拒绝 $adm [b]$user[nick][/b] [br][red]下次账号可能会被切断，请注意！[/red]";
else
$msg = "用户投诉事项 [b]$spamer[nick][/b] 考虑的 $adm [b]$user[nick][/b]. [br][b]$ank[nick][/b] 谢谢你的注意！";
dbquery("INSERT INTO `mail` (`id_user`, `id_kont`, `msg`, `time`) values('0', '$ank[id]', '".my_esc($msg)."', '$time')");
dbquery("DELETE FROM `spamus` WHERE `id` = '$post[id]'");
}
}
if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=NULL)
header("Location: ".$_SERVER['HTTP_REFERER']);
else
header("Location: index.php?".SID);
