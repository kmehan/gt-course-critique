<?php

/**
 *   Takes in a query for ElasticSearch
 *   Outputs the 'hits' in JSON
 */
$blacklist = array('"', "'", '\\');
if (empty($_GET['query'])) {
    die();
}
$queryString = trim($_GET['query']);
$queryString = str_replace($blacklist, "", $queryString);
$queryString = preg_replace('/^([A-Za-z]*)(\d*)$/', '$1 $2', $queryString);
$ch = curl_init('http://web-misc1.gatech.edu:9200/class,prof/_search');
$qry = '{
          "query" : { 
            "query_string" : { 
              "query" : "' . $queryString . '*"
             } 
          }
        }';

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_POSTFIELDS, $qry);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($qry))
);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); //Speed enhancement for Curl
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
echo curl_exec($ch);
curl_close();
