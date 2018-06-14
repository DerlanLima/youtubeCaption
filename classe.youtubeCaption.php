<?php
include 'classe.curl.php'; //Curl class by Ali Saleh.

/**
 * Class youtubeCaption
 * Author Derlan Lima
 * Email derlanrj@gmail.com
 */

class youtubeCaption
{
    /**
     * Function to get available languages in Youtube's video
     * @param $videoId
     */

    private $urltt = "https://www.youtube.com/api/timedtext?";
    private $urlvid = "https://www.youtube.com/watch?v=";
    private $videoId;

    public function videoId($videoId){
        $this->videoId = $videoId;
    }

    private function _get($call){
        $curl = new cURL();
        $curl->open("GET", $this->urltt.$call);

        return $curl->exec()?$curl->responseText:false;
    }

    public function getAvailableLangs(){
        if(!$listcc = $this->_get("type=list&v=".$this->videoId)) die ("Failed to load TTS list");

        if (!$listcc = simplexml_load_string($listcc)) die('Failed to Read XML');

        if (!$listcc->track) return [];

        $cc = array();
        foreach ($listcc->track as $track) {
            $cc[(string)$track['lang_code']] = $track;
        }

        return $cc;
    }

    public function getCaptionText($lang){
        if(!$capXml = $this->_get("v=".$this->videoId."&lang=".$lang)) die ("Failed to load caption");

        if(!$capXml = simplexml_load_string($capXml)) die ("Failed to read caption XML");

        if (!isset($capXml->text)) die ("No text found");

        $caption = [];
        foreach ($capXml->text as $txt) {
            $txt = (string) trim(html_entity_decode(htmlspecialchars_decode($txt, ENT_QUOTES))); //Trim
            $txt = preg_replace('/\n/m',' ',$txt); //Breaklikes to space

            //If the first char is not lowercase(uppercase, numbers, characteres), so break 2 lines to make a paragraph
            if(!ctype_lower($txt[0]) and !preg_match('/[áàãâéêíóôõúüçñ]/', $txt[0])) $txt = "\n\n".$txt;
            $caption[] = $txt;
        }
        return trim(implode(" ", $caption));
    }


}

