<?php
require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../src/fja/FJA.php');

$baseHost='http://flexberryJsonAPI.local/';

// $baseURL='http://prototype-jsonapi-server.ics.perm.ru/';
$domain='bears';
$baseURL="$baseHost/$domain";

$restClient = new GuzzleHttp\Client(['base_uri' => $baseHost]);


\fja\FJA::setDomainsDir("../../domains");
\fja\FJA::setDomain($domain);

\fja\FJA::autoload('Models/Лес');
\fja\FJA::autoload('Schemas/SchemaOfЛес');

\fja\FJA::autoload('Models/Страна');
\fja\FJA::autoload('Schemas/SchemaOfСтрана');


$encoder = \Neomerx\JsonApi\Encoder\Encoder::instance([
    'Медведь' => '\SchemaOfМедведь',
    'Лес' => '\SchemaOfЛес',
    'Страна' => '\SchemaOfСтрана',
    'Берлога' => '\SchemaOfБерлога',
    'Блоха' => '\SchemaOfБлоха',
], new \Neomerx\JsonApi\Encoder\EncoderOptions(JSON_PRETTY_PRINT, $baseURL));


$страна1= \Страна::instance(
    [
        'Название'=>'Белоруссия',
    ]
    );


$лес1= \Лес::instance(
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
    
sendPOSTRequest($restClient,$encoder,"Лес и страна","$domain/Лес/0",$лес1);

// $res=$restClient->request('POST',"$domain/Леса/0", ['body'=>$encoder->encodeData($лес1)]);
// 
// echo "\n\n----------------------------------- Лес1::";
// echo "StatusCode=" . $res->getStatusCode() . "\n";
// echo "ContentType="; print_r($res->getHeader('content-type'));
// echo "Body=" .$res->getBody() . "\n";
//     
    


\fja\FJA::autoload('Models/Медведь');
\fja\FJA::autoload('Schemas/SchemaOfМедведь');
    
$медведь1=  \Медведь::instance(
    [ 
        'ПорядковыйНомер'=>1,
        'Вес'=>101,
        'ЦветГлаз'=>'Зеленый',
        'Пол'=>'Мужской',
        'ДатаРождения'=>'2010/06/08'
    ],
    [
        'ЛесОбитания' => ['data' => $лес1],
    ]
    );

$медведь2=  \Медведь::instance(
    [ 
        'ПорядковыйНомер'=>2,
        'Вес'=>96,
        'ЦветГлаз'=>'Карий',
        'Пол'=>'Женский',
        'ДатаРождения'=>'2010/09/15'
    ],
    [
        'ЛесОбитания' => ['data' => $лес1],
    ]
    );

$медведь3=  \Медведь::instance(
    [ 
        'ПорядковыйНомер'=>3,
        'Вес'=>65,
        'ЦветГлаз'=>'Синий',
        'Пол'=>'Мужской',
        'ДатаРождения'=>'2012/12/15'
    ],
    [
        'ЛесОбитания' => ['data' => $лес1],
        'Папа' => ['data' =>  $медведь1 ],
        'Мама' => ['data' =>  $медведь2 ],
    ]
    );
sendPOSTRequest($restClient,$encoder,"Медведи","$domain/Медведи/3",$медведь3);



\fja\FJA::autoload('Models/Блоха');
\fja\FJA::autoload('Schemas/SchemaOfБлоха');

$блоха1=\Блоха::instance(['Кличка'=>'Машка'],['МедведьОбитания' => ['data' => $медведь1]]);
sendPOSTRequest($restClient,$encoder,"Блоха1","$domain/Блоха/1",$блоха1);

$блоха2=\Блоха::instance(['Кличка'=>'Сашка'],['МедведьОбитания' => ['data' => $медведь1]]);
sendPOSTRequest($restClient,$encoder,"Блоха2","$domain/Блоха/1",$блоха2);

$блоха3=\Блоха::instance(['Кличка'=>'Дашка'],['МедведьОбитания' => ['data' => $медведь2]]);
sendPOSTRequest($restClient,$encoder,"Блоха3","$domain/Блоха/1",$блоха3);

$блоха4=\Блоха::instance(['Кличка'=>'Пашка'],['МедведьОбитания' => ['data' => $медведь3]]);
sendPOSTRequest($restClient,$encoder,"Блоха4","$domain/Блоха/1",$блоха4);

\fja\FJA::autoload('Models/Берлога');
\fja\FJA::autoload('Schemas/SchemaOfБерлога');

$берлога1=\Берлога::instance(
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
sendPOSTRequest($restClient,$encoder,"Берлога1","$domain/Берлога/1",$берлога1);

$берлога2=\Берлога::instance(
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
sendPOSTRequest($restClient,$encoder,"Берлога2","$domain/Берлога/2",$берлога2);

    
$берлога3=\Берлога::instance(
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
sendPOSTRequest($restClient,$encoder,"Берлога3","$domain/Берлога/3",$берлога3);
    
$берлога4=\Берлога::instance(
    [ 
        'Наименование'=>'Детская',
        'Комфортность'=>80,
        'Заброшена'=>false,
    ],
    [
        'ЛесРасположения' => ['data' => $лес1],
        'Медведь' => ['data' =>  $медведь3 ],
    ]
    );
    
sendPOSTRequest($restClient,$encoder,"Берлога4","$domain/Берлога/1",$берлога4);



function sendPOSTRequest($restClient,$encoder,$title,$uri,$instance) {
    $reply=$restClient->request('POST',$uri, ['body'=>$encoder->encodeData($instance)]);
    echo "\n\n---------------- $title -------------\n";
    echo "StatusCode=" . $reply->getStatusCode() . "\n";
    echo "ContentType="; print_r($reply->getHeader('content-type'));
    echo "Body=" .$reply->getBody() . "\n";
}