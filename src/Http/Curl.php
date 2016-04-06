<?php

namespace Xxtime\Http;

class Curl
{

    public $ssl_verifypeer = FALSE;
    public $ssl_verifyhost = 2;

    protected $debug = false;
    protected $url = '';
    protected $postData = [];
    protected $headers = []; // $header[] = 'Content-Type:application/octet-stream';
    protected $cookie = '';
    protected $cookieFile = '';
    protected $userAgent = '';
    protected $timeout = 30;            // 超时
    protected $connectTimeout = 30;     // 超时
    protected $responseHeader = false;  // 是否返回头信息


    // get
    public function get($url = NULL, $data = [])
    {
        return $this->http($url, 'GET', $data);
    }


    // post
    public function post($url = NULL, $data = [])
    {
        if (!empty($url)) {
            $this->url = $url;
        }
        if (!empty($data)) {
            $this->postData = $data;
        }
        return $this->http($url, 'POST', $data);
    }


    public function setDebug($bool = false)
    {
        $this->debug = $bool;
    }


    public function setHeaders($headers = [])
    {
        $this->headers = $headers;
    }


    public function setCookie($cookie = null)
    {
        $this->cookie = $cookie;
    }

    public function setUserAgent($userAgent = null)
    {
        $this->userAgent = $userAgent;
    }


    public function setConnectTimeout($time = null)
    {
        $this->connectTimeout = $time;
    }


    public function setTimeout($time = null)
    {
        $this->timeout = $time;
    }


    private function http($url, $method, $postData = NULL)
    {
        $ch = curl_init();
        $this->url = $url;
        if (!empty($postData)) {
            $this->postData = $postData;
        }

        //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->ssl_verifyhost);
        curl_setopt($ch, CURLOPT_HEADER, $this->responseHeader);    // 是否返回头信息
        // curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'getHeader')); //回调


        // set data & url
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postData);
                break;
            case 'GET':
                if (!empty($this->postData)) {
                    $this->url = "{$this->url}?" . http_build_query($this->postData);
                }
                break;
        }
        curl_setopt($ch, CURLOPT_URL, $this->url);


        // set headers
        if ($this->headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        }


        // set cookies
        if ($this->cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
        }
        if ($this->cookieFile) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
            if (!file_exists($this->cookieFile)) {
                curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
            }
        }


        // execute
        $response = curl_exec($ch);


        // debug info
        if ($this->debug) {
            dump('---------- Request Url ----------', $this->url,
                '---------- Request Headers ----------', $this->headers,
                '---------- Request Data ----------', $this->postData,
                '---------- curl_getinfo ----------', curl_getinfo($ch),
                '---------- Response ----------', $response
            );
            exit();
        }
        curl_close($ch);

        return $response;

    }

}
