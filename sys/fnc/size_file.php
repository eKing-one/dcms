<?
function size_file($filesize = 0)
{
	$filesize_ed = 'B';

	if ($filesize >= 1024)
	{
		$filesize = round($filesize / 1024 , 2);
		$filesize_ed = 'Kb';
	}

	elseif ($filesize >= 1024)
	{
		$filesize = round($filesize / 1024 , 2);
		$filesize_ed = 'Mb';
	}

	elseif ($filesize >= 1024)
	{
		$filesize = round($filesize / 1024 , 2);
		$filesize_ed = 'Gb';
	}

	elseif ($filesize >= 1024)
	{
		$filesize = round($filesize / 1024 , 2);
		$filesize_ed = 'Tb';
	}

	return $filesize . $filesize_ed;
}
?>