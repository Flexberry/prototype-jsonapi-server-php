<?php
// use \Neomerx\JsonApi\Encoder;
require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../fja/FJA.php');  //Базовый класс Flexberry JSON API

spl_autoload_register(['\fja\FJA', 'autoload'], true, true);

$request_uri=$_SERVER["REQUEST_URI"];
switch ($_SERVER["REQUEST_METHOD"]) {
    case 'POST':    //Создание объектов
        echo "Create object $request_uri<br>\n";
        $postData=getPostData();
        echo "postData=";print_r($postData);
        $listObjects=decodePostData($postData);
        echo "listObjects=".print_r($listObjects,true);
        break;;
    case 'GET':    //Запрос объектов
        echo "Fetch object $request_uri<br>\n";
        phpinfo();
        break;;
    case 'PATCH':    //Корректировка объектов
        echo "Update object $request_uri<br>\n";
        $postData=getPostData();
//         echo "postData=";print_r($postData);
        break;;
    case 'DELETE':    //Корректировка объектов
        echo "Delete object $request_uri<br>\n";
        break;;
}

\fja\FJA::autoload('Models/Медведь');
\fja\FJA::autoload('Schemas/SchemaOfМедведь');

\fja\FJA::autoload('Models/ЛесОбитания');
\fja\FJA::autoload('Schemas/SchemaOfЛесОбитания');


function getPostData() {
    $fp=fopen("php://input",'r');
    $request='';
    while ($str=fgets($fp)) {
        $request.= $str;
    }
    $request=trim($request);
    $ret=json_decode($request,true);
    return $ret;
}

function decodePostData($postData) {
    if (!key_exists('data',$postData)) {
        echo "Отсутствует аттрибут data в " . print_r($posrData,true) ;
        return [];
    }
    $ret=[];
    $data=$postData['data'];
    $ret[]=decodeData($data);
   if (key_exists('included',$postData)) { //Есть включенный объекты
        foreach ($postData['included'] as $subData) {
//             $ret=array_merge($ret,decodeData($subData));
            $ret[]=decodeData($subData);
        }
    }
    return $ret;
}

function decodeData($data) {
    if (!key_exists('type',$data)) {
        echo "Отсутствует аттрибут type в " . print_r($data,true) ;
        return [];
    }
    $type=$data['type'];
    if (!key_exists('attributes',$data)) {
        echo "Отсутствует аттрибут attributes в " . print_r($data,true) ;
        return [];
    }
    $attributes=$data['attributes'];
    $relationships=[];
    if (key_exists('relationships',$data)) {
        $relationships=$data['relationships'];
    }
    
    $modelClass="Models/$type";
//     echo "modelClass=$modelClass\n";
    \fja\FJA::autoload($modelClass);
    $schemaClass="Schemas/SchemaOf$type";
    \fja\FJA::autoload($schemaClass);
//     echo "schemaClass=$schemaClass\n";
    $className="\\$type";
//     echo "className=$className\n";
    $object=new $className($attributes,$relationships);
//     echo "Object=".print_r($object,true);
    return $object; 
}