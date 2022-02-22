<?php
echo "版本 DCMS-Social v.$set[dcms_version] $set[dcms_state] " . ((!isset($license) || $license == false) ? '' : '(延长)') . "<br />";
list($php_ver1, $php_ver2, $php_ver3) = explode('.', strtok(strtok(phpversion(), '-'), ' '), 3);
if ($php_ver1 == 5 or $php_ver1 == 7 or $php_ver1 == 8) {
    echo "<span class='on'>PHP版本: $php_ver1.$php_ver2.$php_ver3 (OK)</span><br />";
} else {
    echo "<span class='off'>PHP版本: $php_ver1.$php_ver2.$php_ver3</span><br />";
    $err[] = "测试php版本 $php_ver1.$php_ver2.$php_ver3 未实施";
}
/*
if (function_exists('disk_free_space') && function_exists('disk_total_space'))
{
$free_space=disk_free_space(H);
$total_space=disk_total_space(H);
if ($free_space>1024*1024*5)
echo "<span class='on'>Свободно:</span> ".size_file($free_space).' / '.size_file($total_space)."<br />";
else
{
echo "<span class='off'>Свободно:</span> ".size_file($free_space).' / '.size_file($total_space)."<br />";
$err[]='Мало свободного места на диске';
}
}
*/
if (function_exists('set_time_limit')) echo "<span class='on'>set_time_limit: OK</span><br />";
else echo "<span class='on'>set_time_limit: 禁止使用</span><br />";
if (ini_get('session.use_trans_sid') == true) {
    echo "<span class='on'>session.use_trans_sid: OK</span><br />";
} else {
    echo "<span class='off'>session.use_trans_sid: OFF</span><br />";
    $err[] = '在没有COOKIE支持的浏览器上，会话将丢失';
    $err[] = '加到根部 .htaccess 字符串 <b>php_value session.use_trans_sid 1</b>';
}
if (ini_get('magic_quotes_gpc') == 0) {
    echo "<span class='on'>magic_quotes_gpc: 0 (OK)</span><br />";
} else {
    echo "<span class='off'>magic_quotes_gpc: 启用</span><br />";
    $err[] = '引号转义已启用';
    $err[] = '加到根部  .htaccess 字符串 <b>php_value magic_quotes_gpc 0</b>';
}
if (ini_get('arg_separator.output') == '&amp;') {
    echo "<span class='on'>arg_separator.output: &amp;amp; (OK)</span><br />";
} else {
    echo "<span class='off'>arg_separator.output: " . output_text(ini_get('arg_separator.output')) . "</span><br />";
    $err[] = '可能会发生xml错误';
    $err[] = '加到根部  .htaccess 字符串 <b>php_value arg_separator.output &amp;amp;</b>';
}
if (file_exists(H . 'install/mod_rewrite_test.php')) {
    if (@trim(file_get_contents("http://$_SERVER[HTTP_HOST]/install/mod_rewrite.test")) == 'mod_rewrite-ok') {
        echo "<span class='on'>mod_rewrite: OK</span><br />";
    } elseif (function_exists('apache_get_modules')) {
        $apache_mod = @apache_get_modules();
        if (array_search('mod_rewrite', $apache_mod)) {
            echo "<span class='on'>mod_rewrite: OK</span><br />";
        } else {
            echo "<span class='off'>mod_rewrite: OFF</span><br />";
            $err[] = '需要的支持 mod_rewrite';
        }
    } else {
        echo "<span class='off'>mod_rewrite: OFF</span><br />";
        $err[] = '需要的支持 mod_rewrite';
    }
} elseif (function_exists('apache_get_modules')) {
    $apache_mod = @apache_get_modules();
    if (array_search('mod_rewrite', $apache_mod)) {
        echo "<span class='on'>mod_rewrite: OK</span><br />";
    } else {
        echo "<span class='off'>mod_rewrite: OFF</span><br />";
        $err[] = '需要的支持 mod_rewrite';
    }
} else {
    echo "<span class='off'>mod_rewrite: OFF 数据</span><br />";
}
if (function_exists('imagecreatefromstring') && function_exists('gd_info')) {
    $gdinfo = gd_info();
    echo "<span class='on'>GD: " . $gdinfo['GD Version'] . " OK</span><br />";
} else {
    echo "<span class='off'>GD: OFF</span><br />";
    $err[] = 'GD是正确运行所必需的';
}
if (function_exists('mysql_info')) {
    echo "<span class='on'>MySQL: OK</span><br />";
} else {
    echo "<span class='off'>MySQL: OFF</span><br />";
    $err[] = '没有MySQL，工作是不可能的';
}
if (function_exists('iconv')) {
    echo "<span class='on'>Iconv: OK</span><br />";
} else {
    echo "<span class='off'>Iconv: OFF</span><br />";
    $err[] = '没有Iconv，工作是不可能的';
}
if (class_exists('ffmpeg_movie')) {
    echo "<span class='on'>FFmpeg: OK</span><br />";
} else {
    echo "<span class='on'>FFmpeg: OFF</span><br />";
    echo "*如果没有FFmpeg，则无法自动创建视频的屏幕截图<br />";
}
if (ini_get('register_globals') == false) {
    echo "<span class='on'>register_globals off: OK</span><br />";
} else {
    echo "<span class='off'>register_globals on: !!!</span><br />";
    $err[] = 'register_globals已启用。严重违反保安规定';
}
if (function_exists('mcrypt_cbc')) {
    echo "<span class='on'>COOKIE加密: OK</span><br />";
} else {
    echo "<span class='on'>COOKIE加密: OFF</span><br />";
    echo "* mcrypt不可用<br />";
}
