<?php
/**
 * 计算并返回输入字符串的加密哈希值。
 *
 * @param string $str 输入的字符串。
 * @return string 返回加密后的哈希值。
 */
function shif($str) {
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
	// 检查是否支持 mcrypt 加密模块
	if (function_exists('mcrypt_module_open')) {
		// 使用 rijndael-256 算法和 ofb 模式打开加密模块
		$td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
		// 从指定路径读取初始化向量（IV），如果文件不存在，则生成并保存一个新的 IV
		if (!$iv = @file_get_contents(H . 'sys/dat/shif_iv.dat')) {
			// 生成随机初始化向量
			$iv = base64_encode(mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_RANDOM));
			// 将 IV 保存到文件
			file_put_contents(H . 'sys/dat/shif_iv.dat', $iv);
			// 设置文件权限
			chmod(H . 'sys/dat/shif_iv.dat', 0644);
		}
		// 获取加密算法所需的密钥长度
		$ks = @mcrypt_enc_get_key_size($td);
		// 生成加密密钥，基于用户 ID 和 HTTP 用户代理（User-Agent）
		$key = substr(md5($id . @$_SERVER['HTTP_USER_AGENT']), 0, $ks);
		// 初始化加密模块，使用密钥和初始化向量（IV）
		@mcrypt_generic_init($td, $key, base64_decode($iv));
		// 对传入的字符串进行加密
		$str = @mcrypt_generic($td, $str);
		// 解初始化加密模块
		@mcrypt_generic_deinit($td);
		// 关闭加密模块
		@mcrypt_module_close($td);
	} else {
		// 如果没有 mcrypt 支持，使用 openssl 进行加密
		$ks = openssl_cipher_iv_length($method = 'AES-256-CBC');
		// 生成加密密钥，基于用户 ID 和 HTTP 用户代理
		$key = substr(md5($id . @$_SERVER['HTTP_USER_AGENT']), 0, $ks);
		// 读取或生成初始化向量（IV）
		if (!$iv = @file_get_contents(H . 'sys/dat/shif_iv.dat')) {
			// 生成一个新的随机 IV
			$iv = openssl_random_pseudo_bytes($ks);
			// 保存生成的 IV 到文件
			file_put_contents(H . 'sys/dat/shif_iv.dat', base64_encode($iv));
			// 设置文件权限
			chmod(H . 'sys/dat/shif_iv.dat', 0644);
		}
		// 使用 openssl 加密字符串
		$str = openssl_encrypt($str, $method, $key, $options = OPENSSL_RAW_DATA, base64_decode($iv));
	}
	// 将加密后的数据进行 Base64 编码并返回
	$str = base64_encode($str);
	return $str;
}

/**
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
	// 先进行 Base64 解码
	$str = base64_decode($str);
	// 检查是否支持 mcrypt 解密模块
	if (function_exists('mcrypt_module_open')) {
		// 使用 rijndael-256 算法和 ofb 模式打开解密模块
		$td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
		// 从文件中读取初始化向量（IV），如果文件不存在则生成新的 IV
		if (!$iv = @file_get_contents(H . 'sys/dat/shif_iv.dat')) {
			// 生成随机 IV
			$iv = base64_encode(mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_RANDOM));
			// 保存生成的 IV 到文件
			file_put_contents(H . 'sys/dat/shif_iv.dat', $iv);
			// 设置文件权限
			chmod(H . 'sys/dat/shif_iv.dat', 0644);
		}
		// 获取加密算法所需的密钥长度
		$ks = @mcrypt_enc_get_key_size($td);
		// 生成解密密钥，基于用户 ID 和 HTTP 用户代理
		$key = substr(md5($id . @$_SERVER['HTTP_USER_AGENT']), 0, $ks);
		// 初始化解密模块
		@mcrypt_generic_init($td, $key, base64_decode($iv));
		// 对加密数据进行解密
		$str = @mdecrypt_generic($td, $str);
		// 解初始化解密模块
		@mcrypt_generic_deinit($td);
		// 关闭解密模块
		@mcrypt_module_close($td);
	} else {
		// 如果没有 mcrypt 支持，使用 openssl 进行解密
		$ks = openssl_cipher_iv_length($method = 'AES-256-CBC');
		// 生成解密密钥，基于用户 ID 和 HTTP 用户代理
		$key = substr(md5($id . @$_SERVER['HTTP_USER_AGENT']), 0, $ks);
		// 读取或生成初始化向量（IV）
		if (!$iv = file_get_contents(H . 'sys/dat/shif_iv.dat')) {
			// 生成一个新的随机 IV
			$iv = openssl_random_pseudo_bytes($ks);
			// 保存生成的 IV 到文件
			file_put_contents(H . 'sys/dat/shif_iv.dat', base64_encode($iv));
			// 设置文件权限
			chmod(H . 'sys/dat/shif_iv.dat', 0644);
		}
		// 使用 openssl 解密字符串
		$str = openssl_decrypt($str, $method, $key, $options = OPENSSL_RAW_DATA, base64_decode($iv));
	}
	// 返回解密后的字符串
	return $str;
}
