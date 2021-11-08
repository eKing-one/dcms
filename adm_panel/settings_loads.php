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
user_access('adm_set_loads',null,'index.php?'.SID);
adm_check();
$set['title']='Настройки загрузок';
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['save']))
{
$temp_set['downloads_select']=intval($_POST['downloads_select']);
$temp_set['obmen_limit_up']=intval($_POST['obmen_limit_up']);
$temp_set['loads_new_file_hour']=intval($_POST['loads_new_file_hour']);

if ($_POST['echo_rassh']==1 || $_POST['echo_rassh']==0)
{
$temp_set['echo_rassh']=intval($_POST['echo_rassh']);
}

if (is_file(H.$_POST['copy_path']) || $_POST['copy_path']==null)
{
$temp_set['copy_path']=$_POST['copy_path'];
}

if (save_settings($temp_set))
{

admin_log('Настройки','Загрузки','Изменение настроек загруз-центра');
msg('Настройки успешно приняты');
}
else
$err='Нет прав для изменения файла настроек';
}
err();
aut();



echo "<form method=\"post\" action=\"?\">\n";

echo "Режим скачивания:<br />\n<select name=\"downloads_select\">\n";
echo "<option value=\"0\">Разрешено всем</option>\n";
if ($temp_set['downloads_select']=='1')$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>Только авторизованым</option>\n";
if ($temp_set['downloads_select']=='2')$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"2\"$sel>Авторизованым + 100 баллов</option>\n";
echo "</select><br />\n";



echo "Показ расширений файлов:<br />\n<select name=\"echo_rassh\">\n";
if ($temp_set['echo_rassh']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>Показывать</option>\n";
if ($temp_set['echo_rassh']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>Скрывать</option>\n";
echo "</select><br />\n";


echo "Время, в течении которого файл считается новым (часы):<br />\n<input type='text' name='loads_new_file_hour' value='$temp_set[loads_new_file_hour]' /><br />\n";

echo "Файл копирайта (на картинки):<br />\n<input type='text' name='copy_path' value='$temp_set[copy_path]' /><br />\n";

echo "Обменник (ограничение в баллах на выгрузку файлов):<br />\n<input name=\"obmen_limit_up\" value=\"$temp_set[obmen_limit_up]\" type=\"text\" /><br />\n";

echo "<input value=\"Изменить\" name='save' type=\"submit\" />\n";
echo "</form>\n";

echo "<div class='foot'>\n";
echo "&raquo;<a href='loads_recount.php'>Пересчет файлов в з-ц</a><br />\n";
echo "</div>\n";
if (user_access('adm_panel_show'))
{
echo "<div class='foot'>\n";
echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";
echo "</div>\n";
}
include_once '../sys/inc/tfoot.php';
?>