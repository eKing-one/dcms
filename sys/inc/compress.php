<?php
// 定义压缩输出的函数
function compress_output_gzip($output) {return gzencode($output, 9);}
function compress_output_deflate($output) {return gzdeflate($output, 9);}
// 默认压缩方式
$Content_Encoding['deflate'] = false;
$Content_Encoding['gzip'] = false;
// 如果浏览器支持，启用压缩
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match('#deflate#i',$_SERVER['HTTP_ACCEPT_ENCODING'])) $Content_Encoding['deflate'] = true;
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match('#gzip#i',$_SERVER['HTTP_ACCEPT_ENCODING'])) $Content_Encoding['gzip'] = true;
// 直接启用压缩
if ($Content_Encoding['deflate']) {
    header("Content-Encoding: deflate");
    ob_start("compress_output_deflate");
} elseif($Content_Encoding['gzip']) {
    header("Content-Encoding: gzip");
    ob_start("compress_output_gzip");
} else {
    ob_start(); // 如果不压缩，则仅进行数据缓冲
}
$compress = true;