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
$endpoint = 'metadata/v1.1/metadata/search.htm';
$appId = $config->app_id;
$authKey = $config->auth_key;
$query = isset($_POST['query']) ? $_POST['query'] : "";
// $query = 'rows=1&fqs=' . urlencode('["type:' . $_GET["type"] . '"]');
// $query = 'rows=1&s=' . urlencode('["type:' . $_GET["type"] . '"]');
// $query = (isset($_GET["id"]) ? 'id=' . urlencode($_GET["id"]) : '') . (isset($_GET["url"]) ? '&url=' . urlencode($_GET["url"]) : '');
// $query = (isset($_GET["type"]) ? 'type=' . urlencode($_GET["type"]) : '') . (isset($_GET["start"]) ? '&start=' . urlencode($_GET["start"]) : '') . (isset($_GET["rows"]) ? '&rows=' . urlencode($_GET["rows"]) : '') . (isset($_GET["status"]) ? '&status=' . urlencode($_GET["status"]) : '');
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

?>



<html>
<head>
    <link rel='stylesheet' type='text/css' href='style.css'>
</head>
<body>

    <div class="form-tab">
        <button class="tablinks" onclick="openTab(event, 'field-search')">Field Search</button>
        <button class="tablinks" onclick="openTab(event, 'query-search')">Query Search</button>
    </div>

    <div id="field-search" class='form-tabcontent'>

    </div>

    <div id="query-search" class='form-tabcontent'>
        <form action="#" method="POST">
            <textarea name="query" placeholder="Query" rows="10" value="Mickey" style="width: 90%; margin: 5%;"></textarea>
            <input type="submit" value="Submit">
        </form> 
    </div>

    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'json')">JSON</button>
        <button class="tablinks" onclick="openTab(event, 'search-results')">Structured Search</button>
    </div>

    <div id= 'json' class='tabcontent'>
        <textarea><? echo $resp; ?></textarea>
    </div>

    <div id='search-results' class='tabcontent'>
        <textarea>
        <?php if (isset($json['rows'])){
            foreach ($json['rows'] as $key => $record) {
                echo $record['id'] . "\n";
            }
        }?>
        </textarea>
    </div>

    <script type="text/javascript" src="tabs.js"></script>

</body>
</html>