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
// 默认压缩
$Content_Encoding['deflate']=false;
$Content_Encoding['gzip']=false;
// 浏览器支持时使用压缩
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match('#deflate#',$_SERVER['HTTP_ACCEPT_ENCODING']))$Content_Encoding['deflate']=true;
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match('#gzip#',$_SERVER['HTTP_ACCEPT_ENCODING']))$Content_Encoding['gzip']=true;
// 直接压缩使用
if ($Content_Encoding['deflate']){header("Content-Encoding: deflate");ob_start("compress_output_deflate");}
elseif($Content_Encoding['gzip']){header("Content-Encoding: gzip");ob_start("compress_output_gzip");}
else ob_start(); // 没有压缩时，只需进行数据缓冲即可。
session_name('SESS');
session_start();
$sess=my_esc(session_id()); // my_esc 未定义，需要联系 eKing 确认其用途后修改。--Diamochang
// header("HTTP/1.0 404 Not Found"); // Why 404 only? --Diamochang
// header("Status: 404 Not Found");
header("Refresh: 3; url=/index.php");
if (isset($_GET['err']) && is_numeric($_GET['err']))
{
$err=intval($_GET['err']);
http_response_code($err);
echo "<html>
<head>
<title>发生错误 ($err)</title>";
echo "<link rel=\"stylesheet\" href=\"/style/themes/default/style.css\" type=\"text/css\" />";
echo "</head><body><div class=\"body\"><div class=\"err\">";
if ($err=='400')echo "客户端发送了一个错误的请求";
elseif ($err=='401')echo "请求要求用户的身份认证";
elseif ($err=='402')echo "服务器拒绝服务直到用户支付费用"; // 此状态码不常用 --Diamochang
elseif ($err=='403')echo "拒绝访问";
elseif ($err=='404')echo "请求的页面未找到";
elseif ($err=='500')echo "内部服务器错误";
elseif ($err=='502')echo "服务器从上游服务器接收到了一个无效的响应";
else echo "未知错误";
echo "<br />";
echo "<a href=\"/index.php\">返回首页</a>";
echo "</div></div></body></html>";
exit;
}
else
header ("Location: /index.php?".SID);
exit;
