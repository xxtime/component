<?php

namespace Xxtime;

use Xxtime\Lalit\Array2XML;

class Util
{

    static public function debug()
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


    static public function output($data = array(), $format = 'json')
    {
        if ($format != 'json') {
            header("Content-type:text/xml");
            $xml = Array2XML::createXML('xml', $data);
            $output = $xml->saveXML();
        } else {
            header("Content-type:application/json; charset=utf-8");
            $output = json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        exit($output);
    }


    static public function filter($param)
    {
        $search = array('\\', '/', '"', "'", '%', '=', '(', ')', '<', '>');
        $replace = array('\\\\', '\\/', '\\"', "\\'", '\\%', '\\=', '\\(', '\\)', '\\<', '\\>');
        return str_replace($search, $replace, $param);
    }


    static public function random($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            // $string .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            $string .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $string;
    }


    static public function guid()
    {
        mt_srand((double)microtime() * 10000);
        $charID = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45); // "-"
        $uuid = substr($charID, 0, 8) . $hyphen
            . substr($charID, 8, 4) . $hyphen
            . substr($charID, 12, 4) . $hyphen
            . substr($charID, 16, 4) . $hyphen
            . substr($charID, 20, 12);
        return $uuid;
    }


    static public function createSign($data = array(), $signKey = '', $as = '=', $di = '&')
    {
        ksort($data);
        $string = '';
        foreach ($data as $key => $value) {
            $string .= "$key{$as}$value{$di}";
        }
        return md5(rtrim($string, $di) . $signKey);
    }


    static public function create_sign($data = array(), $signKey = '')
    {
        return self::createSign($data, $signKey);
    }


    static public function writeFile($data = '', $file = 'log.log', $append = true)
    {
        //$data = var_export($data, TRUE);
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


    static public function write_file($data = '', $file = 'log.log', $append = true)
    {
        return self::writeFile($data, $file, $append);
    }


    static public function writeLog($text = '', $file = 'log.log')
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


    static public function write_log($text = '', $file = 'log.log')
    {
        return self::writeLog($text, $file);
    }


    static public function exportCSV($data = array(), $filePath = '', $headerField = false, $fileType = 'csv')
    {
        // 文件名
        $filePath = self::generateFileName($filePath, false);

        // 导出类型
        $split = ",";
        if ($fileType == 'xls') {
            $split = "\t";
        }

        // 表头
        if ($headerField) {
            $first = reset($data);
            $output = implode($split, array_keys($first));
            $output .= "\r\n";
        } else {
            $output = '';
        }

        // 分割写入
        $skip = 1000;
        $max = count($data);
        $fp = fopen($filePath, "a+");
        foreach ($data as $key => $value) {
            $output .= implode($split, array_values($value));
            $output .= "\r\n";
            if ((($key != 0) && ($key % $skip == 0)) || ($max == $key + 1)) {
                fwrite($fp, $output);
                $output = '';
            }
        }
        fclose($fp);
    }


    static private function generateFileName($filePath, $rand = false)
    {
        if (strpos($filePath, '/') !== 0) {
            $filePath = realpath(__DIR__ . '/../../../../') . '/' . $filePath;
        }

        if (!$rand) {
            return $filePath;
        }

        $rand = date('YmdHis') . mt_rand(1000, 9999);
        $suffix = strrchr($filePath, '.');
        if ($suffix) {
            $newSuffix = $rand . $suffix;
            $filePath = str_replace($suffix, $newSuffix, $filePath);
        } else {
            $filePath = $filePath . $rand;
        }
        return $filePath;
    }
}
