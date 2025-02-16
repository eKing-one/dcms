<?php

/**
 * 此函数已弃用！！！！
 * 
 * 计算并返回输入字符串的加密哈希值。
 *
 * @param string $str 输入的字符串。
 * @return string 返回加密后的哈希值。
 */
function shif($str) {
	// 触发弃用警告
	trigger_error("Function 'shif' is deprecated and insecure. Please avoid using it.", E_USER_DEPRECATED);

	// 引入全局变量 $set
	global $set;
	// 获取加密所使用的密钥，来自全局变量 $set
	$key = $set['shif'];
	// 对输入的字符串 $str 进行 MD5 哈希运算
	$str1 = md5((string) $str);
	// 对密钥 $key 进行 MD5 哈希运算
	$str2 = md5($key);
	// 将密钥、加密后的字符串和密钥组合起来，再进行一次 MD5 哈希加密，返回最终结果
	return md5($key . $str1 . $str2 . $key);
}


/**
 * 此函数已弃用！！！！
 * 
 * 对传入的字符串进行加密处理。
 *
 * 使用 mcrypt 或 openssl 进行加密，并返回经过 Base64 编码的加密字符串。
 * 加密时使用的密钥基于用户 ID 和 HTTP 用户代理生成，IV（初始化向量）存储在文件中。
 *
 * @param string $str 需要加密的字符串。
 * @param int $id 可选的用户 ID，默认为 0。
 * @return string 返回加密后的字符串，经过 Base64 编码。
 */
function cookie_encrypt($str, $id = 0) {
	// 触发弃用警告
	trigger_error("Function 'cookie_encrypt' is deprecated and insecure. Please avoid using it.", E_USER_DEPRECATED);

	global $set, $ua;

	// 确保密钥和IV的长度符合要求
	$key = substr(hash('sha256', $set['shif']), 0, 32);
	$iv = substr(hash('sha256', $id . $ua), 0, 16);

	// 返回Base64编码的加密数据
	return base64_encode(openssl_encrypt($str, 'AES-256-CBC', $key, 0, $iv));
}


/**
 * 此函数已弃用！！！！
 * 
 * 对加密的字符串进行解密操作。
 *
 * 使用 mcrypt 或 openssl 进行解密，返回解密后的原始字符串。
 * 解密时需要与加密时相同的密钥和初始化向量（IV）。
 *
 * @param string $str 需要解密的字符串，经过 Base64 编码。
 * @param int $id 可选的用户 ID，默认为 0。
 * @return string 返回解密后的字符串。
 */
function cookie_decrypt($str, $id = 0) {
	// 触发弃用警告
	trigger_error("Function 'cookie_decrypt' is deprecated and insecure. Please avoid using it.", E_USER_DEPRECATED);

	global $set, $ua;

	// 确保密钥和IV的长度符合要求
	$key = substr(hash('sha256', $set['shif']), 0, 32);
	$iv = substr(hash('sha256', $id . $ua), 0, 16);
	
	// 解密数据并返回
	return openssl_decrypt(base64_decode($str), 'AES-256-CBC', $key, 0, $iv);
}
