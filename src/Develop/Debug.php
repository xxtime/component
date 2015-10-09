<?php

if (!function_exists('debug')) {
    function debug()
    {
        echo "<meta charset='UTF-8'><pre style='padding:20px; background: #000000; color: #FFFFFF;'>\r\n";
        if (func_num_args()) {
            foreach (func_get_args() as $k => $v) {
                echo "------- Debug $k -------<br/>\r\n";
                print_r($v);
                echo "<br/>\r\n";
            }
        }
        echo '</pre>';
        exit;
    }
}
