<?php
include_once 'sys/inc/start.php';
include_once 'sys/inc/compress.php';
include_once 'sys/inc/sess.php';
include_once 'sys/inc/home.php';
include_once 'sys/inc/settings.php';
include_once 'sys/inc/db_connect.php';
include_once 'sys/inc/ipua.php';
include_once 'sys/inc/fnc.php';
include_once 'sys/inc/user.php';
include_once 'sys/inc/thead.php';
title();
aut();
?>

当前设备类型为：<?php echo $webbrowser ? 'PC' : 'NoPC'; ?>

文件类型：<?php echo ras_to_mime('jpg'); ?>

<?php
include_once 'sys/inc/tfoot.php';