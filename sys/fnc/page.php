<?php

/**
 * 返回当前页面编号。
 *
 * 该函数用于确定当前显示的页面编号，支持通过GET请求中的`page`参数动态调整页面编号。
 * 如果`page`参数为`end`，则返回最大页面数；如果为数字，则返回对应的页面编号。
 *
 * @param int $k_page 最大页面编号，默认为1。
 * @return int 当前页面编号。
 */
function page($k_page = 1) {
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

/**
 * 计算总页数。
 *
 * 根据帖子总数和每页显示帖子数量计算出总的页数。
 *
 * @param int $k_post 总帖子数，默认为0。
 * @param int $k_p_str 每页显示帖子数量，默认为10。
 * @return int 总页数。如果帖子总数为0，则返回1。
 */
function k_page($k_post = 0, $k_p_str = 10) {
	if ($k_post != 0) {
		$v_pages = ceil($k_post / $k_p_str);
		return $v_pages;
	}
	else return 1;
}

/**
 * 显示分页链接。
 *
 * 生成并输出分页链接HTML代码，允许用户在不同页面间导航。此函数考虑了当前页面、总页数以及链接格式，
 * 并提供了省略号（..）来表示未显示的页面。
 *
 * @param string $link 分页链接的基本部分，默认为'?'。
 * @param int $k_page 总页数，默认为1。
 * @param int $page 当前页面编号，默认为1。
 * @return void
 */
function str($link = '?', $k_page = 1, $page = 1) {
	if ($page < 1) $page = 1;
	echo '<div class="c2">';
	if ($page != 1) echo '<span class="page"><a href="' . $link . 'page=' . $page - 1 . '" title="上一页">&lt;</a></span> ';
	if ($page != 1) {
		echo '<span class="page"><a href="' . $link . 'page=1" title="第 1 页">1</a></span>';
	} else {
		echo ' <span class="str"><b>1</b></span>';
	}
	for ($ot = -3; $ot <= 3; $ot++) {
		if ($page + $ot > 1 && $page + $ot < $k_page) {
			if ($ot == -3 && $page + $ot > 2) echo '<span class="page"> ..';
			if ($ot != 0) {
				echo ' <span class="page"><a href="' . $link . 'page=' . ($page + $ot) . '" title="第 ' . ($page + $ot) . ' 页">' . ($page + $ot) . '</a></span>';
			} else {
				echo ' <span class="str"><b>' . ($page + $ot) . '</b></span>';
			}
			if ($ot == 3 && $page + $ot < $k_page - 1) echo '<span class="page"> ..';
		}
	}
	if ($page != $k_page) {
		echo ' <span class="page"><a href="' . $link . 'page=end" title="第 ' . $k_page . ' 页">' . $k_page . '</a></span>';
	} elseif ($k_page > 1) {
		echo ' <span class="str"><b>' . $k_page . '</b></span>';
	}
	if ($page != $k_page) echo ' <span class="page"><a href="' . $link . 'page=' . $page + 1 . '" title="下一页">&gt;</a></span>';
	echo '</div>';
}