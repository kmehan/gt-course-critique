<?php
/**
*   Takes in a query for ElasticSearch
*   Outputs the 'hits' in JSON
 */
include("config.php");
$blacklist = array('"', "'", '\\');
if(empty($_GET['query'])) {
  die();
}
$queryString = trim($_GET['query']);
$queryString = str_replace($blacklist, "", $queryString);
$ch = curl_init($elasticSearchURL);
$qry = '{
          "query" : { 
            "query_string" : { 
              "query" : "'.$queryString.'*"
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
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,1);
curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
echo curl_exec($ch);
curl_close();
?>
