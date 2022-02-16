<?
//到管理面板
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
user_access('adm_set_foto',null,'index.php?'.SID);
adm_check();
$set['title']='照片库设置';
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['save']))
{
$temp_set['max_upload_foto_x']=intval($_POST['max_upload_foto_x']);
$temp_set['max_upload_foto_y']=intval($_POST['max_upload_foto_y']);
if (save_settings($temp_set))
{
admin_log('设置','照片廊','更改照片库设置');
msg('设置已成功接受');
}
else
$err='没有更改设置文件的权限';
}
err();
aut();
echo "<form method=\"post\" action=\"?\">";
echo "照片宽度 (max):<br /><input type='text' name='max_upload_foto_x' value='$temp_set[max_upload_foto_x]' /><br />";
echo "照片高度 (max):<br /><input type='text' name='max_upload_foto_y' value='$temp_set[max_upload_foto_y]' /><br />";
echo "<input value=\"修改\" name='save' type=\"submit\" />";
echo "</form>";
if (user_access('adm_panel_show')){
echo "<div class='foot'>";
echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
echo "</div>";
}
include_once '../sys/inc/tfoot.php';
?>