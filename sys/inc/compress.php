<?

function compress_output_gzip($output){return gzencode($output,9);}
function compress_output_deflate($output){return gzdeflate($output, 9);}
// сжатие по умолчанию
$Content_Encoding['deflate']=false;
$Content_Encoding['gzip']=false;
// включение сжатия, если поддерживается браузером
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match('#deflate#i',$_SERVER['HTTP_ACCEPT_ENCODING']))
$Content_Encoding['deflate']=true;
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match('#gzip#i',$_SERVER['HTTP_ACCEPT_ENCODING']))
$Content_Encoding['gzip']=true;
// Непосредственное включение сжатия
if ($Content_Encoding['deflate'])
{
header("Content-Encoding: deflate");
ob_start("compress_output_deflate");
}
elseif($Content_Encoding['gzip'])
{
header("Content-Encoding: gzip");
ob_start("compress_output_gzip");
}
else
ob_start(); // если нет сжатия, то просто буферизация данных

$compress=true;
?>