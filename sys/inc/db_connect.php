<?php
// 连接数据库服务器
// 使用 mysqli_connect 函数连接到数据库，传入数据库主机、用户名、密码和数据库名称
// 如果连接失败，输出错误信息并终止脚本
$db = mysqli_connect($set['mysql_host'], $set['mysql_user'], $set['mysql_pass'], $set['mysql_db_name']);
if (mysqli_connect_errno()) { 
	exit("连接 MySQL 失败: " . mysqli_connect_error()); // 显示连接失败的错误信息
}

// 初始化查询计数器和时间变量
$query_number = 0;
$tpassed = 0;

/**
 * 获取查询结果的特定行和字段值
 * 
 * @param $result 查询结果资源
 * @param $row 要获取的行索引
 * @param $field 要获取的字段索引或字段名称，默认为0
 * 
 * @return mixed 返回查询结果的指定字段值，如果没有找到则返回 null
 */
function dbresult($result, $row, $field = 0) {
	// 获取查询结果的总行数
	$numrows = mysqli_num_rows($result);

	// 判断行号是否有效
	if ($numrows && $row <= ($numrows - 1) && $row >= 0) {
		// 将结果指针移到指定的行
		mysqli_data_seek($result, $row);
		
		// 根据是否为数字字段来选择获取方式：行或关联数组
		$resrow = (is_numeric($field)) ? mysqli_fetch_row($result) : mysqli_fetch_assoc($result);
		
		// 如果字段存在，则返回该字段的值
		if (isset($resrow[$field])) {
			return $resrow[$field];
		}
	}
}

/**
 * 执行数据库查询
 * 
 * @param $query SQL 查询语句
 * 
 * @return mixed 返回查询结果的资源
 */
function dbquery($query) {
	global $db;
	return mysqli_query($db, $query); // 执行 SQL 查询并返回结果
}

/**
 * 获取查询结果的行数
 * 
 * @param $result 查询结果资源
 * 
 * @return int 返回查询结果的总行数
 */
function dbrows($result) {
	global $db;
	return mysqli_num_rows($result); // 获取查询结果的行数
}

/**
 * 获取查询结果的下一行数据
 * 
 * @param $result 查询结果资源
 * 
 * @return array 返回查询结果的下一行数据，以数组形式返回
 */
function dbarray($result) {
	global $db;
	return mysqli_fetch_array($result); // 获取查询结果的下一行，并以数组形式返回
}

/**
 * 获取查询结果的下一行关联数组
 * 
 * @param $result 查询结果资源
 * 
 * @return array 返回查询结果的下一行数据，以关联数组形式返回
 */
function dbassoc($result) {
	global $db;
	return mysqli_fetch_assoc($result); // 获取查询结果的下一行，并以关联数组形式返回
}

/**
 * 获取最近插入数据的 ID
 * 
 * @return int 返回最近插入数据的自增 ID
 */
function dbinsertid() {
	global $db;
	return mysqli_insert_id($db); // 获取最后一次插入的 ID
}

// 设置数据库连接的字符集为 UTF-8MB4，确保支持 Emoji 和其他 Unicode 字符
dbquery('set charset utf8mb4',$db); 
dbquery('SET names utf8mb4',$db);
dbquery('set character_set_client="utf8mb4"',$db);
dbquery('set character_set_connection="utf8mb4"',$db);

/**
 * 优化数据库表
 * 
 * 遍历所有数据库表，并对每个表执行优化操作
 */
function db_optimize() {
	$tab = dbquery('SHOW TABLES'); // 获取所有表的列表
	while ($tables = dbarray($tab)) { 
		dbquery("OPTIMIZE TABLE `$tables[0]`"); // 对每个表进行优化
	}
}
