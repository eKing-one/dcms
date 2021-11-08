<?
if ($set['web'])
{

if (dbresult(dbquery("SELECT COUNT(*) FROM `forum_files` WHERE `id_post` = '$post[id]'"), 0)>0)
{
?>
<table width='100%' border='1' style='margin:5px;'>
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
<?
if (isset($user) && $user['level']>1) echo "<td width='14'></td>\n";
?>
</tr>
<?
$q_f=dbquery("SELECT * FROM `forum_files` WHERE `id_post` = '$post[id]'");
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
if (!isset($file['count']))dbquery("ALTER TABLE `forum_files` ADD `count` INT DEFAULT '0' NOT NULL");

echo "<td style='text-align:center;'>$file[count]</td>\n";
echo "<td style='text-align:center;'> ";

$k_vote=dbresult(dbquery("SELECT COUNT(*) FROM `forum_files_rating` WHERE `id_file` = '$file[id]'"), 0);
$sum_vote=dbresult(dbquery("SELECT SUM(`rating`) FROM `forum_files_rating` WHERE `id_file` = '$file[id]'"), 0);

if ($sum_vote==null)$sum_vote=0;


if (isset($user) && $user['balls']>=50 && $user['rating']>=0 && dbresult(dbquery("SELECT COUNT(*) FROM `forum_files_rating` WHERE `id_user` = '$user[id]' AND `id_file` = '$file[id]'"), 0)==0)
echo "<a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/?page=$page&amp;id_file=$file[id]&amp;rating=down\" title=\"投反对票\">[-]</a>";


echo "&nbsp;$sum_vote/$k_vote&nbsp;";

if (isset($user) && $user['balls']>=50 && $user['rating']>=0 && dbresult(dbquery("SELECT COUNT(*) FROM `forum_files_rating` WHERE `id_user` = '$user[id]' AND `id_file` = '$file[id]'"), 0)==0)
echo "<a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/?page=$page&amp;id_file=$file[id]&amp;rating=up\" title=\"给予积极的投票\">[+]</a>";


echo "</td>\n";
echo "<td><a href='/forum/files/$file[id]/$file[name].$file[ras]'>下载</a></td>\n";
if (isset($user) && $user['level']>1)
echo "<td><a href='/forum/files/delete/$file[id]/' title='从列表中删除'><img src='/style/themes/$set[set_them]/forum/14/del_file.png' alt='' /></a></td>\n";

echo "</tr>\n";
}

}

}
else
{
$q_f=dbquery("SELECT * FROM `forum_files` WHERE `id_post` = '$post[id]'");
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
	 $k_vote=dbresult(dbquery("SELECT COUNT(*) FROM `forum_files_rating` WHERE `id_file` = '$file[id]'"), 0);
$sum_vote=dbresult(dbquery("SELECT SUM(`rating`) FROM `forum_files_rating` WHERE `id_file` = '$file[id]'"), 0);

if ($sum_vote==null)$sum_vote=0;if (isset($user) && $user['level']>1)
echo "<a href='/forum/files/delete/$file[id]/' title='从列表中删除'><img src='/style/themes/$set[set_them]/forum/14/del_file.png' alt='' /></a>\n";


echo "<br />\n";

echo "评级: ";

if (isset($user) && $user['balls']>=50 && $user['rating']>=0 && dbresult(dbquery("SELECT COUNT(*) FROM `forum_files_rating` WHERE `id_user` = '$user[id]' AND `id_file` = '$file[id]'"), 0)==0)
echo "<a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/?page=$page&amp;id_file=$file[id]&amp;rating=down\" title=\"投反对票\">[-]</a>";


echo "&nbsp;$sum_vote/$k_vote&nbsp;";

if (isset($user) && $user['balls']>=50 && $user['rating']>=0 && dbresult(dbquery("SELECT COUNT(*) FROM `forum_files_rating` WHERE `id_user` = '$user[id]' AND `id_file` = '$file[id]'"), 0)==0)
echo "<a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/?page=$page&amp;id_file=$file[id]&amp;rating=up\" title=\"给予积极的投票\">[+]</a>";

echo " | ";

echo "已下载: $file[count] раз(а) ";
echo "<br />\n";
}
}

?>