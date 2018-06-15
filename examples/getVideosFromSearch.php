<?php
$time_start = microtime(true);
include '../classe.youtubeCaption.php';
$ytc = new youtubeCaption("your-api-key"); //To search it's necessary an api key

$videos = array();
$page = "";

//Get 100 videos by Search
while (count($videos) < 500) {
    $result = $ytc->searchByQuery("Tips to Draw Better", $page);

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

// Display Script End time
$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start) / 60;

//execution time of the script
echo '<b>Total Execution Time:</b> ' . $execution_time . ' Mins';
