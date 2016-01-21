<?php
use GuzzleHttp\Exception\ClientException;
require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../src/fja/FJA.php');
require_once(__DIR__ . '/../../src/fja/UUID.php');

$baseHost='http://jsonapitest.local';

// $baseURL='http://prototype-jsonapi-server.ics.perm.ru/';
$domain='jsonapitest';
$baseURL="$baseHost";

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
        'primarykey'=>'5a2a7f79-ea7a-41c8-89ad-88e892bc47fe',
        'Название'=>'Белоруссия',
    ]
    );

// $reply=sendPOSTRequest($restClient,$encoder,"Страна","Страны",$страна1);
// $primaryKey=json_decode($reply,true)['data']['attributes']['primarykey'];
// $страна1->attributes['primarykey']=$primaryKey;
echo "Страна=".print_r($страна1,true); 

$лес1= \Лес::instance(
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
    
echo "Лес1=".print_r($лес1,true); 
    
$reply=sendPOSTRequest($restClient,$encoder,"Лес и страна","Леса",$лес1);
exit;

    


\fja\FJA::autoload('Models/Медведь');
\fja\FJA::autoload('Schemas/SchemaOfМедведь');
    
$медведь1=  \Медведь::instance(
    [ 
        'primarykey'=>uuid_gen(),
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
        'primarykey'=>uuid_gen(),
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
        'primarykey'=>uuid_gen(),
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
sendPOSTRequest($restClient,$encoder,"Медведи","Медведи/3",$медведь3);



\fja\FJA::autoload('Models/Блоха');
\fja\FJA::autoload('Schemas/SchemaOfБлоха');

$блоха1=\Блоха::instance(['primarykey'=>uuid_gen(),'Кличка'=>'Машка'],['МедведьОбитания' => ['data' => $медведь1]]);
sendPOSTRequest($restClient,$encoder,"Блоха1","Блоха/1",$блоха1);

$блоха2=\Блоха::instance(['primarykey'=>uuid_gen(),'Кличка'=>'Сашка'],['МедведьОбитания' => ['data' => $медведь1]]);
sendPOSTRequest($restClient,$encoder,"Блоха2","Блоха/1",$блоха2);

$блоха3=\Блоха::instance(['primarykey'=>uuid_gen(),'Кличка'=>'Дашка'],['МедведьОбитания' => ['data' => $медведь2]]);
sendPOSTRequest($restClient,$encoder,"Блоха3","Блоха/1",$блоха3);

$блоха4=\Блоха::instance(['primarykey'=>uuid_gen(),'Кличка'=>'Пашка'],['МедведьОбитания' => ['data' => $медведь3]]);
sendPOSTRequest($restClient,$encoder,"Блоха4","Блоха/1",$блоха4);

\fja\FJA::autoload('Models/Берлога');
\fja\FJA::autoload('Schemas/SchemaOfБерлога');

$берлоги=[];
$берлоги[]=\Берлога::instance(
    [ 
        'primarykey'=>uuid_gen(),
        'Наименование'=>'ТаунХаус',
        'Комфортность'=>99,
        'Заброшена'=>false,
    ],
    [
        'ЛесРасположения' => ['data' => $лес1],
        'Медведь' => ['data' =>  $медведь1 ],
    ]
    );

$берлоги[]=\Берлога::instance(
    [ 
        'primarykey'=>uuid_gen(),
        'Наименование'=>'У сосны',
        'Комфортность'=>60,
        'Заброшена'=>false,
    ],
    [
        'ЛесРасположения' => ['data' => $лес1],
        'Медведь' => ['data' =>  $медведь1 ],
    ]
    );

    
$берлоги[]=\Берлога::instance(
    [ 
        'primarykey'=>uuid_gen(),
        'Наименование'=>'У дуба',
        'Комфортность'=>60,
        'Заброшена'=>false,
    ],
    [
        'ЛесРасположения' => ['data' => $лес1],
        'Медведь' => ['data' =>  $медведь2 ],
    ]
    );
    
$берлоги[]=\Берлога::instance(
    [ 
        'primarykey'=>uuid_gen(),
        'Наименование'=>'Детская',
        'Комфортность'=>80,
        'Заброшена'=>false,
    ],
    [
        'ЛесРасположения' => ['data' => $лес1],
        'Медведь' => ['data' =>  $медведь3 ],
    ]
    );
    
    sendPOSTRequest($restClient,$encoder,"Берлоги","Берлоги",$берлоги);


function uuid_gen() {
    $ret=UUID::v4();
    return $ret;
}


function sendPOSTRequest($restClient,$encoder,$title,$uri,$instance) {
    $body=$encoder->encodeData($instance);
    echo "Sent:" .  print_r(json_decode($body,true),true);
    try {
        $reply=$restClient->request('POST',$uri, ['body'=>$body]);
    } catch (ClientException $e) {
        echo "Ошибка в выполнении запроса: ";
        if ($e->hasResponse()) {
            $response=$e->getResponse();
            $body=$response->getStatusCode() . ' ' . $response->getReasonPhrase();
            echo "Ответ: " .  print_r($body,true);
        }
        exit;
    }
    echo "\n\n---------------- $title -------------\n";
    echo "StatusCode=" . $reply->getStatusCode() . "\n";
    echo "Headers="; print_r($reply->getHeaders());
    $body=$reply->getBody();
    echo "Body=$body\n";
    return $body;
}