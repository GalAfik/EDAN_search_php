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
$nonce = _get_nonce();

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
        <button class="formlinks" onclick="openFormTab(event, 'field-search')">Filters</button>
        <button class="formlinks" onclick="openFormTab(event, 'collections-search')">URL</button>
        <button class="formlinks" onclick="openFormTab(event, 'query-search')">Query</button>
    </div>

    <form action="#" method="POST">

        <div id="query-search" class='formcontent'>
            <br/>
            <span class="label">Raw Decoded Query</span>
            <textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="query" rows="10" style="width: 90%; margin: 5%;"><? echo $query; ?></textarea>
            <p class="description">You may enter your search filters as a raw query into this field in order to narrow down your search results.</p>
            
            <span class="label">Encoded Query</span>
            <textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="encoded_query" rows="10" style="width: 90%; margin: 5%;"><? echo $encodedQuery; ?></textarea>
            <p class="description">This is a URL-safe encoded query to use in your application.</p>
            <input type="submit" value="Submit">
        </div>

        <div id="collections-search" class='formcontent'>
            <br/>
            <span class="label">Collections URL</span>
            <textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="collections_url" placeholder="Enter URL Here" rows="10" style="width: 90%; margin: 5%;"></textarea><p class="description">Paste a <a href="collections.si.edu">collection.si.edu</a> URL into the field above to replicate the search on this page. You may modify the search using the Search Filters above or by adding requirements to the Raw Query below.</p>
            <input type="submit" value="Submit">
        </div>

        <div id="field-search" class='formcontent' style="display: block;">
            <br/>
            <span class="label">q</span><br/>
            <input type="textfield"><br/><p class="description">The q parameter is normally the main query for the request. See Solr query syntax for more information on different types of queries and syntaxes.</p>
            <span class="label">fqs</span><br/>
            <input type="textfield"><br/><p class="description">Applies a filter query to the search results. "fq" stands for Filter Query. This parameter can be used to specify a query that can be used to restrict the super set of documents that can be returned, without influencing score. It can be very useful for speeding up complex queries since the queries specified with fq are cached independently from the main query.</p>
            <span class="label">rows (100 max.)</span><br/>
            <input type="textfield"><br/><p class="description">This parameter is used to paginate results from a query. It specify the maximum number of documents from the complete result set to return to the client for every request. You can consider it as the maximum number of result appear in the page. (default_value value:10)</p>
            <span class="label">start (Do not exceed the maximum number of records!)</span><br/>
            <input type="textfield"><br/><p class="description">This parameter is used to paginate results from a query. When specified, it indicates the offset (by default, 0) in the complete result set for the queries where the set of returned documents should begin.</p>
            <span class="label">Record Type:</span>
            <select>
                <option value="edanmdm">edanmdm</option>
                <option value="ogmt">ogmt</option>
            </select><br/><p class="description">This selection will limit the results to only show records of the selected type. <span style="color: red;">More options are being added.</span></p>

            <span class="label">online_media_type</span><br/>
            <input type="textfield"><br/>
            <span class="label">object_type</span><br/>
            <input type="textfield"><br/>
            <span class="label">topic</span><br/>
            <input type="textfield"><br/>
            <span class="label">name</span><br/>
            <input type="textfield"><br/>
            <span class="label">culture</span><br/>
            <input type="textfield"><br/>
            <span class="label">language</span><br/>
            <input type="textfield"><br/>
            <span class="label">place</span><br/>
            <input type="textfield"><br/>
            <span class="label">date</span><br/>
            <input type="textfield"><br/>
            <span class="label">data_source</span><br/>
            <input type="textfield"><br/>
            <span class="label">tax_kingdom</span><br/>
            <input type="textfield"><br/>
            <span class="label">tax_phylum</span><br/>
            <input type="textfield"><br/>
            <span class="label">tax_class</span><br/>
            <input type="textfield"><br/>
            <span class="label">tax_order</span><br/>
            <input type="textfield"><br/>
            <span class="label">tax_family</span><br/>
            <input type="textfield"><br/>
            <span class="label">scientific name</span><br/>
            <input type="textfield"><br/>
            <input type="submit" value="Submit">

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