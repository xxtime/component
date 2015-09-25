<?php

namespace Xxtime;

use Xxtime\Lalit\Array2XML;

class Util
{

    static function debug()
    {
        echo "<meta charset='UTF-8'><pre style='padding:20px; background: #000000; color: #FFFFFF;'>\r\n";
        if (func_num_args()) {
            foreach (func_get_args() as $k => $v) {
                echo "------- Debug $k -------<br/>\r\n";
                print_r($v);
                echo "\r\n<br/>";
            }
        }
        echo '</pre>';
        exit;
    }


    static function output($data = array(), $format = 'json')
    {
        if ($format != 'json') {
            header("Content-type:text/xml");
            $xml = Array2XML::createXML('xml', $data);
            $output = $xml->saveXML();
        } else {
            $output = json_encode($data);
        }
        exit($output);
    }


    static function random($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            // $string .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            $string .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $string;
    }


    static function create_sign($data = array(), $signKey = '')
    {
        ksort($data);
        $string = '';
        foreach ($data as $key => $value) {
            $string .= "$key=$value&";
        }
        return md5(rtrim($string, "&") . $signKey);
    }


    static function write_file($data = '', $file = 'log.log', $append = true)
    {
        $data = var_export($data, TRUE);
        if (strpos($file, '/') === 0) {
            //return FALSE;
        } else {
            $file = __DIR__ . '/' . $file;
        }
        if ($append) {
            $handle = fopen($file, "a+b");
        } else {
            $handle = fopen($file, "w+b");
        }
        $data .= "\r\n";
        fwrite($handle, $data);
        fclose($handle);
    }


    static function write_log($text = '', $file = 'log.log')
    {
        if (strpos($file, '/') === 0) {
            //return FALSE;
        } else {
            $file = __DIR__ . '/' . $file;
        }
        $handle = fopen($file, "a+b");
        $text = date('Y-m-d H:i:s') . ' ' . $text . "\r\n";
        fwrite($handle, $text);
        fclose($handle);
    }


}
