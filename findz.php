<?php
// $res = shell_exec("find ./ -mtime -1 -ls");
// -cmin -60 - created last 60 min

if ($_GET['find']) {
    $cmd = "find ./ " . $_GET['find'];

} else {
    $cmd = "find ./ -cmin -60 -ls";
}
$res = shell_exec($cmd);
echo '<pre>';
echo 'cmd=' . $cmd . '</br>';
echo $res;
echo '</pre>';
