<?
if (is_file(H."sys/obmen/screens/48/$post[id].$ras"))
{

echo "<img src='/sys/obmen/screens/48/$post[id].$ras' alt='scr...' /><br />\n";
}
elseif (function_exists('imagecreatefromstring'))
{

$imgc=imagecreatefromstring(file_get_contents($file));
$img_x=imagesx($imgc);
$img_y=imagesy($imgc);
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

$screen=imagecreatetruecolor($dstW, $dstH);
imagecopyresampled($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
imagedestroy($imgc);
imagejpeg($screen,H."sys/obmen/screens/48/$post[id].$ras",100);
imagedestroy($screen);
echo "<img src='/sys/obmen/screens/48/$post[id].$ras' alt='scr...' /><br />\n";
}


?>