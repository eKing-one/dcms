<?php
if (test_file(H . 'style/themes/' . $set['set_them'] . '/loads/14/' . $ras . '.png')) {
    echo "<img src='/style/themes/$set[set_them]/loads/14/$ras.png' alt='$ras' title='文件扩展名 $ras'/>";
} else {
    echo "<img src='/style/themes/$set[set_them]/loads/14/file.png' alt='file' title='未知扩展'/>";
}
