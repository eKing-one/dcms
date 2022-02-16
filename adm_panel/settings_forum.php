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
user_access('adm_set_forum',null,'index.php?'.SID);
adm_check();
$set['title']='论坛设置';
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['save']))
{
if ($_POST['show_num_post']==1 || $_POST['show_num_post']==0)
{
$temp_set['show_num_post']=intval($_POST['show_num_post']);
}
if ($_POST['echo_rassh_forum']==1 || $_POST['echo_rassh_forum']==0)
{
$temp_set['echo_rassh_forum']=intval($_POST['echo_rassh_forum']);
}
if ($_POST['forum_counter']==1 || $_POST['forum_counter']==0)
{
$temp_set['forum_counter']=intval($_POST['forum_counter']);
}
if (save_settings($temp_set))
{
admin_log('设置','论坛','更改论坛设置');
msg('设置已成功接受');
}
else
$err='没有更改设置文件的权限';
}
err();
aut();
echo "<form method=\"post\" action=\"?\">";
echo "论坛帖子编号:<br /><select name=\"show_num_post\">";
if ($temp_set['show_num_post']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>展览</option>";
if ($temp_set['show_num_post']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>藏起来</option>";
echo "</select><br />";
echo "论坛柜台:<br /><select name=\"forum_counter\">";
if ($temp_set['forum_counter']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>人数</option>";
if ($temp_set['forum_counter']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>职位/主题</option>";
echo "</select><br />";
echo "显示文件扩展名:<br /><select name=\"echo_rassh_forum\">";
if ($temp_set['echo_rassh_forum']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>展览</option>";
if ($temp_set['echo_rassh_forum']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>藏起来 *</option>";
echo "</select><br />";
echo "* 只有当有一个合适的图标时，它才会被隐藏<br />";
echo "<input value=\"要改变\" name='save' type=\"submit\" />";
echo "</form>";
if (user_access('adm_panel_show')){
echo "<div class='foot'>";
if (user_access('adm_forum_sinc'))
echo "&raquo;<a href='/adm_panel/forum_sinc.php'>论坛表的同步</a><br />";
echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
echo "</div>";
}
include_once '../sys/inc/tfoot.php';
?>