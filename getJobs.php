<?php

require 'db.php';
require 'vendor/autoload.php';

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Psr7\Request;

$client = new Client();

switch($_GET['method']){

  case "run":

    $headers = [
      'Content-Type' => 'application/json'
    ];

    $body = '{
        "location": "dubai",
        "proxy": {
            "useApifyProxy": true,
            "apifyProxyGroups": [
                "RESIDENTIAL"
            ]
        },
        "publishedAt": "r86400",
        "rows": 100,
        "title": "product"
    }';

    // Start Run
    $requestRun = new Request('POST', 'https://api.apify.com/v2/acts/bebity~linkedin-jobs-scraper/runs?token=apify_api_oUFADUs12mZSQPFwJSMv3GZBhEV3f12wTzyY', $headers, $body);
    $resRun = $client->sendAsync($requestRun)->wait();  

    break;

  case "getData":

    // get Dataset
    $allJobs = json_decode(file_get_contents('https://api.apify.com/v2/acts/bebity~linkedin-jobs-scraper/runs/last/dataset/items?token=apify_api_oUFADUs12mZSQPFwJSMv3GZBhEV3f12wTzyY'));

    foreach ($allJobs as $data) {

      if(count((array)$data) > 10){

          $sql = "
          SELECT * FROM jobs
          WHERE 
            position = '".addslashes($data->title)."' 
            AND company = '".addslashes($data->companyName)."' 
            AND location = '".addslashes($data->location)."'";
          $result = $mysqli->query($sql);
          $resultFound = $result->num_rows;

          if($resultFound == 0){

            if($data->applyType == 'EASY_APPLY'){
              $easyApply = 1;
            }else{
              $easyApply = 0;
            }

            $insert = "INSERT INTO jobs(position,description,company,location,date,agoTime,jobUrl,easy_apply,apply_url,source,status, work_type, sector) VALUES(
              '".addslashes($data->title)."',
              '".base64_encode($data->description)."',
              '".addslashes($data->companyName)."',
              '".addslashes($data->location)."',
              '".strtotime($data->postedTime)."',
              '".$data->postedTime."',
              '".$data->jobUrl."',
              '".$easyApply."',
              '".$data->applyUrl."',
              'linkedin',
              'new',
              '".$data->workType."',
              '".$data->sector."'
            )";
            $mysqli->query($insert);
          }
        }
    }

    break;

    case "getRun":
      $getRun = json_decode(file_get_contents('https://api.apify.com/v2/acts/bebity~linkedin-jobs-scraper/runs/last?token=apify_api_oUFADUs12mZSQPFwJSMv3GZBhEV3f12wTzyY'));
      echo $getRun->data->status;
    // echo 'RUNNING';
    // echo 'SUCCEEDED';
      break;
}
