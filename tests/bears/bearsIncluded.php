<?php
require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../src/fja/FJA.php');

$baseHost='http://flexberryJsonAPI.local/';

// $baseURL='http://prototype-jsonapi-server.ics.perm.ru/';
$domain='bears';
$baseURL="$baseHost/$domain";

\fja\FJA::setDomainsDir("../../domains");
\fja\FJA::setDomain($domain);

\fja\FJA::autoload('Models/Медведь');
\fja\FJA::autoload('Schemas/SchemaOfМедведь');

\fja\FJA::autoload('Models/ЛесОбитания');
\fja\FJA::autoload('Schemas/SchemaOfЛесОбитания');

$baseHost='http://jsonapitest.local';
// $baseHost='http://flexberryJsonAPI.local';   // Internal HOST without domain
// $baseHost='http://prototype-jsonapi-server.ics.perm.ru/'; // ternal HOST without domain
$domain='jsonapitest';
$baseURL="$baseHost";

\fja\FJA::setDomainsDir(__DIR__."/../../domains");
\fja\FJA::setDomain($domain);

spl_autoload_register(['\fja\FJA', 'autoload'], true, true);
$страна1= Страна::instance(
    'bel',
    [
        'Название'=>'Белоруссия',
    ]
    );

$ЛесОбитания1= new Лес(
    "forest1",
    [
        'Название'=>'Беловежская Пуща',
        'Площадь'=>222,
        'Заповедник'=>false,
        'ДатаПоследнегоОсмотра'=>'2010/05/05'
    ],
    [
        'Страна' => ['data' => $страна1],
    ]
    );

$медведь1= new Медведь(
    1,
    [ 
        'ПорядковыйНомер'=>1,
        'Вес'=>101,
        'ЦветГлаз'=>'Зеленый',
        'Пол'=>'Мужской',
        'ДатаРождения'=>'2010/06/08'
    ],
    [
        'ЛесОбитания' => ['data' => $ЛесОбитания1],
    ]
    );

$медведь2= new \Медведь(
    2,
    [ 
        'ПорядковыйНомер'=>2,
        'Вес'=>96,
        'ЦветГлаз'=>'Карий',
        'Пол'=>'Женский',
        'ДатаРождения'=>'2010/09/15'
    ],
    [
        'ЛесОбитания' => ['data' => $ЛесОбитания1],
    ]
    );

$медведь3= new \Медведь(
    3,
    [ 
        'ПорядковыйНомер'=>3,
        'Вес'=>65,
        'ЦветГлаз'=>'Синий',
        'Пол'=>'Мужской',
        'ДатаРождения'=>'2012/12/15'
    ],
    [
        'ЛесОбитания' => ['data' => $ЛесОбитания1,'related'=>true,'showSelf'=>true,'showData'=>true],
        'Папа' => ['data' =>  $медведь1 ,'related'=>true,'showSelf'=>true,'showData'=>false],
        'Мама' => ['data' =>  $медведь2 ,'related'=>true,'showSelf'=>true,'showData'=>false],
    ]
    );

    
// echo "медведь3=";print_r($медведь3);

$includePaths=['Папа','ЛесОбитания','ЛесОбитания.Страна'];
$includePaths=[];
$fieldSets=['Медведь'=>['Вес','ПорядковыйНомер','Папа','ЛесОбитания'],'Лес'=>['Название','Площадь','Страна'],'Страна'=>['Название']];
$fieldSets=[];
$encodingParameters = new \Neomerx\JsonApi\Parameters\EncodingParameters($includePaths,$fieldSets);
echo "<pre>encodingParameters=";print_r($encodingParameters);echo "</pre>\n";

$encoder = \Neomerx\JsonApi\Encoder\Encoder::instance([
    'Медведь' => '\SchemaOfМедведь',
    'Лес' => '\SchemaOfЛес',
    'Страна' => '\SchemaOfСтрана',
    'Берлога' => '\SchemaOfБерлога',
    'Блоха' => '\SchemaOfБлоха',
], new \Neomerx\JsonApi\Encoder\EncoderOptions(JSON_PRETTY_PRINT, $baseURL));
echo "<pre>Encoder=";print_r($encoder);echo "</pre>\n";

$json=$encoder->encodeData($медведь3,$encodingParameters);

echo "<pre>JSON=$json</pre>\n";
echo "<pre>PHP=";print_r(json_decode($json,true));echo "</pre>\n";

// echo "<pre>PHP=";print_r($phpData);echo "</pre>\n";


// $uri="$domain/Медведи/3";
// $client = new GuzzleHttp\Client(['base_uri' => $baseHost]);
// // echo "CLIENT=";print_r($client);
// 
// $res=$client->request('POST',$uri, [
//     'body'=>$json
//     ]);
// 
// echo "StatusCode=" . $res->getStatusCode() . "\n";
// echo "ContentType="; print_r($res->getHeader('content-type'));
// echo "Body=" .$res->getBody() . "\n";
// 
// 
// $phpData=json_decode($json,true);



