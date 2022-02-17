<?php
$time=time();
ini_set('register_globals', 0);
ini_set('session.use_cookies', 1);
ini_set('session.use_trans_sid', 1);
ini_set('arg_separator.output', "&amp;");
function compress_output_gzip($output)
{
    return gzencode($output,9);
}
function compress_output_deflate($output)
{
return gzdeflate($output, 9);
}
// сжатие по умолчанию
$Content_Encoding['deflate']=false;
$Content_Encoding['gzip']=false;
// включение сжатия, если поддерживается браузером
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match('#deflate#',$_SERVER['HTTP_ACCEPT_ENCODING']))$Content_Encoding['deflate']=true;
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match('#gzip#',$_SERVER['HTTP_ACCEPT_ENCODING']))$Content_Encoding['gzip']=true;
// Непосредственное включение сжатия
if ($Content_Encoding['deflate']){header("Content-Encoding: deflate");ob_start("compress_output_deflate");}
elseif($Content_Encoding['gzip']){header("Content-Encoding: gzip");ob_start("compress_output_gzip");}
else ob_start(); // если нет сжатия, то просто буферизация данных
session_name('SESS');
session_start();
$sess=mysql_real_escape_string(session_id());
header("HTTP/1.0 404 Not Found");
header("Status: 404 Not Found");
header("Refresh: 3; url=/index.php");
if (isset($_GET['err']) && is_numeric($_GET['err']))
{
$err=intval($_GET['err']);
header("Content-type: text/html",NULL,$err);
echo "<html>
<head>
<title>错误 $err</title>";
echo "<link rel=\"stylesheet\" href=\"/style/themes/default/style.css\" type=\"text/css\" />";
echo "</head><body><div class=\"body\"><div class=\"err\">";
if ($err=='400')echo "检测到请求中的错误";
elseif ($err=='401')echo "无权签发文件";
elseif ($err=='402')echo "未实现的请求代码";
elseif ($err=='403')echo "拒绝进入";
elseif ($err=='404')echo "没有这样的页面";
elseif ($err=='500')echo "内部服务器错误";
elseif ($err=='502')echo "服务器收到来自另一个服务器的无效响应";
else echo "未知错误";
echo "<br />";
echo "<a href=\"/index.php\">到主页</a>";
echo "</div></div></body></html>";
exit;
}
else
header ("Location: /index.php?".SID);
exit;
?>