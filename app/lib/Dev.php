<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


function debug($str) {
    $debug_msg_styles = "
        background-color:#ddd;
        margin:12px;
        border-radius:12px;
        font-size: 1.67em;
        padding: 12px";
    echo '<pre style="'.$debug_msg_styles.'">';
    echo '<span style="font-weight:bold">DEBUG: </span><span>'.__DIR__.'</span><br><hr>';
    var_dump($str);
    echo '</pre>';
    exit;
}
