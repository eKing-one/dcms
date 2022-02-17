<?
if (!defined('H')) define("H", dirname(dirname(__DIR__)) . "/");
if (!defined('I')) define("I", "");

if (!defined('REPLACE')) define("REPLACE", H . "replace/");
$includes = scandir(H . "sys/inc", 0);
check_file(__FILE__);


foreach ($includes as $file) {

    $file_path = "sys/inc/" . $file;
    $file_constant = mb_strtoupper(str_replace(".php", "", $file));

    if (!defined(mb_strtoupper($file_constant))) {
        if (setget('replace', 1) == 1) {

            if (file_exists(REPLACE . $file_path)) define($file_constant, REPLACE . $file_path);
            else define($file_constant, H . $file_path);
        } else  define($file_constant, H . $file_path);
    }

}


function check_replace($source2)
{

    $source = realpath($source2);
    if (!file_exists($source)) $source = $source2;

    $source = str_ireplace(DIRECTORY_SEPARATOR, "/", (string)$source);

    $h = str_ireplace(DIRECTORY_SEPARATOR, "/", H);
    $replace = str_ireplace(DIRECTORY_SEPARATOR, "/", REPLACE);
    $replace_file = str_ireplace($h, $replace, (string)$source);

    if (setget('replace', 1) == 1) {
        if (file_exists($replace_file)) return $replace_file;
        else return $source;
    } else return $source;


}

function test_file($file)
{
    return (is_file(check_replace($file)));

}

function test_file2($file)
{
    return (file_exists(check_replace($file)));

}


function check_file($source)
{
    static $includes;

    if (file_exists(REPLACE . $source)) {
        include_once REPLACE . $source;
        $includes[$source] = TRUE;
        if ($includes[$source] === TRUE) exit();
    }


}


function setget($name, $default = NULL)
{
    global $set;
    if (!isset($set[$name])) {
        if ($default === NULL) $set[$name] = NULL;
        else $set[$name] = $default;
    }
    return $set[$name];
}


$num = 0;
$home = TRUE;