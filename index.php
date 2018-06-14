<?php
include 'classe.youtubeCaption.php';
$ytc = new youtubeCaption();
$ytc->videoId("j7pT7mZmsW8");

$avaialableTracks = $ytc->getAvailableLangs();
//print_r($avaialableTracks);

if(isset($avaialableTracks['pt'])){
    print_r($ytc->getCaptionText("pt"));
}else{
    print "Português não disponível";
}

