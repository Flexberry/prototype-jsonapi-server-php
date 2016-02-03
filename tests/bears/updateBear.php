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


$encoder = \Neomerx\JsonApi\Encoder\Encoder::instance([
    'Медведь' => '\SchemaOfМедведь',
    'Лес' => '\SchemaOfЛес',
    'Страна' => '\SchemaOfСтрана',
    'Берлога' => '\SchemaOfБерлога',
    'Блоха' => '\SchemaOfБлоха',
], new \Neomerx\JsonApi\Encoder\EncoderOptions(JSON_PRETTY_PRINT, $baseURL));


$лес1= Лес::instance(
    "1cae2056-5cb1-425e-a2e0-7c46d2a9da1c"
    );
    
    $bearId='95336993-751d-4cda-b894-8d1dad262049';
$медведь1= Медведь::instance(
    $bearId,
    [ 
        'Вес'=>110,
        'ЦветГлаз'=>'Изумрудный',
    ],
    [
        'ЛесОбитания' => ['data' => $лес1],
    ]
    );
$reply=sendPATCHRequest($restClient,$encoder,"Медведь1","Медведи/$bearId",$медведь1);
// echo "BEAR::медведь1=";print_r($медведь1); 




function sendPATCHRequest($restClient,$encoder,$title,$uri,$instance) {
    $body=$encoder->encodeData($instance);
    echo "BEAR::Sent:" .  print_r(json_decode($body,true),true);
    try {
        $reply=$restClient->request('PATCH',$uri, ['body'=>$body]);
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
    echo "BEAR:Receive:Body=$body\n";
    echo "BEAR:Receive:BODY=".print_r(json_decode($body,true),true);
    return $body;
}