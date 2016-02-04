<?php
use \fja\FJA;
use \Neomerx\JsonApi\Encoder\Encoder;
use \Neomerx\JsonApi\Encoder\EncoderOptions;
use \Neomerx\JsonApi\Parameters\EncodingParameters;
use \Neomerx\JsonApi\Schema\Link;

use \request\Request;
use \request\post\Post;
use \request\delete\Delete;
use \request\patch\Patch;
use \request\get\Get;
use \responce\Responce;
use storage\pdostore\Pdostore;

header ("Content-type: text/html; charset=utf-8");
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
$href=$baseURL.urldecode($request_uri);
$parsedRequest=Request::urlParse($request_uri);
//         echo "parsedRequest=";print_r($parsedRequest);
$path=$parsedRequest['path'];
if (!key_exists('collection',$path) || !trim($path['collection'])) {
        \responce\Responce::sendErrorReply(['status'=>'400','title'=>"Request does'nt contain collection",'detail'=>"Request does'nt contant collection"]);
}

$query=$parsedRequest['query'];
$type=ListTypes::getTypeBySubUrl($path['collection']);
if (!$type) {
    \responce\Responce::sendErrorReply(['status'=>'400','title'=>"Unknown collection ". $path['collection']]);    
}
$path['type']=$type;
// echo "Path=";print_r($path);


switch ($_SERVER["REQUEST_METHOD"]) {
    case 'POST':    //Создание объектов
//         echo "Create object $request_uri<br>\n";
        if (key_exists('id',$path) && trim($path['id'])) {
            \responce\Responce::sendErrorReply(['status'=>'400','title'=>"Create request contain id",'detail'=>"Create request contain id".$path['id']]);    
        }
        $json=Post::addObject($path);
        if ($json) {
            Responce::sendObjects($json,'201',["Location: $location"]);
        } else {
            Responce::sendNoContent();            
        }
        break;;
    case 'DELETE':    //Корректировка объектов
//         echo "Delete object $request_uri<br>\n";
        if (!key_exists('id',$path) || !trim($path['id'])) {
            \responce\Responce::sendErrorReply(['status'=>'400','title'=>"DELETE request does'nt contain id",'detail'=>"DELETE request does'nt contain id"]);    
        }
         $json=Delete::deleteObject($path);       
        if ($json) {
            Responce::sendObjects($json,'200');
        } else {
            Responce::sendNoContent();            
        }
        break;;
    case 'PATCH':    //Корректировка объектов
        echo "Update object $request_uri<br>\n";
        if (!key_exists('id',$path) || !trim($path['id'])) {
            \responce\Responce::sendErrorReply(['status'=>'400','title'=>"PATCH request does'nt contain id",'detail'=>"PATCH request does'nt contain id"]);    
        }
        $json=Patch::updateObject($path);       
        if ($json) {
            Responce::sendObjects($json,'200');
        } else {
            Responce::sendNoContent();            
        }
        break;;
    case 'GET':    //Запрос объектов
//         echo "Fetch object $request_uri<br>\n";
        $json=Get::getObject($path,$query);        
        Responce::sendObjects($json,'200');
        break;;
}








