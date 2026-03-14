<?php
// 计算字符串长度
function strlen2($str) {
	if (extension_loaded('iconv')) {	// 检查 iconv 扩展是否可用
		// 使用 iconv_strlen()，如果 iconv 扩展可用
		return iconv_strlen($str, 'UTF-8');
	} elseif (extension_loaded('mbstring')) {	// 检查 mbstring 扩展是否可用
		// 使用 mb_strlen()，如果 mbstring 扩展可用
		return mb_strlen($str, 'UTF-8');
	} else {
		// 如果两者都不可用，使用 strlen() 来获取字节长度
		return strlen($str);
	}
}