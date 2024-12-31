<?php
// 此脚本将缺失的表添加到数据库
// 它也用于安装引擎
$tab = mysqli_query($db,'SHOW TABLES');
while ($tables = mysqli_fetch_array($tab)) {
	$_ver_table[$tables[0]] = 1;
}
$k_sql = 0;
$ok_sql = 0;
$opdirtables = opendir(H . 'install/db_tables');
while ($filetables = readdir($opdirtables)) {
	if (preg_match('#\.sql$#i', $filetables)) {
		$table_name = preg_replace('#\.sql$#i', '', $filetables);
		if (!isset($_ver_table[$table_name])) {
			include_once check_replace(H.'sys/inc/sql_parser.php');
			$sql = SQLParser::getQueriesFromFile(H . 'install/db_tables/' . $filetables);
			for ($i = 0; $i < count($sql); $i++) {
				$k_sql++; // 查询计数器（用于安装程序）
				if (@mysqli_query($db,$sql[$i])) {
					$ok_sql++; // 成功查询计数器（用于安装程序）
				}
			}
		}
	}
}
closedir($opdirtables);

if (!isset($install)) {
	// 执行一次性查询
	$opdirtables = opendir(H . 'install/update/');
	while ($rd = readdir($opdirtables)) {
		if (preg_match('#^\.#', $rd)) continue;
		if (isset($set['update'][$rd])) continue;
		if (preg_match('#\.sql$#i', $rd)) {
			include_once H . 'sys/inc/sql_parser.php';
			$sql = SQLParser::getQueriesFromFile(H . 'install/update/' . $rd);
			for ($i = 0; $i < count($sql); $i++) {
				mysqli_query($db,$sql[$i]);
			}
			$set['update'][$rd] = true;
			$save_settings = true;
		} elseif (preg_match('#\.php$#i', $rd)) {
			include_once H . 'install/update/' . $rd;
			$set['update'][$rd] = true;
			$save_settings = true;
		}
	}
	closedir($opdirtables);
	if (isset($save_settings)) save_settings($set);
}
