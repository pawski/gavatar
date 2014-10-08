<?php
ini_set('display_errors', false);
ini_set('error_reporting', E_ALL);
set_time_limit(0);

// http://picasaweb.google.com/data/entry/api/user/henryk.nowak@gmail.com?alt=json
$email = filter_var($_GET['u'], FILTER_VALIDATE_EMAIL);

if (!$email) {
    die('Invalid input');
}

$json = retrive('http://picasaweb.google.com/data/entry/api/user/' . $email . '?alt=json');
$jsonDecoded = json_decode($json);

if (isset($jsonDecoded->entry->{'gphoto$thumbnail'}->{'$t'})) {
    $image = retrive($jsonDecoded->entry->{'gphoto$thumbnail'}->{'$t'});
} else {
	$image = retrive(__DIR__ . '/anonymous.jpg');
}

if (!isset($_GET['raw'])) {
    echo '<img src="data:image/jpg;base64,'. base64_encode($image) . '"/>';
} else {
	header("Content-type: " . image_type_to_mime_type(IMAGETYPE_JPG));
    echo $image;
    die();
}


function retrive($url) {
    
    $cacheValidTime = 3600 * 24;
    $cacheFileName = md5($url);
    $cacheFilePath = './cache/' . $cacheFileName;
    
    if(file_exists($cacheFilePath) && (filemtime($cacheFilePath) + $cacheValidTime) > time())
    {
        $response = file_get_contents($cacheFilePath);
    }
    else
    {
        $response = file_get_contents($url);
        file_put_contents($cacheFilePath, $response);
    }
    
    return $response;
}
