<?php
use \fja\FJA;
use \Neomerx\JsonApi\Encoder\Encoder;
use \request\post\Post;
use \request\get\Get;
use \responce\Responce;
use storage\pdo\Pdo;

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../fja/FJA.php');  //Базовый класс Flexberry JSON API

FJA::setDomainsDir($_SERVER["DOCUMENT_ROOT"]. "/../../domains");   //Set home directory for all modelClass and Schemas
$path=explode('.',trim($_SERVER["HTTP_HOST"],'/'));
$domain=$path[0];   //Domain as first subdomain in domain name
FJA::setDomain($domain);   //Set root for all modelClass and Schemas

// phpinfo();
spl_autoload_register(['\fja\FJA', 'autoload'], true, true);
$baseURL="http://".$_SERVER["HTTP_HOST"];
$request_uri=$_SERVER["REQUEST_URI"];
switch ($_SERVER["REQUEST_METHOD"]) {
    case 'POST':    //Создание объектов
//         echo "Create object $request_uri<br>\n";
        $postData=Post::getPostData();
//         echo "postData=";print_r($postData);
        $listObjects=Post::decodePostData($postData);
//         echo "listObjects=".print_r($listObjects,true);
        $nObjects=count($listObjects);
        if ($nObjects==0) {
            Responce::sendErrorReply(['status'=>'403','title'=>'No object in request']);
        } elseif ($nObjects>1) {
            Responce::sendErrorReply(['status'=>'403','title'=>'Several objects in request (included option)']);
        }
        $object=$listObjects[0];
        Pdo::addObjectToDb($object); 
//         echo "object=".print_r($object,true);        
        $schemas=FJA::formSchemas([$object]);
//         echo "schemas=".print_r($schemas,true);
        $encoder = Encoder::instance($schemas, new \Neomerx\JsonApi\Encoder\EncoderOptions(JSON_PRETTY_PRINT, $baseURL));
//         echo "encoder=".print_r($encoder,true);
        $object=FJA::replaceRelationshipsObject($object);        
//         echo "object=".print_r($object,true);
        $json=$encoder->encodeData($object);
//         echo "json=".print_r($json,true);
        $objectTree=json_decode($json,true);
        $location=$objectTree['data']['links']['self'];
        Responce::sendCreatedObject($location,$json);
        break;;
    case 'GET':    //Запрос объектов
        echo "Fetch object $request_uri<br>\n";
        $parsedRequest=Get::urlParse($request_uri);
        echo "REQUEST=<pre>";print_r($parsedRequest);echo "</pre>";
//         $parsed=parse_url($request_uri);
//         print_r($parsed);
//         echo "PATH=".urldecode($parsed['path'])."<br>\n";
//         echo "QUERY=".urldecode($parsed['query'])."<br>\n";
//         phpinfo();
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









