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
$set['title']='Редактирование анкеты';
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
if( !preg_match("#^([A-zА-я0-9\-\_\ ])+$#ui", $_POST['nick']))$err[]='В нике присутствуют запрещенные символы';
if (preg_match("#[a-z]+#ui", $_POST['nick']) && preg_match("#[а-я]+#ui", $_POST['nick']))$err[]='Разрешается использовать символы только русского или только английского алфавита';
if (preg_match("#(^\ )|(\ $)#ui", $_POST['nick']))$err[]='Запрещено использовать пробел в начале и конце ника';
if (strlen2($nick)<3)$err[]='Короткий ник';
if (strlen2($nick)>32)$err[]='Длина ника превышает 32 символа';
}
else $err[]='Ник "'.stripcslashes(htmlspecialchars($_POST['nick'])).'" уже зарегистрирован';

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
else $err[]='Неверный формат имени';
}


//----------глаза------------//
if (isset($_GET['set']) && $_GET['set']=='glaza'){
if (isset($_POST['ank_cvet_glas']) && preg_match('#^([A-zА-я \-]*)$#ui', $_POST['ank_cvet_glas']))
{
$user['ank_cvet_glas']=$_POST['ank_cvet_glas'];
dbquery("UPDATE `user` SET `ank_cvet_glas` = '".my_esc($user['ank_cvet_glas'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='Неверный формат цвет глаз';
}


//----------волосы------------//
if (isset($_GET['set']) && $_GET['set']=='volos'){
if (isset($_POST['ank_volos']) && preg_match('#^([A-zА-я \-]*)$#ui', $_POST['ank_volos']))
{
$user['ank_volos']=$_POST['ank_volos'];
dbquery("UPDATE `user` SET `ank_volos` = '".my_esc($user['ank_volos'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='Неверный формат цвет глаз';
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
else $err[]='Неверный формат дня рождения';

if (isset($_POST['ank_m_r']) && (is_numeric($_POST['ank_m_r']) && $_POST['ank_m_r']>0 && $_POST['ank_m_r']<=12 || $_POST['ank_m_r']==NULL))
{
$user['ank_m_r']= (int) $_POST['ank_m_r'];
if ($user['ank_m_r']==null)$user['ank_m_r']='null';
dbquery("UPDATE `user` SET `ank_m_r` = $user[ank_m_r] WHERE `id` = '$user[id]' LIMIT 1");
if ($user['ank_m_r']=='null')$user['ank_m_r']=NULL;
}
else $err[]='Неверный формат месяца рождения';

if (isset($_POST['ank_g_r']) && (is_numeric($_POST['ank_g_r']) && $_POST['ank_g_r']>0 && $_POST['ank_g_r']<=date('Y') || $_POST['ank_g_r']==NULL))
{
$user['ank_g_r']= (int) $_POST['ank_g_r'];
if ($user['ank_g_r']==null)$user['ank_g_r']='null';
dbquery("UPDATE `user` SET `ank_g_r` = $user[ank_g_r] WHERE `id` = '$user[id]' LIMIT 1");
if ($user['ank_g_r']=='null')$user['ank_g_r']=NULL;
}
else $err[]='Неверный формат года рождения';
}



//---------------город----------------//
if (isset($_GET['set']) && $_GET['set']=='gorod'){
if (isset($_POST['ank_city']) && preg_match('#^([A-zА-я \-]*)$#ui', $_POST['ank_city']))
{
$user['ank_city']=$_POST['ank_city'];
dbquery("UPDATE `user` SET `ank_city` = '".my_esc($user['ank_city'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='Неверный формат названия города';
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
else $err[]='Неверный формат ICQ';
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
else $err[]='Неверный формат веса';
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
else $err[]='Неверный формат роста';
}


//-------------------skype---------------//
if (isset($_GET['set']) && $_GET['set']=='skype'){
if (isset($_POST['ank_skype']) && preg_match('#^([A-z0-9 \-]*)$#ui', $_POST['ank_skype']))
{
$user['ank_skype']=$_POST['ank_skype'];
dbquery("UPDATE `user` SET `ank_skype` = '".my_esc($user['ank_skype'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='Неверный логин Skype';
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
else $err[]='Неверный E-mail';
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

if (preg_match('#[^A-zА-я0-9 _\-\=\+\(\)\*\!\?\.,]#ui',$_POST['ank_avto']))$err[]='在”名称”字段\汽车品牌”禁止使用字符';
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
else $err[]='О партнере нужно писать меньше :)';
}
//-----------------наркотики-----------------//
if (isset($_GET['set']) && $_GET['set']=='nark'){
if (isset($_POST['ank_nark']) && $_POST['ank_nark']==4)
{
$user['ank_nark']=4;
dbquery("UPDATE `user` SET `ank_nark` = '4' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_nark']) && $_POST['ank_nark']==3)
{
$user['ank_nark']=3;
dbquery("UPDATE `user` SET `ank_nark` = '3' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_nark']) && $_POST['ank_nark']==2)
{
$user['ank_nark']=2;
dbquery("UPDATE `user` SET `ank_nark` = '2' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_nark']) && $_POST['ank_nark']==1)
{
$user['ank_nark']=1;
dbquery("UPDATE `user` SET `ank_nark` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_nark']) && $_POST['ank_nark']==0)
{
$user['ank_nark']=0;
dbquery("UPDATE `user` SET `ank_nark` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}

}


//----------------чем занимаюсь-------------//
if (isset($_GET['set']) && $_GET['set']=='zan'){
if (isset($_POST['ank_zan']) && strlen2($_POST['ank_zan'])<=215)
{
if (preg_match('#[^A-zА-я0-9 _\-\=\+\(\)\*\!\?\.,]#ui',$_POST['ank_zan']))$err[]='В поле "Чем занимаюсь" используются запрещенные символы';
else {
$user['ank_zan']=$_POST['ank_zan'];
dbquery("UPDATE `user` SET `ank_zan` = '".my_esc($user['ank_zan'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
}
else $err[]='Слишком большой текст';
}


if (!isset($err))
{
$_SESSION['message'] = 'Изменения успешно приняты';

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
	echo "Имя в реале:<br /><input type='text' name='ank_name' value='".htmlspecialchars($user['ank_name'],false)."' maxlength='32' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='glaza')
	echo "Цвет глаз:<br /><input type='text' name='ank_cvet_glas' value='".htmlspecialchars($user['ank_cvet_glas'],false)."' maxlength='32' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='volos')
	echo "Волосы:<br /><input type='text' name='ank_volos' value='".htmlspecialchars($user['ank_volos'],false)."' maxlength='32' /><br />";
	
	
	if (isset($_GET['set']) && $_GET['set']=='date'){
	echo '出生日期:<br />
	<select name="ank_d_r">
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
	
	echo '<select name="ank_g_r">
	<option selected="'.$user['ank_g_r'].'" value="'.$user['ank_g_r'].'" >'.$user['ank_g_r'].'<option><option value="1996">1996</option><option value="1995">1995</option><option value="1994">1994</option><option value="1993">1993</option><option value="1992">1992</option><option value="1991">1991</option><option value="1990">1990</option><option value="1989">1989</option><option value="1988">1988</option><option value="1987">1987</option><option value="1986">1986</option><option value="1985">1985</option><option value="1984">1984</option><option value="1983">1983</option><option value="1982">1982</option><option value="1981">1981</option><option value="1980">1980</option><option value="1979">1979</option><option value="1978">1978</option><option value="1977">1977</option><option value="1976">1976</option><option value="1975">1975</option><option value="1974">1974</option><option value="1973">1973</option><option value="1972">1972</option><option value="1971">1971</option><option value="1970">1970</option><option value="1969">1969</option><option value="1968">1968</option><option value="1967">1967</option><option value="1966">1966</option><option value="1965">1965</option><option value="1964">1964</option><option value="1963">1963</option><option value="1962">1962</option><option value="1961">1961</option><option value="1960">1960</option><option value="1959">1959</option><option value="1958">1958</option><option value="1957">1957</option><option value="1956">1956</option><option value="1955">1955</option><option value="1954">1954</option><option value="1953">1953</option><option value="1952">1952</option><option value="1951">1951</option><option value="1950">1950</option><option value="1949">1949</option><option value="1948">1948</option><option value="1947">1947</option><option value="1946">1946</option><option value="1945">1945</option><option value="1944">1944</option><option value="1943">1943</option><option value="1942">1942</option><option value="1941">1941</option><option value="1940">1940</option><option value="1939">1939</option><option value="1938">1938</option><option value="1937">1937</option><option value="1936">1936</option><option value="1935">1935</option><option value="1934">1934</option><option value="1933">1933</option><option value="1932">1932</option><option value="1931">1931</option><option value="1930">1930</option><option value="1929">1929</option><option value="1928">1928</option><option value="1927">1927</option><option value="1926">1926</option><option value="1925">1925</option><option value="1924">1924</option><option value="1923">1923</option><option value="1922">1922</option><option value="1921">1921</option><option value="1920">1920</option>
	</select><br/>';
	}
		
	if (isset($_GET['set']) && $_GET['set']=='pol'){
	echo "Пол:<br /> <input name='pol' type='radio' ".($user['pol']==1?' checked="checked"':null)." value='1' />Муж.<br />
	<input name='pol' type='radio' ".($user['pol']==0?' checked="checked"':null)." value='0' />Жен.<br />";
	}
		
	if (isset($_GET['set']) && $_GET['set']=='telo'){
	echo "Телосложение:<br /> 
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==1?' checked="checked"':null)." value='1' />Нет ответа<br />
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==2?' checked="checked"':null)." value='2' />Худощавое<br />
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==3?' checked="checked"':null)." value='3' />Обычное<br />
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==4?' checked="checked"':null)." value='4' />Спортивное<br />
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==5?' checked="checked"':null)." value='5' />Мускулистое<br />
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==6?' checked="checked"':null)." value='6' />Плотное<br />
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==7?' checked="checked"':null)." value='7' />Полное<br />
	<input name='ank_telosl' type='radio' ".($user['ank_telosl']==0?' checked="checked"':null)." value='0' />Не указано<br />";
	}
		
	if (isset($_GET['set']) && $_GET['set']=='avto'){
	echo "Наличие автомобиля:<br /> 
	<input name='ank_avto_n' type='radio' ".($user['ank_avto_n']==0?' checked="checked"':null)." value='0' />Не указано<br />
	<input name='ank_avto_n' type='radio' ".($user['ank_avto_n']==1?' checked="checked"':null)." value='1' />Есть<br />
	<input name='ank_avto_n' type='radio' ".($user['ank_avto_n']==2?' checked="checked"':null)." value='2' />取消<br />
	<input name='ank_avto_n' type='radio' ".($user['ank_avto_n']==3?' checked="checked"':null)." value='3' />Хочу купить<br />";
	echo "Название\Марка авто:<br /><input type='text' name='ank_avto' value='".htmlspecialchars($user['ank_avto'],false)."' maxlength='215' /><br />";
	}
	if (isset($_GET['set']) && $_GET['set']=='nark'){
	echo "Наркотики:<br /> 
	<input name='ank_nark' type='radio' ".($user['ank_nark']==0?' checked="checked"':null)." value='0' />Не указано<br />
	<input name='ank_nark' type='radio' ".($user['ank_nark']==1?' checked="checked"':null)." value='1' />Да, курю травку<br />
	<input name='ank_nark' type='radio' ".($user['ank_nark']==2?' checked="checked"':null)." value='2' />Да, люблю любой вид наркотических средств<br />
	<input name='ank_nark' type='radio' ".($user['ank_nark']==3?' checked="checked"':null)." value='3' />Бросаю, прохожу реабилитацию<br />
	<input name='ank_nark' type='radio' ".($user['ank_nark']==4?' checked="checked"':null)." value='4' />Нет, категорически не приемлю<br />";
	}
	if (isset($_GET['set']) && $_GET['set']=='alko'){
	echo "Алкоголь:<br /> 
	<input name='ank_alko_n' type='radio' ".($user['ank_alko_n']==0?' checked="checked"':null)." value='0' />Не указано<br />
	<input name='ank_alko_n' type='radio' ".($user['ank_alko_n']==1?' checked="checked"':null)." value='1' />Да, выпиваю<br />
	<input name='ank_alko_n' type='radio' ".($user['ank_alko_n']==2?' checked="checked"':null)." value='2' />Редко, по праздникам<br />
	<input name='ank_alko_n' type='radio' ".($user['ank_alko_n']==3?' checked="checked"':null)." value='3' />Нет, категорически не приемлю<br />";
	echo "Напиток:<br /><input type='text' name='ank_alko' value='".htmlspecialchars($user['ank_alko'],false)."' maxlength='215' /><br />";
	}
	if (isset($_GET['set']) && $_GET['set']=='orien'){
	echo "Ориентация:<br /> 
	<input name='ank_orien' type='radio' ".($user['ank_orien']==0?' checked="checked"':null)." value='0' />Не указано<br />
	<input name='ank_orien' type='radio' ".($user['ank_orien']==1?' checked="checked"':null)." value='1' />Гетеро<br />
	<input name='ank_orien' type='radio' ".($user['ank_orien']==2?' checked="checked"':null)." value='2' />Би<br />
	<input name='ank_orien' type='radio' ".($user['ank_orien']==3?' checked="checked"':null)." value='3' />Гей/Лесби<br />";
	}
	if (isset($_GET['set']) && $_GET['set']=='mat_pol'){
	echo "Материальное положение:<br /> 
	<input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==0?' checked="checked"':null)." value='0' />Не указано<br />
	<input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==1?' checked="checked"':null)." value='1' />Непостоянные заработки<br />
	<input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==2?' checked="checked"':null)." value='2' />Постоянный небольшой доход<br />
	<input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==3?' checked="checked"':null)." value='3' />Стабильный средний доход<br />
	<input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==4?' checked="checked"':null)." value='4' />Хорошо зарабатываю / обеспечен<br />
	<input name='ank_mat_pol' type='radio' ".($user['ank_mat_pol']==5?' checked="checked"':null)." value='5' />Не зарабатываю<br />";
	}
	if (isset($_GET['set']) && $_GET['set']=='smok'){
	echo "Курение:<br /> 
	<input name='ank_smok' type='radio' ".($user['ank_smok']==0?' checked="checked"':null)." value='0' />Не указано<br />
	<input name='ank_smok' type='radio' ".($user['ank_smok']==1?' checked="checked"':null)." value='1' />Не курю<br />
	<input name='ank_smok' type='radio' ".($user['ank_smok']==2?' checked="checked"':null)." value='2' />Курю<br />
	<input name='ank_smok' type='radio' ".($user['ank_smok']==3?' checked="checked"':null)." value='3' />Редко<br />
	<input name='ank_smok' type='radio' ".($user['ank_smok']==4?' checked="checked"':null)." value='4' />Бросаю<br />
	<input name='ank_smok' type='radio' ".($user['ank_smok']==5?' checked="checked"':null)." value='5' />Успешно бросил<br />";
	}
	
	if (isset($_GET['set']) && $_GET['set']=='proj'){
	echo "Проживание:<br /> 
	<input name='ank_proj' type='radio' ".($user['ank_proj']==0?' checked="checked"':null)." value='0' />Не указано<br />
	<input name='ank_proj' type='radio' ".($user['ank_proj']==1?' checked="checked"':null)." value='1' />Отдельная квартира (снимаю или своя)<br />
	<input name='ank_proj' type='radio' ".($user['ank_proj']==2?' checked="checked"':null)." value='2' />Комната в общежитии, коммуналка<br />
	<input name='ank_proj' type='radio' ".($user['ank_proj']==3?' checked="checked"':null)." value='3' />Живу с родителями<br />
	<input name='ank_proj' type='radio' ".($user['ank_proj']==4?' checked="checked"':null)." value='4' />Живу с приятелем / с подругой<br />
	<input name='ank_proj' type='radio' ".($user['ank_proj']==5?' checked="checked"':null)." value='5' />Живу с партнером или супругом (-ой)<br />
	<input name='ank_proj' type='radio' ".($user['ank_proj']==6?' checked="checked"':null)." value='6' />Нет постоянного жилья<br />";
	}
	
	
	if (isset($_GET['set']) && $_GET['set']=='baby'){
	echo "Есть ли дети:<br /> 
	<input name='ank_baby' type='radio' ".($user['ank_baby']==0?' checked="checked"':null)." value='0' />Не указано<br />
	<input name='ank_baby' type='radio' ".($user['ank_baby']==1?' checked="checked"':null)." value='1' />取消<br />
	<input name='ank_baby' type='radio' ".($user['ank_baby']==2?' checked="checked"':null)." value='2' />Нет, но хотелось бы<br />
	<input name='ank_baby' type='radio' ".($user['ank_baby']==3?' checked="checked"':null)." value='3' />Есть, живем вместе<br />
	<input name='ank_baby' type='radio' ".($user['ank_baby']==4?' checked="checked"':null)." value='4' />Есть, живем порознь<br />";
	}
	
	if (isset($_GET['set']) && $_GET['set']=='zan')
	echo "Чем занимаюсь:<br /><input type='text' name='ank_zan' value='$user[ank_zan]' maxlength='215' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='gorod')
	echo "Город:<br /><input type='text' name='ank_city' value='$user[ank_city]' maxlength='32' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='rost')
	echo "Рост:<br /><input type='text' name='ank_rost' value='$user[ank_rost]' maxlength='3' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='ves')
	echo "Вес:<br /><input type='text' name='ank_ves' value='$user[ank_ves]' maxlength='3' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='icq')
	echo "ICQ:<br /><input type='text' name='ank_icq' value='$user[ank_icq]' maxlength='9' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='skype')
	echo "Skype логин<br /><input type='text' name='ank_skype' value='$user[ank_skype]' maxlength='16' /><br />";
	
	
	if (isset($_GET['set']) && $_GET['set']=='mail'){
	echo "E-mail:<br />
		<input type='text' name='ank_mail' value='$user[ank_mail]' maxlength='32' /><br />
		<label><input type='checkbox' name='set_show_mail'".($user['set_show_mail']==1?' checked="checked"':null)." value='1' /> Показывать E-mail в анкете</label><br />";
	}
	
	
	if (isset($_GET['set']) && $_GET['set']=='loves'){
	echo "Цели знакомства:<br />
		<label><input type='checkbox' name='ank_lov_1'".($user['ank_lov_1']==1?' checked="checked"':null)." value='1' /> Дружба и общение</label><br />
		<label><input type='checkbox' name='ank_lov_2'".($user['ank_lov_2']==1?' checked="checked"':null)." value='1' /> Переписка</label><br />
		<label><input type='checkbox' name='ank_lov_3'".($user['ank_lov_3']==1?' checked="checked"':null)." value='1' /> Любовь, отношения</label><br />
		<label><input type='checkbox' name='ank_lov_4'".($user['ank_lov_4']==1?' checked="checked"':null)." value='1' /> Регулярный секс вдвоем</label><br />
		<label><input type='checkbox' name='ank_lov_5'".($user['ank_lov_5']==1?' checked="checked"':null)." value='1' /> Секс на один-два раза</label><br />
		<label><input type='checkbox' name='ank_lov_6'".($user['ank_lov_6']==1?' checked="checked"':null)." value='1' /> Групповой секс</label><br />
		<label><input type='checkbox' name='ank_lov_7'".($user['ank_lov_7']==1?' checked="checked"':null)." value='1' /> Виртуальный секс</label><br />
		<label><input type='checkbox' name='ank_lov_8'".($user['ank_lov_8']==1?' checked="checked"':null)." value='1' /> Предлагаю интим за деньги</label><br />
		<label><input type='checkbox' name='ank_lov_9'".($user['ank_lov_9']==1?' checked="checked"':null)." value='1' /> Ищу интим за деньги</label><br />
		<label><input type='checkbox' name='ank_lov_10'".($user['ank_lov_10']==1?' checked="checked"':null)." value='1' /> Брак, создание семьи</label><br />
		<label><input type='checkbox' name='ank_lov_11'".($user['ank_lov_11']==1?' checked="checked"':null)." value='1' /> Рождение, воспитание ребенка</label><br />
		<label><input type='checkbox' name='ank_lov_12'".($user['ank_lov_12']==1?' checked="checked"':null)." value='1' /> Брак для ви是的</label><br />
		<label><input type='checkbox' name='ank_lov_13'".($user['ank_lov_13']==1?' checked="checked"':null)." value='1' /> Совместная аренда жилья</label><br />
		<label><input type='checkbox' name='ank_lov_14'".($user['ank_lov_14']==1?' checked="checked"':null)." value='1' /> Занятия спортом</label><br />
		
		<br />";
	}
	
	if (isset($_GET['set']) && $_GET['set']=='mobile')
	echo "Номер телефона:<br /><input type='text' name='ank_n_tel' value='$user[ank_n_tel]' maxlength='11' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='osebe')
	echo "О себе:<br /><input type='text' name='ank_o_sebe' value='$user[ank_o_sebe]' maxlength='512' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='opar')
	echo "О партнере:<br /><input type='text' name='ank_o_par' value='$user[ank_o_par]' maxlength='215' /><br />";
	
	
	echo "<input type='submit' name='save' value='Сохранить' /></form>";
}else{

echo "<div class='nav2'>";
echo "Основное";
echo "</div>";

echo "<div class='nav1'>";
if ($user['set_nick'] == 1)
{
echo "<a href='?set=nick'> <img src='/style/icons/str.gif' alt='*'>  <b>Nick Name</b></a>";
if ($user['nick']!=NULL)
echo " &#62; $user[nick]<br />";
else
echo "<br />";
}
echo "<a href='?set=name'> <img src='/style/icons/str.gif' alt='*'>  Имя</a>";
if ($user['ank_name']!=NULL)
echo " &#62; $user[ank_name]<br />";
else
echo "<br />";

echo "<a href='?set=pol'> <img src='/style/icons/str.gif' alt='*'>  Пол</a> &#62; ".(($user['pol']==1)?'Мужской':'Женский')."<br />";
echo "<a href='?set=gorod'> <img src='/style/icons/str.gif' alt='*'>  Город</a>";
if ($user['ank_city']!=NULL)
echo " &#62; $user[ank_city]<br />";
else
echo "<br />";




echo "<a href='?set=date'> <img src='/style/icons/str.gif' alt='*'>  Дата рождения</a> ";
if($user['ank_d_r']!=NULL && $user['ank_m_r']!=NULL && $user['ank_g_r']!=NULL)
echo " &#62; $user[ank_d_r].$user[ank_m_r].$user[ank_g_r] г. <br />";
elseif($user['ank_d_r']!=NULL && $user['ank_m_r']!=NULL)
echo " &#62; $user[ank_d_r].$user[ank_m_r]<br />";
echo "</div>";

echo "<div class='nav2'>";
echo "Типаж";
echo "</div>";

echo "<div class='nav1'>";
echo "<a href='?set=rost'> <img src='/style/icons/str.gif' alt='*'>  Рост</a>";
if ($user['ank_rost']!=NULL)
echo " &#62; $user[ank_rost]<br />";
else
echo "<br />";


echo "<a href='?set=ves'> <img src='/style/icons/str.gif' alt='*'>  Вес</a>";
if ($user['ank_ves']!=NULL)
echo " &#62; $user[ank_ves]<br />";
else
echo "<br />";


echo "<a href='?set=glaza'> <img src='/style/icons/str.gif' alt='*'>  Глаза</a>";
if ($user['ank_cvet_glas']!=NULL)
echo " &#62; $user[ank_cvet_glas]<br />";
else
echo "<br />";


echo "<a href='?set=volos'> <img src='/style/icons/str.gif' alt='*'>  Волосы</a>";
if ($user['ank_volos']!=NULL)
echo " &#62; $user[ank_volos]<br />";
else
echo "<br />";

echo "<a href='?set=telo'> <img src='/style/icons/str.gif' alt='*'>  Телосложение</a> ";
if ($user['ank_telosl']==1)
echo " &#62; Нет ответа<br />";
if ($user['ank_telosl']==2)
echo " &#62; Худощавое<br />";
if ($user['ank_telosl']==3)
echo " &#62; Обычное<br />";
if ($user['ank_telosl']==4)
echo " &#62; Спортивное<br />";
if ($user['ank_telosl']==5)
echo " &#62; Мускулистое<br />";
if ($user['ank_telosl']==6)
echo " &#62; Плотное<br />";
if ($user['ank_telosl']==7)
echo " &#62; Полное<br />";
if ($user['ank_telosl']==0)
echo "<br />";
echo "</div>";

echo "<div class='nav2'>";
echo "Для знакомства";
echo "</div>";

echo "<div class='nav1'>";
echo "<a href='?set=orien'> <img src='/style/icons/str.gif' alt='*'>  Ориентация</a> ";
if ($user['ank_orien']==0)
echo "<br />";
if ($user['ank_orien']==1)
echo " &#62;  Гетеро<br />";
if ($user['ank_orien']==2)
echo " &#62;  Би<br />";
if ($user['ank_orien']==3)
echo " &#62;  Гей/Лесби<br />";


echo "<a href='?set=loves'> <img src='/style/icons/str.gif' alt='*'>  Цели знакомства</a><br />";
if ($user['ank_lov_1']==1)echo " &#62; Дружба и общение<br />";
if ($user['ank_lov_2']==1)echo " &#62; Переписка<br />";
if ($user['ank_lov_3']==1)echo " &#62; Любовь, отношения<br />";
if ($user['ank_lov_4']==1)echo " &#62; Регулярный секс вдвоем<br />";
if ($user['ank_lov_5']==1)echo " &#62; Секс на один-два раза<br />";
if ($user['ank_lov_6']==1)echo " &#62; Групповой секс<br />";
if ($user['ank_lov_7']==1)echo " &#62; Виртуальный секс<br />";
if ($user['ank_lov_8']==1)echo "&#62; Предлагаю интим за деньги<br />";
if ($user['ank_lov_9']==1)echo " &#62; Ищу интим за деньги<br />";
if ($user['ank_lov_10']==1)echo " &#62; Брак, создание семьи<br />";
if ($user['ank_lov_11']==1)echo " &#62; Рождение, воспитание ребенка<br />";
if ($user['ank_lov_12']==1)echo " &#62; Брак для ви是的<br />";
if ($user['ank_lov_13']==1)echo " &#62; Совместная аренда жилья<br />";
if ($user['ank_lov_14']==1)echo " &#62; Занятия спортом<br />";


echo "<a href='?set=opar'> <img src='/style/icons/str.gif' alt='*'>  О партнере</a>";
if ($user['ank_o_par']!=NULL)
echo " &#62; ".htmlspecialchars($user['ank_o_par'])."<br />";
else
echo "<br />";
echo "<a href='?set=osebe'> <img src='/style/icons/str.gif' alt='*'>  О себе</a>";
if ($user['ank_o_sebe']!=NULL)
echo " &#62; ".htmlspecialchars($user['ank_o_sebe'])."<br />";
else
echo "<br />";
echo "</div>";

echo "<div class='nav2'>";
echo "Общее положение";
echo "</div>";

echo "<div class='nav1'>";
echo "<a href='?set=zan'> <img src='/style/icons/str.gif' alt='*'>  Чем занимаюсь</a> ";
if ($user['ank_zan']!=NULL)
echo " &#62; ".htmlspecialchars($user['ank_zan']);echo '<br />';



echo "<a href='?set=mat_pol'> <img src='/style/icons/str.gif' alt='*'>  Материальное положение</a>";
if ($user['ank_mat_pol']==1)
echo " &#62; Непостоянные заработки<br />";
if ($user['ank_mat_pol']==2)
echo " &#62; Постоянный небольшой доход<br />";
if ($user['ank_mat_pol']==3)
echo " &#62; Стабильный средний доход<br />";
if ($user['ank_mat_pol']==4)
echo " &#62; Хорошо зарабатываю / обеспечен<br />";
if ($user['ank_mat_pol']==5)
echo " &#62; Не зарабатываю<br />";
if ($user['ank_mat_pol']==0)
echo "<br />";

echo "<a href='?set=avto'> <img src='/style/icons/str.gif' alt='*'>  Наличие автомобиля</a>";
if ($user['ank_avto_n']==1)
echo " &#62; Есть<br />";
if ($user['ank_avto_n']==2)
echo " &#62; 取消<br />";
if ($user['ank_avto_n']==3)
echo " &#62; Хочу купить<br />";
if ($user['ank_avto_n']==0)
echo "<br />";
if ($user['ank_avto'] && $user['ank_avto_n']!=2 && $user['ank_avto_n']!=0)
echo "<img src='/style/icons/str.gif' alt='*'>  ".htmlspecialchars($user['ank_avto'])."<br />";




echo "<a href='?set=proj'> <img src='/style/icons/str.gif' alt='*'>  Проживание</a> ";
if ($user['ank_proj']==1)
echo " &#62; Отдельная квартира (снимаю или своя)<br />";
if ($user['ank_proj']==2)
echo " &#62; Комната в общежитии, коммуналка<br />";
if ($user['ank_proj']==3)
echo " &#62; Живу с родителями<br />";
if ($user['ank_proj']==4)
echo " &#62; Живу с приятелем / с подругой<br />";
if ($user['ank_proj']==5)
echo " &#62; Живу с партнером или супругом (-ой)<br />";
if ($user['ank_proj']==6)
echo " &#62; Нет постоянного жилья<br />";
if ($user['ank_proj']==0)
echo "<br />";


echo "<a href='?set=baby'> <img src='/style/icons/str.gif' alt='*'>  Есть ли дети</a> ";
if ($user['ank_baby']==1)
echo " &#62; 取消<br />";
if ($user['ank_baby']==2)
echo " &#62; Нет, но хотелось бы<br />";
if ($user['ank_baby']==3)
echo " &#62; Есть, живем вместе<br />";
if ($user['ank_baby']==4)
echo " &#62; Есть, живем порознь<br />";
if ($user['ank_baby']==0)
echo "<br />";
echo "</div>";

echo "<div class='nav2'>";
echo "Привычки";
echo "</div>";

echo "<div class='nav1'>";
echo "<a href='?set=smok'> <img src='/style/icons/str.gif' alt='*'>  Курение</a>";
if ($user['ank_smok']==1)
echo " &#62; Не курю<br />";
if ($user['ank_smok']==2)
echo " &#62; Курю<br />";
if ($user['ank_smok']==3)
echo " &#62; Редко<br />";
if ($user['ank_smok']==4)
echo " &#62; Бросаю<br />";
if ($user['ank_smok']==5)
echo " &#62; Успешно бросил<br />";
if ($user['ank_smok']==0)
echo "<br />";

echo "<a href='?set=alko'> <img src='/style/icons/str.gif' alt='*'>  Алкоголь</a> ";
if ($user['ank_alko_n']==1)
echo "&#62; Да, выпиваю<br />";
if ($user['ank_alko_n']==2)
echo "&#62; Редко, по праздникам<br />";
if ($user['ank_alko_n']==3)
echo "&#62; Нет, категорически не приемлю<br />";
if ($user['ank_alko_n']==0)
echo "<br />";
if ($user['ank_alko'] && $user['ank_alko_n']!=3 && $user['ank_alko_n']!=0)
echo "<img src='/style/icons/str.gif' alt='*'>  ".htmlspecialchars($user['ank_alko'])."<br />";


echo "<a href='?set=nark'> <img src='/style/icons/str.gif' alt='*'>  Наркотики</a> ";
if ($user['ank_nark']==1)
echo " Да, курю травку<br />";
if ($user['ank_nark']==2)
echo "&#62; Да, люблю любой вид наркотических средств<br />";
if ($user['ank_nark']==3)
echo "&#62; Бросаю, прохожу реабилитацию<br />";
if ($user['ank_nark']==4)
echo "&#62; Нет, категорически не приемлю<br />";
if ($user['ank_nark']==0)
echo "<br />";

echo "</div>";
echo "<div class='nav2'>";
echo "Контакты";
echo "</div>";

echo "<div class='nav1'>";
echo "<a href='?set=mobile'> <img src='/style/icons/str.gif' alt='*'>  Мобильный</a> ";
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


echo "<div class='foot'><img src='/style/icons/str.gif' alt='*'> <a href='anketa.php'>Посмотреть анкету</a><br />";

if(isset($_SESSION['refer']) && $_SESSION['refer']!=NULL && otkuda($_SESSION['refer']))
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='$_SESSION[refer]'>".otkuda($_SESSION['refer'])."</a><br />";
echo '</div>';
	
include_once '../../sys/inc/tfoot.php';
?>