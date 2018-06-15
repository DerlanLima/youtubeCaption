<?php
include 'classe.curl.php';

/**
 * Class youtubeCaption
 * Author Derlan Lima
 * Email derlanrj@gmail.com
 */
class youtubeCaption
{

    private $urltt = "https://www.youtube.com/api/timedtext?";
    private $urlapi = "https://www.googleapis.com/youtube/v3/";
    private $videoId;
    private $apiKey;


    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;
    }

    public function videoId($videoId)
    {
        $this->videoId = $videoId;
    }

    /**
     * Function to get available languages in Youtube's video
     * @return ArrayObject
     */
    public function getAvailableLangs()
    {
        if (!$listcc = $this->_get("type=list&v=" . $this->videoId)) die ("Failed to load TTS list");

        if (!$listcc = simplexml_load_string($listcc)) die('Failed to Read XML');

        if (!$listcc->track) return [];

        $cc = array();
        foreach ($listcc->track as $track) {
            $cc[(string)$track['lang_code']] = $track;
        }

        return $cc;
    }

    private function _get($call, $type = "tt")
    {
        $curl = new cURL();
        $url = $type == "tt" ? $this->urltt : $this->urlapi;
        $curl->open("GET", $url . $call);

        return $curl->exec() ? $curl->responseText : false;
    }

    /**
     * Function to get caption and parse text
     * @param $lang
     * @return string
     */
    public function getCaptionText($lang)
    {
        if (!$capXml = $this->_get("v=" . $this->videoId . "&lang=" . $lang)) die ("Failed to load caption");

        if (!$capXml = simplexml_load_string($capXml)) die ("Failed to read caption XML");

        if (!isset($capXml->text)) die ("No text found");

        $caption = [];
        foreach ($capXml->text as $txt) {
            $txt = (string)trim(html_entity_decode(htmlspecialchars_decode($txt, ENT_QUOTES))); //Trim
            $txt = preg_replace('/\n/m', ' ', $txt); //Breaklikes to space

            //If the first char is not lowercase(uppercase, numbers, characteres), so break 2 lines to make a paragraph
            if (!ctype_lower($txt[0]) and !preg_match('/[áàãâéêíóôõúüçñ]/', $txt[0])) $txt = "\n\n" . $txt;
            $caption[] = $txt;
        }
        return trim(implode(" ", $caption));
    }


    /**
     * Function to search videos.
     * @param $query
     * @param null $page nextPageToken returned in json
     * @param string $country | use "ISO 3166-1 Alpha 2" code to country. Ex: BR, US, CA, AR.. etc.
     * @param string $caption | "any" to no filter by Caption, "closedCaption" to list only videos with Caption,  or "none" to list only videos WITHOUT caption
     * @return ArrayObject
     */
    public function searchByQuery($query, $page = null, $caption = "closedCaption", $country = "BR")
    {
        $query = urlencode($query);
        if (empty($this->apiKey)) die("Api key need be set in constructor method");
        $jsonSearch = $this->_get("search?part=snippet%2C+id&maxResults=50&order=relevance&q={$query}&regionCode={$country}&type=video&pageToken={$page}&videoCaption={$caption}&key={$this->apiKey}", "api");
        $jsonSearch = json_decode($jsonSearch);
        //print_r($jsonSearch);

        return $jsonSearch;

    }

    /**
     * Function to get videos from a Channel
     * @param $username
     * @param null $page
     * @param string $caption
     * @return ArrayObject
     */
    public function getVideosFromChannel($username, $page = null, $caption = "closedCaption")
    {

        $username = urlencode($username);

        $channelId = $this->getChannelId($username);
        if (!$channelId) {
            $channelId = $username;
        }

        if (empty($this->apiKey)) die("Api key need be set in constructor method");
        $jsonSearch = $this->_get("search?part=snippet%2C+id&maxResults=50&channelId={$channelId}&type=video&pageToken={$page}&videoCaption={$caption}&key={$this->apiKey}", "api");
        $jsonSearch = json_decode($jsonSearch);
        
        return $jsonSearch;

    }


    /**
     * Private function to get ChannelId by Username
     * @param $username
     * @return string
     */
    private function getChannelId($username)
    {
        $query = urlencode($username);
        if (empty($this->apiKey)) die("Api key need be set in constructor method");

        $channelJson = $this->_get("channels?part=id&forUsername={$query}&key={$this->apiKey}", "api");
        $channelJson = json_decode($channelJson);

        if (!isset($channelJson->items[0]->id) or empty($channelJson->items[0]->id)) return false;

        return $channelJson->items[0]->id;
    }


}

