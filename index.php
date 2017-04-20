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
$encodedQuery = urlencode($query);

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
        <button class="formlinks" onclick="openFormTab(event, 'field-search')">Search</button>
        <button class="formlinks" onclick="openFormTab(event, 'collections-search')">Collections URL</button>
        <button class="formlinks" onclick="openFormTab(event, 'query-search')">Raw Query</button>
        <button class="formlinks" onclick="openFormTab(event, 'encoded-search')">Encoded Query</button>
    </div>

    <form action="#" method="POST">

        <div id="encoded-search" class='formcontent' style="display: none;">
            <textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="encoded_query" placeholder="URL Encoded Query" rows="10" style="width: 90%; margin: 5%;"><? echo $encodedQuery; ?></textarea>
            <input type="submit" value="Submit">
        </div>

        <div id="query-search" class='formcontent' style="display: none;">
            <textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="query" placeholder="Query" rows="10" style="width: 90%; margin: 5%;"><? echo $query; ?></textarea>
            <input type="submit" value="Submit">
        </div>

        <div id="collections-search" class='formcontent' style="display: none;">
            <p>Enter a collections.si.edu URL in the field below to view it's search results.</p>
            <textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="collections_url" placeholder="URL" rows="10" style="width: 90%; margin: 5%;"></textarea>
            <input type="submit" value="Submit">
        </div>

        <div id="field-search" class='formcontent' style="display: block;">
            Record Type:
            <select>
                <option value="edanmdm">edanmdm</option>
                <option value="ogmt">ogmt</option>
            </select>

        </div>
    </form>


    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'search-results')">Search Results</button>
        <button class="tablinks" onclick="openTab(event, 'json')">Raw JSON</button>
    </div>

    <div id= 'json' class='tabcontent' style="display: none;">
        <textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><? echo $resp; ?></textarea>
    </div>

    <div id='search-results' class='tabcontent' style="display: block;">
        <?php if (isset($json['rows'])){
            foreach ($json['rows'] as $key => $record) {
                echo $record['content']['descriptiveNonRepeating']['online_media']['media']['thumbnail'] . "\n";
                echo $record['content']['descriptiveNonRepeating']['title']['content'] . "\n";
                echo '-----------------------------------';
            }
        }?>
    </div>

    <script type="text/javascript" src="tabs.js"></script>

</body>
</html>