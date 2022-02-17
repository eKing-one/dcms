<?
if (test_file(H."sys/obmen/screens/128/$file_id[id].gif"))
{
	echo "<img src='/sys/obmen/screens/128/$file_id[id].gif' alt='屏幕...' /><br />";
}

if ($file_id['opis']!=NULL)
{
	echo "资料描述: ";
	echo output_text($file_id['opis']);
	echo "<br />";
}

echo "上传时间: ".vremja($file_id['time'])."<br />";

if (class_exists('ffmpeg_movie'))
{
	$media = new ffmpeg_movie($file);

	if (intval($media->getDuration())>3599)
	echo '时间: '.intval($media->getDuration()/3600).":".date('s',fmod($media->getDuration()/60,60)).":".date('s',fmod($media->getDuration(),3600))."<br />";
	elseif (intval($media->getDuration())>59)
	echo '时间: '.intval($media->getDuration()/60).":".date('s',fmod($media->getDuration(),60))."<br />";
	else
	echo '时间: '.intval($media->getDuration())." сек<br />";
	echo "比特率: ".ceil(($media->getBitRate())/1024)." KBPS<br />";
	if($media->getAudioChannels()==1)echo "Тип: Mono<br />";else echo "Тип: Stereo<br />";
	echo '抽样调查: '.$media->getAudioSampleRate()." Гц<br />";
	if(($media->getArtist())<>"")
	{
		if (function_exists('iconv'))
		echo '遗嘱执行人: '.iconv('windows-1251', 'utf-8', $media->getArtist())."<br />";
		else
		echo '遗嘱执行人: '.$media->getArtist()."<br />";
	}
	if(($media->getGenre())<>"")echo '类型: '.$media->getGenre()."<br />";
}

echo "大小: ".size_file($size)."<br />";
?>