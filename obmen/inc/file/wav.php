<?
if (test_file(H."sys/obmen/screens/128/$file_id[id].gif"))
{
	echo "<img src='/sys/obmen/screens/128/$file_id[id].gif' alt='Скрин...' /><br />";
}

if ($file_id['opis']!=NULL)
{
	echo "Описание: ";
	echo output_text($file_id['opis']);
	echo "<br />";
}

echo "Добавлен: ".vremja($file_id['time'])."<br />";

if (class_exists('ffmpeg_movie'))
{
	$media = new ffmpeg_movie($file);

	if (intval($media->getDuration())>3599)
	echo 'Время: '.intval($media->getDuration()/3600).":".date('s',fmod($media->getDuration()/60,60)).":".date('s',fmod($media->getDuration(),3600))."<br />";
	elseif (intval($media->getDuration())>59)
	echo 'Время: '.intval($media->getDuration()/60).":".date('s',fmod($media->getDuration(),60))."<br />";
	else
	echo 'Время: '.intval($media->getDuration())." сек<br />";
	echo "Битрейт: ".ceil(($media->getBitRate())/1024)." KBPS<br />";
	if($media->getAudioChannels()==1)echo "Тип: Mono<br />";else echo "Тип: Stereo<br />";
	echo 'Дискретизация: '.$media->getAudioSampleRate()." Гц<br />";
	if(($media->getArtist())<>"")
	{
		if (function_exists('iconv'))
		echo 'Исполнитель: '.iconv('windows-1251', 'utf-8', $media->getArtist())."<br />";
		else
		echo 'Исполнитель: '.$media->getArtist()."<br />";
	}
	if(($media->getGenre())<>"")echo 'Жанр: '.$media->getGenre()."<br />";
}

echo "Размер: ".size_file($size)."<br />";
?>