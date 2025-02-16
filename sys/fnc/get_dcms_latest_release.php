<?php
/**
 * 获取最新的发行版稳定版本信息
 *
 * @return array 包含最新版本信息或错误信息的数组
 */
function getLatestStableRelease() {
	// 设置API的URL
	$api_url = "https://api.guguan.us.kg/dcms_github_releases.php";
	
	// 初始化cURL会话
	$ch = curl_init();

	// 设置cURL选项
	curl_setopt($ch, CURLOPT_URL, $api_url);         // 设置请求的URL
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // 返回作为字符串，而不是直接输出
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 忽略SSL证书检查（如果使用的是https）

	// 执行cURL请求并获取响应数据
	$response = curl_exec($ch);

	// 检查是否有错误
	if (curl_errno($ch)) {
		$error_message = 'cURL Error: ' . curl_error($ch);
		curl_close($ch);
		return [
			'success' => false,
			'error' => $error_message
		];
	}

	// 关闭cURL会话
	curl_close($ch);

	// 解析JSON响应数据
	$release_data = json_decode($response, true);

	// 检查响应是否有效
	if (!is_array($release_data) || count($release_data) === 0) {
		return [
			'success' => false,
			'error' => 'No release data found.'
		];
	}

	// 初始化变量以保存最新稳定版本的数据
	$latest_stable_release = null;
	$latest_published_time = null;

	// 遍历所有发布，查找最新的稳定版本
	foreach ($release_data as $release) {
		// 检查该版本是否为非预发布且非草稿
		if (!$release['prerelease'] && !$release['draft']) {
			// 获取发布时间
			$published_at = strtotime($release['published_at']);

			// 如果这是第一个找到的稳定版本，或发布时间比当前保存的更晚，则更新
			if ($latest_stable_release === null || $published_at > $latest_published_time) {
				$latest_stable_release = $release;
				$latest_published_time = $published_at;
			}
		}
	}

	// 检查是否找到了稳定版本
	if ($latest_stable_release !== null) {
		// 返回最新稳定版本的版本号和ZIP源码包下载链接
		return [
			'success' => true,
			'version' => $latest_stable_release['tag_name'],
			'zip_url' => $latest_stable_release['zipball_url']
		];
	} else {
		return [
			'success' => false,
			'error' => 'No stable release found.'
		];
	}
}
