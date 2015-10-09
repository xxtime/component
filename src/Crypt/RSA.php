<?php

namespace Xxtime\Crypt;

class RSA
{

    public $publicKey = '';


    public $privateKey = '';


    private $_block_length = 32;


    public function setPublicKey($file_path = '')
    {
        $this->publicKey = $file_path;
    }


    public function setPrivateKey($file_path = '')
    {
        $this->privateKey = $file_path;
    }


    public function encrypt($plaintext = '')
    {
        if (!file_exists($this->publicKey)) {
            return false;
        }
        $key = file_get_contents($this->publicKey);
        $public_key = openssl_get_publickey($key);
        $plaintext = str_split($plaintext, $this->_block_length);
        $result = '';
        foreach ($plaintext as $block) {
            openssl_public_encrypt($block, $encrypted, $public_key);
            $result .= $encrypted;
        }
        return base64_encode($result);
    }


    public function decrypt($data = '')
    {
        if (!file_exists($this->privateKey)) {
            return false;
        }
        $key = file_get_contents($this->privateKey);
        $private_key = openssl_get_privatekey($key);
        $data = base64_decode($data);
        $data = str_split($data, 64);
        $result = '';
        foreach ($data as $block) {
            openssl_private_decrypt($block, $decrypted, $private_key);
            $result .= $decrypted;
        }
        return $result;
    }

}