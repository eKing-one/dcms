<?//网页标题
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
user_access('adm_ref',null,'index.php?'.SID);
adm_check();
$set['title']='转介服务'; // заголовок страницы
include_once '../sys/inc/thead.php';
title();
aut();
$k_post=dbresult(dbquery("SELECT COUNT(distinct(`url`)) FROM `user_ref`"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
echo "<table class='post'>";
if ($k_post==0)
{
echo "   <tr>";
echo "  <td class='p_t'>";
echo "Нет рефералов";
echo "  </td>";
echo "   </tr>";
}
$q=dbquery("SELECT COUNT(`url`) AS `count`, MAX(`time`) AS `time`, `url` FROM `user_ref` GROUP BY `url` ORDER BY `count` DESC LIMIT $start, $set[p_str]");
while ($ref = dbassoc($q))
{
echo "   <tr>";
echo "  <td class='p_t'>";
echo "URL: <a target='_blank' href='/go.php?go=".base64_encode("http://$ref[url]")."'>".htmlentities($ref['url'])."</a><br />";
echo "  </td>";
echo "   </tr>";
echo "   <tr>";
echo "  <td class='p_m'>";
echo "过渡时期: $ref[count]<br />";
echo "最后一次: ".vremja($ref['time'])."<br />";
echo "  </td>";
echo "   </tr>";
}
echo "</table>";
if ($k_page>1)str("?",$k_page,$page); // Вывод страниц
echo "<div class='foot'>";
if (user_access('adm_panel_show'))
echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
echo "</div>";
include_once '../sys/inc/tfoot.php';
?>