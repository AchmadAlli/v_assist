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
use LINE\LINEBot\Event\MessageEvent\TextMessage;

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
                $textMessage = $event['message']['text'];
                $arraytextMessage = explode( ' ', strtolower($textMessage) );

                // keywords
                $sapaan = ["hi", "hii", "hai", "hello", "helo", "hi!", "hii!", "hai!", "hello!", "helo!"];
                $tolong = ["tolong", "tlg", "tlong", "help", "please", "please!", "please?"];
                $marketPlace = ["toko", "market place", "market", "merchandise", "merchand", "lapak", "shop"];
                $nilai = ["nilai", "nilaiku", "penilaian", "skor", "poin", "point", "grade"];
                $bio = ["bio", "biodata"];

                if (strtolower(substr($textMessage, 0, 6)) == 'apakah') // kerang ajaib
                {
                    $replyMessage = (rand(0, 1)) ? "iya" : "tidak";
                    $result = $bot->replyText($event['replyToken'], $replyMessage);
                }

                if (checkKeyMessage($arraytextMessage, $sapaan)) // perkenalan
                {
                    $replyMessage = "Hai aku adalah Virtual Assistant kamu. Aku ditugaskan untuk mendampingimu selama RAJA BRAWIJAYA 2018 berlangsung. \n\n Semoga kamu betah sama aku ya !";
                    $result = $bot->replyText($event['replyToken'], $replyMessage);
                }

                if (strtolower($textMessage) == 'userid') // menampilkan userid
                {
                    $datanya = $getprofile->getJSONDecodedBody();
                    $result = $bot->replyText($event['replyToken'], $profile['userId']);
                }

                if (checkKeyMessage($arraytextMessage, $tolong)) 
                {
                    // bla bla bla
                    if ($event['message']['type'] == 'text')  // menerima trigger berupa chat text
                    {

                        if (checkKeyMessage($arraytextMessage, $marketPlace)) // carousel market place
                        {
                            $carouselTemplateBuilder = new CarouselTemplateBuilder([
                                new CarouselColumnTemplateBuilder("Gantungan", "Rp 1.000.000,-", "https://arizalmhmd5.000webhostapp.com/barang1.jpg", [
                                    new MessageTemplateActionBuilder('Detail', 'detail-barang1'),
                                    new UriTemplateActionBuilder('Pre Order', 'http://rajabrawijaya.ub.ac.id/')
                                ]),
                                new CarouselColumnTemplateBuilder("Sticker", "Rp 10.000.000,-", "https://arizalmhmd5.000webhostapp.com/barang2.jpg", [
                                    new MessageTemplateActionBuilder('Detail', 'detail-barang2'),
                                    new UriTemplateActionBuilder('Pre Order', 'http://rajabrawijaya.ub.ac.id/')
                                ]),
                                new CarouselColumnTemplateBuilder("Gelang Aluminium", "Rp 100,-", "https://arizalmhmd5.000webhostapp.com/barang3.jpg", [
                                    new MessageTemplateActionBuilder('Detail', 'detail-barang3'),
                                    new UriTemplateActionBuilder('Pre Order', 'http://rajabrawijaya.ub.ac.id/')
                                ]),
                                new CarouselColumnTemplateBuilder("Gelang Silicon", "Rp 100.000.000,-", "https://arizalmhmd5.000webhostapp.com/barang4.jpg", [
                                    new MessageTemplateActionBuilder('Detail', 'detail-barang4'),
                                    new UriTemplateActionBuilder('Pre Order', 'http://rajabrawijaya.ub.ac.id/')
                                ]),
                                new CarouselColumnTemplateBuilder("Sticker UB", "Rp 1.000.000.000,-", "https://arizalmhmd5.000webhostapp.com/barang5.jpg", [
                                    new MessageTemplateActionBuilder('Detail', 'detail-barang5'),
                                    new UriTemplateActionBuilder('Pre Order', 'http://rajabrawijaya.ub.ac.id/')
                                ]),
                            ]);
                            $templateMessage = new TemplateMessageBuilder('Daftar Merchandise', $carouselTemplateBuilder);
                            $result = $bot->replyMessage($event['replyToken'], $templateMessage);
                        }

                        if (checkKeyMessage($arraytextMessage, $nilai)) // menampilkan nilai
                        {
                            $contents = file_get_contents("http://arizalmhmd5.000webhostapp.com/API.php?user_id=" . $profile['userId']);
                            $data = json_decode($contents, true);
                            if ($data['status'] == "oke") {
                                $store = file_get_contents(getenv('apisiam') . "165150701111005");
                                $dataMhs = json_decode($store, true);
                                $replyMessage = new TextMessageBuilder(
                                    "DATA DIRI \n" .
                                    "NIM : " . $dataMhs['nim'] . "\n" .
                                    "Nama : " . $dataMhs['nama'] . "\n" .
                                    "Fakultas : " . $dataMhs['fak'] . "\n" .
                                    "Cluster : " . $dataMhs['clus'] . "\n \n" .
                                    "NILAI  \n" .
                                    "Penugasan Online : 90 \n" .
                                    "Penugasan 1 : 80 \n" .
                                    "Penugasan Upload : 70 \n" .
                                    "Kehadiran Seluruh rangkaian : 90%"
                                );
                            } else {
                                $replyMessage = new TextMessageBuilder("user belum terdaftar");
                            }
                            $result = $bot->replyMessage($event['replyToken'], $replyMessage);
                        }

                        if (checkKeyMessage($arraytextMessage, $bio))  // menampilkan biodata
                        {
                            $contents = file_get_contents("http://arizalmhmd5.000webhostapp.com/API.php?user_id=" . $profile['userId']);
                            $data = json_decode($contents, true);
                            if ($data['status'] == "oke") {
                                $store = file_get_contents(getenv('apisiam') . "165150701111005");
                                $dataMhs = json_decode($store, true);
                                $replyMessage = new TextMessageBuilder(
                                    "DATA DIRI \n" .
                                    "NIM : " . $dataMhs['nim'] . "\n" .
                                    "Nama : " . $dataMhs['nama'] . "\n" .
                                    "Tempat, Tanggal Lahir : " . $dataMhs['ttl']. "\n" .
                                    "Agama : " . $dataMhs['agama'] . "\n" .
                                    "Fakultas : " . $dataMhs['fak'] . "\n" .
                                    "Program Studi : " . $dataMhs['prod'] . "\n" .
                                    "Angkatan : " . $dataMhs['ang'] . "\n" .
                                    "Cluster : " . $dataMhs['clus'] . ""
                                );
                            } else {
                                $replyMessage = new TextMessageBuilder("user belum terdaftar");
                            }
                            $result = $bot->replyMessage($event['replyToken'], $replyMessage);
                        }
                    }
                }

                if (strtolower(substr($textMessage, 0, 6)) == 'detail') // menampilkan detail product
                {
                    $product = explode('-', $textMessage, 2);
                    $multipleMessageBuilder = new MultiMessageBuilder;
                    $multipleMessageBuilder->add(new ImageMessageBuilder("https://arizalmhmd5.000webhostapp.com/" . $product[1] . ".jpg", "https://arizalmhmd5.000webhostapp.com/" . $product[1] . ".jpg")) // tampilkan gambar product
                        ->add(new TextMessageBuilder( // deskripsi product
                            "Deskripsi Barang \n" .
                            "Nama Barang : " . $product[1] ."\n".
                            "Berat Barang : 1 Kwintal\n" .
                            "Dibuat Di : Zimbabwe\n" .
                            "Pembuat : Ovuvuevuevue Enyetuenwuevue Ugbemugbem Osas"
                        ));
                    $result = $bot->replyMessage($event['replyToken'], $multipleMessageBuilder);
                }

                if ((strtolower(substr($textMessage, 0, 7)) == "getdata") && ($profile['userId'] == "U7297ad52024284a89e83c406b298553c"))  // menampilkan biodata
                {
                    $store = file_get_contents(getenv('apisiam') . substr($textMessage, 8, 15));
                    $dataMhs = json_decode($store, true);
                    $replyMessage = new TextMessageBuilder(
                        "DATA DIRI \n" .
                        "NIM : " . $dataMhs['nim'] . "\n" .
                        "Nama : " . $dataMhs['nama'] . "\n" .
                        "Tempat, Tanggal Lahir : " . $dataMhs['ttl'] . "\n" .
                        "Agama : " . $dataMhs['agama'] . "\n" .
                        "Fakultas : " . $dataMhs['fak'] . "\n" .
                        "Program Studi : " . $dataMhs['prod'] . "\n" .
                        "Angkatan : " . $dataMhs['ang'] . "\n" .
                        "Cluster : " . $dataMhs['clus']
                    );
                    $result = $bot->replyMessage($event['replyToken'], $replyMessage);
                }
            }
        }
    }
});
$app->run();

function checkKeyMessage($arr1, $arr2) // check elements of arrays
{
    foreach ($arr1 as $key) {
        if (in_array(strtolower($key), $arr2)) {
            return true;
        }
    }
    return false;
}