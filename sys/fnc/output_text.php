<?php
/**
 * 为输出到浏览器准备文本字符串（执行多种过滤和格式化处理）
 * 
 * 注意：出于安全考虑，强烈不建议随意修改此函数逻辑
 * 
 * @param string $str 原始输入字符串
 * @param int $br 是否处理换行（0/1）
 * @param int $html 是否转义HTML特殊字符（0/1）
 * @param int $smiles 是否转换表情符号（0/1）
 * @param int $links 是否处理超链接（0/1）
 * @param int $bbcode 是否解析BBCode标记（0/1）
 * @return string 处理后的安全字符串
 */
function output_text($str, $br = 1, $html = 1, $smiles = 1, $links = 1, $bbcode = 1) {
	global $theme_ini;
	$str = $str ?? ''; // PHP7空合并运算符，确保处理null值
	
	// HTML实体转义（转换所有特殊字符包括单双引号）
	if ($html) {
		$str = htmlentities($str, ENT_QUOTES, 'UTF-8');
	}
	
	// 自动检测并转换文本中的网址为可点击链接
	if ($links) {
		$str = links($str); // 假设links()是自定义链接处理函数
	}
	
	// 转换文本表情符号（如 :) → 😊）
	if ($smiles) {
		$str = smiles($str); // 假设smiles()是表情符号转换函数
	}
	
	// 解析BBCode标记（如 [b]文本[/b] → <strong>文本</strong>）
	if ($bbcode) {
		$tmp_str = $str; // 保存原始字符串（当前未使用，可能需要调试）
		$str = bbcode($str); // BBCode解析函数
	}
	
	// 转换换行符为HTML换行标签
	if ($br) {
		$str = br($str); // 假设br()处理换行的函数（如 nl2br()）
	}
	
	// 去除反斜杠转义（用于取消magic_quotes等自动转义）
	return stripslashes($str);
}

/**
 * 表单字段预处理（安全过滤但不处理换行和表情符号）
 * 
 * 适用于表单输入值的显示，保留基础HTML过滤但禁用部分格式处理
 * 
 * @param string $str 表单输入值
 * @return string 安全过滤后的字符串
 */
function input_value_text($str) {
	// 参数顺序对应：$br=0, $html=1, $smiles=0, $links=0, $bbcode=0
	return output_text($str, 0, 1, 0, 0, 0);
}

/**
 * 文本摘要生成器（第1级截断 - 短版）
 * 
 * 用于生成内容预览摘要，包含结尾标记 »（显示更多指示符）
 * 
 * @param string $text 原始文本
 * @param int $maxwords 允许的最大单词数（默认15个单词）
 * @param int $maxchar 允许的最大字符数（默认100字符）
 * @return string 处理后的摘要文本
 */
function rez_text($text, $maxwords = 15, $maxchar = 100) {
	$sep = ' ';      // 单词分隔符
	$sep2 = ' &raquo;'; // 结尾标识符（HTML右箭头）
	$words = explode($sep, $text);
	$char = iconv_strlen($text, 'utf-8'); // UTF-8兼容的字符计数

	// 按单词数截断
	if (count($words) > $maxwords) {
		$text = join($sep, array_slice($words, 0, $maxwords));
	}

	// 按字符数截断（使用多字节安全方法）
	if ($char > $maxchar) {
		$text = iconv_substr($text, 0, $maxchar, 'utf-8');
	}

	return output_text($text) . $sep2;
}

/**
 * 文本摘要生成器（第2级截断 - 中版）
 * 
 * 适用于中等长度的内容预览（默认70单词/700字符，无结尾标记）
 */
function rez_text2($text, $maxwords = 70, $maxchar = 700) {
	$sep = ' ';
	$sep2 = '';  // 不添加结尾标识符
	$words = explode($sep, $text);
	$char = iconv_strlen($text, 'utf-8');

	// 处理逻辑同上
	if (count($words) > $maxwords) {
		$text = join($sep, array_slice($words, 0, $maxwords));
	}
	if ($char > $maxchar) {
		$text = iconv_substr($text, 0, $maxchar, 'utf-8');
	}

	return output_text($text) . $sep2;
}

/**
 * 文本摘要生成器（第3级截断 - 长版）
 * 
 * 适用于长文本的完整展示（默认150单词/1500字符，无结尾标记）
 */
function rez_text3($text, $maxwords = 150, $maxchar = 1500) {
	$sep = ' ';
	$sep2 = '';
	$words = explode($sep, $text);
	$char = iconv_strlen($text, 'utf-8');

	if (count($words) > $maxwords) {
		$text = join($sep, array_slice($words, 0, $maxwords));
	}
	if ($char > $maxchar) {
		$text = iconv_substr($text, 0, $maxchar, 'utf-8');
	}

	return output_text($text) . $sep2;
}
