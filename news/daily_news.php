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
err();


// 请求API获取数据
$url = "https://60s.viki.moe/60s?v2=1";
$response = file_get_contents($url);
$data = json_decode($response, true);

// 确认数据正常加载
if ($data['status'] !== 200) {
    die("无法加载新闻数据，请稍后重试！");
}

$newsList = $data['data']['news'];
$tip = $data['data']['tip'];
$cover = $data['data']['cover'];
$updateTime = date("Y-m-d H:i:s", $data['data']['updated'] / 1000);
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
    <img src="<?= htmlspecialchars($cover) ?>" alt="封面图片" class="cover">
    <h2>今日新闻</h2>
    <p>更新时间：<?= htmlspecialchars($updateTime) ?></p>
    <?php foreach ($newsList as $news): ?>
        <div class="news-item"><?= htmlspecialchars($news) ?></div>
    <?php endforeach; ?>
    <div class="tip">微语：<?= htmlspecialchars($tip) ?></div>
</div>
<div class="footer">
    数据来源于官方 | <a href="https://github.com/vikiboss/60s" target="_blank">开源地址</a>
</div>


<?php
include_once '../sys/inc/tfoot.php';