<?php
include '../classe.youtubeCaption.php';
$ytc = new youtubeCaption();
$ytc->videoId("j7pT7mZmsW8"); //Set videoId

$avaialableTracks = $ytc->getAvailableLangs(); //Get ALL available languages


if(isset($avaialableTracks['pt'])){ //Verify if 'pt' is available
    print_r($ytc->getCaptionText("pt")); //Get caption portuguese
}else{
    print "PT Caption not available";
}

