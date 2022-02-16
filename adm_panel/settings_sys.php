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
user_access('adm_set_sys',null,'index.php?'.SID);
adm_check();

$set['title']='系统设置';
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['save']))
{

// ShaMan
$temp_set['title']=esc(stripcslashes(htmlspecialchars($_POST['title'])),1);
// Тут конец моих дум
$temp_set['mail_backup']=esc($_POST['mail_backup']);
$temp_set['p_str']=intval($_POST['p_str']);
dbquery("ALTER TABLE `user` CHANGE `set_p_str` `set_p_str` INT( 11 ) DEFAULT '$temp_set[p_str]'");


if (!preg_match('#\.\.#',$_POST['set_them']) && is_dir(H.'style/themes/'.$_POST['set_them']))
{
$temp_set['set_them']=$_POST['set_them'];
dbquery("ALTER TABLE `user` CHANGE `set_them` `set_them` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '$temp_set[set_them]'");
}

if (!preg_match('#\.\.#',$_POST['set_them2']) && is_dir(H.'style/themes/'.$_POST['set_them2']))
{
$temp_set['set_them2']=$_POST['set_them2'];
dbquery("ALTER TABLE `user` CHANGE `set_them2` `set_them2` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '$temp_set[set_them2]'");
}

if ($_POST['show_err_php']==1 || $_POST['show_err_php']==0)
{
$temp_set['show_err_php']=intval($_POST['show_err_php']);
}

if (isset($_POST['antidos']) && $_POST['antidos']==1)
$temp_set['antidos']=1; else $temp_set['antidos']=0;

if (isset($_POST['antimat']) && $_POST['antimat']==1)
$temp_set['antimat']=1; else $temp_set['antimat']=0;

$temp_set['meta_keywords']=esc(stripcslashes(htmlspecialchars($_POST['meta_keywords'])),1);
$temp_set['meta_description']=esc(stripcslashes(htmlspecialchars($_POST['meta_description'])),1);




if (save_settings($temp_set))
{
admin_log('设置','系统','更改系统设置');
msg('设置已成功接受');
}

else
$err='没有更改设置文件的权限';
}
err();
aut();



echo "<form method=\"post\" action=\"?\">";

echo "网站名称:<br /><input name=\"title\" value=\"$temp_set[title]\" type=\"text\" /><br />";
echo "每页积分:<br /><input name=\"p_str\" value=\"$temp_set[p_str]\" type=\"text\" /><br />";



echo "题目 (WAP):<br /><select name='set_them'>";
$opendirthem=opendir(H.'style/themes');
while ($themes=readdir($opendirthem)){
// пропускаем корневые папки и файлы
if ($themes=='.' || $themes=='..' || !is_dir(H."style/themes/$themes"))continue;
// пропускаем темы для web браузеров
if (file_exists(H."style/themes/$themes/.only_for_web"))continue;
echo "<option value='$themes'".($temp_set['set_them']==$themes?" selected='selected'":null).">".trim(file_get_contents(H.'style/themes/'.$themes.'/them.name'))."</option>";
}
closedir($opendirthem);
echo "</select><br />";

echo "题目 (WEB):<br /><select name='set_them2'>";
$opendirthem=opendir(H.'style/themes');

while ($themes=readdir($opendirthem)){
// пропускаем корневые папки и файлы
if ($themes=='.' || $themes=='..' || !is_dir(H."style/themes/$themes"))continue;
// пропускаем темы для wap браузеров
if (file_exists(H."style/themes/$themes/.only_for_wap"))continue;
echo "<option value='$themes'".($temp_set['set_them2']==$themes?" selected='selected'":null).">".trim(file_get_contents(H.'style/themes/'.$themes.'/them.name'))."</option>";
}
closedir($opendirthem);
echo "</select><br />";
echo "关键词 (META):<br />";
echo "<textarea name='meta_keywords'>$temp_set[meta_keywords]</textarea><br />";
echo "资料描述 (META):<br />";
echo "<textarea name='meta_description'>$temp_set[meta_description]</textarea><br />";


echo "<label><input type='checkbox'".($temp_set['antidos']?" checked='checked'":null)." name='antidos' value='1' /> 反Dos*</label><br />";
echo "<label><input type='checkbox'".($temp_set['antimat']?" checked='checked'":null)." name='antimat' value='1' /> 反CC</label><br />";

echo "解释器错误:<br /><select name=\"show_err_php\">";
echo "<option value='0'".($temp_set['show_err_php']==0?" selected='selected'":null).">藏起来</option>";
echo "<option value='1'".($temp_set['show_err_php']==1?" selected='selected'":null).">节目管理</option>";
echo "</select><br />";




echo "E-mail для BackUp:<br /><input type='text' name='mail_backup' value='$temp_set[mail_backup]'  /><br />";

echo "<br />";
echo "* 反Dos-防止来自单个IP地址的频繁请求<br />";
echo "<input value=\"保存\" name='save' type=\"submit\" />";
echo "</form>";

if (user_access('adm_panel_show')){
echo "<div class='foot'>";
echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
echo "</div>";
}
include_once '../sys/inc/tfoot.php';
?>