<?

function DownloadFile($filename, $name, $mimetype='application/octet-stream')
{
if (!file_exists($filename))
die('Файл не найден');
@ob_end_clean();
$from=0;
$size=filesize($filename);
$to=$size;
if (isset($_SERVER['HTTP_RANGE']))
{
if (preg_match ('#bytes=-([0-9]*)#i',$_SERVER['HTTP_RANGE'],$range)) // если указан отрезок от конца файла
{
$from=$size-$range[1];
$to=$size;
}
elseif(preg_match('#bytes=([0-9]*)-#i',$_SERVER['HTTP_RANGE'],$range)) // если указана только начальная метка
{
$from=$range[1];
$to=$size;
}
elseif(preg_match('#bytes=([0-9]*)-([0-9]*)#i',$_SERVER['HTTP_RANGE'],$range)) // если указан отрезок файла
{
$from=$range[1];
$to=$range[2];
}
header('HTTP/1.1 206 Partial Content');


$cr='Content-Range: bytes '.$from .'-'.$to.'/'.$size;
}
else
header('HTTP/1.1 200 Ok');
$etag=md5($filename);
$etag=substr($etag, 0, 8) . '-' . substr($etag, 8, 7) . '-' . substr($etag, 15, 8);
header('ETag: "'.$etag.'"');
header('Accept-Ranges: bytes');
header('Content-Length: ' .($to-$from));
if (isset($cr))header($cr);
header('Connection: close');
header('Content-Type: ' . $mimetype);
header('Last-Modified: ' . gmdate('r', filemtime($filename)));
header("Last-Modified: ".gmdate("D, d M Y H:i:s", filemtime($filename))." GMT");
header("Expires: ".gmdate("D, d M Y H:i:s", time() + 3600)." GMT");
$f=fopen($filename, 'rb');


if (preg_match('#^image/#i',$mimetype))
header('Content-Disposition: filename="'.$name.'";');
else
header('Content-Disposition: attachment; filename="'.$name.'";');

fseek($f, $from, SEEK_SET);
$size=$to;
$downloaded=0;
while(!feof($f) and !connection_status() and ($downloaded<$size))
{
$block = min(1024*8, $size - $downloaded);
echo fread($f, $block);
$downloaded += $block;
flush();
}
fclose($f);
}


?>