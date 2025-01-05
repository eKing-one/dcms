<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
$set['title'] = '每日新闻';
include_once '../sys/inc/thead.php';
title();
aut();

/**
 * 获取数据并缓存
 */
function getCachedData() {
	$cacheValidity = 3600; // 缓存有效时间：1小时
	$url = "https://60s.viki.moe/60s?v2=1";

	// 查询缓存数据
	$result = dbquery("SELECT data, time FROM daily_news_cache LIMIT 1");

	// 使用封装好的函数处理查询结果
	if ($row = dbassoc($result)) { // dbassoc 返回关联数组
		$cachedData = $row;
		$cachedTime = strtotime($cachedData['time']);

		// 检查缓存是否有效
		if (time() - $cachedTime < $cacheValidity) {
			// 缓存有效，直接返回缓存的原始数据
			return $cachedData['data'];
		}
	}

	// 缓存无效或不存在，调用API
	try {
		$response = fetchFromAPI($url);

		if (empty($response)) {
			throw new Exception("Invalid API response.");
		}

		// 更新缓存
		dbquery("REPLACE INTO daily_news_cache (id, data, time) VALUES (1, '$response', CURRENT_TIMESTAMP)");

		return $response;
	} catch (Exception $e) {
		error_log($e->getMessage()); // 记录错误日志

		// 如果API请求失败，返回过期缓存
		if (!empty($cachedData)) {
			return $cachedData['data'];
		}

		throw new Exception("Failed to fetch data and no valid cache available.");
	}
}

/**
 * 通过cURL请求API
 */
function fetchFromAPI($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 设置超时时间
	$response = curl_exec($ch);

	if (curl_errno($ch)) {
		throw new Exception('cURL Error: ' . curl_error($ch));
	}

	curl_close($ch);

	return $response;
}

if ($set['daily_news'] == '1') {
	try {
		// 请求API获取数据
		$data = json_decode(getCachedData(), true);

		// 确认数据正常加载
		if ($data['status'] !== 200) {
			$err = '无法加载新闻数据，请稍后重试！';
		}

		$newsList = $data['data']['news'];
		$tip = $data['data']['tip'];
		$cover = $data['data']['cover'];
		$updateTime = date("Y-m-d H:i:s", $data['data']['updated'] / 1000);
	} catch (Exception $e) {
		$err = $e->getMessage();
	}

	err();

	?>

	<style>
		header {
			padding: 20px 10px;
			text-align: center;
		}
		header h1 {
			margin: 0;
			font-size: 24px;
		}
		.container {
			max-width: 800px;
			margin: 20px auto;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			padding: 20px;
		}
		.news-item {
			margin-bottom: 15px;
			padding-bottom: 10px;
			border-bottom: 1px solid #ddd;
		}
		.news-item:last-child {
			border-bottom: none;
		}
		.tip {
			margin: 20px 0;
			padding: 10px;
			border-left: 5px solid #0078d7;
			font-style: italic;
		}
		.footer {
			text-align: center;
			margin-top: 20px;
		}
		img.cover {
			width: 100%;
			border-radius: 8px;
		}
	</style>
	<div class="container">
		<?php if (filter_var($cover, FILTER_VALIDATE_URL)): ?><img src="<?= htmlspecialchars($cover) ?>" alt="封面图片" class="cover"><?php endif; ?>
		<h2>今日新闻</h2>
		<p>更新时间：<?= htmlspecialchars($updateTime) ?>
		<?php foreach ($data['data']['news'] as $news): ?>
			<div class="news-item"><?= htmlspecialchars($news) ?></div>
		<?php endforeach; ?>
		<div class="tip">微语：<?= htmlspecialchars($data['data']['tip']) ?></div>
	</div>
	<div class="footer">
		<div class="sourceUrl">来源：<?php if (filter_var($data['data']['url'], FILTER_VALIDATE_URL)): ?><a href="<?= htmlspecialchars($data['data']['url']) ?>" target="_blank">知乎文章</a></div><?php endif; ?>
		数据来源于公共API | <a href="https://github.com/vikiboss/60s" target="_blank">开源地址</a>
	</div>
<?php
} else {
	$err = '管理员已关闭每日新闻功能';
	err();
}

echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*"> <a href="index.php">新闻中心</a><br />';
echo '</div>';
include_once '../sys/inc/tfoot.php';