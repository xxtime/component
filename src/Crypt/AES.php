<?php

namespace Xxtime\Crypt;

class AES
{

    private $_secret_key = 'default_secret_key';


    public $encode = ''; //base64 | hex | none


    private $algorithm = MCRYPT_RIJNDAEL_256;


    private $mode = MCRYPT_MODE_CBC;


    private $vi_mode = MCRYPT_RAND; // MCRYPT_DEV_RANDOM


    // 设置密钥
    public function setKey($key)
    {
        $this->_secret_key = $key;
    }


    // 编码格式
    public function setEncode($code = '')
    {
        $this->encode = $code;
    }


    // 加密
    public function encrypt($data)
    {
        $td = mcrypt_module_open($this->algorithm, '', $this->mode, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), $this->vi_mode);
        mcrypt_generic_init($td, $this->_secret_key, $iv);
        $encrypted = mcrypt_generic($td, $data);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        // return format
        if ($this->encode == 'hex') {
            return bin2hex($iv . $encrypted);
        } elseif ($this->encode == 'base64') {
            return base64_encode($iv . $encrypted);
        }
        return $iv . $encrypted;
    }


    // 解密
    public function decrypt($encrypted)
    {
        $td = mcrypt_module_open($this->algorithm, '', $this->mode, '');
        if ($this->encode == 'hex') {
            $encrypted = hex2bin($encrypted);
        } elseif ($this->encode == 'base64') {
            $encrypted = base64_decode($encrypted);
        }
        $iv = substr($encrypted, 0, 32);
        $encrypted = substr($encrypted, 32);
        mcrypt_generic_init($td, $this->_secret_key, $iv);
        $decrypted = mdecrypt_generic($td, $encrypted);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return $decrypted;
    }

}