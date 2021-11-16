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
user_access('adm_ip_edit',null,'index.php?'.SID);
adm_check();
$opsos=NULL;
$set['title']='添加运算符';
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['min']) && isset($_POST['max']) && isset($_POST['opsos']))
{
if (!preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#",$_POST['min']))$err='无效的IP格式';
if (!preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#",$_POST['max']))$err='Неверный формат IP';
if ($_POST['opsos']==NULL)$err='Введите название оператора';
$min=ip2long($_POST['min']);
$max=ip2long($_POST['max']);
$opsos=my_esc(stripcslashes(htmlspecialchars($_POST['opsos'])));
dbquery("INSERT INTO `opsos` (`min`, `max`, `opsos`) values('$min', '$max', '$opsos')",$db);
msg ('Диапазон успешно добавлен');
}
if (isset($_GET['delmin'])  && isset($_GET['delmax']) &&
 dbresult(dbquery("SELECT COUNT(*) FROM `opsos` WHERE `min` = '".$_GET['delmin']."' AND `max` = '".$_GET['delmax']."' LIMIT 1",$db), 0)!=0)
{
dbquery("DELETE FROM `opsos` WHERE `min` = '".$_GET['delmin']."' AND `max` = '".$_GET['delmax']."' LIMIT 1");
dbquery("OPTIMIZE TABLE `opsos`");
msg('Диапазон успешно удален');
}
err();
aut();
$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `opsos`"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
echo "<table class='post'>";
if ($k_post==0)
{
echo "   <tr>";
echo "  <td class='p_t'>";
echo "Нет операторов";
echo "  </td>";
echo "   </tr>";
}
$q=dbquery("SELECT * FROM `opsos` ORDER BY `opsos` ASC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q))
{
echo "   <tr>";
echo "  <td class='p_t'>";
echo long2ip($post['min']).' - '.long2ip($post['max']);
echo "  </td>";
echo "   </tr>";
echo "   <tr>";
echo "  <td class='p_m'>";
echo "$post[opsos]<br />";
echo "<a href=\"?page=$page&amp;delmin=$post[min]&amp;delmax=$post[max]\">Удалить</a><br />";
echo "  </td>";
echo "   </tr>";
}
echo "</table>";
if ($k_page>1)str('?',$k_page,$page); // Вывод страниц
echo "<form method=\"post\" action=\"\">";
echo "Начальный IP адрес:<br /><input name=\"min\" size=\"16\"  value=\"\" type=\"text\" /><br />";
echo "Завершающий IP:<br /><input name=\"max\" size=\"16\" value=\"\" type=\"text\" /><br />";
echo "Оператор:<br /><input name=\"opsos\" size=\"16\" value=\"$opsos\" type=\"text\" /><br />";
echo "<input value=\"Добавить\" type=\"submit\" />";
echo "</form>";
if (user_access('adm_panel_show')){
echo "<div class='foot'>";
echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
echo "</div>";
}
include_once '../sys/inc/tfoot.php';
?>