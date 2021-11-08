<?
function aut($title=NULL)
{
	global $set;

	if ($set['web'] == false)
	{
		if ($title == NULL)
			$title = $set['title'];

		echo '<table cellspacing="0" cellpadding="0"><tr>';

		if ($_SERVER['PHP_SELF'] != '/index.php')
		{
			echo '<td class="titles">';
			echo '<a href="/index.php"><img src="/style/icons/icon_glavnaya.gif" alt="DS" /></a>';
			echo '</td>'; 
		}
		
		echo '<td class="title">' . $title . '</td>';
		echo '</table>';
	}
}
?>