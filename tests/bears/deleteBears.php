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

$jsonСписокМедведей=sendGETRequest($restClient,"Список медведей","/Медведи?include=Папа,Мама");
$СписокМедведей=json_decode(strstr($jsonСписокМедведей,'{'),true);
echo "СписокМедведей=";print_r($СписокМедведей);
$children=[];
$bears=[];
foreach ($СписокМедведей['data'] as $медведь) {
    $id=$медведь['id'];
    $bears[$id]=$медведь;
    if (key_exists('Папа',$медведь['relationships'])) {
        $parendId=$медведь['relationships']['Папа']['data']['id'];
        echo "Папа $id ->  $parendId\n";
        $children[$parendId][]=$id;
    }
    if (key_exists('Мама',$медведь['relationships'])) {
        $parendId=$медведь['relationships']['Мама']['data']['id'];
        echo "Мама $id ->  $parendId\n";
        $children[$parendId][]=$id;
    }
}
// echo "children=";print_r($children);
foreach ($children as  $parentId=>$childrenIds) {
    foreach ($childrenIds as $childId) {
        if (key_exists($childId,$bears)) {
            deleteМедведь($restClient,$bears[$childId]);
            unset($bears[$childId]);
        }
    }
    deleteМедведь($restClient,$bears[$parentId]);
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
    $deleteBody=sendDELETERequest($restClient,"Удаление  $Название ($id)",$deleteURL,$json);
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


function deleteМедведь($restClient,$медведь) {
    $id=$медведь['id'];
    $ПорядковыйНомер=$медведь['attributes']['ПорядковыйНомер'];
    print_r($медведь);
    echo "$ПорядковыйНомер $id\n";
    $deleteURL="/Медведи/$id";
    $json='';
    $deleteBody=sendDELETERequest($restClient,"Удаление медведя $ПорядковыйНомер ($id)",$deleteURL,$json);
    print_r(json_decode($deleteBody,true));
}



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