<?
if (is_file(H."sys/obmen/screens/48/$post[id].gif"))
{

echo "<img src='/sys/obmen/screens/48/$post[id].gif' alt='scr...' /><br />\n";
}
elseif (class_exists('ffmpeg_movie')){
$media = new ffmpeg_movie($file);
$k_frame=intval($media->getFrameCount());

$w = $media->GetFrameWidth();
$h = $media->GetFrameHeight();
$ff_frame = $media->getFrame(intval($k_frame/2));
if ($ff_frame) {
$gd_image = $ff_frame->toGDImage();
if ($gd_image) {
$des_img = imagecreatetruecolor(48, 48);
$s_img = $gd_image;
imagecopyresampled($des_img, $s_img, 0, 0, 0, 0, 48, 48, $w, $h);
imagegif($des_img,H."sys/obmen/screens/48/$post[id].gif");
chmod(H."sys/obmen/screens/48/$post[id].gif", 0777);
imagedestroy($des_img);
imagedestroy($s_img);





echo "<img src='/sys/obmen/screens/48/$post[id].gif' alt='scr...' /><br />\n";

}
}
}


?>