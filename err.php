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
<title>Ошибка $err</title>\n";
echo "<link rel=\"stylesheet\" href=\"/style/themes/default/style.css\" type=\"text/css\" />\n";
echo "</head>\n<body>\n<div class=\"body\"><div class=\"err\">\n";
if ($err=='400')echo "Обнаруженная ошибка в запросе\n";
elseif ($err=='401')echo "Нет прав для выдачи документа\n";
elseif ($err=='402')echo "Не реализованный код запроса\n";
elseif ($err=='403')echo "Доступ запрещен\n";
elseif ($err=='404')echo "Нет такой страницы\n";
elseif ($err=='500')echo "Внутренняя ошибка сервера\n";
elseif ($err=='502')echo "Сервер получил недопустимые ответы другого сервера\n";
else echo "Неизвестная ошибка\n";
echo "<br />";
echo "<a href=\"/index.php\">На главную</a>";
echo "</div>\n</div>\n</body>\n</html>";
exit;
}
else
header ("Location: /index.php?".SID);
exit;
?>