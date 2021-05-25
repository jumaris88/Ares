<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// add this php file to your web server and enter the complete url in AutoResponder (e.g. https://www.example.com/api_autoresponder.php)
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }
    
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

function exceptions_error_handler($severity, $message, $filename, $lineno) {
    throw new ErrorException($message, 0, $severity, $filename, $lineno);
}

set_error_handler('exceptions_error_handler');

// access a custom header added in your AutoResponder rule
// replace XXXXXX_XXXX with the name of the header in UPPERCASE (and with '-' replaced by '_')
//$myheader = $_SERVER['HTTP_XXXXXX_XXXX'];
//include dirname(__FILE__)."/alquran.php";
//include dirname(__FILE__)."/tanyaalquran.php";
require "vendor/autoload.php";
use ares\responder;

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->query) &&
    !empty($data->appPackageName) &&
    !empty($data->messengerPackageName) &&
    !empty($data->query->sender) &&
    !empty($data->query->message) or
    !empty($data->message) 
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
        try {
            $ares= new responder($message);
            $ares->send();
        }catch(Exception $e){
            echo json_encode(array("replies" => array(array("message" => ""),array(""=>""))));
        }
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
            array("message" => "",""=>"")
            //array("message" => "JSON data is incomplete. Was the request sent by AutoResponder?")
            )));
    }
    
    