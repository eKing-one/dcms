<?
function cmp2 ($a, $b) 
{
    if ($a['2'] == $b['2']) return 0;
    return ($a['2'] > $b['2']) ? -1 : 1;
}

if (isset($_POST))
{
	foreach($_POST as $key => $value)
	{
		$_POST[$key] = fiera($value);
	}
}
?>