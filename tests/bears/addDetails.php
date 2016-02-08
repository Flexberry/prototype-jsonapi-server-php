<?php
use GuzzleHttp\Exception\ClientException;
use \fja\FJA;
use \Neomerx\JsonApi\Encoder\Encoder;
use \Neomerx\JsonApi\Encoder\EncoderOptions;
use \Neomerx\JsonApi\Parameters\EncodingParameters;
use \Neomerx\JsonApi\Schema\Link;

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../src/fja/FJA.php');
echo "BEAR::" . __DIR__ . "\n";


$baseHost='http://jsonapitest.local';
// $baseHost='http://flexberryJsonAPI.local';   // Internal HOST without domain
// $baseHost='http://prototype-jsonapi-server.ics.perm.ru/'; // ternal HOST without domain
$domain='jsonapitest';
$baseURL="$baseHost";

\fja\FJA::setDomainsDir(__DIR__."/../../domains");
\fja\FJA::setDomain($domain);

spl_autoload_register(['\fja\FJA', 'autoload'], true, true);




$restClient = new GuzzleHttp\Client(['base_uri' => $baseHost]);


$encoder = \Neomerx\JsonApi\Encoder\Encoder::instance([
    'Медведь' => '\SchemaOfМедведь',
    'Лес' => '\SchemaOfЛес',
    'Страна' => '\SchemaOfСтрана',
    'Берлога' => '\SchemaOfБерлога',
    'Блоха' => '\SchemaOfБлоха',
], new \Neomerx\JsonApi\Encoder\EncoderOptions(JSON_PRETTY_PRINT, $baseURL));

$clientGeneratedUUid=false;
// $лес1= Лес::instance(
//     'лес1',
//     [
//         'Название'=>'Беловежская Пуща',
//         'Площадь'=>222,
//         'Заповедник'=>false,
//         'ДатаПоследнегоОсмотра'=>'2015-11-23 13:44:16.616936'
//     ],
//     [
//     ]
//     );


$берлога1=\Берлога::instance(
    'берлога1',
    [ 
        'Наименование'=>'ТаунХаус',
        'Комфортность'=>99,
        'Заброшена'=>false,
    ],
    [
//         'ЛесРасположения'=>['data'=>$лес1],
    ]
    );
    
    $берлога2=\Берлога::instance(
    'берлога2',
    [ 
        'Наименование'=>'У сосны',
        'Комфортность'=>60,
        'Заброшена'=>false,
    ],
    [
//         'ЛесРасположения'=>['data'=>$лес1],
    ]
    );
    
$медведь1= Медведь::instance(
    null,
    [ 
        'ПорядковыйНомер'=>1,
        'Вес'=>101,
        'ЦветГлаз'=>'Зеленый',
        'Пол'=>'Мужской',
        'ДатаРождения'=>'2012-09-08 13:56:02.560886'
    ],
    [
        'Берлоги' => ['data' => [$берлога1,$берлога2]],
//         'ЛесОбитания'=>['data'=>$лес1]
    ]
    );
    
$includePaths=['Берлоги','ЛесОбитания','Берлоги.ЛесРасположения'];
$encodingParameters = new EncodingParameters($includePaths,null);

// $body=$encoder->encodeData($медведь1,$encodingParameters);
// echo "медведь=\n";print_r(json_decode($body,true));
    
if ($clientGeneratedUUid) $медведь1->setId(\fja\FJA::uuid_gen());
$reply=sendPOSTRequest($restClient,$encoder,"Медведь1","Медведи",$медведь1,$encodingParameters);
$id=\fja\FJA::getDataFromJson($reply)['id'];
$медведь1->setId($id);
// echo "BEAR::медведь1=";print_r($медведь1); 




function sendPOSTRequest($restClient,$encoder,$title,$uri,$instance,$encodingParameters) {
    $body=$encoder->encodeData($instance,$encodingParameters);
    echo "BEAR::Sent:\n$body\n" ;
    $str = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');}, $body);
//     echo "BEAR::Sent:" .  print_r(json_decode($body,true),true);
    echo "BEAR::Sent:\n$str\n";exit;
    try {
        $reply=$restClient->request('POST',$uri, ['body'=>$body]);
    } catch (ClientException $e) {
        echo "Ошибка в выполнении запроса: ";
        if ($e->hasResponse()) {
            $response=$e->getResponse();
            echo "StatusCode=".$response->getStatusCode()."\n";
            $body=$response->getBody();
            $jsonPos=strpos($body,'{');
            echo "Carbage=".substr($body,/*0,*/$jsonPos);;
            $content=json_decode(strstr($body,'{'),true);
            echo "\nShift=$jsonPos\nContent=";print_r($content);
        }
        exit;
    }
    echo "\n\n---------------- $title -------------\n";
    echo "BEAR::StatusCode=" . $reply->getStatusCode() . "\n";
//     echo "BEAR::Headers="; print_r($reply->getHeaders());
    $body=$reply->getBody();
    echo "BEAR::Body=$body\n";
    echo "BEAR::BODY=".print_r(json_decode(strstr($body,'{'),true),true);
    return $body;
}