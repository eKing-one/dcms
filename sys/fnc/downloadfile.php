<?php
function DownloadFile($filename, $name, $mimetype = null, $inline = false, $headers = []) {
	// 检查文件
	if (!file_exists($filename)) {
		http_response_code(404);
		die('Error: File not found.');
	}

	if (is_dir($filename)) {
		http_response_code(403);
		die('Error: Cannot download a directory.');
	}

	ob_end_clean();
	$size = filesize($filename);
	$from = 0;
	$to = $size - 1; // 默认到文件末尾
	$fileMd5 = md5_file($filename);

	// 处理 Range 请求
	if (isset($_SERVER['HTTP_RANGE'])) {
		if (preg_match('#bytes=([0-9]+)-([0-9]+)#i', $_SERVER['HTTP_RANGE'], $range)) {
			if ($range[1] > $to || $range[2] > $to) {
				http_response_code(416);
				exit;
			}
			$from = $range[1];
			$to = $range[2];
		} elseif (preg_match('#bytes=([0-9]+)-#i', $_SERVER['HTTP_RANGE'], $range)) {
			if ($range[1] > $to) {
				http_response_code(416);
				exit;
			}
			$from = $range[1];
		} elseif (preg_match('#bytes=-([0-9]+)#i', $_SERVER['HTTP_RANGE'], $range)) {
			if ($range[1] > $to) {
				http_response_code(416);
				exit;
			}
			$from = $size - $range[1];
		}
		http_response_code(206);
		header('Content-Range: bytes ' . $from . '-' . $to . '/' . $size);
	}

	// 默认请求头
	$defaultHeaders = [
		'ETag' => '"' . $fileMd5 . '"',
		'Accept-Ranges' => 'bytes',
		'Content-Length' => $to - $from + 1,
		'Content-Type' => $mimetype ?: (mime_content_type($filename) ?: 'application/octet-stream'),
		'Last-Modified' => gmdate('D, d M Y H:i:s', filemtime($filename)) . ' GMT',
		'Content-Disposition' => ($inline ? 'inline' : 'attachment' . '; filename="' . rawurlencode($name) . '"')
	];

	// 合并用户提供的请求头，优先使用输入的设置
	$finalHeaders = array_merge($defaultHeaders, $headers);

	// 设置所有请求头
	foreach ($finalHeaders as $key => $value) {
		// 清理请求头值，防止 CRLF 注入
		$value = str_replace(["\r", "\n"], '', $value);
		header("$key: $value");
	}

	// 检查浏览器缓存
	if ((isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= filemtime($filename)) ||
		(isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $fileMd5)) {
		http_response_code(304);
		exit;
	}

	// 打开文件并读取内容
	$f = fopen($filename, 'rb');
	if (fseek($f, $from, SEEK_SET) !== 0) {
		http_response_code(500);
		error_log('File read failed: ' . $filename);
		exit;
	}

	// 启动下载，分块传输
	$downloaded = 0;
	while (!feof($f) && connection_status() === CONNECTION_NORMAL && ($downloaded < ($to - $from + 1))) {
		$block = min(1024 * 8, ($to - $from + 1) - $downloaded);
		echo fread($f, $block);
		$downloaded += $block;
		flush();
	}

	fclose($f);
	exit;
}
