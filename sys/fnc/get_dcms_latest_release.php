<?php
/**
 * 获取最新的发行版稳定版本信息
 *
 * @return array 包含最新版本信息或错误信息的数组
 */
function getLatestStableRelease() {
	// 设置API的URL
	$api_url = "https://api.guguan.us.kg/dcms_github_releases.php";

	// 执行cURL请求并获取响应数据
	$response = execute_curl_request($api_url);

	// 解析JSON响应数据
	if (isset($response['error'])) {
		return [
			'success' => false,
			'error' => 'Failed to fetch release data.'
		];
	} else {
		$release_data = json_decode($response, true);
	}

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
