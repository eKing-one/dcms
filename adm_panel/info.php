<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_info', null, 'index.php?' . SID);
adm_check();
$set['title'] = '一般资料';
include_once '../sys/inc/thead.php';
title();
err();
aut();
include_once H . 'sys/inc/testing.php';
echo "<hr />";
include_once H . 'sys/inc/chmod_test.php';
if (isset($err)) {
    if (is_array($err)) {
        foreach ($err as $key => $value) {
            echo "<div class='err'>$value</div>";
        }
    } else
        echo "<div class='err'>$err</div>";
}
if (user_access('adm_panel_show')) {
    echo "<div class='foot'>";
    echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
    echo "</div>";
}
include_once '../sys/inc/tfoot.php';

?>
