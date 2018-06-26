<?php
require __DIR__.'/vendor/autoload.php';
 
use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use \LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;

// set false for production
$pass_signature = true;
 
// set LINE channel_access_token and channel_secret
$channel_access_token = getenv('accessToken');
$channel_secret = getenv('channelSecret');

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

    // kode aplikasi nanti disini
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
                    // bla bla bla
                    if ($event['message']['type'] == 'text') {
                        if (strtolower($event['message']['text']) == 'hai') // perkenalan
                        {
                            $replyMessage = "Hai namaku adalah VISI, aku adalah virtual assisten kamu (love)";
                            $result = $bot->replyText($event['replyToken'], $replyMessage);
                        }

                        if (strtolower(substr($event['message']['text'], 0, 6)) == 'apakah') // kerang ajaib
                        {
                            $replyMessage = (rand(0, 1)) ? "iya" : "tidak";
                            $result = $bot->replyText($event['replyToken'], $replyMessage);
                        }

                        if (strtolower($event['message']['text']) == 'listbarang') // carousel market place
                        {
                            $carouselTemplateBuilder = new CarouselTemplateBuilder([
                                new CarouselColumnTemplateBuilder("Gantungan", "Rp 1.000.000,-", "https://arizalmhmd5.000webhostapp.com/barang1.jpg", [
                                    new MessageTemplateActionBuilder('Detail', 'tampil-barang1')
                                ]),
                                new CarouselColumnTemplateBuilder("Sticker", "Rp 1.000.000,-", "https://arizalmhmd5.000webhostapp.com/barang2.jpg", [
                                    new MessageTemplateActionBuilder('Detail', 'tampil-barang2')
                                ]),
                            ]);
                            $templateMessage = new TemplateMessageBuilder('Daftar Merchandise', $carouselTemplateBuilder);
                            $result = $bot->replyMessage($event['replyToken'], $templateMessage);
                        }

                        if (strtolower(substr($event['message']['text'], 0, 6)) == 'tampil') // tampilkan detail product
                        {
                            $split = str_split($event['message']['text'], 7);
                            $multipleMessageBuilder = new MultiMessageBuilder;
                            $multipleMessageBuilder->add(new ImageMessageBuilder("https://arizalmhmd5.000webhostapp.com/" . $split[1] . ".jpg", "https://arizalmhmd5.000webhostapp.com/" . $split[1] . ".jpg")) // tampilkan gambar product
                                ->add(new TextMessageBuilder("Deskripsi Barang \nbla bla bla", 'fitur ini hanya untuk melihat saja, untuk pre-order tekan iya untuk langsung di arahkan ke website resmi')) // deskripsi product
                                ->add(new TemplateMessageBuilder(
                                    'Confirmation pre-Order',
                                    new ConfirmTemplateBuilder( // confirmation pre-Order
                                        "Jadi pre-Order",
                                        [
                                            new UriTemplateActionBuilder('Ya', 'http://rajabrawijaya.ub.ac.id/'),
                                            new MessageTemplateActionBuilder('Tidak', 'gak jadi hehe'),
                                        ]
                                    )
                                ));
                            $result = $bot->replyMessage($event['replyToken'], $multipleMessageBuilder);
                        }

                        if (strtolower($event['message']['text']) == 'groupid') // tampilkan groupId
                        {
                            $result = $bot->replyText($event['replyToken'], $event['source']['groupId']);
                        }

                        if (strtolower($event['message']['text']) == 'myuserid')
                        {
                            $result = $bot->replyText($event['replyToken'], $userId);
                        }
                    }
                } else { // jika pc
                    // bla bla bla
                    if ($event['message']['type'] == 'text') {
                        if (strtolower($event['message']['text']) == 'hai') // perkenalan
                        {
                            $replyMessage = "Hai namaku adalah VISI, aku adalah virtual assisten kamu (love)";
                            $result = $bot->replyText($event['replyToken'], $replyMessage);
                        }

                        if (strtolower(substr($event['message']['text'], 0, 6)) == 'apakah') // kerang ajaib
                        {
                            $replyMessage = (rand(0, 1)) ? "iya" : "tidak";
                            $result = $bot->replyText($event['replyToken'], $replyMessage);
                        }

                        if (strtolower($event['message']['text']) == 'listbarang') // carousel market place
                        {
                            $carouselTemplateBuilder = new CarouselTemplateBuilder([
                                new CarouselColumnTemplateBuilder("Gantungan", "Rp 1.000.000,-", "https://arizalmhmd5.000webhostapp.com/barang1.jpg", [
                                    new MessageTemplateActionBuilder('Detail', 'tampil-barang1')
                                ]),
                                new CarouselColumnTemplateBuilder("Sticker", "Rp 1.000.000,-", "https://arizalmhmd5.000webhostapp.com/barang2.jpg", [
                                    new MessageTemplateActionBuilder('Detail', 'tampil-barang2')
                                ]),
                            ]);
                            $templateMessage = new TemplateMessageBuilder('Daftar Merchandise', $carouselTemplateBuilder);
                            $result = $bot->replyMessage($event['replyToken'], $templateMessage);
                        }

                        if (strtolower(substr($event['message']['text'], 0, 6)) == 'tampil') // tampilkan detail product
                        {
                            $split = str_split($event['message']['text'], 7);
                            $multipleMessageBuilder = new MultiMessageBuilder;
                            $multipleMessageBuilder->add(new ImageMessageBuilder("https://arizalmhmd5.000webhostapp.com/" . $split[1] . ".jpg", "https://arizalmhmd5.000webhostapp.com/" . $split[1] . ".jpg")) // tampilkan gambar product
                                ->add(new TextMessageBuilder("Deskripsi Barang \nbla bla bla", 'fitur ini hanya untuk melihat saja, untuk pre-order tekan iya untuk langsung di arahkan ke website resmi')) // deskripsi product
                                ->add(new TemplateMessageBuilder(
                                    'Confirmation pre-Order',
                                    new ConfirmTemplateBuilder( // confirmation pre-Order
                                        "Jadi pre-Order",
                                        [
                                            new UriTemplateActionBuilder('Ya', 'http://rajabrawijaya.ub.ac.id/'),
                                            new MessageTemplateActionBuilder('Tidak', 'gak jadi hehe'),
                                        ]
                                    )
                                ));
                            $result = $bot->replyMessage($event['replyToken'], $multipleMessageBuilder);
                        }
                    }
                }
            }
        }
    }
});
$app->run();