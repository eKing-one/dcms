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
$set['title']='用户设置';
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
admin_log('设置','用户',"更改用户设置");
msg('设置已成功接受');
}
else
$err='没有更改设置文件的权限';
}
err();
aut();
echo "<form method=\"post\" action=\"?\">";
echo "注册模式:<br /><select name=\"reg_select\">";
echo "<option value=\"close\">已关闭</option>";
if ($temp_set['reg_select']=='open')$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"open\"$sel>打开</option>";
if ($temp_set['reg_select']=='open_mail')$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"open_mail\"$sel>打开 + E-mail</option>";
echo "</select><br />";
echo "访客模式:<br /><select name=\"guest_select\">";
echo "<option value=\"0\">一切都是开放的</option>";
if ($temp_set['guest_select']=='1')$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>一切都关闭了 *</option>";
echo "</select><br />";
echo " * 注册和授权仍然开放<br />";
echo "炫耀:<br /><select name=\"show_away\">";
if ($temp_set['show_away']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>展示</option>";
if ($temp_set['show_away']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>藏起来</option>";
echo "</select><br />";
echo "他们写在客人:<br /><select name=\"write_guest\">";
if ($temp_set['write_guest']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>全部</option>";
if ($temp_set['write_guest']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>获授权</option>";
echo "</select><br />";
echo "在其他模块中显示状态:<br /><select name=\"st\">";
if ($temp_set['st']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>展示</option>";
if ($temp_set['st']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>藏起来</option>";
echo "</select><br />";
echo "<input value=\"修改\" name='save' type=\"submit\" />";
echo "</form>";
if (user_access('user_mass_delete')){
echo "<div class='foot'>";
echo "&raquo;<a href='/adm_panel/delete_users.php'>删除用户</a><br />";
echo "</div>";
}
if (user_access('adm_panel_show')){
echo "<div class='foot'>";
echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
echo "</div>";
}
include_once '../sys/inc/tfoot.php';
?>