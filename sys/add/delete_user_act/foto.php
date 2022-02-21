<?
$gallery_q=dbquery("SELECT * FROM `gallery` WHERE `id_user` = '$ank[id]'");
while ($gallery = dbassoc($gallery_q))
{
$q=dbquery("SELECT * FROM `gallery_foto` WHERE `id_gallery` = '$gallery[id]'");
while ($post = dbassoc($q))
{
@unlink(H."sys/gallery/48/$post[id].jpg");
@unlink(H."sys/gallery/128/$post[id].jpg");
@unlink(H."sys/gallery/640/$post[id].jpg");
@unlink(H."sys/gallery/foto/$post[id].jpg");
dbquery("DELETE FROM `gallery_foto` WHERE `id` = '$post[id]' LIMIT 1");
dbquery("DELETE FROM `gallery_komm` WHERE `id_foto` = '$post[id]'");
dbquery("DELETE FROM `gallery_rating` WHERE `id_foto` = '$post[id]'");
}
}
dbquery("DELETE FROM `gallery` WHERE `id_user` = '$ank[id]'");
dbquery("DELETE FROM `gallery_komm` WHERE `id_user` = '$ank[id]'");
if (isset($_GET['all']) && count($collisions)>1)
{
for ($i=1;$i<count($collisions);$i++)
{
$gallery_q=dbquery("SELECT * FROM `gallery` WHERE `id_user` = '$collisions[$i]'");
while ($gallery = dbassoc($gallery_q))
{
$q=dbquery("SELECT * FROM `gallery_foto` WHERE `id_gallery` = '$gallery[id]'");
while ($post = dbassoc($q))
{
@unlink(H."sys/gallery/48/$post[id].jpg");
@unlink(H."sys/gallery/128/$post[id].jpg");
@unlink(H."sys/gallery/640/$post[id].jpg");
@unlink(H."sys/gallery/foto/$post[id].jpg");
dbquery("DELETE FROM `gallery_foto` WHERE `id` = '$post[id]' LIMIT 1");
dbquery("DELETE FROM `gallery_komm` WHERE `id_foto` = '$post[$i]'");
dbquery("DELETE FROM `gallery_rating` WHERE `id_foto` = '$post[$i]'");
}
}
dbquery("DELETE FROM `gallery` WHERE `id_user` = '$collisions[$i]'");
}
}
?>