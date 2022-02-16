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
user_access('adm_themes',null,'index.php?'.SID);
adm_check();
$set['title']='设计主题';
include_once '../sys/inc/thead.php';
title();
$opendirthem=opendir(H.'style/themes');
while ($themes2=readdir($opendirthem))
{
// запись всех тем в массив
if ($themes2=='.' || $themes2=='..')continue;
$themes3[]=$themes2;
}
closedir($opendirthem);
if (isset($_GET['delete']) && in_array("$_GET[delete]", $themes3) && isset($_GET['ok']))
{
$del_them=$_GET['delete'];
if ($del_them==$temp_set['set_them2'] || $del_them==$temp_set['set_them'])
$err='主题默认使用';
else
{
if (@delete_dir(H.'style/themes/'.$del_them))
{
$themes3=NUll;
$opendirthem=opendir(H.'style/themes');
while ($themes2=readdir($opendirthem))
{
// запись всех тем в массив
if ($themes2=='.' || $themes2=='..')continue;
$themes3[]=$themes2;
}
closedir($opendirthem);
msg("Тема успешно удалена");
}
else
$err="无法删除主题";
}
}
err();
aut();
$k_post=sizeof($themes3);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
echo "<table class='post'>";
for($i=$start;$i<$k_post && $i<$set['p_str']*$page;$i++)
{
// постраничный вывод тем
$themes=$themes3[$i];
echo "   <tr>";
if ($set['set_show_icon']==2){
echo "  <td class='icon48' rowspan='2'>";
if (is_file(H.'style/themes/'.$themes.'/screen_48.png'))
echo "<img src='".H."style/themes/".$themes."/screen_48.png' alt='' /><br />";
else
echo "Нет";
echo "  </td>";
}
echo "  <td class='p_t'>";
echo ($name=@file_get_contents(H.'style/themes/'.$themes.'/them.name'))?$name:$themes;
echo "  </td>";
echo "   </tr>";
echo "   <tr>";
echo "  <td class='p_m'>";
echo "主题文件夹: <span title='/style/themes/$themes/'>$themes</span><br />";
// размер файла таблиц стилей
echo (is_file(H.'style/themes/'.$themes.'/style.css'))?"<a href='/style/themes/$themes/style.css'>style.css</a>: ".size_file(filesize(H.'style/themes/'.$themes.'/style.css'))."<br />":"Нет style.css<br />";
if ($themes==$temp_set['set_them'])
{
echo "默认情况下，WAP<br />";
}
if ($themes==$temp_set['set_them2'])
{
echo "默认情况下为WEB<br />";
}
echo '站在'.dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `set_them` = '$themes' OR `set_them2` = '$themes'"),0)." 伙计.<br />";
echo "<a href='?delete=$themes&amp;page=$page'>移走</a><br />";
echo "  </td>";
echo "   </tr>";
}
echo "</table>";
if (isset($_GET['delete']) && in_array("$_GET[delete]", $themes3))
{
$del_them=$_GET['delete'];
echo "<div class='err'>";
if ($del_them==$temp_set['set_them2'] || $del_them==$temp_set['set_them'])
echo "Тема ".(($name=@file_get_contents(H.'style/themes/'.$del_them.'/them.name'))?$name:$del_them)." 默认安装<br /><a href='?page=$page'>取消</a><br />";
else
{
echo "确认删除 (".(($name=@file_get_contents(H.'style/themes/'.$del_them.'/them.name'))?$name:$del_them)."):<br />";
echo "<a href='?delete=$del_them&amp;page=$page&amp;ok'>移走</a> | <a href='?page=$page'>取消</a><br />";
}
echo "</div>";
}
if ($k_page>1)str('?',$k_page,$page); // Вывод страниц
echo "<div class='foot'>";
echo "&raquo;<a href='them_installer.php'>安装主题</a><br />";
if (user_access('adm_panel_show'))
echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
echo "</div>";
include_once '../sys/inc/tfoot.php';
?>