<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


function debug($str) {
    $debugMsgStyles = "
        background-color:#ddd;
        margin:12px;
        border-radius:12px;
        font-size: 1.67em;
        padding: 12px";
    echo '<pre style="'.$debugMsgStyles.'">';
    echo '<span style="font-weight:bold">DEBUG: </span><span>'.__DIR__.'</span><br><hr>';
    var_dump($str);
    echo '</pre>';
    exit;
}
