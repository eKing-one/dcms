<?
if (is_file(H."sys/obmen/screens/128/$file_id[id].gif"))
{
	echo "<img src='/sys/obmen/screens/128/$file_id[id].gif' alt='Скрин...' /><br />\n";
}

if ($file_id['opis']!=NULL)
{
	echo "Описание: ";
	echo output_text($file_id['opis']);
	echo "<br />\n";
}

echo "Добавлен: ".vremja($file_id['time'])."<br />\n";

if (class_exists('ffmpeg_movie'))
{
	$media = new ffmpeg_movie($file);

	if (intval($media->getDuration())>3599)
	echo 'Время: '.intval($media->getDuration()/3600).":".date('s',fmod($media->getDuration()/60,60)).":".date('s',fmod($media->getDuration(),3600))."<br />\n";
	elseif (intval($media->getDuration())>59)
	echo 'Время: '.intval($media->getDuration()/60).":".date('s',fmod($media->getDuration(),60))."<br />\n";
	else
	echo 'Время: '.intval($media->getDuration())." сек<br />\n";
	echo "Битрейт: ".ceil(($media->getBitRate())/1024)." KBPS<br />\n";
	if($media->getAudioChannels()==1)echo "Тип: Mono<br />\n";else echo "Тип: Stereo<br />\n";
	echo 'Дискретизация: '.$media->getAudioSampleRate()." Гц<br />\n";
	if(($media->getArtist())<>"")
	{
		if (function_exists('iconv'))
		echo 'Исполнитель: '.iconv('windows-1251', 'utf-8', $media->getArtist())."<br />\n";
		else
		echo 'Исполнитель: '.$media->getArtist()."<br />\n";
	}
	if(($media->getGenre())<>"")echo 'Жанр: '.$media->getGenre()."<br />\n";
}

echo "Размер: ".size_file($size)."<br />\n";
?>