<?php
function size_file($filesize = 0) {
    $units = ['B', 'Kb', 'Mb', 'Gb', 'Tb'];
    $i = 0;

    while ($filesize >= 1024 && $i < count($units) - 1) {
        $filesize /= 1024;
        $i++;
    }

    return round($filesize, 2) . ' ' . $units[$i];
}
