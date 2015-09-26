<?php

namespace Xxtime\Crypt;

class RSA
{

    public $publicKey = '';


    public $privateKey = '';


    public function setPublicKey($file_path = '')
    {
        $this->publicKey = $file_path;
    }


    public function setPrivateKey($file_path = '')
    {
        $this->privateKey = $file_path;
    }


    public function encrypt($data = '')
    {
        if (!file_exists($this->publicKey)) {
            return false;
        }
        $key = file_get_contents($this->publicKey);
        $public_key = openssl_get_publickey($key);
        openssl_public_encrypt($data, $encrypted, $public_key);
        return $encrypted;
    }


    public function decrypt($data = '')
    {
        if (!file_exists($this->privateKey)) {
            return false;
        }
        $key = file_get_contents($this->privateKey);
        $private_key = openssl_get_privatekey($key);
        openssl_private_decrypt($data, $decrypted, $private_key);
        return $decrypted;
    }

}