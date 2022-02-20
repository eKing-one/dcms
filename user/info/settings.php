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

only_reg();
$set['title']='我的设置';
include_once '../../sys/inc/thead.php';
title();

if (isset($_POST['save'])){


if (isset($_POST['add_konts']) && ($_POST['add_konts']==2 || $_POST['add_konts']==1 || $_POST['add_konts']==0))
{
$user['add_konts']=intval($_POST['add_konts']);
dbquery("UPDATE `user` SET `add_konts` = '$user[add_konts]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='添加联系人模式错误';
if (isset($_POST['set_files']) && ($_POST['set_files']==1 || $_POST['set_files']==0))
{
$user['set_files']=intval($_POST['set_files']);
dbquery("UPDATE `user` SET `set_files` = '$user[set_files]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='文件模式错误';/*Метка 18+ */
if (isset($_POST['metka']) && ($_POST['metka']==1 || $_POST['metka']==0))
{
$user['abuld']=intval($_POST['metka']);
dbquery("UPDATE `user` SET `abuld` = '$user[abuld]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='标签错误18+';

if (isset($_POST['show_url']) && ($_POST['show_url']==1 || $_POST['show_url']==0))
{
$user['show_url']=intval($_POST['show_url']);
dbquery("UPDATE `user` SET `show_url` = '$user[show_url]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='位置模式错误';
if (isset($_POST['set_time_chat']) && (is_numeric($_POST['set_time_chat']) && $_POST['set_time_chat']>=0 && $_POST['set_time_chat']<=900))
{
$user['set_time_chat']=intval($_POST['set_time_chat']);
$set['time_chat']=$user['set_time_chat'];
dbquery("UPDATE `user` SET `set_time_chat` = '$user[set_time_chat]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='自动更新时间错误';

if (isset($_POST['set_news_to_mail']) && $_POST['set_news_to_mail']==1)
{
$user['set_news_to_mail']=1;
dbquery("UPDATE `user` SET `set_news_to_mail` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['set_news_to_mail']=0;
dbquery("UPDATE `user` SET `set_news_to_mail` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}if (isset($_POST['set_them']) && preg_match('#^([A-z0-9\-_\(\)]+)$#ui', $_POST['set_them']) && is_dir(H.'style/themes/'.$_POST['set_them']))
{
$user['set_them']=$_POST['set_them'];
dbquery("UPDATE `user` SET `set_them` = '$user[set_them]' WHERE `id` = '$user[id]' LIMIT 1");
}
elseif (isset($_POST['set_them2']) && preg_match('#^([A-z0-9\-_\(\)]+)$#ui', $_POST['set_them2']) && is_dir(H.'style/themes/'.$_POST['set_them2']))
{
$user['set_them2']=$_POST['set_them2'];
dbquery("UPDATE `user` SET `set_them2` = '$user[set_them2]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='主题应用程序错误';if (isset($_POST['set_p_str']) && is_numeric($_POST['set_p_str']) && $_POST['set_p_str']>0 && $_POST['set_p_str']<=100)
{
$user['set_p_str']=intval($_POST['set_p_str']);
$set['p_str']=$user['set_p_str'];
dbquery("UPDATE `user` SET `set_p_str` = '$user[set_p_str]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='每页项目数量不正确';

if (isset($_POST['set_timesdvig']) && (is_numeric($_POST['set_timesdvig']) && $_POST['set_timesdvig']>=-12 && $_POST['set_timesdvig']<=12))
{
$user['set_timesdvig']=intval($_POST['set_timesdvig']);
dbquery("UPDATE `user` SET `set_timesdvig` = '$user[set_timesdvig]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='每页项目数量不正确';

if (!isset($err)){$_SESSION['message'] = '更改已成功接受';header("Location: ?"); exit;}
}
err();
aut();

echo "<div id='comments' class='menus'>";

echo "<div class='webmenu'>";
echo "<a href='/user/info/settings.php' class='activ'>普通</a>";
echo "</div>"; 

echo "<div class='webmenu last'>";
echo "<a href='/user/tape/settings.php'>录音带</a>";
echo "</div>"; 

echo "<div class='webmenu last'>";
echo "<a href='/user/discussions/settings.php'>讨论</a>";
echo "</div>"; 

echo "<div class='webmenu last'>";
echo "<a href='/user/notification/settings.php'>通知书</a>";
echo "</div>"; echo "<div class='webmenu last'>";
echo "<a href='/user/info/settings.privacy.php' >私隐保护</a>";
echo "</div>"; 
echo "<div class='webmenu last'>";echo "<a href='/user/info/secure.php' >密码</a>";echo "</div>"; 
echo "</div>";

echo "<form method='post' action='?$passgen'>";

echo "聊天中自动更新:<br /><input type='text' name='set_time_chat' value='$set[time_chat]' maxlength='3' /><br />";
echo "每页积分:<br /><input type='text' name='set_p_str' value='$set[p_str]' maxlength='3' /><br />";

echo "主题 (".($webbrowser?'WEB':'WAP')."):<br /><select name='set_them".($webbrowser?'2':null)."'>";
$opendirthem=opendir(H.'style/themes');
while ($themes=readdir($opendirthem)){
// пропускаем корневые папки и файлы
if ($themes=='.' || $themes=='..' || !is_dir(H."style/themes/$themes"))continue;
// пропускаем темы для определенных браузеров
if (file_exists(H."style/themes/$themes/.only_for_".($webbrowser?'wap':'web')))continue;

echo "<option value='$themes'".($user['set_them'.($webbrowser?'2':null)]==$themes?" selected='selected'":null).">".trim(file_get_contents(H.'style/themes/'.$themes.'/them.name'))."</option>";
}
closedir($opendirthem);
echo "</select><br />";echo "上传文件:<br /><select name='set_files'>";
echo "<option value='1'".($user['set_files']==1?" selected='selected'":null).">展场</option>";
echo "<option value='0'".($user['set_files']==0?" selected='selected'":null).">否定使用上传</option>";
echo "</select><br />";

echo "地点:<br /><select name='show_url'>";
echo "<option value='1'".($user['show_url']==1?" selected='selected'":null).">显示</option>";
echo "<option value='0'".($user['show_url']==0?" selected='selected'":null).">隐藏</option>";
echo "</select><br />";

echo "添加联系人:<br /><select name='add_konts'>";
echo "<option value='2'".($user['add_konts']==2?" selected='selected'":null).">阅读讯息时</option>";
echo "<option value='1'".($user['add_konts']==1?" selected='selected'":null).">写消息时</option>";
echo "<option value='0'".($user['add_konts']==0?" selected='selected'":null).">只能手动操作</option>";
echo "</select><br />";

echo "时间<br /><select name=\"set_timesdvig\"><br />";
for ($i=-12;$i<12;$i++){
echo "<option value='$i'".($user['set_timesdvig']==$i?" selected='selected'":null).">".date("G:i", $time+$i*60*60)."</option>";}
echo "</select><br />";

if ($user['ank_mail'])
echo "<label><input type='checkbox' name='set_news_to_mail'".($user['set_news_to_mail']?" checked='checked'":null)." value='1' /> 通过电子邮件接收新闻</label><br />";
echo "显示色情材料没有警告:<br />";
echo "<input name='metka'".($user['abuld']==0?" checked='checked'":null)."  type='radio' value='0' />开 ";
echo "<input name='metka'".($user['abuld']==1?" checked='checked'":null)."  type='radio' value='1' />关<br />";

echo "<input type='submit' name='save' value='保存' />";
echo "</form>";

echo "<div class=\"foot\">";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php?id=$user[id]'>$user[nick]</a> | ";
echo '<b>普通</b>';
echo "</div>";
include_once '../../sys/inc/tfoot.php';
?>