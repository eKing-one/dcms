<?

if (is_file(H."sys/loads/screens/48/$size.$name.$ras.png"))
echo "<img src='/sys/loads/screens/48/$size.$name.$ras.png' alt='$ras' /><br />\n";
else
{


include_once H.'sys/inc/zip.php';

$zip=new PclZip($file);
$content = $zip->extract(PCLZIP_OPT_BY_NAME, "META-INF/MANIFEST.MF" ,PCLZIP_OPT_EXTRACT_AS_STRING);

$icon=false;
if(@preg_match("#MIDlet-Icon:[^(\n|\r)]*(\n|\r)#i", $content[0]['content'], $jad))
$icon=preg_replace("#(MIDlet-Icon:( )*)|(\n|\r)#i", NULL, $jad[0]);
elseif (@preg_match("#MIDlet-1:[^(\n|\r)]*(\n|\r)#i", $content[0]['content'], $jad))
{
$icon=preg_replace("#(MIDlet-1:( )*)|(\n|\r)#i", NULL, $jad[0]);
$icon=preg_replace("#(^[^,]*,)|(,[^,]*$)#i", NULL, $icon);
}
$icon=preg_replace('#^ *| *$#i', NULL, $icon);
$icon=preg_replace("#(^(/){1,})|((/){1,}$)#","",$icon);
//echo "$icon";
if ($icon==NULL)$icon=false;

if ($icon){
$content = $zip->extract(PCLZIP_OPT_BY_NAME, $icon,PCLZIP_OPT_EXTRACT_AS_STRING);

$j=@fopen(H."sys/tmp/$sess.png", 'w');
@fwrite($j, $content[0]['content']);
@fclose($j);
@chmod(H."sys/tmp/$sess.png", 0777);


if ($imgc=@imagecreatefrompng(H."sys/tmp/$sess.png"))
{
$img_x=imagesx($imgc);
$img_y=imagesy($imgc);

if ($img_x>48 && $img_y>48){
if ($img_x==$img_y)
{
$dstW=48; // ширина
$dstH=48; // высота 
}
elseif ($img_x>$img_y)
{
$prop=$img_x/$img_y;
$dstW=48;
$dstH=ceil($dstW/$prop);
}
else
{
$prop=$img_y/$img_x;
$dstH=48;
$dstW=ceil($dstH/$prop);
}
$screen=imagecreate($dstW, $dstH);
imagecopyresized($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
imagedestroy($imgc);

}
else
$screen=$imgc;



imagepng($screen,H."sys/loads/screens/48/$size.$name.$ras.png");
echo "<img src=\"/sys/loads/screens/48/$size.$name.$ras.png\" alt=\"$ras\" /><br />";
@chmod(H."sys/loads/screens/48/$size.$name.$ras.png", 0777);
unlink(H."sys/tmp/$sess.png");
}
elseif (is_file(H."style/themes/default/loads/48/$ras.png"))
{

@copy (H."style/themes/default/loads/48/$ras.png",H."sys/loads/screens/48/$size.$name.$ras.png");
echo "<img src=\"/sys/loads/screens/48/$size.$name.$ras.png\" alt=\"$ras\" /><br />\n";
}
else
{
@copy (H."style/themes/default/loads/48/file.png",H."sys/loads/screens/48/$size.$name.$ras.png");
echo "<img src=\"/sys/loads/screens/48/$size.$name.$ras.png\" alt=\"$ras\" /><br />\n";
}

}
elseif (is_file(H."style/themes/default/loads/48/$ras.png"))
{

@copy (H."style/themes/default/loads/48/$ras.png",H."sys/loads/screens/48/$size.$name.$ras.png");
echo "<img src=\"/sys/loads/screens/48/$size.$name.$ras.png\" alt=\"$ras\" /><br />\n";
}
else
{
@copy (H."style/themes/default/loads/48/file.png",H."sys/loads/screens/48/$size.$name.$ras.png");
echo "<img src=\"/sys/loads/screens/48/$size.$name.$ras.png\" alt=\"$ras\" /><br />\n";
}



}


?>