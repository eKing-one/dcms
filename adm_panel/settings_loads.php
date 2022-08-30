<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
$temp_set=$set;
include_once '../sys/inc/db_connect.php';
include_once '../sys/c/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_set_loads',null,'index.php?'.SID);
adm_check();
$set['title']='下载设置';
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['save']))
{
$temp_set['downloads_select']=intval($_POST['downloads_select']);
$temp_set['down_limit_up']=intval($_POST['down_limit_up']);
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
admin_log('设置','下载','更改加载中心设置');
msg('设置已成功接受');
}
else
$err='没有更改设置文件的权限';
}
err();
aut();
echo "<form method=\"post\" action=\"?\">";
echo "下载模式:<br /><select name=\"downloads_select\">";
echo "<option value=\"0\">允许所有人</option>";
if ($temp_set['downloads_select']=='1')$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>只获授权</option>";
if ($temp_set['downloads_select']=='2')$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"2\"$sel>授权+100积分</option>";
echo "</select><br />";
echo "显示文件扩展名:<br /><select name=\"echo_rassh\">";
if ($temp_set['echo_rassh']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>显示</option>";
if ($temp_set['echo_rassh']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>隐藏</option>";
echo "</select><br />";
echo "文件被视为新文件的时间（小时）:<br /><input type='text' name='loads_new_file_hour' value='$temp_set[loads_new_file_hour]' /><br />";
echo "版权档案(图片):<br /><input type='text' name='copy_path' value='$temp_set[copy_path]' /><br />";
echo "下载中心(上载档案的点数限制):<br /><input name=\"down_limit_up\" value=\"$temp_set[down_limit_up]\" type=\"text\" /><br />";
echo "<input value=\"修改\" name='save' type=\"submit\" />";
echo "</form>";
echo "<div class='foot'>";
echo "&raquo;<a href='loads_recount.php'>将文件重新计算为z-z</a><br />";
echo "</div>";
if (user_access('adm_panel_show'))
{
echo "<div class='foot'>";
echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
echo "</div>";
}
include_once '../sys/inc/tfoot.php';
?>