<?
if (is_file(H."sys/obmen/screens/128/$file_id[id].$ras"))
{
	echo "<img src='/sys/obmen/screens/128/$file_id[id].$ras' alt='Скрин...' /><br />\n";
}
elseif (function_exists('imagecreatefromstring'))
{
	$imgc=imagecreatefromstring(file_get_contents($file));
	$img_x=imagesx($imgc);
	$img_y=imagesy($imgc);
	if ($img_x==$img_y)
	{
		$dstW=128; // ширина
		$dstH=128; // высота 
	}
	elseif ($img_x>$img_y)
	{
		$prop=$img_x/$img_y;
		$dstW=128;
		$dstH=ceil($dstW/$prop);
	}
	else
	{
		$prop=$img_y/$img_x;
		$dstH=128;
		$dstW=ceil($dstH/$prop);
	}

	$screen=imagecreatetruecolor($dstW, $dstH);
	imagecopyresampled($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
	imagedestroy($imgc);
	$screen=img_copyright($screen); // наложение копирайта
	imagejpeg($screen,H."sys/obmen/screens/128/$file_id[id].$ras",90);
	imagedestroy($screen);
	echo "<img src='/sys/obmen/screens/128/$file_id[id].$ras' alt='Скрин...' /><br />\n";
}

if ($file_id['opis']!=NULL)
{
	echo "Описание: ";
	echo output_text($file_id['opis']);
	echo "<br />\n";
}

if (function_exists('getimagesize'))
{
	$img_size=getimagesize($file);
	echo "Разрешение: $img_size[0]*$img_size[1] пикс.<br />\n";
}

echo "Добавлен: ".vremja($file_id['time'])."<br />\n";
echo "Размер: ".size_file($size)."<br />\n";
?>