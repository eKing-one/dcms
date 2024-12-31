<?php
function shif($str) {
	global $set;
	$key=$set['shif'];
	$str1=md5((string)$str);
	$str2=md5($key);
	return md5($key.$str1.$str2.$key);
}
/*
 *
 * From Dcms-Social Next by ua0sqq
 */
function cookie_encrypt($str,$id=0) {
	if (function_exists('mcrypt_module_open') ) {
		$td = mcrypt_module_open ('rijndael-256', '', 'ofb', '');
		if (!$iv = @file_get_contents(H.'sys/dat/shif_iv.dat')) {
			$iv=base64_encode( mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_DEV_RANDOM));
			file_put_contents(H.'sys/dat/shif_iv.dat', $iv);
			chmod(H . 'sys/dat/shif_iv.dat', 0644);
		}
		$ks = @mcrypt_enc_get_key_size ($td);
		/* Создать ключ */
		$key = substr (md5 ($id.@$_SERVER['HTTP_USER_AGENT']), 0, $ks);
		@mcrypt_generic_init ($td, $key, base64_decode($iv));
		$str = @mcrypt_generic ($td, $str);
		@mcrypt_generic_deinit ($td);
		@mcrypt_module_close ($td);
	} else {
		$ks = openssl_cipher_iv_length($method = 'AES-256-CBC');
		$key = substr(md5($id.@$_SERVER['HTTP_USER_AGENT']), 0, $ks);
		if (!$iv = @file_get_contents(H . 'sys/dat/shif_iv.dat')) {
			$iv = openssl_random_pseudo_bytes($ks);
			file_put_contents(H . 'sys/dat/shif_iv.dat', base64_encode($iv));
			chmod(H . 'sys/dat/shif_iv.dat', 0644);
		}
		$str = openssl_encrypt($str, $method, $key, $options=OPENSSL_RAW_DATA, base64_decode($iv));
	}
	$str = base64_encode($str);
	return $str;
}
function cookie_decrypt($str,$id=0) {
	$str=base64_decode($str);
	if (function_exists('mcrypt_module_open')) {
		$td = mcrypt_module_open ('rijndael-256', '', 'ofb', '');
		if (!$iv = @file_get_contents(H.'sys/dat/shif_iv.dat')) {
			$iv = base64_encode( mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_DEV_RANDOM));
			file_put_contents(H.'sys/dat/shif_iv.dat', $iv);
			chmod(H.'sys/dat/shif_iv.dat', 0644);
		}
		$ks = @mcrypt_enc_get_key_size ($td);
		/* Создать ключ */
		$key = substr (md5 ($id.@$_SERVER['HTTP_USER_AGENT']), 0, $ks);
		@mcrypt_generic_init ($td, $key, base64_decode($iv));
		$str = @mdecrypt_generic ($td, $str);
		@mcrypt_generic_deinit ($td);
		@mcrypt_module_close ($td);
	} else {
		$ks = openssl_cipher_iv_length($method = 'AES-256-CBC');
		$key = substr(md5($id.@$_SERVER['HTTP_USER_AGENT']), 0, $ks);
		if (!$iv = file_get_contents(H . 'sys/dat/shif_iv.dat')) {
			$iv = openssl_random_pseudo_bytes($ks);
			file_put_contents(H . 'sys/dat/shif_iv.dat', base64_encode($iv));
			chmod(H . 'sys/dat/shif_iv.dat', 0644);
		}
		$str = openssl_decrypt($str, $method, $key, $options=OPENSSL_RAW_DATA, base64_decode($iv));
	}
	return $str;
}
