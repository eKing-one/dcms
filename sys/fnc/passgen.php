<?
function passgen($k_simb = 8, $types = 3) 
{
	$password = null;	
	$small = 'abcdefghijklmnopqrstuvwxyz';	
	$large = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';	
	$numbers = '1234567890';	
	
	mt_srand((double)microtime()*1000000);	 
	
	for ($i = 0; $i < $k_simb; $i++) 
	{		
		$type = mt_rand(1,min($types,3));	
			
		switch ($type) 
		{		
			case 3:		
			$password .= $large[mt_rand(0,25)];			
			break;			
			case 2:			
			$password .= $small[mt_rand(0,25)];			
			break;			
			case 1:			
			$password .= $numbers[mt_rand(0,9)];			
			break;		
		}	
	}	

	return $password;
}

$passgen = @passgen();
?>
