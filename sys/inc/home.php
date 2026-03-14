<?php

// 定义常量 H，如果未定义，则设置为当前文件的上两级目录路径加上斜杠
if (!defined('H')) define("H", dirname(dirname(__DIR__)) . "/");

// 定义常量 I，如果未定义，则设置为空字符串
if (!defined('I')) define("I", "");

// 定义常量 REPLACE，如果未定义，则设置为 H 目录下的 "replace/" 子目录
if (!defined('REPLACE')) define("REPLACE", H . "replace/");

/*
===========意义不明的代码=============

// 扫描 H 目录下的 "sys/inc" 子目录，获取文件列表（不包括子目录）
$includes = scandir(H . "sys/inc", 0);

// 检查当前文件是否存在并执行相关逻辑
check_file(__FILE__);

// 遍历 $includes 数组中的每个文件
foreach ($includes as $file) {
	// 构造文件的相对路径
	$file_path = "sys/inc/" . $file;
	// 将文件名转换为大写常量名，去除 ".php" 后缀
	$file_constant = strtoupper(str_replace(".php", "", $file));
	// 如果该常量尚未定义，则进行定义
	if (!defined(strtoupper($file_constant))) {
		// 检查是否启用了替换功能（通过 setget 函数）
		if (setget('replace', 1) == 1) {
			if (file_exists(REPLACE . $file_path)) {
				// 如果替换目录中存在该文件，则定义常量为替换路径
				define($file_constant, REPLACE . $file_path);
			} else {
				// 否则定义常量为原始路径
				define($file_constant, H . $file_path);
			}
		} else {
			// 未启用替换功能时，直接定义常量为原始路径
			define($file_constant, H . $file_path);
		}
	}
}

function check_file($source) {
	// 使用静态变量记录已包含的文件
	static $includes;
	// 如果替换路径中的文件存在，则包含该文件
	if (file_exists(REPLACE . $source)) {
		include_once REPLACE . $source;
		$includes[$source] = TRUE;
		// 如果文件已包含，则退出脚本
		if ($includes[$source] === TRUE) exit();
	}
}
======================================
*/

// 初始化变量 $num 为 0
$num = 0;

// 设置配置数组 $conf 的 'home' 键为 TRUE
$conf['home'] = TRUE;
