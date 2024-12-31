<?
/**
 * 根据文件扩展名返回对应的 MIME 类型
 *
 * 该函数通过解析 `.htaccess` 文件中的 `AddType` 指令来获取 MIME 类型和文件扩展名的映射。
 * 如果传入的扩展名无法匹配到对应的 MIME 类型，则返回默认的 `application/octet-stream`。
 *
 * @param string|null $ras 文件扩展名（如 "jpg", "png"）。如果为空，则返回默认值。
 * @return string 返回 MIME 类型。如果无法匹配，返回 `application/octet-stream`。
 *
 * 依赖：
 * - 函数依赖 `.htaccess` 文件的正确格式。
 * - 常量 `H` 必须预先定义为 `.htaccess` 文件所在的目录路径。
 *
 * 示例：
 * 假设 `.htaccess` 文件内容为：
 *   AddType image/jpeg jpg
 *   AddType image/png png
 * 
 * 调用：
 *   ras_to_mime('jpg') // 返回 "image/jpeg"
 *   ras_to_mime('pdf') // 返回 "application/octet-stream" （未定义扩展名）
 *   ras_to_mime(null)  // 返回 "application/octet-stream"
 */
function ras_to_mime($ras = null)
{
	// 如果没有传入扩展名，返回默认 MIME 类型
	if ($ras === null) {
		return 'application/octet-stream';
	}

	// 使用静态变量缓存解析后的 MIME 类型映射，避免重复解析
	static $mime = null;

	// 如果静态变量为空，则解析 .htaccess 文件
	if ($mime === null) {
		$mime = []; // 初始化 MIME 类型数组
		$htaccessPath = H . '.htaccess'; // 构建 .htaccess 文件路径

		// 检查 .htaccess 文件是否存在且可读
		if (!file_exists($htaccessPath)) {
			error_log("[ras_to_mime] Error: .htaccess file not found at path: $htaccessPath");
			return 'application/octet-stream';
		}

		if (!is_readable($htaccessPath)) {
			error_log("[ras_to_mime] Error: .htaccess file is not readable at path: $htaccessPath");
			return 'application/octet-stream';
		}

		// 尝试读取 .htaccess 文件内容
		try {
			$htaccess = file($htaccessPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		} catch (Exception $e) {
			error_log("[ras_to_mime] Error: Failed to read .htaccess file - " . $e->getMessage());
			return 'application/octet-stream';
		}

		// 逐行解析 .htaccess 文件
		foreach ($htaccess as $line) {
			$line = trim($line); // 去掉行首尾空白字符
		
			// 跳过空行或注释行
			if (empty($line) || str_starts_with($line, '#')) {
				continue;
			}
		
			// 匹配 AddType 指令
			if (preg_match('#^AddType\s+(\S+)\s+(.+)$#i', $line, $matches)) {
				$mimeType = $matches[1]; // 提取 MIME 类型
				$extensions = preg_split('/\s+/', $matches[2]); // 提取所有扩展名（用空白分隔）
		
				// 将每个扩展名映射到对应的 MIME 类型
				foreach ($extensions as $extension) {
					$mime[str_replace('.', '', $extension)] = $mimeType;
				}
			} else {
				// 记录无效的 AddType 行
				error_log("[ras_to_mime] Warning: Invalid AddType line in .htaccess: $line");
			}
		}
		
	}

	// 返回 MIME 类型，如果未找到扩展名对应的 MIME 类型，返回默认值
	if (!isset($mime[$ras])) {
		error_log("[ras_to_mime] Warning: MIME type not found for extension: $ras");
	}

	return $mime[$ras] ?? 'application/octet-stream';
}

// 检查 PHP 版本是否支持 str_starts_with ，以适配 PHP 8.0 之前的版本
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return strpos($haystack, $needle) === 0;
    }
}
