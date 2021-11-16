<?
if (is_file(H."sys/obmen/screens/128/$file_id[id].gif"))
{
	echo "<img src='/sys/obmen/screens/128/$file_id[id].gif' alt='scr...' /><br />";
}
elseif (class_exists('ffmpeg_movie'))
{
	$media = new ffmpeg_movie($file);
	$k_frame=intval($media->getFrameCount());

	$w = $media->GetFrameWidth();
	$h = $media->GetFrameHeight();
	$ff_frame = $media->getFrame(intval($k_frame/2));
	if ($ff_frame) 
	{
		$gd_image = $ff_frame->toGDImage();
		if ($gd_image) 
		{
			$des_img = imagecreatetruecolor(128, 128);
			$s_img = $gd_image;
			imagecopyresampled($des_img, $s_img, 0, 0, 0, 0, 128, 128, $w, $h);
			$des_img=img_copyright($des_img); // наложение копирайта
			imagegif($des_img,H."sys/obmen/screens/128/$file_id[id].gif");
			chmod(H."sys/obmen/screens/128/$file_id[id].gif", 0777);
			imagedestroy($des_img);
			imagedestroy($s_img);
			if (function_exists('iconv'))
			echo "<img src='".iconv('windows-1251', 'utf-8',"/sys/obmen/screens/128/$file_id[id].gif")."' alt='scr...' /><br />";
			else
			echo "<img src='/sys/obmen/screens/128/$file_id[id].gif' alt='scr...' /><br />";
		}
	}
}

if ($file_id['opis']!=NULL)
{
	echo "Описание: ";
	echo output_text($file_id['opis']);
	echo "<br />";
}

if (class_exists('ffmpeg_movie'))
{
	$media = new ffmpeg_movie($file);

	echo 'Разрешение: '. $media->GetFrameWidth().'x'.$media->GetFrameHeight()."пикс<br />";
	echo 'Частота кадров: '.$media->getFrameRate()."<br />";
	echo 'Кодек (видео): '.$media->getVideoCodec()."<br />";

	if (intval($media->getDuration())>3599)
	echo 'Время: '.intval($media->getDuration()/3600).":".date('s',fmod($media->getDuration()/60,60)).":".date('s',fmod($media->getDuration(),3600))."<br />";
	elseif (intval($media->getDuration())>59)
	echo 'Время: '.intval($media->getDuration()/60).":".date('s',fmod($media->getDuration(),60))."<br />";
	else
	echo 'Время: '.intval($media->getDuration())." сек<br />";
	echo "Битрейт: ".ceil(($media->getBitRate())/1024)." KBPS<br />";
}

echo "Добавлен: ".vremja($file_id['time'])."<br />";
echo "Размер: ".size_file($size)."<br />";
?>