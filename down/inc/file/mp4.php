<?php
$url = '/down' . $dir_id['dir'] . $file_id['id'] . '.' . $file_id['ras'] . '';

// 检查是否存在缩略图
if (test_file(H . "files/screens/128/{$file_id['id']}.gif")) {
	echo "<img src='/files/screens/128/{$file_id['id']}.gif' alt='缩略图...' /><br />";
} else {
	// 尝试使用 ffmpeg_movie
	if (class_exists('ffmpeg_movie')) {
		$media = new ffmpeg_movie($file);
		$k_frame = intval($media->getFrameCount());
		$w = $media->GetFrameWidth();
		$h = $media->GetFrameHeight();
		$ff_frame = $media->getFrame(intval($k_frame / 2));
		if ($ff_frame) {
			$gd_image = $ff_frame->toGDImage();
			if ($gd_image) {
				$des_img = imagecreatetruecolor(128, 128);
				$s_img = $gd_image;
				imagecopyresampled($des_img, $s_img, 0, 0, 0, 0, 128, 128, $w, $h);
				$des_img = img_copyright($des_img); // 版权叠加
				imagegif($des_img, H . "files/screens/128/{$file_id['id']}.gif");
				chmod(H . "files/screens/128/{$file_id['id']}.gif", 0777);
				imagedestroy($des_img);
				imagedestroy($s_img);
				echo "<img src='/files/screens/128/{$file_id['id']}.gif' alt='缩略图...' /><br />";
			}
		}
	}
	// 如果 ffmpeg_movie 不可用，尝试使用 getID3
	elseif (class_exists('getID3')) {
		$getID3 = new getID3();
		$fileInfo = $getID3->analyze($file);

		if (isset($fileInfo['video']['frame_rate']) && isset($fileInfo['video']['resolution_x']) && isset($fileInfo['video']['resolution_y'])) {
			$w = $fileInfo['video']['resolution_x'];
			$h = $fileInfo['video']['resolution_y'];

			// 尝试提取封面图片
			if (isset($fileInfo['comments']['picture'][0]['data'])) {
				$imageData = $fileInfo['comments']['picture'][0]['data'];
				$sourceImage = imagecreatefromstring($imageData);
				if ($sourceImage) {
					$des_img = imagecreatetruecolor(128, 128);
					imagecopyresampled($des_img, $sourceImage, 0, 0, 0, 0, 128, 128, $w, $h);
					$des_img = img_copyright($des_img); // 版权叠加
					imagegif($des_img, H . "files/screens/128/{$file_id['id']}.gif");
					chmod(H . "files/screens/128/{$file_id['id']}.gif", 0777);
					imagedestroy($des_img);
					imagedestroy($sourceImage);
					echo "<img src='/files/screens/128/{$file_id['id']}.gif' alt='缩略图...' /><br />";
				}
			}
		}
	}
}
?>

<video controls width="100%" height="400">
	<source src="<?= $url ?>">
	您的浏览器不支持在线视频观看
</video>
</br>

<?php
if ($file_id['opis'] != NULL) {
	echo "资料描述: ";
	echo output_text($file_id['opis']);
	echo "<br />";
}

// 获取视频信息
if (class_exists('ffmpeg_movie')) {
	$media = new ffmpeg_movie($file);
	echo '分辨率: ' . $media->GetFrameWidth() . 'x' . $media->GetFrameHeight() . "像素<br />";
	echo '帧速率: ' . $media->getFrameRate() . "<br />";
	echo '编解码器(视频): ' . $media->getVideoCodec() . "<br />";
	if (intval($media->getDuration()) > 3599)
		echo '时长: ' . intval($media->getDuration() / 3600) . ":" . date('s', fmod($media->getDuration() / 60, 60)) . ":" . date('s', fmod($media->getDuration(), 3600)) . "<br />";
	elseif (intval($media->getDuration()) > 59)
		echo '时长: ' . intval($media->getDuration() / 60) . ":" . date('s', fmod($media->getDuration(), 60)) . "<br />";
	else
		echo '时长: ' . intval($media->getDuration()) . " 秒<br />";
	echo "比特率: " . ceil(($media->getBitRate()) / 1024) . " Kbps<br />";
} elseif (class_exists('getID3')) {
	$getID3 = new getID3();
	$fileInfo = $getID3->analyze($file);

	if (isset($fileInfo['video'])) {
		echo '分辨率: ' . $fileInfo['video']['resolution_x'] . 'x' . $fileInfo['video']['resolution_y'] . "像素<br />";
		echo '帧速率: ' . $fileInfo['video']['frame_rate'] . "<br />";
		if (isset($fileInfo['video']['codec'])) {
			echo '编解码器(视频): ' . $fileInfo['video']['codec'] . "<br />";
		} else {
			echo '编解码器(视频): N/A<br />';
		}		
		if (isset($fileInfo['playtime_seconds'])) {
			$duration = $fileInfo['playtime_seconds'];
			if ($duration > 3599) {
				echo '时长: ' . intval(round($duration / 3600)) . ":" . date('s', round(fmod($duration / 60, 60))) . ":" . date('s', round(fmod($duration, 3600))) . "<br />";
			} elseif ($duration > 59) {
				echo '时长: ' . intval(round($duration / 60)) . ":" . date('s', round(fmod($duration, 60))) . "<br />";
			} else {
				echo '时长: ' . intval(round($duration)) . " 秒<br />";
			}
		}
		if (isset($fileInfo['bitrate']))
			echo "比特率: " . ceil($fileInfo['bitrate'] / 1024) . " Kbps<br />";
	}
}

echo "上传时间: " . vremja($file_id['time']) . "<br />";
echo "大小: " . size_file($size) . "<br />";
