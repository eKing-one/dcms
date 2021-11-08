<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
$set['title']='论坛-前20个文件'; //网页标题
include_once '../sys/inc/thead.php';
title();
aut();
if ($set['web'])
{
if (dbresult(dbquery("SELECT COUNT(*) FROM `forum_files`"), 0)>0)
{
?>
<table width='100%' border='1' align='center'>
<tr class='forum_file_table_title'>
<td width='14'>
</td>
<td>
档案
</td>
<td>
类型
</td>
<td width='50'>
大小
</td>
<td width='50'>
已下载
</td>
<td width='50'>
评级
</td>
<td width='50'>
</td>
</tr>
<?
$q_f=dbquery("SELECT COUNT(`forum_files_rating`.`rating`) AS `c_rating`, SUM(`forum_files_rating`.`rating`) AS `rating`, `forum_files`.* FROM `forum_files` LEFT JOIN `forum_files_rating` ON
`forum_files`.`id` = `forum_files_rating`.`id_file` GROUP BY `forum_files`.`id` ORDER BY `rating` DESC LIMIT 20");
while ($file = dbassoc($q_f))
{
echo "<tr class='forum_file_table_file'>\n";
echo "<td>\n";
if (is_file(H.'style/themes/'.$set['set_them'].'/loads/14/'.$file['ras'].'.png'))
{
echo "<img src='/style/themes/$set[set_them]/loads/14/$file[ras].png' alt='$file[ras]' />\n";
if ($set['echo_rassh_forum']==1)$ras=".$file[ras]";else $ras=NULL;
}
else
{
echo "<img src='/style/themes/$set[set_them]/forum/14/file.png' alt='' />\n";
$ras=".$file[ras]";
}
echo "</td>\n";
echo "<td>$file[name]$ras</td>\n";
echo "<td>$file[type]</td>\n";
echo "<td>".size_file($file['size'])."</td>\n";
echo "<td style='text-align:center;'>$file[count]</td>\n";
echo "<td style='text-align:center;'> ";
if ($file['rating']==null)$file['rating']=0;
echo "&nbsp;$file[rating]/$file[c_rating]&nbsp;";
echo "</td>\n";
echo "<td><a href='/forum/files/$file[id]/$file[name].$file[ras]'>下载</a></td>\n";
echo "</tr>\n";
}
echo "</table><br />\n";
}
}
else
{
$q_f=dbquery("SELECT COUNT(`forum_files_rating`.`rating`) AS `c_rating`, SUM(`forum_files_rating`.`rating`) AS `rating`, `forum_files`.* FROM `forum_files` LEFT JOIN `forum_files_rating` ON
`forum_files`.`id` = `forum_files_rating`.`id_file` GROUP BY `forum_files`.`id` ORDER BY `rating` DESC LIMIT 20");
while ($file = dbassoc($q_f))
{
if (is_file(H.'style/themes/'.$set['set_them'].'/loads/14/'.$file['ras'].'.png'))
{
echo "<img src='/style/themes/$set[set_them]/loads/14/$file[ras].png' alt='$file[ras]' />\n";
if ($set['echo_rassh_forum']==1)$ras=".$file[ras]";else $ras=NULL;
}
else
{
echo "<img src='/style/themes/$set[set_them]/forum/14/file.png' alt='' />\n";
$ras=".$file[ras]";
}
echo "<a href='/forum/files/$file[id]/$file[name].$file[ras]'>$file[name]$ras</a> (".size_file($file['size']).") \n";
echo "<br />\n";
echo "Рейтинг: ";
if ($file['rating']==null)$file['rating']=0;
echo "&nbsp;$file[rating]/$file[c_rating]&nbsp;";
echo " | ";
echo "Скачано: $file[count] раз(а) ";
echo "<br />\n";
}
}
echo "<div class=\"foot\">\n";
echo "&laquo;<a href=\"/forum/\">论坛</a><br />\n";
echo "</div>\n";
include_once '../sys/inc/tfoot.php';
?>