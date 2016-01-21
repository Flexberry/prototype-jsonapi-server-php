<?php
// use \Neomerx\JsonApi\Encoder;
require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../fja/FJA.php');  //Базовый класс Flexberry JSON API
// phpinfo();
spl_autoload_register(['\fja\FJA', 'autoload'], true, true);
$baseURL="http://".$_SERVER["HTTP_HOST"];
$request_uri=$_SERVER["REQUEST_URI"];
switch ($_SERVER["REQUEST_METHOD"]) {
    case 'POST':    //Создание объектов
//         echo "Create object $request_uri<br>\n";
        $postData=getPostData();
//         echo "postData=";print_r($postData);
        $listObjects=decodePostData($postData);
        echo "listObjects=".print_r($listObjects,true);
        $nObjects=count($listObjects);
        if ($nObjects==0) {
            sendErrorReply(['status'=>'403','title'=>'No object in request']);
        } elseif ($nObjects>1) {
            sendErrorReply(['status'=>'403','title'=>'Several objects in request (included option)']);
        }
        $object=$listObjects[0];
        addObjectToDb($object); 
        echo "object=".print_r($object,true);
        
        $schemas=formSchemas([$object]);
        echo "schemas=".print_r($schemas,true);
        $encoder = \Neomerx\JsonApi\Encoder\Encoder::instance($schemas, new \Neomerx\JsonApi\Encoder\EncoderOptions(JSON_PRETTY_PRINT, $baseURL));
        echo "encoder=".print_r($encoder,true);
        $json=$encoder->encodeData($object);
        echo "json=".print_r($json,true);exit;
        $objectTree=json_decode($json,true);
        $location=$objectTree['data']['links']['self'];
        sendCreatedObject($location,$json);
        
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


function isAssoc($arr) {
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function decodePostData($postData) {
    if (!key_exists('data',$postData)) {
        sendErrorReply(['status'=>'400','title'=>'Missed field data in request']);
    }
    $ret=[];
    $datas=[];
    $data=$postData['data'];
    if (isAssoc($data)) {
        $datas[]=$data;
    } else {
        sendErrorReply(['status'=>'400','title'=>'Several objects in request']);
    }
//     echo "datas=";print_r($datas);
    foreach ($datas as $data) {
        $ret[]=decodeData($data);    
    }
   if (key_exists('included',$postData)) { //Есть включенный объекты
        foreach ($postData['included'] as $subData) {
//             $ret=array_merge($ret,decodeData($subData));
            $ret[]=decodeData($subData);
        }
    }
    return $ret;
}

function formSchemas($objects) {
    $ret=[];
    foreach ($objects as $object) {
        $type=get_class($object);
        $ret[$type]="SchemaOf$type";
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


/* REST-Interface */
function  sendCreatedObject($location,$json,$headers=[]) {
    $status='201';
    http_response_code($status);
    $headers[]="Content-type: application/vnd.api+json";
    $headers[]="Location: $location";
    foreach ($headers as $header) {
        header($header);
    }
    echo $json;
}


function sendErrorReply($e,$headers=[]) {
    $status=$e['status'];
    $status='200';
    http_response_code($status);
    $headers[]="Content-type: application/vnd.api+json";
    foreach ($headers as $header) {
        header($header);
    }
    $url="http://".$_SERVER["SERVER_NAME"]. $_SERVER["REQUEST_URI"];
    $encoder = \Neomerx\JsonApi\Encoder\Encoder::instance([], new \Neomerx\JsonApi\Encoder\EncoderOptions(JSON_PRETTY_PRINT, $url));
    $error=new \Neomerx\JsonApi\Document\Error(@$e['idx'],@$e['aboutLink'],@$e['status'],@$e['code'],@$e['title'],@$e['detail'],@$e['source'],@$e['meta']);
    $body=$encoder->encodeError($error);
    echo $body;
    exit;
}

/* ------ PDO ------- */
function connectDb() {
    $dsn = 'pgsql:host=10.130.5.119;port=5432;dbname=JsonApiTest;';
    $user = 'flexberry_orm_tester';
    $password = 'sa3dfE';
    try {
        $dbh = new PDO($dsn, $user, $password);
    } catch (PDOException $e) {
        echo 'Подключение не удалось: ' . $e->getMessage();
    }
    return $dbh;
}

function addObjectToDb($object) {
    echo "object=".print_r($object,true);
    $primaryKeyName=$object->primaryKeyName;
//     echo "primaryKeyName=$primaryKeyName\n";
    if (!key_exists($primaryKeyName,$object->attributes) || !trim($object->attributes[$primaryKeyName])) {
        $primaryKey=uuid_gen();
        $object->attributes[$primaryKeyName]=$primaryKey;
    } else {
        $primaryKey=$object->attributes[$primaryKeyName];
    }
//     echo "primaryKey=$primaryKey\n";
    $className=get_class($object);
    $insertCmd="INSERT INTO public." . $className . ' ';; 
    $fieldNames=[];
    $fieldValues=[];
    foreach ($object->attributes as $name=>$value) {
        $fieldNames[]='"'.$name.'"';
        if ($object->isBoolean($name)) {
            $value=($value?'true':'false');
        } else {
            $value="'".$value."'";
        }
        $fieldValues[]=$value;
    }
    foreach ($object->relationships as $name=>$value) {
        $fieldNames[]='"'.$name.'"';
        $fieldValues[]="'".$value['data']['id']."'";        
    }    
    
    $insertCmd.='('. implode(',',$fieldNames) . ') VALUES (' . implode(',',$fieldValues) . ')';
    echo "insertCmd=$insertCmd\n";

    $dbh=connectDb();
//     echo "DBH=".print_r($dbh,true);
    $count = $dbh->exec($insertCmd);
    echo "Inserted $count records\n";
    return $object;    
}

/* UUID */
function uuid_gen() {
    require_once(__DIR__ . '/../../src/fja/UUID.php');
    $ret=UUID::v4();
    return $ret;
}
