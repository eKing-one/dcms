<?php
require_once '../sys/inc/start.php';
require_once '../sys/inc/compress.php';
require_once '../sys/inc/sess.php';
require_once '../sys/inc/home.php';
require_once '../sys/inc/settings.php';
require_once '../sys/inc/db_connect.php';
require_once '../sys/inc/ipua.php';
$ban_ip_page = true; // 这样就不会有循环
require_once '../sys/inc/fnc.php';
require_once '../sys/inc/user.php';
$set['title'] = '你的 IP 被封锁';
require_once '../sys/inc/thead.php';
title();
$err = "<h1>你的 IP ({$ip}) 已被封锁</h1>";
err();
aut();

echo '<h2>可能的原因:</h2>
    1）从同一个 IP 地址频繁访问服务器<br />
    2)您的 IP 地址与入侵者的地址匹配<br />
    <h2>解决方法:</h2>
    1) 重新启动您的互联网连接。<br />
    2）在静态 IP 地址的情况下，您可以使用代理服务器。
    <h2>如对该封锁有任何异议：</h2>
    请立即加入 CN_DCMS-Social 的官方 QQ 群组：310379632，说明来意，并详细提供封锁信息。管理团队会尽快处理。
    <br />';

require_once '../sys/inc/tfoot.php';