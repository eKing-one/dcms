<?php
// curl相关函数
function execute_curl_request($url, $post_data=null, $referer=null, $cookie=null, $header=false, $ua=null, $nobody=false, $addheader=null) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);			// 设置URL
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);	// 启用SSL证书验证
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);	// 启用SSL主机验证
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	// 将结果以字符串形式返回
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);  		// 设置超时10秒

	$httpheader = [
		"Accept: */*",
		"Accept-Encoding: gzip,deflate,sdch",
		"Accept-Language: zh-CN,zh;q=0.8",
		"Connection: close"
	];
	if ($addheader) {
		$httpheader = array_merge($httpheader, $addheader);
	}
	curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);

	if ($post_data) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	}

	if ($header) {
		curl_setopt($ch, CURLOPT_HEADER, true);
	}

	if ($cookie) {
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	}

	if ($referer) {
		curl_setopt($ch, CURLOPT_REFERER, $referer);
	}

	if ($ua) {
		curl_setopt($ch, CURLOPT_USERAGENT, $ua);
	} else {
		global $set;
		if (isset($set['dcms_version'])) curl_setopt($ch, CURLOPT_USERAGENT, "CN_DCMS/{$set['dcms_version']}");
	}

	if ($nobody) {
		curl_setopt($ch, CURLOPT_NOBODY, 1);
	}

	$ret = curl_exec($ch);

	if (curl_errno($ch)) {
		$error_msg = curl_error($ch);
		curl_close($ch);
		return ['error' => $error_msg];
	}

	curl_close($ch);
	return $ret;
}