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

// 初始化全局变量
try {
	$db = new GuGuan123\dcms\Database([
		'driver' => 'mysql',
		'host' => $set['sql_host'],
		'dbname' => $set['sql_db_name'],
		'username' => $set['sql_user'],
		'password' => $set['sql_pass'],
		'timezone' => date('P')
	]);
} catch (Exception $e) {
	// 连接失败时，输出错误信息并终止脚本执行
	http_response_code(506);
	die("Error: " . $e->getMessage());
}

if ($set['use_mysqli'] == 1) {
	// mysqli 连接数据库服务器
	// 使用 mysqli_connect 函数连接到数据库，传入数据库主机、用户名、密码和数据库名称
	// 如果连接失败，输出错误信息并终止脚本
	$mydb = mysqli_connect($set['sql_host'], $set['sql_user'], $set['sql_pass'], $set['sql_db_name']);
	if (mysqli_connect_errno()) { 
		exit("连接 MySQL 失败: " . mysqli_connect_error()); // 显示连接失败的错误信息
	}

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
		global $mydb;
		return mysqli_query($mydb, $query); // 执行 SQL 查询并返回结果
	}

	/**
	 * 获取查询结果的行数
	 * 
	 * @param $result 查询结果资源
	 * 
	 * @return int 返回查询结果的总行数
	 */
	function dbrows($result) {
		global $mydb;
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
		global $mydb;
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
		global $mydb;
		return mysqli_fetch_assoc($result); // 获取查询结果的下一行，并以关联数组形式返回
	}

	/**
	 * 获取最近插入数据的 ID
	 * 
	 * @return int 返回最近插入数据的自增 ID
	 */
	function dbinsertid() {
		global $mydb;
		return mysqli_insert_id($mydb); // 获取最后一次插入的 ID
	}

	// 设置数据库时区
	dbquery("SET time_zone = '" . date('P') . "';");

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
} else {
	// 使用 PDO 兼容原有接口

	/**
	 * 执行数据库查询
	 * 
	 * @param $query SQL 查询语句
	 * 
	 * @return mixed 返回查询结果 PDOStatement 对象
	 */
	function dbquery($query, $params = []) {
		global $db;
		return $db->executeStatement($query, $params);
	}

	/**
	 * 获取查询结果的行数
	 * 
	 * @param $result 查询结果资源
	 * 
	 * @return int 返回查询结果的总行数
	 */
	function dbrows($result) {
		return $result->rowCount(); // PDOStatement 的 rowCount 方法
	}

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
		$currentRow = 0;

		// 如果 $field 是数字，使用索引数组（FETCH_NUM）；否则使用关联数组（FETCH_ASSOC）
		$fetchMode = is_numeric($field) ? PDO::FETCH_NUM : PDO::FETCH_ASSOC;

		while ($data = $result->fetch($fetchMode)) {
			if ($currentRow == $row) {
				return isset($data[$field]) ? $data[$field] : null;
			}
			$currentRow++;
		}
		return null;
	}

	function dbarray($result) {
		// 返回索引和关联数组
		return $result->fetch(PDO::FETCH_BOTH);
	}

	function dbassoc($result) {
		// 返回关联数组
		return $result->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * 获取最近插入数据的 ID
	 * 
	 * @return int 返回最近插入数据的自增 ID
	 */
	function dbinsertid() {
		global $db;
		return $db->lastInsertId();
	}

	/**
	 * 优化数据库表
	 * 
	 * 遍历所有数据库表，并对每个表执行优化操作
	 */
	function db_optimize() {
		global $db;

		// 获取所有表的列表
		$tables = $db->queryAll('SHOW TABLES');
		
		// 遍历并优化每个表
		foreach ($tables as $table) {
			$tableName = reset($table); // 获取表名（第一个键值）
			$db->query("OPTIMIZE TABLE `$tableName`");
		}
	}
}

// 意义不明的全局变量
$query_number = 0;
$tpassed = 0;
