<?php
// 定义压缩输出的函数
function compress_output_gzip($output) {return gzencode($output, 9);}
function compress_output_deflate($output) {return gzdeflate($output, 9);}

// 如果浏览器支持，启用压缩（已禁用）
// 压缩由 Web 服务器（如 Nginx 或 Apache）处理，而不是在 PHP 脚本中实现
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match('#deflate#i', $_SERVER['HTTP_ACCEPT_ENCODING']) && false) {
	header("Content-Encoding: deflate");
	ob_start("compress_output_deflate");
	$conf['compress'] = true;
} elseif (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match('#gzip#i', $_SERVER['HTTP_ACCEPT_ENCODING']) && false) {
	header("Content-Encoding: gzip");
	ob_start("compress_output_gzip");
	$conf['compress'] = true;
} else {
	ob_start(); // 如果不压缩，则仅进行数据缓冲
}