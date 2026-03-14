<?php
// src/Services/PrivateMessage.php
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

namespace GuGuan123\dcms\Services;

class PrivateMessage
{
	public function __construct(private array $set, private \GuGuan123\dcms\Database $db) {
		$this->set = $set;
		$this->db = $db;
	}

	/**
	 * 获取某条消息
	 * 
	 * @param int $id 消息ID
	 * @return array{time: int, msg: string, read: int, unlink: int}
	 */
	public function get(int $id) {
		$result = $this->db->query('SELECT * FROM `mail` WHERE id = ? LIMIT 1', [$id]);
		return [
			'id'          => $result['id'],
			'sender_id'   => $result['id_user'],
			'receiver_id' => $result['id_kont'],
			'time'        => $result['time'],
			'msg'         => $result['msg'],
			'read'        => $result['read'],
			'unlink'      => $result['unlink']
		];
	}

	/**
	 * 获取私信消息列表
	 * 
	 * @param int $user_id 发送者ID
	 * @param int $target_id 接受者ID
	 * @param int $offset 偏移量
	 * @param int $limit 返回信息条数
	 * @return array
	 */
	public function list(int $user_id, int $target_id, int $offset, int $limit = 10) {
		$results = $this->db->queryAll('SELECT * FROM `mail` WHERE `unlink` != ? AND `id_user` = ? AND `id_kont` = ? OR `id_user` = ? AND `id_kont` = ? AND `unlink` != ? ORDER BY id DESC LIMIT ?, ?', [
			$user_id,
			$user_id,
			$target_id,
			$target_id,
			$user_id,
			$user_id,
			$offset,
			$limit
		]);

		return array_map(function($row) {
			return [
				'id'          => $row['id'],
				'sender_id'   => $row['id_user'],
				'receiver_id' => $row['id_kont'],
				'time'        => $row['time'],
				'msg'         => $row['msg'],
				'read'        => $row['read'],
				'unlink'      => $row['unlink']
			];
		}, $results);
	}

	/**
	 * 删除某条消息
	 * 
	 * @param int $id 消息ID
	 * @return bool 是否操作成功
	 */
	public function delete(int $id) {
		return $this->db->delete('DELETE FROM `mail` WHERE `id` = ?', [$id]);
	}

	/**
	 * 将某条信息标记为删除
	 * 
	 * @param int $id 消息ID
	 * @return bool 是否操作成功
	 */
	public function unlink(int $id, int $user_id) {
		return $this->db->update('UPDATE `mail` SET `unlink` = ? WHERE `id` = ? LIMIT 1', [$user_id, $id]);
	}

	/**
	 * 清理某个用户的与某个联系人的所有私信消息
	 * 
	 * @param int $user_id 清理的用户ID
	 * @param int $target_id 清理的联系人ID
	 */
	public function clear_all($user_id, $target_id) {
		$this->db->delete('DELETE FROM `mail` WHERE `unlink` = ?  AND `id_user` = ? AND `id_kont` = ? OR `id_user` = ? AND `id_kont` = ? AND `unlink` = ?', [
			$target_id,
			$user_id,
			$target_id,
			$target_id,
			$user_id,
			$target_id
		]);
		$this->db->update('UPDATE `mail` SET `unlink` = ? WHERE  `id_user` = ? AND `id_kont` = ? OR `id_user` = ? AND `id_kont` = ?', [
			$user_id,
			$user_id,
			$target_id,
			$target_id,
			$user_id
		]);
	}
}