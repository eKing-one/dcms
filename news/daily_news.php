<?php
require_once '../sys/inc/start.php';
require_once '../sys/inc/compress.php';
require_once '../sys/inc/sess.php';
require_once '../sys/inc/home.php';
require_once '../sys/inc/settings.php';
require_once '../sys/inc/db_connect.php';
require_once '../sys/inc/ipua.php';
require_once '../sys/inc/fnc.php';
require_once '../sys/inc/user.php';
$set['title'] = '每日新闻';
require_once '../sys/inc/thead.php';
title();
aut();

/**
 * 获取数据并缓存
 */
function getCachedData($forceRefresh = false) {
	$cacheValidity = 3600; // 缓存有效时间：1小时
	$url = "https://60s-api.viki.moe/v2/60s";
	global $db;

	// 查询缓存数据
	$cachedData = $db->query('SELECT cache, time, url FROM daily_news_data LIMIT 1');

	// 使用封装好的函数处理查询结果
	if ($cachedData) { // dbassoc 返回关联数组
		$cachedTime = strtotime($cachedData['time']);

		// 检查缓存是否有效
		if (!$forceRefresh && time() - $cachedTime < $cacheValidity) {
			// 缓存有效，直接返回缓存的原始数据
			return $cachedData['cache'];
		}
	}

	// 缓存无效或不存在，调用API
	try {
		$response = execute_curl_request($cachedData['url'] ?? $url);
		if (isset($response['error'])) {
			throw new Exception('API Error: ' . $response['error']);
		}

		// 更新缓存
		$db->query("REPLACE INTO daily_news_data (id, cache, time) VALUES (1, ?, CURRENT_TIMESTAMP)", [$response]);

		return $response;
	} catch (Exception $e) {
		error_log($e->getMessage()); // 记录错误日志

		// 如果API请求失败，返回过期缓存
		if (!empty($cachedData)) {
			return $cachedData['cache'];
		}

		throw new Exception("Failed to fetch data and no valid cache available: " . $e->getMessage());
	}
}

if ($set['daily_news'] == '1') {
	$forceRefresh = false;
	if (user_access('adm_news') && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['force_refresh'])) {
		// 强制刷新数据
		$forceRefresh = true;
	}

	try {
		// 请求API获取数据
		$data = json_decode(getCachedData($forceRefresh), true);

		// 确认数据正常加载
		if ($data['code'] !== 200) {
			if (isset($data['message'])) {
				$err = 'API 报错：' . $data['message'];
			} else {
				$err = '无法加载新闻数据，请稍后重试！';
			}
		}

	} catch (Exception $e) {
		$err = $e->getMessage();
	}

	err();

	if ($forceRefresh) {
		msg('数据已刷新');
	}

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
		.refresh-form {
			text-align: center;
			margin: 20px 0;
		}
	</style>
	<div class="container">
		<?php if (isset($data['data']['news'])): ?><h2>今日新闻</h2><?php endif; ?>
		<?php if (isset($data['data']['updated_at'])): ?><p>更新时间：<?= htmlspecialchars(date("Y-m-d H:i:s", $data['data']['updated_at'] / 1000)) ?><?php endif; ?>
		<?php if (isset($data['data']['news'])): foreach ($data['data']['news'] as $news): ?>
			<div class="news-item"><?= htmlspecialchars($news) ?></div>
		<?php endforeach; endif; ?>
		<?php if (isset($data['data']['tip'])): ?><div class="tip">微语：<?= htmlspecialchars($data['data']['tip']) ?></div><?php endif; ?>
	</div>
	<?php if (user_access('adm_news')): ?><div class="refresh-form"><form method="POST"><button type="submit" name="force_refresh">强制刷新</button></form></div><?php endif; ?>
	<div class="footer">
		<?php if (isset($data['data']['link']) && filter_var($data['data']['link'], FILTER_VALIDATE_URL)): ?><div class="sourceUrl">来源：<a href="<?= htmlspecialchars($data['data']['link']) ?>" target="_blank">微信公众号文章</a></div><?php endif; ?>
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
require_once '../sys/inc/tfoot.php';