<?php
function br($msg, $br = '<br />')
{
    return preg_replace("#((<br( ?/?)>)|\n|\r)+#i", $br, $msg);
} // 换行符
function my_esc($text, $br = NULL)
{ // 剪切所有不可读字符
    if ($br != NULL)
        for ($i = 0; $i <= 31; $i++) $text = str_replace(chr($i), NULL, $text);
    else {
        for ($i = 0; $i < 10; $i++) $text = str_replace(chr($i), NULL, $text);
        for ($i = 11; $i < 20; $i++) $text = str_replace(chr($i), NULL, $text);
        for ($i = 21; $i <= 31; $i++) $text = str_replace(chr($i), NULL, $text);
    }
    return $text;
}
function output_text($str, $br = true, $html = true, $smiles = true, $links = true, $bbcode = true)
{
    if ($html == true)
        $str = htmlentities($str, ENT_QUOTES, 'UTF-8'); // 将所有操作转换为正常的浏览器消化
    if ($br == true) {
        $str = br($str); // 换行符
        $str = my_esc($str); // 我们删除了所有无法读取的字符，这些字符会破坏我们的标记:)
    } else {
        //$str=br($str, ' '); // 空格代替进位
        $str = my_esc($str); // 我们删除了所有无法读取的字符，这些字符会破坏我们的标记:)
    }
    return $str; // 返回已处理的字符串
}
function msg($msg)
{
    echo '<div class="msg">' . $msg . '</div>';
} // 消息输出

function passgen($k_simb = 8, $types = 3)
{
    $password = "";
    $small = "abcdefghijklmnopqrstuvwxyz";
    $large = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $numbers = "1234567890";
    mt_srand((float)microtime() * 1000000);
    for ($i = 0; $i < $k_simb; $i++) {
        $type = mt_rand(1, min($types, 3));
        switch ($type) {
            case 3:
                $password .= $large[mt_rand(0, 25)];
                break;
            case 2:
                $password .= $small[mt_rand(0, 25)];
                break;
            case 1:
                $password .= $numbers[mt_rand(0, 9)];
                break;
        }
    }
    return $password;
}
$passgen = passgen();
// 保存系统设置
function save_settings($set)
{
    // 从数组中移除特定键
    unset($set['web']);
    
    // 构建配置文件内容
    $configContent = "<?php\nreturn " . var_export($set, true) . ";\n";

    // 定义配置文件路径
    $filePath = H . 'sys/dat/settings.php';

    // 尝试打开文件写入内容
    if ($fopen = @fopen($filePath, 'w')) {
        @fputs($fopen, $configContent);
        @fclose($fopen);
        @chmod($filePath, 0777);
        return true;
    } else {
        return false;
    }
}
// 递归删除文件夹
function delete_dir($dir)
{
    if (is_dir($dir)) {
        $od = opendir($dir);
        while ($rd = readdir($od)) {
            if ($rd == '.' || $rd == '..') continue;
            if (is_dir("$dir/$rd")) {
                @chmod("$dir/$rd", 0777);
                delete_dir("$dir/$rd");
            } else {
                @chmod("$dir/$rd", 0777);
                @unlink("$dir/$rd");
            }
        }
        closedir($od);
        @chmod("$dir", 0777);
        return @rmdir("$dir");
    } else {
        @chmod("$dir", 0777);
        @unlink("$dir");
    }
}
