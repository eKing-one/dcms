<?//到管理面板
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
include_once '../sys/inc/icons.php'; // Иконки главного меню
user_access('adm_menu',null,'index.php?'.SID);
adm_check();
$set['title']='主菜单';
include_once '../sys/inc/thead.php';
title();
$opendiricon=opendir(H.'style/icons');
while ($icons=readdir($opendiricon))
{
// запись всех тем в массив
if (preg_match('#^\.|default.png#',$icons))continue;
$icon[]=$icons;
}
closedir($opendiricon);
if (isset($_POST['add']) && isset($_POST['name']) && $_POST['name']!=NULL && isset($_POST['url']) && $_POST['url']!=NULL && isset($_POST['counter']))
{
$name=esc(stripcslashes(htmlspecialchars($_POST['name'])));
$url=esc(stripcslashes(htmlspecialchars($_POST['url'])));
$counter=esc(stripcslashes(htmlspecialchars($_POST['counter'])));
$pos=dbresult(dbquery("SELECT MAX(`pos`) FROM `menu`"), 0)+1;
$icon=preg_replace('#[^a-z0-9 _\-\.]#i', null, $_POST['icon']);
dbquery("INSERT INTO `menu` (`name`, `url`, `counter`, `pos`, `icon`) VALUES ('$name', '$url', '$counter', '$pos', '$icon')");
msg('链接成功添加');
}
if (isset($_POST['add']) && isset($_POST['name']) && $_POST['name']!=NULL && isset($_POST['counter']) && isset($_POST['type']) && $_POST['type']=='razd')
{
$name=esc(stripcslashes(htmlspecialchars($_POST['name'])));
$url=esc(stripcslashes(htmlspecialchars($_POST['url'])));
$counter=esc(stripcslashes(htmlspecialchars($_POST['counter'])));
$pos=dbresult(dbquery("SELECT MAX(`pos`) FROM `menu`"), 0)+1;
$icon=preg_replace('#[^a-z0-9 _\-\.]#i', null, $_POST['icon']);
dbquery("INSERT INTO `menu` (`type`, `name`, `url`, `counter`, `pos`, `icon`) VALUES ('razd', '$name', '$url', '$counter', '$pos', '$icon')");
msg('链接成功添加');
}
if (isset($_POST['change']) && isset($_GET['id']) && isset($_POST['name']) && $_POST['name']!=NULL && isset($_POST['url']) && isset($_POST['counter']))
{
$id=intval($_GET['id']);
$name=esc(stripcslashes(htmlspecialchars($_POST['name'])));
$url=esc(stripcslashes(htmlspecialchars($_POST['url'])));
$counter=esc(stripcslashes(htmlspecialchars($_POST['counter'])));
$icon=preg_replace('#[^a-z0-9 _\-\.]#i', null, $_POST['icon']);
dbquery("UPDATE `menu` SET `name` = '$name', `url` = '$url', `counter` = '$counter', `icon` = '$icon' WHERE `id` = '$id' LIMIT 1");
msg('菜单项已成功更改');
}
if (isset($_GET['id']) && isset($_GET['act']) && dbresult(dbquery("SELECT COUNT(*) FROM `menu` WHERE `id` = '".intval($_GET['id'])."'"),0))
{
$menu=dbassoc(dbquery("SELECT * FROM `menu` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));
if ($_GET['act']=='up')
{
dbquery("UPDATE `menu` SET `pos` = '".($menu['pos'])."' WHERE `pos` = '".($menu['pos']-1)."' LIMIT 1");
dbquery("UPDATE `menu` SET `pos` = '".($menu['pos']-1)."' WHERE `id` = '".intval($_GET['id'])."' LIMIT 1");
msg('菜单项已向上移动一个位置');
}
if ($_GET['act']=='down')
{
dbquery("UPDATE `menu` SET `pos` = '".($menu['pos'])."' WHERE `pos` = '".($menu['pos']+1)."' LIMIT 1");
dbquery("UPDATE `menu` SET `pos` = '".($menu['pos']+1)."' WHERE `id` = '".intval($_GET['id'])."' LIMIT 1");
msg('菜单项已向下移动一个位置');
}
if ($_GET['act']=='del')
{
dbquery("DELETE FROM `menu` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1");
msg('菜单项已被删除');
}
}
err();
aut();
echo "<table class='post'>";
$q=dbquery("SELECT * FROM `menu` ORDER BY `pos` ASC");
while ($post = dbassoc($q))
{
echo "   <tr>";
if (!isset($post['icon']))dbquery('ALTER TABLE `menu` ADD `icon` VARCHAR( 32 ) NULL DEFAULT NULL');
if (!isset($post['type']))dbquery("ALTER TABLE  `menu` ADD  `type` ENUM('link', 'razd') NOT NULL DEFAULT 'link' AFTER `id`");
echo "  <td class='p_t'>";
if ($post['type']=='link')echo icons($post['icon'],'code');
echo "$post[pos]) $post[name] ".($post['type']=='link'?"($post[url])":null);
echo "  </td>";
echo "   </tr>";
echo "   <tr>";
echo "  <td class='p_m'>";
if (isset($_GET['id']) && $_GET['id']==$post['id'] && isset($_GET['act']) && $_GET['act']=='edit')
{
echo "<form action=\"?id=$post[id]\" method=\"post\">";
echo "类型: ".($post['type']=='link'?'连结':'分离器')."<br />";
echo "标题:<br />";
echo "<input type='text' name='name' value=\"$post[name]\" /><br />";
if ($post['type']=='link'){
echo "连结:<br />";
echo "<input type='text' name='url' value='$post[url]' /><br />";
}
else
echo "<input type='hidden' name='url' value='' />";
echo "柜台:<br />";
echo "<input type='text' name='counter' value='$post[counter]' /><br />";
if ($post['type']=='link'){
echo "图标:<br />";
echo "<select name='icon'>";
echo "<option value='default.png'>默认情况下</option>";
for ($i=0;$i<sizeof($icon);$i++)
{
echo "<option value='$icon[$i]'".($post['icon']==$icon[$i]?" selected='selected'":null).">$icon[$i]</option>";
}
echo "</select><br />";
}
else
echo "<input type='hidden' name='icon' value='$post[icon]' />";
echo "<input class=\"submit\" name=\"change\" type=\"submit\" value=\"要改变\" /><br />";
echo "</form>";
echo "<a href='?'>取消</a><br />";
}
else
{
echo "柜台: ".($post['counter']==null?'缺席':$post['counter'])."<br />";
echo "<a href='?id=$post[id]&amp;act=up&amp;$passgen'>更高</a> | ";
echo "<a href='?id=$post[id]&amp;act=down&amp;$passgen'>下面</a> | ";
echo "<a href='?id=$post[id]&amp;act=del&amp;$passgen'>移走 </a><br />";
echo "<a href='?id=$post[id]&amp;act=edit&amp;$passgen'>编辑 </a><br />";
}
echo "  </td>";
echo "   </tr>";
}
echo "</table>";
if (isset($_GET['add'])){
echo "<form action='?add=$passgen' method=\"post\">";
echo "类型:<br />";
echo "<select name='type'>";
echo "<option value='link'>连结 (1)</option>";
echo "<option value='razd'>章 (2)</option>";
echo "</select><br />";
echo "标题 (1,2):<br />";
echo "<input type=\"text\" name=\"name\" value=\"\"/><br />";
echo "连结(1):<br />";
echo "<input type=\"text\" name=\"url\" value=\"\"/><br />";
echo "柜台 (1,2):<br />";
echo "<input type=\"text\" name=\"counter\" value=\"\"/><br />";
echo "图标 (1):<br />";
echo "<select name='icon'>";
echo "<option value='default.png'>默认情况下</option>";
for ($i=0;$i<sizeof($icon);$i++)
{
echo "<option value='$icon[$i]'>$icon[$i]</option>";
}
echo "</select><br />";
echo "<input class='submit' name='add' type='submit' value='添加' /><br />";
echo "<a href='?$passgen'>取消</a><br />";
echo "</form>";
}
else echo "<div class='foot'><a href='?add=$passgen'>添加项目</a></div>";
if (user_access('adm_panel_show')){
echo "<div class='foot'>";
echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
echo "</div>";
}
include_once '../sys/inc/tfoot.php';
?>