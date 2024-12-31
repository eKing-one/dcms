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
		if (!file_exists($htaccessPath) || !is_readable($htaccessPath)) {
			return 'application/octet-stream'; // 如果文件不可用，返回默认 MIME 类型
		}

		// 逐行读取 .htaccess 文件并解析 MIME 类型映射
		$htaccess = file($htaccessPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($htaccess as $line) {
			// 使用正则表达式匹配 "AddType" 指令并提取 MIME 类型和扩展名
			if (preg_match('#^AddType\s+(\S+)\s+(\S+)$#i', trim($line), $matches)) {
				$mime[str_replace('.', '', $matches[2])] = $matches[1]; // 构建映射：扩展名 => MIME 类型
			}
		}
	}

	// 返回 MIME 类型，如果未找到扩展名对应的 MIME 类型，返回默认值
	return $mime[$ras] ?? 'application/octet-stream';
}