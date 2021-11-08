<?php
$pre_w = 120; // ширина превью изображения
$dir = array('tmp'=>H.'sys/tmp/', 'scr'=>'scr/'); // папки для временных файлов и скринов


function make_pre($dir_loads2,$file2){
	global $dir, $pre_w;
	$filename = $dir_loads2.'/'.$file2;
	$now = time();
	$xml = NULL;
	$scr_name = '';
	if(file_exists($filename))	{
		$file = $filename;
		$archive = new Archive_Tar($filename);
		$xml = $archive -> extractInString('Theme.xml');
		if($xml === NULL)		{
			$list = $archive -> listContent();
			if(is_array($list))			{
				if(preg_match('/\.xml$/i', $list[$i]['filename']))				{
					$xml = $archive -> extractInString($list[$i]['filename']);
					break;
				}
			}
		}
		if($xml !== NULL)		{
			if((preg_match('#<Standby_image Source="(.*?)"/>#si', $xml, $res) or preg_match('#<Desktop_image Source="(.*?)"/>#si', $xml, $res)) and !empty($res[1])) $scr_name=$res[1];
			unset($res);
			if(!empty($scr_name) and preg_match('/[a-z0-9]{3,4}$/i', $scr_name, $res))			{
				$scr_ext = strtolower($res[0]);
				$filename = $dir['tmp'].$now.rand(1,999) . '.' . $scr_ext;
				$fp = fopen($filename, 'wb');
				fputs($fp, $archive -> extractInString($scr_name));
				fclose($fp);
				@chmod($filename, 0666);
				$scr_stat = getimagesize($filename);				
				if($scr_stat !== false)				{
					if($scr_stat[0] > $pre_w)					{
						switch($scr_stat[2])						{
							case 1: //gif
							$i_scr = imagecreatefromgif($filename);
							break;
							case 2: //jpg
							$i_scr = imagecreatefromjpeg($filename);
							break;
							case 3: //png
							$i_scr = imagecreatefrompng($filename);
							break;
							default:
							$i_scr = '';
						}
												if(!empty($i_scr))						{
							$ratio = $scr_stat[0] / $pre_w;
							$pre_h = round($scr_stat[1] / $ratio);
							$i_pre = imagecreatetruecolor($pre_w, $pre_h);
							imagecopyresampled($i_pre, $i_scr, 0, 0, 0, 0, $pre_w, $pre_h, $scr_stat[0], $scr_stat[1]);
							$color1 = imagecolorallocate($i_pre, 255,250,50);
							$color2 = imagecolorallocate($i_pre, 0,0,0);
							  ##### ###  ####
							$font = '../SYSTEM/images/a.ttf';
							$text = '.::ZonArt::.';
							//imagepstext($i_pre,$text,$font,10,$color1,$color2,4,130,1,2,0,20);
							//$logo=imagecreatefrompng('../style/logo.png');
							///imagecopy($i_pre, $logo, 65, 85, 0, 0, 51, 45);
							//imagecopy($i_pre, $logo, 0, 0, 0, 0, 120, 120);
							//imagedestroy($logo);
							//imagecolorallocatealpha($alpha,0,0,255,120);

							$data = explode('/', $file);
							$var = $data[(sizeof($data) - 1)];
							$var = preg_replace('/[a-z]{3,4}$/i', 'thm.JPG', $var);
							//header('Content-type: image/jpeg');
							$var = $dir['scr'] . $var;
							@chmod($var, 0777);
							//imagejpeg($i_pre, $var);
							$i_pre = img_copyright($i_pre); // копирайт
							imagejpeg($i_pre, $dir_loads2 . '/' . $file2 . '.JPG', 100);
							imagedestroy($i_pre);
							imagedestroy($i_scr);
							unlink($filename);
							return $var;
						}						//else echo "<img src=\"../style/swf.jpg\" alt=\"SWF!\" /><br />\n";
					}					else echo'ненадо преобразований<br />';
				}				else echo'не изображение<br/>';
				unlink($filename);
			}			else echo'не найдены изображения для создания скрина<br/>';
		}		//else echo "<img src=\"../style/xml.jpg\" alt=\"XML не найден!\" /><br />\n";
	}	else echo'файл не найден<br />';
	clearstatcache();
	return false;
}
?>