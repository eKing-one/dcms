<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_themes',null,'index.php?'.SID);
adm_check();
include_once '../sys/inc/zip.php';
$set['title']='Установка тем';
include_once '../sys/inc/thead.php';
title();
if (isset($_FILES['file']) && filesize($_FILES['file']['tmp_name'])!=0)
{
$file=esc(stripcslashes(htmlspecialchars($_FILES['file']['name'])));
$file=preg_replace('#(|\?)#', NULL, $file);
$name=esc(trim(retranslit(preg_replace('#\.[^\.]*$#', NULL, $file)))); // имя файла без расширения
$ras=strtolower(preg_replace('#^.*\.#i', NULL, $file));
if ($ras!='zip')$err='Тема должна находиться в ZIP архиве';
if (!isset($err))
{
$zip=new PclZip($_FILES['file']['tmp_name']);
$them_default=new PclZip(H.'sys/add/them.zip');
$content = $zip->extract(PCLZIP_OPT_BY_NAME, 'them.name' ,PCLZIP_OPT_EXTRACT_AS_STRING);
$them_name=trim(esc(@$content[0]['content']));
if (strlen2($them_name)==null)$err='Файл "them.name" пуст или не найден';
$content = $zip->extract(PCLZIP_OPT_BY_NAME, 'style.css' ,PCLZIP_OPT_EXTRACT_AS_STRING);
$css=trim(esc(@$content[0]['content']));
if (strlen2($them_name)==null)$err='Файл "style.css" пуст или не найден';
@mkdir(H.'style/themes/'.$name, 0777);
@chmod(H.'style/themes/'.$name, 0777);
if ($name!=NULL)
@delete_dir(PCLZIP_OPT_PATH, H.'style/themes/'.$name);
$zip->extract(PCLZIP_OPT_PATH, H.'style/themes/'.$name, PCLZIP_OPT_SET_CHMOD, 0777,PCLZIP_OPT_BY_PREG, "#^[^\.ht]+#ui");
if (isset($_POST['add_of_default']) && $_POST['add_of_default']==1)
$them_default->extract(PCLZIP_OPT_PATH, H.'style/themes/'.$name, PCLZIP_OPT_SET_CHMOD, 0777);
@chmod(H.'style/themes/'.$name.'/forum/', 0777);
@chmod(H.'style/themes/'.$name.'/forum/14/', 0777);
@chmod(H.'style/themes/'.$name.'/forum/48/', 0777);
@chmod(H.'style/themes/'.$name.'/chat/', 0777);
@chmod(H.'style/themes/'.$name.'/chat/14/', 0777);
@chmod(H.'style/themes/'.$name.'/chat/48/', 0777);
@chmod(H.'style/themes/'.$name.'/lib/', 0777);
@chmod(H.'style/themes/'.$name.'/lib/14/', 0777);
@chmod(H.'style/themes/'.$name.'/lib/48/', 0777);
@chmod(H.'style/themes/'.$name.'/loads/', 0777);
@chmod(H.'style/themes/'.$name.'/loads/14/', 0777);
@chmod(H.'style/themes/'.$name.'/loads/48/', 0777);
@chmod(H.'style/themes/'.$name.'/user/', 0777);
@chmod(H.'style/themes/'.$name.'/votes/', 0777);
@chmod(H.'style/themes/'.$name.'/graph/', 0777);
}
else $err='无法创建带有主题的文件夹';
if (!isset($err))msg('Тема "'.$name.' ('.$them_name.')" успешно установлена');
}
err();
aut();
echo "<form class='foot' enctype=\"multipart/form-data\" action='?' method=\"post\">";
echo "Выгрузить:<br />";
echo "<input name='file' type='file' accept='application/zip' /><br />";
echo "<label><input type=\"checkbox\" name=\"add_of_default\" value=\"1\" /> Добавить недостающие файлы</label><br />";
echo "<input class=\"submit\" type=\"submit\" value=\"Далее\" /><br />";
echo "Тема должна находится в zip архиве без папки<br />";
echo "Присутствие файлов them.name и style.css обязательно<br />";
echo "Название папки темы будет взято из названия архива<br />";
echo "</form>";
echo "<div class='foot'>";
echo "&laquo;<a href='themes.php'>Темы оформления</a><br />";
if (user_access('adm_panel_show'))
echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
echo "</div>";
include_once '../sys/inc/tfoot.php';
?>