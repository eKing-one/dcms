<?php
$url =   '/down' . $dir_id['dir'] . $file_id['id'] . '.' . $file_id['ras'] . '';
if (test_file(H."files/screens/128/$file_id[id].gif")) {
	echo "<img src='/files/screens/128/$file_id[id].gif' alt='scr...' /><br />";
} elseif (class_exists('ffmpeg_movie')) {
	$media = new ffmpeg_movie($file);
	$k_frame=intval($media->getFrameCount());
	$w = $media->GetFrameWidth();
	$h = $media->GetFrameHeight();
	$ff_frame = $media->getFrame(intval($k_frame / 2));
	if ($ff_frame) {
		$gd_image = $ff_frame->toGDImage();
		if ($gd_image) {
			$des_img = imagecreatetruecolor(128, 128);
			$s_img = $gd_image;
			imagecopyresampled($des_img, $s_img, 0, 0, 0, 0, 128, 128, $w, $h);
			$des_img=img_copyright($des_img); // 版权叠加
			imagegif($des_img,H."files/screens/128/{$file_id['id']}.gif");
			chmod(H."files/screens/128/{$file_id['id']}.gif", 0777);
			imagedestroy($des_img);
			imagedestroy($s_img);
			if (function_exists('iconv')) {
				echo "<img src='" . iconv('windows-1252', 'utf-8',"/files/screens/128/{$file_id['id']}.gif") . "' alt='scr...' /><br />";
			} else {
				echo "<img src='/files/screens/128/{$file_id['id']}.gif' alt='scr...' /><br />";
			}
		}
	}
}
?>
<video controls width="100%" height="400">
	<source src="<?=$url?>"><!-- MP4 和 Safari， IE9， iPhone， iPad， Android， и Windows Phone 7 -->
	</object>
	您的浏览器不支持在线视频观看
</video>
</br>
<?php
if ($file_id['opis']!=NULL) {
	echo "资料描述: ";
	echo output_text($file_id['opis']);
	echo "<br />";
}
if (class_exists('ffmpeg_movie')) {
	$media = new ffmpeg_movie($file);
	echo '许可: ' . $media->GetFrameWidth() . 'x' . $media->GetFrameHeight() . "пикс<br />";
	echo '帧速率: ' . $media->getFrameRate() . "<br />";
	echo '编解码器(视频): ' . $media->getVideoCodec() . "<br />";
	if (intval($media->getDuration()) > 3599)
	echo '时间: ' . intval($media->getDuration()/3600) . ":" . date('s', fmod($media->getDuration() / 60, 60)) . ":" . date('s', fmod($media->getDuration(), 3600)) . "<br />";
	elseif (intval($media->getDuration()) > 59)
	echo '时间: ' . intval($media->getDuration() / 60) . ":" . date('s', fmod($media->getDuration(), 60)) . "<br />";
	else
	echo '时间: ' . intval($media->getDuration()) . " 秒<br />";
	echo "比特率: " . ceil(($media->getBitRate()) / 1024) . " KBPS<br />";
}
echo "上传时间: " . vremja($file_id['time']) . "<br />";
echo "大小: " . size_file($size) . "<br />";