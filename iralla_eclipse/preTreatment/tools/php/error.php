<?php
function myErrorHandler ($errno , $errstr ){
    echo"ERROR: $errno, $errstr\n";
    echo"";
}
function error_handler($output)
{
    $error = error_get_last();
    $output = "";
    foreach ($error as $info => $string)
        $output .= "{$info}: {$string}\n";
    return $output;
}
ob_start("error_handler");
set_error_handler("myErrorHandler");


