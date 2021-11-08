<?
function gif_resize($string,$x,$y)
{
global $sess;
include_once H.'sys/inc/gifdecoder.php';
include_once H.'sys/inc/gifencoder.php';
$giff = new GIFDecoder ( $string );
$arr = $giff->GIFGetFrames ( ); // разрезание gif анимации на отдельные картинки
$dly = $giff->GIFGetDelays ( );
$imgs=imagecreatefromstring($string);
$img_x=imagesx($imgs);
$img_y=imagesy($imgs);
if ($img_x==$img_y)
{
$dstW=$x; // ширина
$dstH=$y; // высота
}
elseif ($img_x>$img_y)
{
$prop=$img_x/$img_y;
$dstW=$x;
$dstH=ceil($dstW/$prop);
}
else
{
$prop=$img_y/$img_x;
$dstH=$y;
$dstW=ceil($dstH/$prop);
}

for ($i=0;$i<count($arr);$i++)
{
$frames[]=H."sys/tmp/frame_$sess.$i.gif";
file_put_contents(H."sys/tmp/frame_$sess.$i.gif", $arr[$i]);
@chmod(H."sys/tmp/frame_$sess.$i.gif",0777);
}

for ($i=0;$i<count($arr);$i++)
{
$imgc[$i]=imagecreatefromgif(H."sys/tmp/frame_$sess.$i.gif");
$frame_img[$i]=imagecreatetruecolor($dstW, $dstH);
imagecopyresampled($frame_img[$i], $imgc[$i], 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
imagedestroy($imgc[$i]);
imagegif($frame_img[$i],H."sys/tmp/frame_$sess.$i.gif");
//@chmod(H."sys/tmp/frame_$sess.$i.gif",0777);
imagedestroy($frame_img[$i]);
}

$gif = @new GIFEncoder	($frames,$dly,0,2,0, 0, 0,"url");



for ($i=0;$i<count($arr);$i++)
{
unlink(H."sys/tmp/frame_$sess.$i.gif");
}

return ($gif->GetAnimation());
}
?>