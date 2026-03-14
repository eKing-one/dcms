<?php
// 此脚本将缺失的表添加到数据库
// 它也用于安装引擎
$tab = mysqli_query($mydb,'SHOW TABLES');
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
				try {
					if (mysqli_query($mydb, $sql[$i])) {
						$ok_sql++; // 成功查询计数器（用于安装程序）
					}
				} catch (mysqli_sql_exception $e) {
					// 如果执行失败，直接报错并抓出文件名
					echo '<br><div style="border:2px solid red; padding:10px; background:#fff0f0;">';
					echo '出错的文件: <b>install/db_tables/' . $filetables . '</b><br>';
					echo '报错信息: ' . $e->getMessage() . '<br>';
					echo '坏掉的 SQL 语句: <pre>' . htmlspecialchars($sql[$i]) . '</pre>';
					echo '</div>';
					exit;
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
				mysqli_query($mydb,$sql[$i]);
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
