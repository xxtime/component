<?php

namespace Xxtime\Crypt;

class RSA
{

    private $encode = '';


    private $_publicKey;


    private $_privateKey;


    private $_max_length = 117;


    private $_block_length = 128; // 默认支持1024位私钥


    // (file|string) key
    public function setPublicKey($key = '')
    {
        if (!file_exists($key)) {
            $this->_publicKey = "-----BEGIN PUBLIC KEY-----\n" . chunk_split($key, 64, "\n") . "-----END PUBLIC KEY-----";
        } else {
            $this->_publicKey = file_get_contents($key);
        }

        $len = strlen($this->_publicKey); // len 182, 272, 451

        if ($len <= 182) {
            $this->_block_length = 64;
        } elseif ($len <= 272) {
            $this->_block_length = 128;
        } elseif ($len <= 451) {
            $this->_block_length = 256;
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


    // string key
    public function getKeyString($key = '')
    {
        if (!file_exists($key)) {
            return false;
        }
        $key = file_get_contents($key);
        $key = explode("\n", trim($key));
        array_shift($key);
        array_pop($key);
        $result = '';
        foreach ($key as $v) {
            $result .= $v;
        }
        return $result;
    }


    // hex, base64
    public function setEncode($encode_type = '')
    {
        $this->encode = $encode_type;
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
        } elseif ($this->encode == 'base64') {
            return base64_encode($result);
        }
        return $result;
    }


    public function decrypt($data = '')
    {
        if (!$this->_privateKey) {
            return false;
        }
        $private_key = openssl_get_privatekey($this->_privateKey);
        if ($this->encode == 'hex') {
            $data = hex2bin($data);
        } elseif ($this->encode == 'base64') {
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