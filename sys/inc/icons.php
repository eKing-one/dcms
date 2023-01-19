<?
// 该功能首先在所选主题中搜索图标，然后在标准图标列表中搜索图标
function icons($name, $code = 'path')
{
    global $set;
    $name = preg_replace('#[^a-z0-9 _\-\.]#i', null, $name);
    if (test_file2(H . "style/themes/$set[set_them]/icons/$name") && $name != null) {
        $path = "/style/themes/$set[set_them]/icons/$name";
    } elseif (test_file2(H . "style/icons/$name") && $name != null) {
        $path = "/style/icons/$name";
    } else {
        $path = "/style/icons/default.png";
    }
    if ($code == 'path')
        return $path;
    else
        return "<img src=\"$path\" alt=\"\" />";
}
