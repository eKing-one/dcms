<?php
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
user_access('adm_mysql', null, 'index.php?' . session_id());
adm_check();
$set['title'] = 'MySQL 请求';
include_once '../sys/inc/thead.php';
title();

if (isset($_GET['set']) && $_GET['set'] == 'set' && isset($_POST['query'])) {
    $sql = trim($_POST['query']);
    if ($conf['phpversion'] == 5) {
        include_once H . 'sys/inc/sql_parser.php';
        $sql = SQLParser::getQueries($sql); // 在解析器的帮助下，查询被更准确地分解，但这只适用于php5
    } else {
        $sql = split(";(|\\r)*", $sql);
    }
    $k_z = 0;
    $k_z_ok = 0;
    for ($i = 0; $i < count($sql); $i++) {
        if ($sql[$i] != '') {
            $k_z++;
            if (dbquery($sql[$i])) {
                $k_z_ok++;
            }
        }
    }
    if ($k_z_ok > 0) {
        if ($k_z_ok == 1 && $k_z = 1) {
            msg("请求成功完成");
        } else {
            msg("完成了 $k_z_ok 来自 $k_z");
        }
        admin_log('管理面板', 'MySQL', "完成了 $k_z_ok 要求(s)");
    }
}
err();
aut();

echo "<form method=\"post\" action=\"mysql.php?set=set\">";
echo "<textarea name=\"query\" ></textarea><br />";
echo "<input value=\"执行\" type=\"submit\" />";
echo "</form>";

if (user_access('adm_panel_show')) {
    echo "<div class='foot'>";
    echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
    echo "&laquo;<a href='tables.php'>填写档案</a><br />";
    echo "</div>";
}
include_once '../sys/inc/tfoot.php';
