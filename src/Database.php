<?php
// src/database.php
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

namespace GuGuan123\dcms;

/**
 * Database 类用于简化与数据库的交互。
 * 
 * 该类封装了PDO的常用操作，包括查询、插入、更新、删除等。
 * 
 * 使用示例：
 * 
 * // 创建数据库连接
 * $db = new Database(['driver' => 'mysql', 'host' => 'localhost', 'dbname' => 'test_db', 'username' => 'root', 'password' => 'password123', 'timezone' => date('P')]);
 * 
 * // 查询单条记录
 * $result = $db->query('SELECT * FROM users WHERE id = ?', [1]);
 * print_r($result);
 * 
 * // 查询多条记录
 * $results = $db->queryAll('SELECT * FROM users');
 * print_r($results);
 * 
 * // 插入新记录并获取插入的 ID
 * $insertId = $db->insert('INSERT INTO users (name, email) VALUES (?, ?)', ['John Doe', 'john@example.com']);
 * echo "Inserted ID: " . $insertId;
 * 
 * // 更新记录
 * $updated = $db->update('UPDATE users SET email = ? WHERE id = ?', ['newemail@example.com', 1]);
 * echo $updated ? 'Update successful' : 'Update failed';
 * 
 * // 删除记录
 * $deleted = $db->delete('DELETE FROM users WHERE id = ?', [1]);
 * echo $deleted ? 'Delete successful' : 'Delete failed';
 */
class Database {
	/** @var \PDO PDO实例 */
	private $pdo;

	/**
	 * 构造函数
	 * 
	 * 根据配置初始化PDO连接，并设置相关属性。
	 * 
	 * @param array $config 数据库配置数组，包含以下键：
	 *                      - driver: 数据库驱动，默认'mysql'
	 *                      - host: 数据库主机地址
	 *                      - dbname: 数据库名称
	 *                      - username: 数据库用户名
	 *                      - password: 数据库密码
	 *                      - timezone: 时区设置（可选）
	 * @throws \Exception 如果数据库连接失败，抛出异常
	 */
	public function __construct(array $config) {
		try {
			$dsn = sprintf("%s:host=%s;dbname=%s", $config['driver'] ?? 'mysql', $config['host'], $config['dbname']);
			$this->pdo = new \PDO($dsn, $config['username'], $config['password']);
			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			$this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
			if (isset($config['timezone'])) {
				$this->pdo->exec("SET time_zone = '" . $config['timezone'] . "';");
			}
		} catch (\PDOException $e) {
			throw new \Exception("Database connection failed: " . $e->getMessage());
		}
	}

	/**
	 * 执行SQL语句
	 * 
	 * @param string $sql SQL语句
	 * @param array $params 绑定参数数组
	 * @return \PDOStatement 返回PDOStatement对象
	 * @throws \Exception 如果执行失败，抛出异常
	 */
	public function executeStatement($sql, $params = []) {
		try {
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute($params);
			return $stmt;
		} catch (\PDOException $e) {
			throw new \Exception("Statement execution failed: " . $e->getMessage());
		}
	}

	/**
	 * 执行查询并返回单条记录
	 * 
	 * @param string $sql SQL查询语句
	 * @param array $params 绑定参数数组
	 * @param int $fetchMode 获取模式，默认PDO::FETCH_ASSOC
	 * @return array|null 返回查询结果数组，如果没有结果返回null
	 */
	public function query($sql, $params = [], $fetchMode = \PDO::FETCH_ASSOC) {
		$result = $this->executeStatement($sql, $params)->fetch($fetchMode);
		return $result === false ? null : $result;
	}

	/**
	 * 执行查询并返回所有记录
	 * 
	 * @param string $sql SQL查询语句
	 * @param array $params 绑定参数数组
	 * @param int $fetchMode 获取模式，默认PDO::FETCH_ASSOC
	 * @return array 返回查询结果数组
	 */
	public function queryAll($sql, $params = [], $fetchMode = \PDO::FETCH_ASSOC) {
		return $this->executeStatement($sql, $params)->fetchAll($fetchMode);
	}

	/**
	 * 执行查询并返回单条记录
	 * 
	 * @param string $sql SQL查询语句
	 * @param array $params 绑定参数数组
	 * @param int $fetchMode 获取模式，默认PDO::FETCH_ASSOC
	 * @return array|null 返回查询结果数组，如果没有结果返回null
	 */
	public function queryColumn($sql, $params = [], $column_number = 0) {
		$result = $this->executeStatement($sql, $params)->fetchColumn($column_number);
		return $result === false ? null : $result;
	}

	/**
	 * 执行插入操作并返回最后插入的ID
	 * 
	 * @param string $sql SQL插入语句
	 * @param array $params 绑定参数数组
	 * @return string 返回最后插入的ID
	 */
	public function insert($sql, $params = []) {
		$this->executeStatement($sql, $params);
		return $this->pdo->lastInsertId();
	}

	/**
	 * 执行更新操作
	 * 
	 * @param string $sql SQL更新语句
	 * @param array $params 绑定参数数组
	 * @return bool 如果更新成功返回true，否则返回false
	 */
	public function update($sql, $params = []) {
		return $this->executeStatement($sql, $params)->rowCount() > 0;
	}

	/**
	 * 执行删除操作
	 * 
	 * @param string $sql SQL删除语句
	 * @param array $params 绑定参数数组
	 * @return bool 如果删除成功返回true，否则返回false
	 */
	public function delete($sql, $params = []) {
		return $this->executeStatement($sql, $params)->rowCount() > 0;
	}

	/**
	 * 开启事务
	 * 
	 * @return bool 如果事务开启成功返回true，否则返回false
	 */
	public function beginTransaction() {
		return $this->pdo->beginTransaction();
	}

	/**
	 * 提交事务
	 * 
	 * @return bool 如果事务提交成功返回true，否则返回false
	 */
	public function commit() {
		return $this->pdo->commit();
	}

	/**
	 * 回滚事务
	 * 
	 * @return bool 如果事务回滚成功返回true，否则返回false
	 */
	public function rollBack() {
		return $this->pdo->rollBack();
	}

	/**
	 * 获取最后插入的ID
	 * 
	 * @return string 返回最后插入的ID
	 */
	public function lastInsertId() {
		return $this->pdo->lastInsertId();
	}
}
