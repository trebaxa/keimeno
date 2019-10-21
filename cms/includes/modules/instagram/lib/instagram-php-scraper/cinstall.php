<?php

error_reporting(E_ALL);
echo 'install composer<br>';

function cexec($str) {
    $last_line = exec($str, $retval);
    echo $str . '<br>' . nl2br($last_line) . '<hr>';
}

$cmds = array(
    'touch .bashrc',
    "alias php7cli='/usr/local/bin/php7-73LATEST-CLI'",
    "alias composer='php7cli ~/composer.phar'",
    'source .bashrc',
    'curl -sS https://getcomposer.org/installer | php7cli',
    'php7cli composer.phar install');
cexec(implode(' ; ', $cmds));
