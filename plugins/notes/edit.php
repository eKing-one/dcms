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

/* Бан пользователя */ 
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0)
{
header('Location: /ban.php?'.SID);exit;
}

only_reg();

$set['title']='Дневники';
include_once '../../sys/inc/thead.php';
title();
aut();

if (dbresult(dbquery("SELECT COUNT(*) FROM `notes` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1",$db), 0)==0){
header("Location: index.php?".SID);
exit;
}

$notes=dbarray(dbquery("select * from `notes` where `id` = '".intval($_GET['id'])."'"));

if (user_access('notes_edit') || $user['id'] == $notes['id_user'])
{
$avtor = get_user($notes['id_user']);

if (isset($_GET['edit']) && isset($_POST['name']) && $_POST['name']!=NULL && isset($_POST['msg']))
{
$msg=my_esc($_POST['msg']);
$id_dir=intval($_POST['id_dir']);
$privat=intval($_POST['private']);
$privat_komm=intval($_POST['private_komm']);

$type=0;
if($_POST['name']==null)$name=substr(esc(stripslashes(htmlspecialchars($_POST['msg']))),0,24);
else
$name=$_POST['name'];

if (strlen2($name)>50)$err='Длина названия превышает предел в 50 символов';
if (strlen2($msg)<3)$err='Короткий Текст';
if (strlen2($msg)>10000)$err='Длина текста превышает предел в 10000 символа';

if (!isset($err))
{
dbquery("UPDATE `notes` SET `name` = '".my_esc($name)."', `type` = '$type', `id_dir` = '$id_dir', `msg` = '$msg', `private` = '$privat', `private_komm` = '$privat_komm' WHERE `id`='".intval($_GET['id'])."'");

$_SESSION['message'] = 'Изменения успешно приняты';
header("Location: list.php?id=".intval($_GET['id'])."".SID);
exit;
}
}

err();

echo "<div class=\"foot\">";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='index.php'>Дневники</a> | <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>";
echo " | <a href='list.php?id=$notes[id]'>" . text($notes['name']) . "</a> | <b>Редактирование</b>";
echo "</div>";

$notes=dbarray(dbquery("select * from `notes` where `id`='".intval($_GET['id'])."';"));

echo "<form method='post' name='message' action='?id=".intval($_GET['id'])."&amp;edit'>";
echo "Название:<br /><input type=\"text\" name=\"name\" value=\""  . text($notes['name']) . "\" /><br />";
$msg2 = text($notes['msg']);
if ($set['web'] && is_file(H.'style/themes/'.$set['set_them'].'/altername_post_form.php')){include_once H.'style/themes/'.$set['set_them'].'/altername_post_form.php';}else{echo "Сообщение:$tPanel<textarea name=\"msg\">"  . text($notes['msg']) . "</textarea><br />";}
echo "Категория:<br /><select name='id_dir'>";
$q=dbquery("SELECT * FROM `notes_dir` ORDER BY `id` DESC");

echo "<option value='0'".(!$notes['id_dir'] ? " selected='selected'":null)."><b>Без категории</b></option>";

while ($post = dbassoc($q))
{
echo "<option value='$post[id]'".($notes['id_dir'] == $post['id'] ?" selected='selected'":null).">" . text($post['name']) . "</option>";
}
echo "</select><br />";
echo "<div class='main'>Могут смотреть:<br /><input name='private' type='radio' ".($notes['private']==0?' checked="checked"':null)." value='0' />Все ";
echo "<input name='private' type='radio' ".($notes['private']==1?' checked="checked"':null)." value='1' />Друзья ";
echo "<input name='private' type='radio' ".($notes['private']==2?' checked="checked"':null)." value='2' />Только я</div>";

echo "<div class='main'>Могут комментировать:<br /><input name='private_komm' type='radio' ".($notes['private_komm']==0?' checked="checked"':null)." value='0' />Все ";
echo "<input name='private_komm' type='radio' ".($notes['private_komm']==1?' checked="checked"':null)." value='1' />Друзья ";
echo "<input name='private_komm' type='radio' ".($notes['private_komm']==2?' checked="checked"':null)." value='2' />Только я</div>";

echo "<input value=\"Применить\" type=\"submit\" />";
echo "</form>";
echo "<div class=\"foot\">";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='index.php'>Дневники</a> | <a href='/info.php?id=$avtor[id]'>$avtor[nick]</a>";
echo " | <a href='list.php?id=$notes[id]'>" . text($notes['name']) . "</a> | <b>Редактирование</b>";
echo "</div>";
}
include_once '../../sys/inc/tfoot.php';

?>