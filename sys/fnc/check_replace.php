<?php
/**
 * 检查并返回文件的替换路径或原始路径
 *
 * @param string $source2 输入的文件路径
 * @return string 返回替换后的文件路径或原始路径
 */
function check_replace($source2) {
	// 获取文件的真实路径，如果不存在则使用原始输入
	$source = realpath($source2);
	if (!file_exists($source)) $source = $source2;
	// 将路径中的目录分隔符统一替换为正斜杠
	$source = str_ireplace(DIRECTORY_SEPARATOR, "/", (string)$source);
	$h = str_ireplace(DIRECTORY_SEPARATOR, "/", H);
	$replace = str_ireplace(DIRECTORY_SEPARATOR, "/", REPLACE);
	// 计算替换路径
	$replace_file = str_ireplace($h, $replace, (string)$source);
	// 检查是否启用了替换功能
	if (function_exists('setget') && setget('replace', 1) == 1) {
		// 如果替换文件存在，返回替换路径
		if (file_exists($replace_file)) {
			return $replace_file;
		} else {
			// 否则返回原始路径
			return $source;
		}
	} else {
		// 未启用替换功能时，返回原始路径
		return $source;
	}
}
