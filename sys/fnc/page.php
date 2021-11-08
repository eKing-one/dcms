<?
// Выдает текущую страницу
function page($k_page=1)
{ 
	$page = 1;

	if (isset($_GET['page']))
	{
		if ($_GET['page'] == 'end')
			$page = intval($k_page);
			
		elseif(is_numeric($_GET['page'])) 
		$page = intval($_GET['page']);
	}

	if ($page < 1)$page = 1;

	if ($page > $k_page)
		$page = $k_page;
		
	return $page;
}

// Высчитывает количество страниц
function k_page($k_post = 0, $k_p_str = 10)
{ 
	if ($k_post != 0) 
	{
		$v_pages = ceil($k_post / $k_p_str);
		return $v_pages;
	}

	else return 1;
}

// Вывод номеров страниц (только на первый взгляд кажется сложно ;))
function str($link = '?', $k_page = 1,$page = 1)
{ 
	if ($page < 1)
		$page = 1;

	echo '<div class="c2">';

	if ($page != 1)
		echo '<span class="page"><a href="' . $link . 'page=1" title="Первая страница">&lt;</a></span> ';

	if ($page != 1)
		echo '<span class="page"><a href="' . $link . 'page=1" title="Страница №1">1</a></span>';
	else 
		echo ' <span class="str"><b>1</b></span>';

	for ($ot = -3; $ot <= 3; $ot++)
	{
		if ($page + $ot > 1 && $page + $ot < $k_page)
		{
			if ($ot == -3 && $page + $ot > 2)
				echo '<span class="page"> ..';

			if ($ot != 0)
				echo ' <span class="page"><a href="' . $link . 'page=' . ($page + $ot) . '" title="Страница №' . ($page + $ot) . '">' . ($page + $ot) . '</a></span>';
			else 
				echo ' <span class="str"><b>' . ($page + $ot) . '</b></span>';

			if ($ot == 3 && $page + $ot < $k_page - 1)
				echo '<span class="page"> ..';
		}
	}

	if ($page != $k_page)
		echo ' <span class="page"><a href="' . $link . 'page=end" title="Страница №' . $k_page . '">' . $k_page . '</a></span>';
	
	elseif ($k_page > 1)
		echo ' <span class="str"><b>' . $k_page . '</b></span>';
		
	if ($page != $k_page)
		echo ' <span class="page"><a href="' . $link . 'page=end" title="Последняя страница">&gt;</a></span>';

	echo '</div>';
}
?>