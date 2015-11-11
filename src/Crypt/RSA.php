<?php

namespace Xxtime\Crypt;

class RSA
{

    public $encode = 'hex';     // base64


    private $_publicKey;


    private $_privateKey;


    private $_max_length = 117;


    private $_block_length = 128; // 默认支持1024位私钥


    public function setPublicKey($file_path = '')
    {
        if (!file_exists($file_path)) {
            return false;
        }
        $this->_publicKey = file_get_contents($file_path);

        $len = strlen($this->_publicKey);
        if ($len == 182) {
            $this->_block_length = 64;
        } elseif ($len == 451) {
            $this->_block_length = 256;
        } else {
            $this->_block_length = 128;
        }

        $this->_max_length = $this->_block_length - 11;
    }


    public function setPrivateKey($file_path = '')
    {
        if (!file_exists($file_path)) {
            return false;
        }
        $this->_privateKey = file_get_contents($file_path);
    }


    public function encrypt($plaintext = '')
    {
        if (!$this->_publicKey) {
            return false;
        }
        $public_key = openssl_get_publickey($this->_publicKey);
        $plaintext = str_split($plaintext, $this->_max_length);
        $result = '';
        foreach ($plaintext as $block) {
            openssl_public_encrypt($block, $encrypted, $public_key);
            $result .= $encrypted;
        }
        if ($this->encode == 'hex') {
            return bin2hex($result);
        }
        return base64_encode($result);
    }


    public function decrypt($data = '')
    {
        if (!$this->_privateKey) {
            return false;
        }
        $private_key = openssl_get_privatekey($this->_privateKey);
        if ($this->encode == 'hex') {
            $data = hex2bin($data);
        } else {
            $data = base64_decode($data);
        }
        $data = str_split($data, $this->_block_length);
        $result = '';
        foreach ($data as $block) {
            openssl_private_decrypt($block, $decrypted, $private_key);
            $result .= $decrypted;
        }
        return $result;
    }

}