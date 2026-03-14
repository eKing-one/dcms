<?php
/*
 * MIT License
 * 
 * Copyright (c) 2025 GuGuan123
 * 
 * 本软件基于 MIT 许可证发布。具体许可条款如下：
 * 
 * 允许在本软件及其附带文档文件（以下简称“软件”）的基础上进行修改、复制、分发及/或销售，
 * 且在提供软件的副本时，需附上此许可证声明和版权声明。
 * 
 * 本软件按“原样”提供，不作任何形式的明示或暗示的担保，包括但不限于对适销性、适合某一特定用途的担保。
 * 在任何情况下，无论是在合同诉讼、侵权或其他诉讼中，作者或版权持有者对因使用本软件或其他交易的结果
 * 所产生的任何索赔、损害或其他责任不承担任何责任。
 * 
 * 你可以在 https://choosealicense.com/licenses/mit/ 查看详细的 MIT 原始许可证条款。
 */

if (is_file(__DIR__ . '/../../sys/dat/settings.php')) {
	echo '必须删除<b>sys/dat/settings.php</b>文件才能继续安装';
	exit;
}

if (!is_file(__DIR__ . '/../../sys/dat/default.ini')) {
	echo '找不到必须的前置配置文件（sys/dat/default.ini）';
	exit;
}

$tmp_set = $set = parse_ini_file(__DIR__ . '/../../sys/dat/default.ini', false);

$tmp_set['siteurl'] = $set['siteurl'] = get_http_type() . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';
