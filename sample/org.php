<?php
session_start();
$orgID = $_POST['orgID'];
	
$data = array(
    "apikey"=> "txjamlL8",
    "objectType"=>"orgs",
    "objectAction"=>"publicSearch",
    "search"=> array(
        "resultStart"=> "0",
        "resultLimit"=> "100",
        "resultSort"=> "orgID",
        "resultOrder"=> "asc",
	"filters"=> array(
		array(
			 "fieldName"=> "orgID",
               		 "operation"=> "greaterthan",
			 "criteria"=> $orgID,
		),
	),      
        "filterProcessing"=> "1",
	"fields"=> array("orgID","orgLocation","orgName","orgAddress","orgCity","orgState","orgPostalcode","orgCountry","orgPhone","orgEmail","orgWebsiteUrl","orgFacebookUrl","orgAdoptionUrl","orgServeAreas","orgAdoptionProcess","orgAbout","orgServices","orgMeetPets","orgType","orgLocationDistance"),
);
);
$jsonData = json_encode($data);
$ch = curl_init('https://api.rescuegroups.org/http/v2.json');

curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
curl_setopt($ch, CURLOPT_URL, "https://api.rescuegroups.org/http/v2.json");

curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);

if(curl_errno($ch)) {
        $results = curl_error($ch);
}else{
        curl_close($ch);
        $results = $result;
}


echo json_encode($results);


$result = postToApi($data);
if (!$result){
        //echo "login issue wih the API.";
        exit;
}
print_r($result);
exit;

function postJson($url, $json){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
                return array(
                        "result" => "",
                        "status" => "error",
                        "error" => curl_error($ch)
                );
        }else{
                curl_close($ch);
        }
        return array(
                "status" => "ok",
                "error" => "",
                "result" => $result,
        );
}


function postToApi($data){
        $resultJson = postJson($GLOBALS["httpApiUrl"], $data);
        if ($resultJson["status"] == "ok"){
                $result = json_decode($resultJson["result"], true);
                $jsonError = getJsonError();
                if (!$jsonError && $resultJson["status"] == "ok") {
                        return $result;
                }else{
                        return array(
                                "status" => "error",
                                "text" => $result["error"] . $jsonError,
                                "errors" => array()
                        );
                }
        }else return false;
}

function getJsonError() {
        switch (json_last_error()){
        case JSON_ERROR_NONE:
                return false;
                break;
        case JSON_ERROR_DEPTH:
                return "Maximum stack depth exceeded";
                break;
        case JSON_ERROR_STATE_MISMATCH:
                return "Underflow or the modes mismatch";
                break;
        case JSON_ERROR_CTRL_CHAR:
                return "unexpected control character found";
                break;
        case JSON_ERROR_SYNTAX:
                return "Syntax error, malformed JSON";
                break;
        default:
                return "Unknown error";
                break;
        }
//}
}
?>
