<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// get posted data
$data = json_decode(file_get_contents("php://input"));

// make sure json data is not incomplete
if (
    !empty($data->query) &&
    !empty($data->appPackageName) &&
    !empty($data->messengerPackageName) &&
    !empty($data->query->sender) &&
    !empty($data->query->message)
    ) {
        
        // package name of AutoResponder to detect which AutoResponder the message comes from.
        $appPackageName = $data->appPackageName;
        // package name of messenger to detect which messenger the message comes from
        $messengerPackageName = $data->messengerPackageName;
        // name/number of the message sender (like shown in the Android notification)
        $sender = $data->query->sender;
        // text of the incoming message
        $message = $data->query->message;
        // is the sender a group? true or false
        $isGroup = $data->query->isGroup;
        // id of the AutoResponder rule which has sent the web server request
        $ruleId = $data->query->ruleId;
        
        http_response_code(200);
        $ares = new responder($message);
        $ares->send();
        /*echo json_encode(array("replies" => array(
        array("message" => "AssalamuAlaikum " . $sender . "!\n: Pesan antum sedang di proses.\nMohon Menunggu..."),
        array("message"=>$isGroup)
        )));
        */
        
        
        
        // send one or multiple replies to AutoResponder
        /**/
        
        // or this instead for no reply:
        // echo json_encode(array("replies" => array()));
    }
    // tell the user json data is incomplete
    else
    {
        
        // set response code - 400 bad request
        http_response_code(400);
        
        // send error
        echo json_encode(array("replies" => array(
            array("message" => "semua ada hikmah nya")
            //array("message" => "JSON data is incomplete. Was the request sent by AutoResponder?")
            )));
    }
    
    
