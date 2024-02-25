<?
if (test_file(H."files/screens/128/$file_id[id].$ras"))
{
	echo "<img src='/files/screens/128/$file_id[id].$ras' alt='Скрин...' /><br />";
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
	imagegif($screen,H."files/screens/128/$file_id[id].$ras");
	imagedestroy($screen);
	echo "<img src='/files/screens/128/$file_id[id].$ras' alt='Скрин...' /><br />";
}
if ($file_id['opis']!=NULL)
{
	echo "资料描述: ";
	echo output_text($file_id['opis']);
	echo "<br />";
}
if (function_exists('getimagesize'))
{
	$img_size=getimagesize($file);
	echo "许可: $img_size[0]*$img_size[1] пикс.<br />";
}
echo "上传时间: ".vremja($file_id['time'])."<br />";
echo "大小: ".size_file($size)."<br />";
?>