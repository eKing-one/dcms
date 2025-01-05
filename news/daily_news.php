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
	// 缓存有效时间，单位：秒（1小时）
	$cacheValidity = 3600;

	// 查询缓存数据
	$result = dbquery("SELECT data, time FROM daily_news_cache LIMIT 1");

	if (!empty($result)) {
		// 数据库返回的结果
		$cachedData = $result[0];
		$cachedTime = $cachedData['time'];

		// 检查缓存是否有效
		if (time() - $cachedTime < $cacheValidity) {
			// 缓存有效，直接返回缓存的原始数据
			return $cachedData['data'];
		}
	}

	// 如果缓存不存在或已过期，则请求 API 并更新缓存
	$url = "https://60s.viki.moe/60s?v2=1";
	$response = file_get_contents($url);

	if ($response === false) {
		// 如果 API 请求失败，返回错误信息
		throw new Exception("Failed to fetch data from API.");
	}

	// 将新数据存入缓存（覆盖旧数据）
	$escapedData = addslashes($response); // 防止数据中的特殊字符导致 SQL 问题
	$currentTime = time();
	dbquery("DELETE FROM daily_news_cache"); // 清除旧缓存
	dbquery("INSERT INTO daily_news_cache (data) VALUES ($escapedData)");

	// 返回最新数据
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
		<?php if ($cover && filter_var($cover, FILTER_VALIDATE_URL)): ?><img src="<?= htmlspecialchars($cover) ?>" alt="封面图片" class="cover"><?php endif; ?>
		<h2>今日新闻</h2>
		<?php if ($updateTime): ?><p>更新时间：<?= htmlspecialchars($updateTime) ?></p><?php endif; ?>
		<?php if (is_array($data['data']['news'])): foreach ($data['data']['news'] as $news): ?>
			<div class="news-item"><?= htmlspecialchars($news) ?></div>
		<?php endforeach; endif; ?>
		<?php if ($data['data']['tip']): ?><div class="tip">微语：<?= htmlspecialchars($data['data']['tip']) ?></div><?php endif; ?>
	</div>
	<div class="footer">
		<div class="sourceUrl">来源：<?php if ($data['data']['url']): ?><a href="<?= htmlspecialchars($data['data']['url']) ?>" target="_blank">知乎文章</a></div><?php endif; ?>
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