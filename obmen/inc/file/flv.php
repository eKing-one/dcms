<?
if ($set['web'] && (!isset($set['downloads_select']) || $set['downloads_select']=='0'))
{
?>
<center><p id='preview'>Install Flash Player</p>
<script type='text/javascript' src='/sys/swfobject.js'></script>
<script type='text/javascript'>
var s1 = new SWFObject('/sys/player.swf','player','350','300','9');
s1.addParam('allowfullscreen','true');
s1.addParam('allowscriptaccess','always');
//s1.addParam('flashvars','file=".$_GET['id'].".flv&amp;image=".$_GET['id'].".jpg');
s1.addParam('flashvars','file=<? echo "/obmen$dir_id[dir]".urlencode($file_id['name']).".$file_id[ras]&amp;image=/sys/obmen/screens/128/$file_id[id].gif"; ?>');
s1.write('preview');
</script>
</center>
<br />
<?
}

if (is_file(H."sys/obmen/screens/128/$file_id[id].gif"))
{
	echo "<img src='/sys/obmen/screens/128/$file_id[id].gif' alt='scr...' /><br />\n";
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
			echo "<img src='".iconv('windows-1251', 'utf-8',"/sys/obmen/screens/128/$file_id[id].gif")."' alt='scr...' /><br />\n";
			else
			echo "<img src='/sys/obmen/screens/128/$file_id[id].gif' alt='scr...' /><br />\n";
		}
	}
}

if ($file_id['opis']!=NULL)
{
	echo "Описание: ";
	echo output_text($file_id['opis']);
	echo "<br />\n";
}

if (class_exists('ffmpeg_movie'))
{
	$media = new ffmpeg_movie($file);

	echo 'Разрешение: '. $media->GetFrameWidth().'x'.$media->GetFrameHeight()."пикс<br />\n";
	echo 'Частота кадров: '.$media->getFrameRate()."<br />\n";
	echo 'Кодек (видео): '.$media->getVideoCodec()."<br />\n";

	if (intval($media->getDuration())>3599)
	echo 'Время: '.intval($media->getDuration()/3600).":".date('s',fmod($media->getDuration()/60,60)).":".date('s',fmod($media->getDuration(),3600))."<br />\n";
	elseif (intval($media->getDuration())>59)
	echo 'Время: '.intval($media->getDuration()/60).":".date('s',fmod($media->getDuration(),60))."<br />\n";
	else
	echo 'Время: '.intval($media->getDuration())." сек<br />\n";
	echo "Битрейт: ".ceil(($media->getBitRate())/1024)." KBPS<br />\n";
}

echo "Добавлен: ".vremja($file_id['time'])."<br />\n";
echo "Размер: ".size_file($size)."<br />\n";
?>