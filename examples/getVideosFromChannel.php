<?php
include '../classe.youtubeCaption.php';
$ytc = new youtubeCaption("Your-api-key"); //To search it's necessary an api key

$videos = array();
$page = "";

//Get 100 videos by channel
while (count($videos) < 500) {
    $result = $ytc->getVideosFromChannel("UC4rlAVgAK0SGk-yTfe48Qpw", $page); //Use channel's username or channelId

    if (!isset($result->nextPageToken)) break;
    $page = $result->nextPageToken;

    if (isset($result->items) or !empty($result->items)) {
        foreach ($result->items as $video) {
            $videos[] = $video;
        }
    }

    sleep(0.2);
}

var_dump($videos);

