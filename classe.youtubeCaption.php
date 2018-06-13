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

    private function _get($call){
        $curl = new cURL();
        $curl->open("GET", $this->urltt.$call);

        return $curl->exec()?$curl->responseText:false;
    }

    public function getAvailableLangs($videoId){
        if(!$listcc = $this->_get("type=list&v=mRVnB4TXPdU")) die ("Failed to load TTS list");

        if (!$listcc = simplexml_load_string($listcc)) die('Failed to Read XML');

        if (!$listcc->track) return [];

        $cc = array();
        foreach ($listcc->track as $track) {
            $cc[(string)$track->lang_code] = $track;
        }

        return $cc;
    }


}

