<?php
require_once 'sys/inc/start.php';
require_once 'sys/inc/compress.php';
require_once 'sys/inc/sess.php';
require_once 'sys/inc/home.php';
require_once 'sys/inc/settings.php';
require_once 'sys/inc/db_connect.php';
require_once 'sys/inc/ipua.php';
require_once 'sys/inc/fnc.php';
require_once 'sys/inc/user.php';
require_once 'sys/inc/thead.php';

$path = "."; 
// 获取字节数
$totalByte = disk_total_space($path);
$freeByte = disk_free_space($path);
$usedByte = $totalByte - $freeByte;
// 计算使用百分比
$usedPercent = round(($usedByte / $totalByte) * 100, 2);
function formatSize($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    for ($i = 0; $bytes >= 1024 && $i < 4; $i++) {
        $bytes /= 1024;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

title();
aut();
err();
?>

当前版本：<?php echo $set['dcms_version']; ?><br>
siteurl：<?php echo $set['siteurl']; ?><br>
PHP 时间：<?php echo date('Y-m-d H:i:s'); ?><br>
数据库时间：<?php echo dbresult(dbquery("SELECT NOW() AS db_time"), 0, 'db_time'); ?><br>
数据库时区：<?php echo date_default_timezone_get(); ?><br>
网站当前时区：<?php echo date_default_timezone_get(); ?><br>
<hr>
总空间: <?php echo formatSize($totalByte); ?><br>
已用空间: <?php echo formatSize($usedByte) . ' (' . $usedPercent . '%)'; ?><br>
剩余空间: <?php echo formatSize($freeByte); ?><br>
<hr>
当前用户ID：<?php echo $user['id'] ?? 'N/A' ?><br>
当前登录方式为：<?php echo $user['type_input'] ?? 'N/A' ?><br>
当前设备类型为：<?php echo $webbrowser ? 'PC' : 'NoPC'; ?><br>
当前设备UA为：<?php echo $ua; ?><br>
当前设备IP为：<?php echo $ip; ?><br>

<?php
require_once 'sys/inc/tfoot.php';
