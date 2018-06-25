<?php
require __DIR__.'/vendor/autoload.php';
 
use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;

// set false for production
$pass_signature = true;
 
// set LINE channel_access_token and channel_secret
$channel_access_token = getenv('ch_acc_token');
$channel_secret = getenv('ch_secret');

// inisiasi objek bot
$httpClient = new CurlHTTPClient($channel_access_token);
$bot = new LINEBot($httpClient, ['channelSecret' => $channel_secret]);
$configs = [
    'settings' => ['displayErrorDetails' => true],
];
$app = new Slim\App($configs);

// buat route untuk url homepage
$app->get('/', function($req, $res)
{
    echo "WELCOME";
});

// buat route untuk webhook
$app->post('/webhook', function($request, $response) use ($bot, $pass_signature){
    // get request body and line signature header
    $body = file_get_contents('php://input');
    $signature = isset($_SERVER['HTTP_X_LINE_SIGNATURE']) ? $_SERVER['HTTP_X_LINE_SIGNATURE'] : '';
 
    // log body and signature
    file_put_contents('php://stderr', 'Body: ' . $body);

    if ($pass_signature === false) {
        // is LINE_SIGNATURE exists in request header?
        if (empty($signature)) {
            return $response->withStatus(400, 'Signature not set');
        }
 
        // is this request comes from LINE?
        if (!SignatureValidator::validateSignature($body, $channel_secret, $signature)) {
            return $response->withStatus(400, 'Invalid signature');
        }
    }

    // kode aplikasi nanati disini
    $data = json_decode($body, true);
    if (is_array($data['events'])) {
        foreach ($data['events'] as $event) {
            if($event['type'] == 'message')
            {
                $userId = $event['source']['userId'];
                $getprofile = $bot->getProfile($userId);
                $profile = $getprofile->getJSONDecodedBody();

                // jika group
                if ($event['source']['type'] == 'group' or $event['source']['type'] == 'room') 
                {
                    // bla bla
                    if ($event['message']['type'] == 'text') {
                        $userId = $event['source']['userId'];
                        $getprofile = $bot->getProfile($userId);
                        $profile = $getprofile->getJSONDecodedBody();

                        if ($event['message']['text'] == 'hai') {
                            $replyMessage = "Hai namaku adalah VISI, aku adalah virtual assisten kamu (love)";
                            $result = $bot->replyText($event['replyToken'], $replyMessage);
                        }
                    }
                } else { // jika pc
                    if ($event['message']['type'] == 'text') {
                        $userId = $event['source']['userId'];
                        $getprofile = $bot->getProfile($userId);
                        $profile = $getprofile->getJSONDecodedBody();
                        
                        if ($event['message']['text'] == 'siapa kamu ?') {
                            $replyMessage = "Hai namaku adalah VISI, aku adalah virtual assisten kamu (love)";
                            $result = $bot->replyText($event['replyToken'], $replyMessage);
                        }
                    }
                }
            }
        }
    }
});
$app->run();