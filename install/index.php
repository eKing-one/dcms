<?php
error_reporting(E_ALL); // 启用错误显示
ini_set('display_errors',true); // 启用错误显示
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/fnc/shif.php';

//include_once '../sys/fnc/output_text.php';
include_once '../sys/fnc/strlen2.php';
include_once '../sys/fnc/size_file.php';
include_once 'inc/functions.php';
include_once 'inc/settings.php';
include_once '../sys/inc/ipua.php';

$install=true;
if (!isset($_SESSION['install_step']))$_SESSION['install_step']=0;
include 'inc/step_'.$_SESSION['install_step'].'.php';