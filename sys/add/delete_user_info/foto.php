<?
$gallery_q=dbquery("SELECT * FROM `gallery` WHERE `id_user` = '$ank[id]'");
$foto=0;
while ($gallery = dbassoc($gallery_q))
{
$foto+=dbresult(dbquery("SELECT COUNT(*) FROM `gallery_foto` WHERE `id_gallery` = '$gallery[id]'"),0);
}
if (count($collisions)>1 && isset($_GET['all']))
{
$foto_coll=0;
for ($i=1;$i<count($collisions);$i++)
{
$gallery_q=dbquery("SELECT * FROM `gallery` WHERE `id_user` = '$collisions[$i]'");
while ($gallery = dbassoc($gallery_q))
{
$foto_coll+=dbresult(dbquery("SELECT COUNT(*) FROM `gallery_foto` WHERE `id_gallery` = '$gallery[id]'"),0);
}
}
if ($downnik_coll!=0)
$foto="$foto +$foto_coll*";
}
echo "<span class=\"ank_n\">Фотографии:</span> <span class=\"ank_d\">$foto</span><br />";
