<?
function ras_to_mime($ras = null)
{
	if ($ras == null)
	{
		return 'application/octet-stream';
	}
	else
	{
		$htaccess = file(H.'.htaccess');
		
		for ($i = 0; $i < count($htaccess); $i++)
		{
			if (preg_match('#^AddType#i', trim($htaccess[$i])))
			{
				$type = explode(' ', trim($htaccess[$i]));
				$rass = str_replace('.', null, $type[2]);
				$mime[$rass] = $type[1];
			}
		}

		if (isset($mime[$ras]))
		{
			return $mime[$ras];
		}
		else
			return 'application/octet-stream';
	}
}
?>