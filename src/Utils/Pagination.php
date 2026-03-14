<?php
// src/Utils/Pagination.php
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

namespace GuGuan123\dcms\Utils;

class Pagination
{
	public function __construct(private array $set, private \GuGuan123\dcms\Database $db) {
		$this->set = $set;
		$this->db = $db;
	}

	/**
	 * 返回当前页面编号。
	 *
	 * 该函数用于确定当前显示的页面编号，支持通过GET请求中的`page`参数动态调整页面编号。
	 * 如果`page`参数为`end`，则返回最大页面数；如果为数字，则返回对应的页面编号。
	 *
	 * @param int $k_page 最大页面编号，默认为1。
	 * @return int 当前页面编号。
	 */
	public function page(int $k_page = 1, $page = 1): int {
		$page = 1;
		if (isset($_GET['page'])) {
			if ($_GET['page'] == 'end')
				$page = intval($k_page);
			elseif(is_numeric($_GET['page'])) 
			$page = intval($_GET['page']);
		}
		if ($page < 1) $page = 1;
		if ($page > $k_page) $page = $k_page;
		return $page;
	}
}
