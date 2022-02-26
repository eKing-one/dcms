<?
if (test_file("inc/opis/$ras.php"))include "inc/opis/$ras.php";
else
{
echo '尺寸: '.size_file($size)."<br />";
$ank=dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = '$post[id_user]' LIMIT 1"));
echo "上传者:  ".user::nick($ank['id'],1,1,0).vremja($post['time'])." <br />";
}
