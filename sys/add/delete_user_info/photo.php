<?
$gallery_q=dbquery("SELECT * FROM `gallery` WHERE `id_user` = '$ank[id]'");
$photo=0;
while ($gallery = dbassoc($gallery_q))
{
$photo+=dbresult(dbquery("SELECT COUNT(*) FROM `gallery_photo` WHERE `id_gallery` = '$gallery[id]'"),0);
}
if (count($collisions)>1 && isset($_GET['all']))
{
$photo_coll=0;
for ($i=1;$i<count($collisions);$i++)
{
$gallery_q=dbquery("SELECT * FROM `gallery` WHERE `id_user` = '$collisions[$i]'");
while ($gallery = dbassoc($gallery_q))
{
$photo_coll+=dbresult(dbquery("SELECT COUNT(*) FROM `gallery_photo` WHERE `id_gallery` = '$gallery[id]'"),0);
}
}
if ($downnik_coll!=0)
$photo="$photo +$photo_coll*";
}
echo "<span class=\"ank_n\">Фотографии:</span> <span class=\"ank_d\">$photo</span><br />";
