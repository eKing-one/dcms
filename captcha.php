<?php
//include_once 'sys/inc/start.php';
//include_once 'sys/inc/sess.php';
//include_once 'sys/inc/home.php';
//include_once 'sys/inc/settings.php';
//include_once 'sys/inc/db_connect.php';
//include_once 'sys/inc/ipua.php';
//include_once 'sys/inc/fnc.php';
//include_once 'sys/inc/MultiWave.php';
define('H', $_SERVER['DOCUMENT_ROOT'] . '/');
session_name('SESS');
session_start();
$show_all = true; //为大家展示, 否则无法完成注册。
//include_once 'sys/inc/user.php';
require H . 'sys/inc/captcha.php';
$_SESSION['captcha'] = '';
// генерируем код
for ($i = 0; $i < 5; $i++) {
   $_SESSION['captcha'] .= mt_rand(0, 9);
}
$captcha = new captcha($_SESSION['captcha']);
$captcha->create();
//$captcha->MultiWave(); // 图像失真
$captcha->colorize();
$captcha->output();
