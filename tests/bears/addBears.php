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

$страна1= Страна::instance(
    null,
    [
        'Название'=>'Белоруссия',
    ]
    );
if ($clientGeneratedUUid || true) $страна1->setId(\fja\FJA::uuid_gen());
$reply=sendPOSTRequest($restClient,$encoder,"Страна","Страны",$страна1);
// $id=\fja\FJA::getDataFromJson($reply)['id'];
// $страна1->setId($id);
echo "BEAR::страна1=";print_r($страна1);


$лес1= Лес::instance(
    null,
    [
        'Название'=>'Беловежская Пуща',
        'Площадь'=>222,
        'Заповедник'=>false,
        'ДатаПоследнегоОсмотра'=>'2015-11-23 13:44:16.616936'
    ],
    [
        'Страна' => ['data' => $страна1],
    ]
    );
if ($clientGeneratedUUid) $лес1->setId(\fja\FJA::uuid_gen());

$includePaths=['Страны'];
$encodingParameters = new EncodingParameters($includePaths,null);

$reply=sendPOSTRequest($restClient,$encoder,"Лес и страна","Леса",$лес1);
$id=\fja\FJA::getDataFromJson($reply)['id'];
$лес1->setId($id); 
// echo "BEAR::Лес1=";print_r($лес1); 
    
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
        'ЛесОбитания' => ['data' => $лес1],
    ]
    );
if ($clientGeneratedUUid) $медведь1->setId(\fja\FJA::uuid_gen());
$reply=sendPOSTRequest($restClient,$encoder,"Медведь1","Медведи",$медведь1);
$id=\fja\FJA::getDataFromJson($reply)['id'];
$медведь1->setId($id);
echo "BEAR::медведь1=";print_r($медведь1); 

$медведь2=  Медведь::instance(
    null,
    [ 
        'ПорядковыйНомер'=>2,
        'Вес'=>96,
        'ЦветГлаз'=>'Карий',
        'Пол'=>'Женский',
        'ДатаРождения'=>'2013-10-03 14:04:16.806259'
    ],
    [
        'ЛесОбитания' => ['data' => $лес1],
    ]
    );
if ($clientGeneratedUUid) $медведь2->setId(\fja\FJA::uuid_gen());
$reply=sendPOSTRequest($restClient,$encoder,"Медведь2","Медведи",$медведь2);
$id=\fja\FJA::getDataFromJson($reply)['id'];
$медведь2->setId($id); exit;
echo "BEAR::медведь2=";print_r($медведь2); 

$медведь3=  Медведь::instance(
    null,
    [ 
        'ПорядковыйНомер'=>3,
        'Вес'=>65,
        'ЦветГлаз'=>'Синий',
        'Пол'=>'Мужской',
        'ДатаРождения'=>'2015-11-23 14:47:40.065452'
    ],
    [
        'ЛесОбитания' => ['data' => $лес1],
        'Папа' => ['data' =>  $медведь1 ],
        'Мама' => ['data' =>  $медведь2 ],
    ]
    );
if ($clientGeneratedUUid) $медведь3->setId(\fja\FJA::uuid_gen());
$reply=sendPOSTRequest($restClient,$encoder,"Медведь3","Медведи",$медведь3);
$id=\fja\FJA::getDataFromJson($reply)['id'];
$медведь3->setId($id);
echo "BEAR::медведь3=";print_r($медведь3); 



$блоха1=\Блоха::instance(null,['Кличка'=>'Машка'],['МедведьОбитания' => ['data' => $медведь1]]);
if ($clientGeneratedUUid) $блоха1->setId(\fja\FJA::uuid_gen());
$reply=sendPOSTRequest($restClient,$encoder,"Блоха1","Блохи",$блоха1);
$id=\fja\FJA::getDataFromJson($reply)['id'];
$блоха1->setId($id);
echo "BEAR::блоха1=";print_r($блоха1); 

$блоха2=\Блоха::instance(null,['Кличка'=>'Сашка'],['МедведьОбитания' => ['data' => $медведь1]]);
if ($clientGeneratedUUid) $блоха2->setId(\fja\FJA::uuid_gen());
$reply=sendPOSTRequest($restClient,$encoder,"Блоха2","Блохи",$блоха2);
$id=\fja\FJA::getDataFromJson($reply)['id'];
$блоха2->setId($id);
echo "BEAR::блоха2=";print_r($блоха2); 

$блоха3=\Блоха::instance(null,['Кличка'=>'Дашка'],['МедведьОбитания' => ['data' => $медведь2]]);
if ($clientGeneratedUUid) $блоха3->setId(\fja\FJA::uuid_gen());
$reply=sendPOSTRequest($restClient,$encoder,"Блоха3","Блохи",$блоха3);
$id=\fja\FJA::getDataFromJson($reply)['id'];
$блоха3->setId($id);
echo "BEAR::блоха3=";print_r($блоха3); 

$блоха4=\Блоха::instance(null,['Кличка'=>'Пашка'],['МедведьОбитания' => ['data' => $медведь3]]);
if ($clientGeneratedUUid) $блоха4->setId(\fja\FJA::uuid_gen());
$reply=sendPOSTRequest($restClient,$encoder,"Блоха4","Блохи",$блоха4);
$id=\fja\FJA::getDataFromJson($reply)['id'];
$блоха4->setId($id);
echo "BEAR::блоха4=";print_r($блоха4); 

$берлога1=\Берлога::instance(
    null,
    [ 
        'Наименование'=>'ТаунХаус',
        'Комфортность'=>99,
        'Заброшена'=>false,
    ],
    [
        'ЛесРасположения' => ['data' => $лес1],
        'Медведь' => ['data' =>  $медведь1 ],
    ]
    );
if ($clientGeneratedUUid) $берлога1->setId(\fja\FJA::uuid_gen());
$reply=sendPOSTRequest($restClient,$encoder,"Берлога1","Берлоги",$берлога1);
$id=\fja\FJA::getDataFromJson($reply)['id'];
$берлога1->setId($id);
echo "BEAR::берлога1=";print_r($берлога1); 

$берлога2=\Берлога::instance(
    null,
    [ 
        'Наименование'=>'У сосны',
        'Комфортность'=>60,
        'Заброшена'=>false,
    ],
    [
        'ЛесРасположения' => ['data' => $лес1],
        'Медведь' => ['data' =>  $медведь1 ],
    ]
    );
if ($clientGeneratedUUid) $берлога2->setId(\fja\FJA::uuid_gen());
$reply=sendPOSTRequest($restClient,$encoder,"Берлога2","Берлоги",$берлога2);
$id=\fja\FJA::getDataFromJson($reply)['id'];
$берлога2->setId($id);
echo "BEAR::берлога2=";print_r($берлога2); 

    
$берлога3=\Берлога::instance(
    null,
    [ 
        'Наименование'=>'У дуба',
        'Комфортность'=>60,
        'Заброшена'=>false,
    ],
    [
        'ЛесРасположения' => ['data' => $лес1],
        'Медведь' => ['data' =>  $медведь2 ],
    ]
    );
if ($clientGeneratedUUid) $берлога3->setId(\fja\FJA::uuid_gen());
$reply=sendPOSTRequest($restClient,$encoder,"Берлога3","Берлоги",$берлога3);
$id=\fja\FJA::getDataFromJson($reply)['id'];
$берлога3->setId($id);
echo "BEAR::берлога3=";print_r($берлога3); 
    
$берлога4=\Берлога::instance(
    null,
    [ 
        'Наименование'=>'Детская',
        'Комфортность'=>80,
        'Заброшена'=>true,
    ],
    [
        'ЛесРасположения' => ['data' => $лес1],
        'Медведь' => ['data' =>  $медведь3 ],
    ]
    );
if ($clientGeneratedUUid) $берлога4->setId(\fja\FJA::uuid_gen());
$reply=sendPOSTRequest($restClient,$encoder,"Берлога4","Берлоги",$берлога4);
$id=\fja\FJA::getDataFromJson($reply)['id'];
$берлога4->setId($id);
echo "BEAR::берлога4=";print_r($берлога4); 



function sendPOSTRequest($restClient,$encoder,$title,$uri,$instance) {
    $body=$encoder->encodeData($instance);
    $str = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');}, $body);
    echo "BEAR::Sent:\n$str\n";
//    echo "BEAR::Sent:\n$body";
    try {
        $reply=$restClient->request('POST',$uri, ['body'=>$body]);
    } catch (ClientException $e) {
        echo "Ошибка в выполнении запроса: ";
        if ($e->hasResponse()) {
            $response=$e->getResponse();
            echo "StatusCode=".$response->getStatusCode()."\n";
            $body=$response->getBody();
            $jsonPos=strpos($body,'{');
            echo "Carbage=".substr($body,0,$jsonPos);;
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
    echo "BEAR::BODY=".print_r(json_decode($body,true),true);
    return $body;
}