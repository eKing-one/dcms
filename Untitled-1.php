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

当前设备类型为：<?php echo $webbrowser ? 'PC' : 'NoPC'; ?><br>
当前设备UA为：<?php echo my_esc($_SERVER['HTTP_USER_AGENT']); ?><br>
当前设备IP为：<?php echo $ip; ?><br>
当前网站根目录：<?php echo $_SERVER['DOCUMENT_ROOT']; ?><br>
123456：<?php echo password_hash('123456', PASSWORD_BCRYPT);?>

<?php
include_once 'sys/inc/tfoot.php';