<?php
use GuzzleHttp\Exception\ClientException;
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


// $encoder = \Neomerx\JsonApi\Encoder\Encoder::instance([
//     'Медведь' => '\SchemaOfМедведь',
//     'Лес' => '\SchemaOfЛес',
//     'Страна' => '\SchemaOfСтрана',
//     'Берлога' => '\SchemaOfБерлога',
//     'Блоха' => '\SchemaOfБлоха',
// ], new \Neomerx\JsonApi\Encoder\EncoderOptions(JSON_PRETTY_PRINT, $baseURL));
// 
// $json='{
//   "data": [
//     { "type": "comments", "id": "12" },
//     { "type": "comments", "id": "13" }
//   ]
// }';

$jsonСписокБерлог=sendGETRequest($restClient,"Список берлог","/Берлоги");
$СписокБерлог=json_decode(strstr($jsonСписокБерлог,'{'),true);
// echo "jsonСписокБерлог=$jsonСписокБерлог СписокБерлог=";print_r($СписокБерлог);
foreach ($СписокБерлог['data'] as $берлога) {
    $id=$берлога['id'];
    print_r($берлога);
    $Наименование=$берлога['attributes']['Наименование'];
    echo "$Наименование $id\n";
//     $deleteURL="/Берлоги/$id/relationships/ЛесРасположения";
//     $json=json_encode(['data'=>'null']);
    $deleteURL="/Берлоги/$id";
    $json='';
    $deleteBody=sendDELETERequest($restClient,"Удаление берлоги $Наименование ($id)",$deleteURL,$json);
    print_r(json_decode($deleteBody,true));
}

$jsonСписокБлох=sendGETRequest($restClient,"Список блох","/Блохи");
$СписокБлох=json_decode(strstr($jsonСписокБлох,'{'),true);
// echo "jsonСписокБлох=$jsonСписокБлох СписокБлох=";print_r($СписокБлох);
foreach ($СписокБлох['data'] as $блоха) {
    $id=$блоха['id'];
    $Кличка=$блоха['attributes']['Кличка'];
    print_r($блоха);
    echo "$Кличка $id\n";

    $deleteURL="/Блохи/$id";
    $json='';
    $deleteBody=sendDELETERequest($restClient,"Удаление блохи $Кличка ($id)",$deleteURL,$json);
    print_r(json_decode($deleteBody,true));
}

$jsonСписокМедведей=sendGETRequest($restClient,"Список медведей","/Медведи");
$СписокМедведей=json_decode(strstr($jsonСписокМедведей,'{'),true);
// echo "jsonСписокМедведей=$jsonСписокМедведей СписокМедведей=";print_r($СписокМедведей);
foreach ($СписокМедведей['data'] as $медведь) {
    $id=$медведь['id'];
    $ПорядковыйНомер=$медведь['attributes']['ПорядковыйНомер'];
    print_r($медведь);
    echo "$ПорядковыйНомер $id\n";
    $deleteURL="/Медведи/$id";
    $json='';
    $deleteBody=sendDELETERequest($restClient,"Удаление медведя $ПорядковыйНомер ($id)",$deleteURL,$json);
    print_r(json_decode($deleteBody,true));
}

$jsonСписокЛесов=sendGETRequest($restClient,"Список лесов","/Леса");
$СписокЛесов=json_decode(strstr($jsonСписокЛесов,'{'),true);
// echo "jsonСписокЛесов=$jsonСписокЛесов СписокЛесов=";print_r($СписокЛесов);
foreach ($СписокЛесов['data'] as $лес) {
    $id=$лес['id'];
    $Название=$лес['attributes']['Название'];
    print_r($лес);
    echo "$Название $id\n";
    $deleteURL="/Леса/$id";
    $json='';
    $deleteBody=sendDELETERequest($restClient,"Удаление леса $Название ($id)",$deleteURL,$json);
    print_r(json_decode($deleteBody,true));
}

$jsonСписокСтран=sendGETRequest($restClient,"Список стран","/Страны");
$СписокСтран=json_decode(strstr($jsonСписокСтран,'{'),true);
// echo "jsonСписокСтран=$jsonСписокСтран СписокСтран=";print_r($СписокСтран);
foreach ($СписокСтран['data'] as $страна) {
    $id=$страна['id'];
    $Название=$страна['attributes']['Название'];
    print_r($страна);
    echo "$Название $id\n";
    $deleteURL="/Страны/$id";
    $json='';
    $deleteBody=sendDELETERequest($restClient,"Удаление страны $Название ($id)",$deleteURL,$json);
    print_r(json_decode($deleteBody,true));
}

// print_r(json_decode($jsonСписокСтран,true));





function sendGETRequest($restClient,$title,$uri) {
    echo "BEAR:GET:Sent: uri=$uri\n";
    try {
        $reply=$restClient->request('GET',$uri);
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
    $body=$reply->getBody();
//     echo "\n\n---------------- $title -------------\n";
//     echo "BEAR::StatusCode=" . $reply->getStatusCode() . "\n";
//     echo "BEAR::Headers="; print_r($reply->getHeaders());
//     echo "BEAR::Body=$body\n";
//     echo "BEAR::BODY=".print_r(json_decode($body,true),true);
    return $body;
}


function sendDELETERequest($restClient,$title,$uri,$body='') {
    echo "BEAR:DELETE:Sent: uri=$uri $body=" .  print_r(json_decode($body,true),true)."\n";
    try {
        $reply=$restClient->request('DELETE',$uri, ['body'=>$body]);
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