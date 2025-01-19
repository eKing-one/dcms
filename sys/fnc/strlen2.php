<?php
// 计算字符串长度
function strlen2($str) {
	return iconv_strlen($str,"UTF-8");
}