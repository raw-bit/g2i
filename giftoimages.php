<?php

// All in
if(!empty($_GET['search']) && isset($_GET['search'])) {
  $search = $_GET['search'];
} else {
  $wordApi = "https://random-word-api.herokuapp.com/word?number=1";
  $wordResult = apiCall($wordApi);
  $search = json_decode($wordResult);
}

function apiCall($url) {
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
  $result = curl_exec($curl);
  curl_close($curl);
  return $result;
}

// Out with the old
$gifFile = "./downloaded.gif";
if(file_exists($gifFile)) {
        unlink($gifFile);
}
$folder = 'imgs';
$files = glob($folder . '/*');
foreach($files as $file){
    if(is_file($file)){
        unlink($file);
    }
}

// In with the new
$apiKey = "dhyKXWGT4tRHkK24m6bKPudLr1wWMdJa";
$url = "https://api.giphy.com/v1/gifs/search?api_key=" . $apiKey . "&q=" . $search;
$result = apiCall($url);
$json = json_decode($result, true);
$gifNumber = count($json['data']);
$selectNumber = rand(0, $gifNumber - 1);
$gifImage = $json['data'][$selectNumber]["images"]["fixed_height"];
$gifUrl = $gifImage["url"];
$gifWidth = $gifImage["width"];
$gifHeight = $gifImage["height"];
$path = "./downloaded.gif";
$result = apiCall($gifUrl);
file_put_contents($path, $result);
exec("convert ". $path . " -coalesce imgs/gif_%05d.gif");

// All out
$fileList = glob($folder . "/*.gif");
$fileNumber = count($fileList);

$gifDimensions = array("width" => $gifWidth, "height" => $gifHeight);
$gifData = array("url" => $gifUrl, "dimensions" => $gifDimensions);
$fileData = array("total" => $fileNumber, "filelist" => $fileList);
$data = array("searchphrase" => $search, "files" => $fileData, "original" => $gifData);

header( "HTTP/1.1 200 OK" );
header("Access-Control-Allow-Origin: *");

echo(json_encode($data));

?>
