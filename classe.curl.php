<?php

/*
 *	Classe cURL
 *    by Ali Saleh
 *    E-mail: palhacoal1alu@gmail.com
*/

class cURL
{

    public $response, $responseText;
    public $followLocation, $postData, $error, $referer, $version, $cookie, $cookie_file, $userAgent;
    private $ch, $headers, $proxy, $timeout;

    /*
     *	Inicialização da classe :D
    */

    function __construct()
    {

        if (!function_exists("curl_init")) {
            throw new Exception("Erro ao inicializar classe cURL");
        }

        $this->version = curl_version();
        $this->headers = Array();
        $this->followLocation = true;
        $this->timeout = 9999;
        $this->ch = curl_init();
    }

    function __toString()
    {
        return $this->responseText;
    }

    /*
     *	Abrir página (HTTP)
    */


    function open($method = "GET", $page = "", $auth = "")
    {

        $this->cookie = null;
        $this->postData = null;
        $this->userAgent = null;
        $this->headers = Array();
        $this->referer = null;
        $this->error = null;

        curl_setopt($this->ch, CURLOPT_URL, $page);
        curl_setopt($this->ch, CURLOPT_ENCODING, "gzip,deflate");
        curl_setopt($this->ch, CURLOPT_HEADER, true);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);

        if (strtoupper($method) == "POST" || strtoupper($method) == "GET") {
            if (strtoupper($method) == "POST")
                curl_setopt($this->ch, CURLOPT_POST, 1);
        } else {
            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        if (preg_match("/:/", $auth)) {
            curl_setopt($this->ch, CURLOPT_USERPWD, $auth);
        }
    }

    /*
     *	Setar proxy
    */

    function SetProxy($proxy = "")
    {
        $this->proxy = "$proxy";
    }

    /*
     *	Setar header na requisição
    */

    function setHeader($name, $value)
    {
        $this->headers[] = "$name:$value";
    }


    /*
     *	Pegar cabeçalho de Headers
    */

    function getHeaders($type = 1)
    {

        $C = substr($this->response, 0, (curl_getinfo($this->ch, CURLINFO_HEADER_SIZE) - 4));

        if ($type != "info") {
            return (!empty($C)) ? $C : false;
        } else {
            return curl_getinfo($this->ch);
        }
    }

    /*
     *	Executar toda a seção montada do cURL
    */

    public function exec()
    {


        if (!empty($this->cookie_file)) {
            if (is_array($this->cookie_file)) {
                $load = $this->cookie_file['load'];
                $save = $this->cookie_file['save'];
            } else {
                $load = $this->cookie_file;
                $save = $this->cookie_file;
            }
            curl_setopt($this->ch, CURLOPT_COOKIEJAR, dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $save);
            curl_setopt($this->ch, CURLOPT_COOKIEFILE, dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $load);
        }

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);

        if ($this->referer == true) {
            curl_setopt($this->ch, CURLOPT_AUTOREFERER, true);
        } else if (!empty($this->referer)) {
            curl_setopt($this->ch, CURLOPT_REFERER, $this->referer);
        }

        if ($this->postData != null && $this->postData != "") {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->postData);
        }

        if ($this->userAgent != null && $this->userAgent != "") {
            curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent);
        } else {
            curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; pt-BR; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.15");
        }

        if ($this->cookie != null && $this->cookie != "") {
            curl_setopt($this->ch, CURLOPT_COOKIE, $this->cookie);
        }

        if (!empty($this->proxy)) {
            curl_setopt($this->ch, CURLOPT_PROXY, $this->proxy);
        }

        @curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, $this->followLocation);

        if ($result = @curl_exec($this->ch)) {
            $tmp = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
            $this->responseText = substr($result, $tmp);
            $this->response = $result;
            $this->error = null;
            return true;
        } else {
            $this->response = null;
            $this->responseText = null;
            $this->error = @curl_error($this->ch);
            return false;
        }

    }

}