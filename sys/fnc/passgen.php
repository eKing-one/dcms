<?php
/**
 * 生成一个随机密码
 *
 * 该函数根据指定的长度和字符类型生成一个随机密码。可以选择密码的长度（默认为 8 个字符）和包含的字符类型（默认为包含小写字母、大写字母和数字三种类型）。
 * 
 * 参数:
 * - $k_simb (int) : 密码的长度，默认为 8。可以根据需要传递一个整数值来生成不同长度的密码。
 * - $types (int) : 密码中包含的字符类型的数量，默认为 3。该参数决定了生成的密码中包括小写字母、大写字母和数字的数量。例如：
 *     - 1 表示密码只包含数字。
 *     - 2 表示密码只包含小写字母和数字。
 *     - 3 表示密码包含小写字母、大写字母和数字。
 * 
 * 返回值:
 * - 返回一个包含随机字符的字符串，作为生成的密码。
 *
 * 例子：
 * - passgen(8, 3) 会返回一个包含小写字母、大写字母和数字的 8 个字符的随机密码。
 * - passgen(10, 2) 会返回一个包含小写字母和数字的 10 个字符的随机密码。
 * 
 * 注意：
 * - 密码的字符类型是从小写字母、大写字母和数字中随机选择的。
 * - `mt_rand` 用于生成随机数，`mt_srand` 用于初始化随机数生成器，确保生成的密码具有足够的随机性。
 * - 如果未指定参数，函数会生成一个默认长度为 8，包含小写字母、大写字母和数字的密码。
 *
 * 示例用法：
 * $generatedPassword = passgen(12, 3); // 返回一个包含小写字母、大写字母和数字的 12 位密码
 */
function passgen($k_simb = 8, $types = 3) {
	$password = null;	
	$small = 'abcdefghijklmnopqrstuvwxyz';	// 小写字母集合
	$large = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';	// 大写字母集合
	$numbers = '1234567890';	// 数字集合
	
	// 初始化随机数生成器
	mt_srand((double)microtime() * 1000000);	 
	
	// 生成密码
	for ($i = 0; $i < $k_simb; $i++) {		
		$type = mt_rand(1, min($types, 3));	// 随机选择字符类型
		
		// 根据选定的字符类型生成密码字符
		switch ($type) {		
			case 3: // 大写字母
				$password .= $large[mt_rand(0, 25)];			
				break;			
			case 2: // 小写字母
				$password .= $small[mt_rand(0, 25)];			
				break;			
			case 1: // 数字
				$password .= $numbers[mt_rand(0, 9)];			
				break;		
		}	
	}	
	
	// 返回生成的密码
	return $password;
}

// 生成一个默认的随机密码
$passgen = @passgen();