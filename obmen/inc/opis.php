<?
if (test_file("inc/opis/$ras.php"))include "inc/opis/$ras.php";
else
{
echo '尺寸: '.size_file($size)."<br />\n";
$ank=dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$post[id_user]' LIMIT 1"));
echo "上传时间: <a href='/info.php?id=$ank[id]'>$ank[nick]</a> ".vremja($post['time'])." <br />\n";
}
?>