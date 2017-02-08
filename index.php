<?php

function dumper($s, $b = 1){
    echo '<pre>' . var_export($s, true) . '</pre>';
    if($b == 1){
        die();
    }
}

function _get_nonce($length = 8){
    $return = '';
    for ($i=0; $i < $length; $i++) { 
        $possible = array('1','2','3','4','5','6','7','8','9','0','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
        $return = $return . $possible[array_rand($possible)];
    }
    return $return;
}

//authorization settings
$config = require('config.php');
$host = $config->host;
$endpoint = 'metadata/v1.1/metadata/search';
$appId = $config->app_id;
$authKey = $config->auth_key;
// search query
$query = 'rows=1&fqs=' . urlencode('["type:' . $_GET["type"] . '"]');
// $query = 'id=' . urlencode($_GET["id"]);
$nonce = _get_nonce(15);
date_default_timezone_set('America/New_York');
$date = date('Y-m-d H:i:s');

$authContent = base64_encode(sha1($nonce . "\n" . (isset($query) ? $query . "\n" : '') . $date . "\n" . $authKey ));

// Get cURL resource
$curl = curl_init();

// Set custom headers
curl_setopt_array($curl, array(
    CURLOPT_HTTPHEADER => array(
        'X-AppId:' . $appId,
        'X-RequestDate:' . $date,
        'X-AuthContent:' . $authContent,
        'X-Nonce:' . $nonce,
    ),
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $host . $endpoint . (isset($query) ? '?' . $query : ''),
    ));

// Send the request & save response to $resp
$resp = curl_exec($curl);
// Close request to clear up some resources
curl_close($curl);

$json = json_decode($resp, true);

// do whatever you want with json here

dumper($resp);