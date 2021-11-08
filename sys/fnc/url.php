<?
function url($url)
{
	$url2 = preg_split('#&(amp;)?#', $url);
	$url3 = NULL;

	for ($i = 0; $i < count($url2); $i++)
	{
		$url4 = explode('=', $url2[$i]);

		if (isset($url4[1]))
			$url3 .= $url4[0] . '=' . urlencode($url4[1]);

		else 
			$url3 .= $url4[0];
			
		if ($i < count($url2) - 1)
			$url3 .= '&amp;';
	}
	return $url3;
}


function url2($url)
{
	$url2 = explode('/', $url);

	for ($i = 0; $i < sizeof($url2); $i++)
	{
		$url2[$i] = urlencode($url2[$i]);
	}

	return implode('/',$url2);
}
?>