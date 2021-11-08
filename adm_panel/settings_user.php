<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
$temp_set=$set;
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_set_user',null,'index.php?'.SID);
adm_check();

$set['title']='Пользовательские настройки';
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['save']))
{
if ($_POST['write_guest']==1 || $_POST['write_guest']==0)
{
$temp_set['write_guest']=intval($_POST['write_guest']);
}

if ($_POST['show_away']==1 || $_POST['show_away']==0)
{
$temp_set['show_away']=intval($_POST['show_away']);
}
if ($_POST['guest_select']==1 || $_POST['guest_select']==0)
{
$temp_set['guest_select']=intval($_POST['guest_select']);
}
if ($_POST['st']==1 || $_POST['st']==0)
{
$temp_set['st']=intval($_POST['st']);
}

$temp_set['reg_select']=esc($_POST['reg_select']);
if (save_settings($temp_set))
{
admin_log('Настройки','Пользователи',"Изменение пользовательских настроек");
msg('Настройки успешно приняты');
}
else
$err='Нет прав для изменения файла настроек';
}
err();
aut();



echo "<form method=\"post\" action=\"?\">\n";

echo "Режим регистрации:<br />\n<select name=\"reg_select\">\n";
echo "<option value=\"close\">Закрыта</option>\n";
if ($temp_set['reg_select']=='open')$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"open\"$sel>Открыта</option>\n";
if ($temp_set['reg_select']=='open_mail')$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"open_mail\"$sel>Открыта + E-mail</option>\n";
echo "</select><br />\n";

echo "Режим гостя:<br />\n<select name=\"guest_select\">\n";
echo "<option value=\"0\">Открыто все</option>\n";
if ($temp_set['guest_select']=='1')$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>Закрыто все *</option>\n";
echo "</select><br />\n";
echo " * остаются открытыми регистрация и авторизация<br />\n";



echo "Показ away:<br />\n<select name=\"show_away\">\n";
if ($temp_set['show_away']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>Показывать</option>\n";
if ($temp_set['show_away']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>Скрывать</option>\n";
echo "</select><br />\n";


echo "Пишут в гостевой:<br />\n<select name=\"write_guest\">\n";
if ($temp_set['write_guest']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>Все</option>\n";
if ($temp_set['write_guest']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>Авторизованые</option>\n";
echo "</select><br />\n";


echo "Показ статусов в прочих модулях:<br />\n<select name=\"st\">\n";
if ($temp_set['st']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>Показывать</option>\n";
if ($temp_set['st']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>Скрывать</option>\n";
echo "</select><br />\n";


echo "<input value=\"Изменить\" name='save' type=\"submit\" />\n";
echo "</form>\n";

if (user_access('user_mass_delete')){
echo "<div class='foot'>\n";
echo "&raquo;<a href='/adm_panel/delete_users.php'>Удаление пользователей</a><br />\n";
echo "</div>\n";
}
if (user_access('adm_panel_show')){
echo "<div class='foot'>\n";
echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";
echo "</div>\n";
}
include_once '../sys/inc/tfoot.php';
?>