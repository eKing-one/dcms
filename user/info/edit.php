<?php

include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';

only_reg();
$set['title']='编辑个人资料';
include_once '../../sys/inc/thead.php';
title();
aut();
if (isset($_GET['set']))
{
	$get = htmlspecialchars($_GET['set']);

	if (isset($_GET['act']) && $_GET['act']=='ank')
	$get2 = "act=ank&amp;";
	elseif (isset($_GET['act']) && $_GET['act']=='ank_web')
	$get2 = "act=ank_web&amp;";
	else
	$get2 = null;


if (isset($_POST['save']) && isset($_GET['set'])){

//----------ник------------//
if (isset($_GET['set']) && $_GET['set']=='nick' && $user['set_nick'] == 1){

if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `nick` = '".my_esc($_POST['nick'])."'"),0)==0)
{
$nick=my_esc($_POST['nick']);
if( !preg_match("#^([A-z0-9\-\_\ ])+$#ui", $_POST['nick']))$err[]='输入的有禁止的符号。';
if (preg_match("#[a-z]+#ui", $_POST['nick']))$err[]='只能使用英语字母。';
if (preg_match("#(^\ )|(\ $)#ui", $_POST['nick']))$err[]='禁止使用昵称开头和结尾的空白';
if (strlen2($nick)<3)$err[]='短昵';
if (strlen2($nick)>32)$err[]='昵称超过32字';
}
else $err[]='用户名 "'.stripcslashes(htmlspecialchars($_POST['nick'])).'" 已登记。';

if (isset($_POST['nick']) && !isset($err))
{
$user['nick']=$_POST['nick'];
dbquery("UPDATE `user` SET `nick` = '".my_esc($user['nick'])."' , `set_nick` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}

}


//----------имя------------//
if (isset($_GET['set']) && $_GET['set']=='name'){
if (isset($_POST['ank_name']) && preg_match('#^([A-zА-я \-]*)$#ui', $_POST['ank_name']))
{
$user['ank_name']=$_POST['ank_name'];
dbquery("UPDATE `user` SET `ank_name` = '".my_esc($user['ank_name'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='无效的命名';
}


//----------глаза------------//
if (isset($_GET['set']) && $_GET['set']=='glaza'){
if (isset($_POST['ank_cvet_glas']) && preg_match('#^([A-z \-]*)$#ui', $_POST['ank_cvet_glas']))
{
$user['ank_cvet_glas']=$_POST['ank_cvet_glas'];
dbquery("UPDATE `user` SET `ank_cvet_glas` = '".my_esc($user['ank_cvet_glas'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='颜色格式不正确';
}


//----------волосы------------//
if (isset($_GET['set']) && $_GET['set']=='volos'){
if (isset($_POST['ank_volos']) && preg_match('#^([A-zА-я \-]*)$#ui', $_POST['ank_volos']))
{
$user['ank_volos']=$_POST['ank_volos'];
dbquery("UPDATE `user` SET `ank_volos` = '".my_esc($user['ank_volos'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='颜色格式不正确';
}


//----------дата рождения------------//
if (isset($_GET['set']) && $_GET['set']=='date'){
if (isset($_POST['ank_d_r']) && (is_numeric($_POST['ank_d_r']) && $_POST['ank_d_r']>0 && $_POST['ank_d_r']<=31 || $_POST['ank_d_r']==NULL))
{
$user['ank_d_r']= (int) $_POST['ank_d_r'];
if ($user['ank_d_r']==null)$user['ank_d_r']='null';
dbquery("UPDATE `user` SET `ank_d_r` = $user[ank_d_r] WHERE `id` = '$user[id]' LIMIT 1");
if ($user['ank_d_r']=='null')$user['ank_d_r']=NULL;
}
else $err[]='无效的生日格式';

if (isset($_POST['ank_m_r']) && (is_numeric($_POST['ank_m_r']) && $_POST['ank_m_r']>0 && $_POST['ank_m_r']<=12 || $_POST['ank_m_r']==NULL))
{
$user['ank_m_r']= (int) $_POST['ank_m_r'];
if ($user['ank_m_r']==null)$user['ank_m_r']='null';
dbquery("UPDATE `user` SET `ank_m_r` = $user[ank_m_r] WHERE `id` = '$user[id]' LIMIT 1");
if ($user['ank_m_r']=='null')$user['ank_m_r']=NULL;
}
else $err[]='出生月份格式无效';

if (isset($_POST['ank_g_r']) && (is_numeric($_POST['ank_g_r']) && $_POST['ank_g_r']>0 && $_POST['ank_g_r']<=date('Y') || $_POST['ank_g_r']==NULL))
{
$user['ank_g_r']= (int) $_POST['ank_g_r'];
if ($user['ank_g_r']==null)$user['ank_g_r']='null';
dbquery("UPDATE `user` SET `ank_g_r` = $user[ank_g_r] WHERE `id` = '$user[id]' LIMIT 1");
if ($user['ank_g_r']=='null')$user['ank_g_r']=NULL;
}
else $err[]='出生年份格式无效';
}



//---------------город----------------//
if (isset($_GET['set']) && $_GET['set']=='gorod'){
if (isset($_POST['ank_city']) && preg_match('#^([A-zА-я \-]*)$#ui', $_POST['ank_city']))
{
$user['ank_city']=$_POST['ank_city'];
dbquery("UPDATE `user` SET `ank_city` = '".my_esc($user['ank_city'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='城市名称格式不正确';
}

//--------------icq----------------//
if (isset($_GET['set']) && $_GET['set']=='icq'){
if (isset($_POST['ank_icq']) && (is_numeric($_POST['ank_icq']) && strlen($_POST['ank_icq'])>=5 && strlen($_POST['ank_icq'])<=9 || $_POST['ank_icq']==NULL))
{
$user['ank_icq']=$_POST['ank_icq'];
if ($user['ank_icq']==null)$user['ank_icq']='null';
dbquery("UPDATE `user` SET `ank_icq` = $user[ank_icq] WHERE `id` = '$user[id]' LIMIT 1");
if ($user['ank_icq']=='null')$user['ank_icq']=NULL;
}
else $err[]='无效的ICQ格式';
}


//--------------вес----------------//
if (isset($_GET['set']) && $_GET['set']=='ves'){
if (isset($_POST['ank_ves']) && (intval($_POST['ank_ves']) && strlen($_POST['ank_ves'])>=1 && strlen($_POST['ank_ves'])<=4 || $_POST['ank_ves']==NULL))
{
$user['ank_ves']=$_POST['ank_ves'];
if ($user['ank_ves']==null)$user['ank_ves']='null';
dbquery("UPDATE `user` SET `ank_ves` = $user[ank_ves] WHERE `id` = '$user[id]' LIMIT 1");

if ($user['ank_ves']=='null')$user['ank_ves']=NULL;
}
else $err[]='体重格式不正确';
}


//--------------рост----------------//
if (isset($_GET['set']) && $_GET['set']=='rost'){
if (isset($_POST['ank_rost']) && (intval($_POST['ank_rost']) && strlen($_POST['ank_rost'])>=1 && strlen($_POST['ank_rost'])<=4 || $_POST['ank_rost']==NULL))
{
$user['ank_rost']=$_POST['ank_rost'];
if ($user['ank_rost']==null)$user['ank_rost']='null';
dbquery("UPDATE `user` SET `ank_rost` = $user[ank_rost] WHERE `id` = '$user[id]' LIMIT 1");

if ($user['ank_rost']=='null')$user['ank_rost']=NULL;
}
else $err[]='生长格式不正确';
}


//-------------------skype---------------//
if (isset($_GET['set']) && $_GET['set']=='skype'){
if (isset($_POST['ank_skype']) && preg_match('#^([A-z0-9 \-]*)$#ui', $_POST['ank_skype']))
{
$user['ank_skype']=$_POST['ank_skype'];
dbquery("UPDATE `user` SET `ank_skype` = '".my_esc($user['ank_skype'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='无效的Skype登录';
}

//----------------email------------------//
if (isset($_GET['set']) && $_GET['set']=='mail'){
if (isset($_POST['set_show_mail']) && $_POST['set_show_mail']==1)
{
$user['set_show_mail']=1;
dbquery("UPDATE `user` SET `set_show_mail` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['set_show_mail']=0;
dbquery("UPDATE `user` SET `set_show_mail` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}

if (isset($_POST['ank_mail']) && ($_POST['ank_mail']==null || preg_match('#^[A-z0-9-\._]+@[A-z0-9]{2,}\.[A-z]{2,4}$#ui',$_POST['ank_mail'])))
{
$user['ank_mail']=$_POST['ank_mail'];
dbquery("UPDATE `user` SET `ank_mail` = '$user[ank_mail]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='无效的电子邮件';
}


//----------------email------------------//
if (isset($_GET['set']) && $_GET['set']=='loves'){

if (isset($_POST['ank_lov_1']) && $_POST['ank_lov_1']==1)
{
$user['ank_lov_1']=1;
dbquery("UPDATE `user` SET `ank_lov_1` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_1']=0;
dbquery("UPDATE `user` SET `ank_lov_1` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_2']) && $_POST['ank_lov_2']==1)
{
$user['ank_lov_2']=1;
dbquery("UPDATE `user` SET `ank_lov_2` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_2']=0;
dbquery("UPDATE `user` SET `ank_lov_2` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_3']) && $_POST['ank_lov_1']==1)
{
$user['ank_lov_3']=1;
dbquery("UPDATE `user` SET `ank_lov_3` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_3']=0;
dbquery("UPDATE `user` SET `ank_lov_3` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_4']) && $_POST['ank_lov_4']==1)
{
$user['ank_lov_4']=1;
dbquery("UPDATE `user` SET `ank_lov_4` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_4']=0;
dbquery("UPDATE `user` SET `ank_lov_4` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_5']) && $_POST['ank_lov_5']==1)
{
$user['ank_lov_5']=1;
dbquery("UPDATE `user` SET `ank_lov_5` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_5']=0;
dbquery("UPDATE `user` SET `ank_lov_5` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_6']) && $_POST['ank_lov_6']==1)
{
$user['ank_lov_6']=1;
dbquery("UPDATE `user` SET `ank_lov_6` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_6']=0;
dbquery("UPDATE `user` SET `ank_lov_6` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_7']) && $_POST['ank_lov_7']==1)
{
$user['ank_lov_7']=1;
dbquery("UPDATE `user` SET `ank_lov_7` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_7']=0;
dbquery("UPDATE `user` SET `ank_lov_7` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_8']) && $_POST['ank_lov_8']==1)
{
$user['ank_lov_8']=1;
dbquery("UPDATE `user` SET `ank_lov_8` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_8']=0;
dbquery("UPDATE `user` SET `ank_lov_8` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_9']) && $_POST['ank_lov_9']==1)
{
$user['ank_lov_9']=1;
dbquery("UPDATE `user` SET `ank_lov_9` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_9']=0;
dbquery("UPDATE `user` SET `ank_lov_9` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_10']) && $_POST['ank_lov_10']==1)
{
$user['ank_lov_10']=1;
dbquery("UPDATE `user` SET `ank_lov_10` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_10']=0;
dbquery("UPDATE `user` SET `ank_lov_10` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_11']) && $_POST['ank_lov_11']==1)
{
$user['ank_lov_11']=1;
dbquery("UPDATE `user` SET `ank_lov_11` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_11']=0;
dbquery("UPDATE `user` SET `ank_lov_11` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_12']) && $_POST['ank_lov_12']==1)
{
$user['ank_lov_12']=1;
dbquery("UPDATE `user` SET `ank_lov_12` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_12']=0;
dbquery("UPDATE `user` SET `ank_lov_12` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_13']) && $_POST['ank_lov_13']==1)
{
$user['ank_lov_13']=1;
dbquery("UPDATE `user` SET `ank_lov_13` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_13']=0;
dbquery("UPDATE `user` SET `ank_lov_13` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####
if (isset($_POST['ank_lov_14']) && $_POST['ank_lov_14']==1)
{
$user['ank_lov_14']=1;
dbquery("UPDATE `user` SET `ank_lov_14` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['ank_lov_14']=0;
dbquery("UPDATE `user` SET `ank_lov_14` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
####


}


//-----------------------телефон------------------//
if (isset($_GET['set']) && $_GET['set']=='mobile'){
if (isset($_POST['ank_n_tel']) && (is_numeric($_POST['ank_n_tel']) && strlen($_POST['ank_n_tel'])>=5 && strlen($_POST['ank_n_tel'])<=11 || $_POST['ank_n_tel']==NULL))
{
$user['ank_n_tel']=$_POST['ank_n_tel'];
dbquery("UPDATE `user` SET `ank_n_tel` = '$user[ank_n_tel]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='Неверный формат номера телефона';
}


//-----------------телосложение-----------------//
if (isset($_GET['set']) && $_GET['set']=='telo'){
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==1)
{
$user['ank_telosl']=1;
dbquery("UPDATE `user` SET `ank_telosl` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==0)
{
$user['ank_telosl']=0;
dbquery("UPDATE `user` SET `ank_telosl` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==2)
{
$user['ank_telosl']=2;
dbquery("UPDATE `user` SET `ank_telosl` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==3)
{
$user['ank_telosl']=3;
dbquery("UPDATE `user` SET `ank_telosl` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==4)
{
$user['ank_telosl']=4;
dbquery("UPDATE `user` SET `ank_telosl` = '4' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==5)
{
$user['ank_telosl']=5;
dbquery("UPDATE `user` SET `ank_telosl` = '5' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==6)
{
$user['ank_telosl']=6;
dbquery("UPDATE `user` SET `ank_telosl` = '6' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_telosl']) && $_POST['ank_telosl']==7)
{
$user['ank_telosl']=7;
dbquery("UPDATE `user` SET `ank_telosl` = '7' WHERE `id` = '$user[id]' LIMIT 1");
}
}

//-----------------Ориентация-----------------//
if (isset($_GET['set']) && $_GET['set']=='orien'){
if (isset($_POST['ank_orien']) && $_POST['ank_orien']==1)
{
$user['ank_orien']=1;
dbquery("UPDATE `user` SET `ank_orien` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_orien']) && $_POST['ank_orien']==0)
{
$user['ank_orien']=0;
dbquery("UPDATE `user` SET `ank_orien` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_orien']) && $_POST['ank_orien']==2)
{
$user['ank_orien']=2;
dbquery("UPDATE `user` SET `ank_orien` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_orien']) && $_POST['ank_orien']==3)
{
$user['ank_orien']=3;
dbquery("UPDATE `user` SET `ank_orien` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
}

//-----------------есть ли дети-----------------//
if (isset($_GET['set']) && $_GET['set']=='baby'){
if (isset($_POST['ank_baby']) && $_POST['ank_baby']==1)
{
$user['ank_baby']=1;
dbquery("UPDATE `user` SET `ank_baby` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_baby']) && $_POST['ank_baby']==0)
{
$user['ank_baby']=0;
dbquery("UPDATE `user` SET `ank_baby` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_baby']) && $_POST['ank_baby']==2)
{
$user['ank_baby']=2;
dbquery("UPDATE `user` SET `ank_baby` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_baby']) && $_POST['ank_baby']==3)
{
$user['ank_baby']=3;
dbquery("UPDATE `user` SET `ank_baby` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_baby']) && $_POST['ank_baby']==4)
{
$user['ank_baby']=4;
dbquery("UPDATE `user` SET `ank_baby` = '4' WHERE `id` = '$user[id]' LIMIT 1");
}
}


//-----------------Курение-----------------//
if (isset($_GET['set']) && $_GET['set']=='smok'){
if (isset($_POST['ank_smok']) && $_POST['ank_smok']==1)
{
$user['ank_smok']=1;
dbquery("UPDATE `user` SET `ank_smok` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_smok']) && $_POST['ank_smok']==0)
{
$user['ank_smok']=0;
dbquery("UPDATE `user` SET `ank_smok` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_smok']) && $_POST['ank_smok']==2)
{
$user['ank_smok']=2;
dbquery("UPDATE `user` SET `ank_smok` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_smok']) && $_POST['ank_smok']==3)
{
$user['ank_smok']=3;
dbquery("UPDATE `user` SET `ank_smok` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_smok']) && $_POST['ank_smok']==4)
{
$user['ank_smok']=4;
dbquery("UPDATE `user` SET `ank_smok` = '4' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_smok']) && $_POST['ank_smok']==5)
{
$user['ank_smok']=5;
dbquery("UPDATE `user` SET `ank_smok` = '5' WHERE `id` = '$user[id]' LIMIT 1");
}
}

//-----------------материальное положение-----------------//
if (isset($_GET['set']) && $_GET['set']=='mat_pol'){
if (isset($_POST['ank_mat_pol']) && $_POST['ank_mat_pol']==1)
{
$user['ank_mat_pol']=1;
dbquery("UPDATE `user` SET `ank_mat_pol` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_mat_pol']) && $_POST['ank_mat_pol']==0)
{
$user['ank_mat_pol']=0;
dbquery("UPDATE `user` SET `ank_mat_pol` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_mat_pol']) && $_POST['ank_mat_pol']==2)
{
$user['ank_mat_pol']=2;
dbquery("UPDATE `user` SET `ank_mat_pol` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_mat_pol']) && $_POST['ank_mat_pol']==3)
{
$user['ank_mat_pol']=3;
dbquery("UPDATE `user` SET `ank_mat_pol` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_mat_pol']) && $_POST['ank_mat_pol']==4)
{
$user['ank_mat_pol']=4;
dbquery("UPDATE `user` SET `ank_mat_pol` = '4' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_mat_pol']) && $_POST['ank_mat_pol']==5)
{
$user['ank_mat_pol']=5;
dbquery("UPDATE `user` SET `ank_mat_pol` = '5' WHERE `id` = '$user[id]' LIMIT 1");
}
}

//-----------------проживание-----------------//
if (isset($_GET['set']) && $_GET['set']=='proj'){
if (isset($_POST['ank_proj']) && $_POST['ank_proj']==1)
{
$user['ank_proj']=1;
dbquery("UPDATE `user` SET `ank_proj` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_proj']) && $_POST['ank_proj']==0)
{
$user['ank_proj']=0;
dbquery("UPDATE `user` SET `ank_proj` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_proj']) && $_POST['ank_proj']==2)
{
$user['ank_proj']=2;
dbquery("UPDATE `user` SET `ank_proj` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_proj']) && $_POST['ank_proj']==3)
{
$user['ank_proj']=3;
dbquery("UPDATE `user` SET `ank_proj` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_proj']) && $_POST['ank_proj']==4)
{
$user['ank_proj']=4;
dbquery("UPDATE `user` SET `ank_proj` = '4' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_proj']) && $_POST['ank_proj']==5)
{
$user['ank_proj']=5;
dbquery("UPDATE `user` SET `ank_proj` = '5' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_proj']) && $_POST['ank_proj']==6)
{
$user['ank_proj']=6;
dbquery("UPDATE `user` SET `ank_proj` = '6' WHERE `id` = '$user[id]' LIMIT 1");
}
}

//-----------------пол-----------------//
if (isset($_GET['set']) && $_GET['set']=='pol'){
if (isset($_POST['pol']) && $_POST['pol']==1)
{
$user['pol']=1;
dbquery("UPDATE `user` SET `pol` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['pol']) && $_POST['pol']==0)
{
$user['pol']=0;
dbquery("UPDATE `user` SET `pol` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
}


//-----------------автомобиль-----------------//
if (isset($_GET['set']) && $_GET['set']=='avto'){
if (isset($_POST['ank_avto_n']) && $_POST['ank_avto_n']==3)
{
$user['ank_avto_n']=3;
dbquery("UPDATE `user` SET `ank_avto_n` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_avto_n']) && $_POST['ank_avto_n']==2)
{
$user['ank_avto_n']=2;
dbquery("UPDATE `user` SET `ank_avto_n` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_avto_n']) && $_POST['ank_avto_n']==1)
{
$user['ank_avto_n']=1;
dbquery("UPDATE `user` SET `ank_avto_n` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_avto_n']) && $_POST['ank_avto_n']==0)
{
$user['ank_avto_n']=0;
dbquery("UPDATE `user` SET `ank_avto_n` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}

if (isset($_POST['ank_avto']) && strlen2($_POST['ank_avto'])<=215)
{

if (preg_match('#[^A-zА-я0-9 _\-\=\+\(\)\*\!\?\.,]#ui',$_POST['ank_avto']))$err[]='在”汽车名称”字段禁止使用字符';
else {
$user['ank_avto']=$_POST['ank_avto'];
dbquery("UPDATE `user` SET `ank_avto` = '".my_esc($user['ank_avto'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
}
else $err[]='你需要少写你的车 :)';

}

//-----------------напиток-----------------//
if (isset($_GET['set']) && $_GET['set']=='alko'){
if (isset($_POST['ank_alko_n']) && $_POST['ank_alko_n']==3)
{
$user['ank_alko_n']=3;
dbquery("UPDATE `user` SET `ank_alko_n` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_alko_n']) && $_POST['ank_alko_n']==2)
{
$user['ank_alko_n']=2;
dbquery("UPDATE `user` SET `ank_alko_n` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_alko_n']) && $_POST['ank_alko_n']==1)
{
$user['ank_alko_n']=1;
dbquery("UPDATE `user` SET `ank_alko_n` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_alko_n']) && $_POST['ank_alko_n']==0)
{
$user['ank_alko_n']=0;
dbquery("UPDATE `user` SET `ank_alko_n` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}

if (isset($_POST['ank_alko']) && strlen2($_POST['ank_alko'])<=215)
{

if (preg_match('#[^A-zА-я0-9 _\-\=\+\(\)\*\!\?\.,]#ui',$_POST['ank_alko']))$err[]='禁止字符在“Nanpitok”字段中使用';
else {
$user['ank_alko']=$_POST['ank_alko'];
dbquery("UPDATE `user` SET `ank_alko` = '".my_esc($user['ank_alko'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
}
else $err[]='О любимом напитке нужно писать меньше :)';

}



//----------------о себе-------------//
if (isset($_GET['set']) && $_GET['set']=='osebe'){
if (isset($_POST['ank_o_sebe']) && strlen2($_POST['ank_o_sebe'])<=512)
{

if (preg_match('#[^A-zА-я0-9 _\-\=\+\(\)\*\!\?\.,]#ui',$_POST['ank_o_sebe']))$err[]='禁止字符用于”关于我”字段';
else {
$user['ank_o_sebe']=$_POST['ank_o_sebe'];
dbquery("UPDATE `user` SET `ank_o_sebe` = '".my_esc($user['ank_o_sebe'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
}
else $err[]='О себе нужно писать меньше :)';
}

//----------------о партнере-------------//
if (isset($_GET['set']) && $_GET['set']=='opar'){
if (isset($_POST['ank_o_par']) && strlen2($_POST['ank_o_par'])<=215)
{

if (preg_match('#[^A-zА-я0-9 _\-\=\+\(\)\*\!\?\.,]#ui',$_POST['ank_o_par']))$err[]='禁止字符用于”关于合作伙伴”字段';
else {
$user['ank_o_par']=$_POST['ank_o_par'];
dbquery("UPDATE `user` SET `ank_o_par` = '".my_esc($user['ank_o_par'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
}
else $err[]='你需要少写你的伴侣 :)';
}


//----------------чем занимаюсь-------------//
if (isset($_GET['set']) && $_GET['set']=='zan'){
if (isset($_POST['ank_zan']) && strlen2($_POST['ank_zan'])<=215)
{
if (preg_match('#[^A-zА-я0-9 _\-\=\+\(\)\*\!\?\.,]#ui',$_POST['ank_zan']))$err[]='禁止字符用于"我做什么"字段';
else {
$user['ank_zan']=$_POST['ank_zan'];
dbquery("UPDATE `user` SET `ank_zan` = '".my_esc($user['ank_zan'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
}
else $err[]='文字太大';
}


if (!isset($err))
{
$_SESSION['message'] = '更改已成功接受';

	dbquery("UPDATE `user` SET `rating_tmp` = '".($user['rating_tmp']+1)."' WHERE `id` = '$user[id]' LIMIT 1");
		
		if (isset($_GET['act']) && $_GET['act']=='ank')
			header("Location: /user/info/anketa.php?".SID);
			
		elseif (isset($_GET['act']) && $_GET['act']=='ank_web')
			header("Location: /info.php".SID);
			
		else
			header("Location: /user/info/edit.php?".SID);
			
			exit;
}



}
err();

	echo "<form method='post' action='?".$get2."set=$get'>";
	if (isset($_GET['set']) && $_GET['set']=='nick' && $user['set_nick'] == 1)
	echo "<div class='mess'>注意！您只能更改一次昵称！</div> Nick Name:<br /><input type='text' name='nick' value='".htmlspecialchars($user['nick'],false)."' maxlength='32' /><br />";
	
	
	if (isset($_GET['set']) && $_GET['set']=='name')
	echo "真实名字:<br /><input type='text' name='ank_name' value='".htmlspecialchars($user['ank_name'],false)."' maxlength='32' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='glaza')
	echo "眼睛颜色:<br /><input type='text' name='ank_cvet_glas' value='".htmlspecialchars($user['ank_cvet_glas'],false)."' maxlength='32' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='volos')
	echo "头发:<br /><input type='text' name='ank_volos' value='".htmlspecialchars($user['ank_volos'],false)."' maxlength='32' /><br />";
	
	
	if (isset($_GET['set']) && $_GET['set']=='date'){
	echo '出生日期:<br />';
	//年
	echo '<select name="ank_g_r">';
    if (!empty($user['ank_g_r']))  echo '<option  value=""></option>';
		echo '<option selected="'.$user['ank_g_r'].'" value="'.$user['ank_g_r'].'" >'.$user['ank_g_r'].'</option>';



		for( $i = date("Y")-16; $i >= 1940; $i--) {

		echo '<option  value="' . $i . '">' . $i . '</option>';
		}
	echo '</select><br/>';
//月
	echo '<select name="ank_m_r">
	<option selected="'.$user['ank_m_r'].'" value="'.$user['ank_m_r'].'" >'.$user['ank_m_r'].'<option>	
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	<option value="8">8</option>
	<option value="9">9</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	</select>';
//日
	echo '<select name="ank_d_r">
	<option selected="'.$user['ank_d_r'].'" value="'.$user['ank_d_r'].'" >'.$user['ank_d_r'].'<option>
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	<option value="8">8</option>
	<option value="9">9</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	<option value="13">13</option>
	<option value="14">14</option>
	<option value="15">15</option>
	<option value="16">16</option>
	<option value="17">17</option>
	<option value="18">18</option>
	<option value="19">19</option>
	<option value="20">20</option>
	<option value="21">21</option>
	<option value="22">22</option>
	<option value="23">23</option>
	<option value="24">24</option>
	<option value="25">25</option>
	<option value="26">26</option>
	<option value="27">27</option>
	<option value="28">28</option>
	<option value="29">29</option>
	<option value="30">30</option>
	<option value="31">31</option>
	</select>';
		
	
	}
		
	if (isset($_GET['set']) && $_GET['set']=='pol'){
	echo "性别:<br /> <input name='pol' type='radio' ".($user['pol']==1?' checked="checked"':null)." value='1' />男<br />
	<input name='pol' type='radio' ".($user['pol']==0?' checked="checked"':null)." value='0' />女<br />";
	}
		
	if (isset($_GET['set']) && $_GET['set']=='telo'){
	echo "身体状况:<br /> 
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==1?' checked="checked"':null)." value='1' />没有人回答<br />
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==2?' checked="checked"':null)." value='2' />瘦骨嶙峋<br />
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==3?' checked="checked"':null)." value='3' />平常的<br />
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==4?' checked="checked"':null)." value='4' />运动项目<br />
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==5?' checked="checked"':null)." value='5' />肌肉发达<br />
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==6?' checked="checked"':null)." value='6' />密密麻麻<br />
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==7?' checked="checked"':null)." value='7' />全<br />
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==0?' checked="checked"':null)." value='0' />未指定<br />";
	}
		
	if (isset($_GET['set']) && $_GET['set']=='avto'){
	echo "汽车的可用性:<br /> 
	<input name='ank_avto_n' type='radio' ".($user['ank_avto_n']==0?' checked="checked"':null)." value='0' />未指定<br />
	<input name='ank_avto_n' type='radio' ".($user['ank_avto_n']==1?' checked="checked"':null)." value='1' />有<br />
	<input name='ank_avto_n' type='radio' ".($user['ank_avto_n']==2?' checked="checked"':null)." value='2' />没有<br />
	<input name='ank_avto_n' type='radio' ".($user['ank_avto_n']==3?' checked="checked"':null)." value='3' />我要买了。<br />";
	echo "标题\汽车品牌:<br /><input type='text' name='ank_avto' value='".htmlspecialchars($user['ank_avto'],false)."' maxlength='215' /><br />";
	}
	
	if (isset($_GET['set']) && $_GET['set']=='alko'){
	echo "酒精:<br /> 
	<input name='ank_alko_n' type='radio' ".($user['ank_alko_n']==0?' checked="checked"':null)." value='0' />未指定<br />
	<input name='ank_alko_n' type='radio' ".($user['ank_alko_n']==1?' checked="checked"':null)." value='1' />是的，我在喝酒。<br />
	<input name='ank_alko_n' type='radio' ".($user['ank_alko_n']==2?' checked="checked"':null)." value='2' />很少，在节假日<br />
	<input name='ank_alko_n' type='radio' ".($user['ank_alko_n']==3?' checked="checked"':null)." value='3' />不，我绝对不能接受<br />";
	echo "饮料:<br /><input type='text' name='ank_alko' value='".htmlspecialchars($user['ank_alko'],false)."' maxlength='215' /><br />";
	}
	if (isset($_GET['set']) && $_GET['set']=='orien'){
	echo "方向感:<br /> 
	<input name='ank_orien' type='radio' ".($user['ank_orien']==0?' checked="checked"':null)." value='0' />未指定<br />
	<input name='ank_orien' type='radio' ".($user['ank_orien']==1?' checked="checked"':null)." value='1' />杂种<br />
	<input name='ank_orien' type='radio' ".($user['ank_orien']==2?' checked="checked"':null)." value='2' />比<br />
	<input name='ank_orien' type='radio' ".($user['ank_orien']==3?' checked="checked"':null)." value='3' />男女同性恋<br />";
	}
	if (isset($_GET['set']) && $_GET['set']=='mat_pol'){
	echo "财务状况:<br /> 
	<input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==0?' checked="checked"':null)." value='0' />未指定<br />
	<input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==1?' checked="checked"':null)." value='1' />非固定收入<br />
	<input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==2?' checked="checked"':null)." value='2' />固定的少量收入<br />
	<input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==3?' checked="checked"':null)." value='3' />稳定平均收入<br />
	<input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==4?' checked="checked"':null)." value='4' />收入不错/有保障<br />
	<input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==5?' checked="checked"':null)." value='5' />不赚钱<br />";
	}
	if (isset($_GET['set']) && $_GET['set']=='smok'){
	echo "吸烟:<br /> 
	<input name='ank_smok' type='radio' ".($user['ank_smok']==0?' checked="checked"':null)." value='0' />未指定<br />
	<input name='ank_smok' type='radio' ".($user['ank_smok']==1?' checked="checked"':null)." value='1' />不吸烟。<br />
	<input name='ank_smok' type='radio' ".($user['ank_smok']==2?' checked="checked"':null)." value='2' />吸烟<br />
	<input name='ank_smok' type='radio' ".($user['ank_smok']==3?' checked="checked"':null)." value='3' />很少<br />
	<input name='ank_smok' type='radio' ".($user['ank_smok']==4?' checked="checked"':null)." value='4' />戒烟<br />
	<input name='ank_smok' type='radio' ".($user['ank_smok']==5?' checked="checked"':null)." value='5' />成功退出<br />";
	}
	
	if (isset($_GET['set']) && $_GET['set']=='proj'){
	echo "住宿设施:<br /> 
	<input name='ank_proj' type='radio' ".($user['ank_proj']==0?' checked="checked"':null)." value='0' />未指定<br />
	<input name='ank_proj' type='radio' ".($user['ank_proj']==1?' checked="checked"':null)." value='1' />单独的公寓（出租或自有）<br />
	<input name='ank_proj' type='radio' ".($user['ank_proj']==2?' checked="checked"':null)." value='2' />宿舍房间、公用设施<br />
	<input name='ank_proj' type='radio' ".($user['ank_proj']==3?' checked="checked"':null)." value='3' />和父母住在一起<br />
	<input name='ank_proj' type='radio' ".($user['ank_proj']==4?' checked="checked"':null)." value='4' />和一个朋友/女朋友住在一起<br />
	<input name='ank_proj' type='radio' ".($user['ank_proj']==5?' checked="checked"':null)." value='5' />与伴侣或配偶生活在一起<br />
	<input name='ank_proj' type='radio' ".($user['ank_proj']==6?' checked="checked"':null)." value='6' />没有永久住所<br />";
	}
	
	
	if (isset($_GET['set']) && $_GET['set']=='baby'){
	echo "有没有孩子:<br /> 
	<input name='ank_baby' type='radio' ".($user['ank_baby']==0?' checked="checked"':null)." value='0' />未指定<br />
	<input name='ank_baby' type='radio' ".($user['ank_baby']==1?' checked="checked"':null)." value='1' />取消<br />
	<input name='ank_baby' type='radio' ".($user['ank_baby']==2?' checked="checked"':null)." value='2' />不，但我希望我能。<br />
	<input name='ank_baby' type='radio' ".($user['ank_baby']==3?' checked="checked"':null)." value='3' />吃，住在一起<br />
	<input name='ank_baby' type='radio' ".($user['ank_baby']==4?' checked="checked"':null)." value='4' />是的，我们分开生活<br />";
	}
	
	if (isset($_GET['set']) && $_GET['set']=='zan')
	echo "我的工作:<br /><input type='text' name='ank_zan' value='$user[ank_zan]' maxlength='215' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='gorod')
	echo "城市:<br /><input type='text' name='ank_city' value='$user[ank_city]' maxlength='32' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='rost')
	echo "身高:<br /><input type='text' name='ank_rost' value='$user[ank_rost]' maxlength='3' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='ves')
	echo "体重:<br /><input type='text' name='ank_ves' value='$user[ank_ves]' maxlength='3' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='icq')
	echo "ICQ:<br /><input type='text' name='ank_icq' value='$user[ank_icq]' maxlength='9' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='skype')
	echo "Skype登录<br /><input type='text' name='ank_skype' value='$user[ank_skype]' maxlength='16' /><br />";
	
	
	if (isset($_GET['set']) && $_GET['set']=='mail'){
	echo "E-mail:<br />
		<input type='text' name='ank_mail' value='$user[ank_mail]' maxlength='32' /><br />
		<label><input type='checkbox' name='set_show_mail'".($user['set_show_mail']==1?' checked="checked"':null)." value='1' /> 在资料中显示电子邮件</label><br />";
	}
	
	
	if (isset($_GET['set']) && $_GET['set']=='loves'){
	echo "约会目标:<br />
		<label><input type='checkbox' name='ank_lov_1'".($user['ank_lov_1']==1?' checked="checked"':null)." value='1' /> 友谊与沟通</label><br />
		<label><input type='checkbox' name='ank_lov_2'".($user['ank_lov_2']==1?' checked="checked"':null)." value='1' /> 通信</label><br />
		<label><input type='checkbox' name='ank_lov_3'".($user['ank_lov_3']==1?' checked="checked"':null)." value='1' /> 爱情，关系</label><br />
		<label><input type='checkbox' name='ank_lov_4'".($user['ank_lov_4']==1?' checked="checked"':null)." value='1' /> 经常性在一起</label><br />
		<label><input type='checkbox' name='ank_lov_5'".($user['ank_lov_5']==1?' checked="checked"':null)." value='1' /> 性一两次</label><br />
		<label><input type='checkbox' name='ank_lov_6'".($user['ank_lov_6']==1?' checked="checked"':null)." value='1' /> 团体性</label><br />
		<label><input type='checkbox' name='ank_lov_7'".($user['ank_lov_7']==1?' checked="checked"':null)." value='1' /> 虚拟性</label><br />
		<label><input type='checkbox' name='ank_lov_8'".($user['ank_lov_8']==1?' checked="checked"':null)." value='1' /> 我为钱提供性</label><br />
		<label><input type='checkbox' name='ank_lov_9'".($user['ank_lov_9']==1?' checked="checked"':null)." value='1' /> 寻找性别为了钱</label><br />
		<label><input type='checkbox' name='ank_lov_10'".($user['ank_lov_10']==1?' checked="checked"':null)." value='1' /> 婚姻、家庭创造</label><br />
		<label><input type='checkbox' name='ank_lov_11'".($user['ank_lov_11']==1?' checked="checked"':null)." value='1' /> 出生，抚养孩子</label><br />
		<label><input type='checkbox' name='ank_lov_12'".($user['ank_lov_12']==1?' checked="checked"':null)." value='1' /> 为vi结婚是的</label><br />
		<label><input type='checkbox' name='ank_lov_13'".($user['ank_lov_13']==1?' checked="checked"':null)." value='1' /> 联合出租房屋</label><br />
		<label><input type='checkbox' name='ank_lov_14'".($user['ank_lov_14']==1?' checked="checked"':null)." value='1' /> 体育活动</label><br />
		
		<br />";
	}
	
	if (isset($_GET['set']) && $_GET['set']=='mobile')
	echo "电话号码:<br /><input type='text' name='ank_n_tel' value='$user[ank_n_tel]' maxlength='11' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='osebe')
	echo "关于我:<br /><input type='text' name='ank_o_sebe' value='$user[ank_o_sebe]' maxlength='512' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='opar')
	echo "关于合作伙伴:<br /><input type='text' name='ank_o_par' value='$user[ank_o_par]' maxlength='215' /><br />";
	
	
	echo "<input type='submit' name='save' value='保存' /></form>";
}else{

echo "<div class='nav2'>";
echo "基本信息";
echo "</div>";

echo "<div class='nav1'>";
if ($user['set_nick'] == 1)
{
echo "<a href='?set=nick'> <img src='/style/icons/str.gif' alt='*'>  <b>Nick name</b></a>";
if ($user['nick']!=NULL)
echo " &#62; $user[nick]<br />";
else
echo "<br />";
}
echo "<a href='?set=name'> <img src='/style/icons/str.gif' alt='*'>  姓名</a>";
if ($user['ank_name']!=NULL)
echo " &#62; $user[ank_name]<br />";
else
echo "<br />";

echo "<a href='?set=pol'> <img src='/style/icons/str.gif' alt='*'>  性别</a> &#62; ".(($user['pol']==1)?'男':'女')."<br />";
echo "<a href='?set=gorod'> <img src='/style/icons/str.gif' alt='*'>  城市</a>";
if ($user['ank_city']!=NULL)
echo " &#62; $user[ank_city]<br />";
else
echo "<br />";




echo "<a href='?set=date'> <img src='/style/icons/str.gif' alt='*'>  出生日期</a> ";
if($user['ank_d_r']!=NULL && $user['ank_m_r']!=NULL && $user['ank_g_r']!=NULL)
echo " &#62; $user[ank_d_r].$user[ank_m_r].$user[ank_g_r] . <br />";
elseif($user['ank_d_r']!=NULL && $user['ank_m_r']!=NULL)
echo " &#62; $user[ank_d_r].$user[ank_m_r]<br />";
echo "</div>";

echo "<div class='nav2'>";
echo "类型";
echo "</div>";

echo "<div class='nav1'>";
echo "<a href='?set=rost'> <img src='/style/icons/str.gif' alt='*'>  身高</a>";
if ($user['ank_rost']!=NULL)
echo " &#62; $user[ank_rost]<br />";
else
echo "<br />";


echo "<a href='?set=ves'> <img src='/style/icons/str.gif' alt='*'>  体重</a>";
if ($user['ank_ves']!=NULL)
echo " &#62; $user[ank_ves]<br />";
else
echo "<br />";


echo "<a href='?set=glaza'> <img src='/style/icons/str.gif' alt='*'>  眼睛</a>";
if ($user['ank_cvet_glas']!=NULL)
echo " &#62; $user[ank_cvet_glas]<br />";
else
echo "<br />";


echo "<a href='?set=volos'> <img src='/style/icons/str.gif' alt='*'>  头发</a>";
if ($user['ank_volos']!=NULL)
echo " &#62; $user[ank_volos]<br />";
else
echo "<br />";

echo "<a href='?set=telo'> <img src='/style/icons/str.gif' alt='*'>  身体状况</a> ";
if ($user['ank_telosl']==1)
echo " &#62; 没有人回答<br />";
if ($user['ank_telosl']==2)
echo " &#62; 瘦骨嶙峋<br />";
if ($user['ank_telosl']==3)
echo " &#62; 平常的<br />";
if ($user['ank_telosl']==4)
echo " &#62; 运动项目<br />";
if ($user['ank_telosl']==5)
echo " &#62; 肌肉发达<br />";
if ($user['ank_telosl']==6)
echo " &#62; 密密麻麻<br />";
if ($user['ank_telosl']==7)
echo " &#62; 全<br />";
if ($user['ank_telosl']==0)
echo "<br />";
echo "</div>";

echo "<div class='nav2'>";
echo "约会用";
echo "</div>";

echo "<div class='nav1'>";
echo "<a href='?set=orien'> <img src='/style/icons/str.gif' alt='*'>  方向感</a> ";
if ($user['ank_orien']==0)
echo "<br />";
if ($user['ank_orien']==1)
echo " &#62;  杂种<br />";
if ($user['ank_orien']==2)
echo " &#62;  毕<br />";
if ($user['ank_orien']==3)
echo " &#62;  同性恋<br />";


echo "<a href='?set=loves'> <img src='/style/icons/str.gif' alt='*'>  约会目标</a><br />";
if ($user['ank_lov_1']==1)echo " &#62; 友谊与沟通<br />";
if ($user['ank_lov_2']==1)echo " &#62; 通信<br />";
if ($user['ank_lov_3']==1)echo " &#62; 爱情，关系<br />";
if ($user['ank_lov_4']==1)echo " &#62; 经常性在一起<br />";
if ($user['ank_lov_5']==1)echo " &#62; 性一两次<br />";
if ($user['ank_lov_6']==1)echo " &#62; 团体性<br />";
if ($user['ank_lov_7']==1)echo " &#62; 虚拟性<br />";
if ($user['ank_lov_8']==1)echo "&#62; 我为钱提供性<br />";
if ($user['ank_lov_9']==1)echo " &#62; 寻找性别为了钱<br />";
if ($user['ank_lov_10']==1)echo " &#62; 婚姻、家庭创造<br />";
if ($user['ank_lov_11']==1)echo " &#62; 出生，抚养孩子<br />";
if ($user['ank_lov_12']==1)echo " &#62; 为vi结婚是的<br />";
if ($user['ank_lov_13']==1)echo " &#62; 联合出租房屋<br />";
if ($user['ank_lov_14']==1)echo " &#62; 体育活动<br />";


echo "<a href='?set=opar'> <img src='/style/icons/str.gif' alt='*'>  关于合作伙伴</a>";
if ($user['ank_o_par']!=NULL)
echo " &#62; ".htmlspecialchars($user['ank_o_par'])."<br />";
else
echo "<br />";
echo "<a href='?set=osebe'> <img src='/style/icons/str.gif' alt='*'>  关于我</a>";
if ($user['ank_o_sebe']!=NULL)
echo " &#62; ".htmlspecialchars($user['ank_o_sebe'])."<br />";
else
echo "<br />";
echo "</div>";

echo "<div class='nav2'>";
echo "一般情况";
echo "</div>";

echo "<div class='nav1'>";
echo "<a href='?set=zan'> <img src='/style/icons/str.gif' alt='*'>  我的工作</a> ";
if ($user['ank_zan']!=NULL)
echo " &#62; ".htmlspecialchars($user['ank_zan']);echo '<br />';



echo "<a href='?set=mat_pol'> <img src='/style/icons/str.gif' alt='*'>  财务状况</a>";
if ($user['ank_mat_pol']==1)
echo " &#62; 非永久性收入<br />";
if ($user['ank_mat_pol']==2)
echo " &#62; 永久小额收入<br />";
if ($user['ank_mat_pol']==3)
echo " &#62; 稳定的平均收入<br />";
if ($user['ank_mat_pol']==4)
echo " &#62; 我挣得很好/我有条件<br />";
if ($user['ank_mat_pol']==5)
echo " &#62; 我不赚钱<br />";
if ($user['ank_mat_pol']==0)
echo "<br />";

echo "<a href='?set=avto'> <img src='/style/icons/str.gif' alt='*'>  汽车的可用性</a>";
if ($user['ank_avto_n']==1)
echo " &#62; 有<br />";
if ($user['ank_avto_n']==2)
echo " &#62; 取消<br />";
if ($user['ank_avto_n']==3)
echo " &#62; 我要买了<br />";
if ($user['ank_avto_n']==0)
echo "<br />";
if ($user['ank_avto'] && $user['ank_avto_n']!=2 && $user['ank_avto_n']!=0)
echo "<img src='/style/icons/str.gif' alt='*'>  ".htmlspecialchars($user['ank_avto'])."<br />";




echo "<a href='?set=proj'> <img src='/style/icons/str.gif' alt='*'>  住宿设施</a> ";
if ($user['ank_proj']==1)
echo " &#62; 独立公寓（出租或拥有）<br />";
if ($user['ank_proj']==2)
echo " &#62; 宿舍、公共公寓<br />";
if ($user['ank_proj']==3)
echo " &#62; 我和父母住在一起<br />";
if ($user['ank_proj']==4)
echo " &#62; 我和朋友住在一起/和朋友住在一起<br />";
if ($user['ank_proj']==5)
echo " &#62; 我和伴侣或配偶住在一起<br />";
if ($user['ank_proj']==6)
echo " &#62; 没有永久住房<br />";
if ($user['ank_proj']==0)
echo "<br />";


echo "<a href='?set=baby'> <img src='/style/icons/str.gif' alt='*'>  有没有孩子</a> ";
if ($user['ank_baby']==1)
echo " &#62; 取消<br />";
if ($user['ank_baby']==2)
echo " &#62; 不，但我想<br />";
if ($user['ank_baby']==3)
echo " &#62; 是的，我们住在一起<br />";
if ($user['ank_baby']==4)
echo " &#62; 是的，我们分开住<br />";
if ($user['ank_baby']==0)
echo "<br />";
echo "</div>";

echo "<div class='nav2'>";
echo "习惯";
echo "</div>";

echo "<div class='nav1'>";
echo "<a href='?set=smok'> <img src='/style/icons/str.gif' alt='*'>  吸烟</a>";
if ($user['ank_smok']==1)
echo " &#62; 我不抽烟<br />";
if ($user['ank_smok']==2)
echo " &#62; 我抽烟<br />";
if ($user['ank_smok']==3)
echo " &#62; 很少<br />";
if ($user['ank_smok']==4)
echo " &#62; 我不干了<br />";
if ($user['ank_smok']==5)
echo " &#62; 成功退出<br />";
if ($user['ank_smok']==0)
echo "<br />";

echo "<a href='?set=alko'> <img src='/style/icons/str.gif' alt='*'>  酒精</a> ";
if ($user['ank_alko_n']==1)
echo "&#62; 是的，我喝酒<br />";
if ($user['ank_alko_n']==2)
echo "&#62; 很少，在假期<br />";
if ($user['ank_alko_n']==3)
echo "&#62; 不，我断然不接受<br />";
if ($user['ank_alko_n']==0)
echo "<br />";

if ($user['ank_alko'] && $user['ank_alko_n']!=3 && $user['ank_alko_n']!=0)
echo "<img src='/style/icons/str.gif' alt='*'>  ".htmlspecialchars($user['ank_alko'])."<br />";

echo "<div class='nav2'>";
echo "联络人";
echo "</div>";

echo "<div class='nav1'>";
echo "<a href='?set=mobile'> <img src='/style/icons/str.gif' alt='*'>  移动电话</a> ";
if ($user['ank_n_tel'])echo "&#62; $user[ank_n_tel]<br />";
else
echo "<br />";
echo "<a href='?set=icq'> <img src='/style/icons/str.gif' alt='*'>  ICQ</a> ";
if ($user['ank_icq'])echo "&#62; $user[ank_icq]<br />";
else
echo "<br />";
echo "<a href='?set=mail'> <img src='/style/icons/str.gif' alt='*'>  E-Mail</a> ";
if ($user['ank_mail'])echo "&#62; $user[ank_mail]<br />";
else
echo "<br />";
echo "<a href='?set=skype'> <img src='/style/icons/str.gif' alt='*'>  Skype</a> "; 
if ($user['ank_skype'])echo "&#62; $user[ank_skype]<br />";
else
echo "<br />";
echo "</div>";
}


echo "<div class='foot'><img src='/style/icons/str.gif' alt='*'> <a href='anketa.php'>查看资料</a><br />";

if(isset($_SESSION['refer']) && $_SESSION['refer']!=NULL && otkuda($_SESSION['refer']))
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='$_SESSION[refer]'>".otkuda($_SESSION['refer'])."</a><br />";
echo '</div>';
	
include_once '../../sys/inc/tfoot.php';
?>