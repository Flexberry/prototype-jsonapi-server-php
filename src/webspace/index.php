<?php
use \fja\FJA;
use \request\Request;
use \request\post\Post;
use \request\delete\Delete;
use \request\patch\Patch;
use \request\get\Get;
use \responce\Responce;
//header ("Content-type: text/html; charset=utf-8");
require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../fja/FJA.php');  //Базовый класс Flexberry JSON API

error_log("Operation: ".$_SERVER["REQUEST_METHOD"]. " " . $_SERVER["REQUEST_URI"]."\n");
FJA::setDomainsDir($_SERVER["DOCUMENT_ROOT"]. "/../../domains");   //Set home directory for all modelClass and Schemas
$path=explode('/',trim($_SERVER["REQUEST_URI"],'/'));
$domain=array_shift($path);   //Domain as first subdomain in path, shift path left 
FJA::setDomain($domain);   //Set root for all modelClass and Schemas

spl_autoload_register(['\fja\FJA', 'autoload'], true, true);

$baseURL="http://".$_SERVER["HTTP_HOST"] . "/$domain/";
$request_uri=implode('/',$path);
$href=$baseURL.urldecode($request_uri);
// echo "<pre>domain=$domain\nrequest_uri=$request_uri\nhref=$href</pre>";
$parsedRequest=Request::urlParse($request_uri,$domain);
// echo "<pre>parsedRequest=";print_r($parsedRequest);echo "</pre>";
$path=$parsedRequest['path'];
if (!key_exists('collection',$path) || !trim($path['collection'])) {
        \responce\Responce::sendErrorReply(['status'=>'400','title'=>"Request does'nt contain collection",'detail'=>"Request does'nt contant collection"]);
}


// echo "Path=";print_r($path);


switch ($_SERVER["REQUEST_METHOD"]) {
    case 'POST':    //Создание объектов
//         echo "Create object $request_uri<br>\n";
        if (key_exists('id',$path) && trim($path['id'])) {
            \responce\Responce::sendErrorReply(['status'=>'400','title'=>"POST request contain id",'detail'=>"Create request contain id".$path['id']]);    
        }
        $json=Post::addObject($path,$baseURL);
        if ($json) {
            $objectTree=json_decode($json,true);
            $location=$objectTree['data']['links']['self'];
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
         $json=Delete::deleteObject($path,$baseURL);       
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
        $json=Patch::updateObject($path,$baseURL);       
        if ($json) {
            Responce::sendObjects($json,'200');
        } else {
            Responce::sendNoContent();            
        }
        break;;
    case 'GET':    //Запрос объектов
//         echo "Fetch object $request_uri<br>\n";
        $json=Get::getObject($parsedRequest,$baseURL);        
        Responce::sendObjects($json,'200');
        break;;
        
    case 'OPTIONS':    //Запрос наследие Odata
//         echo "Fetch object $request_uri<br>\n";
//         $body=Request::getBody();
//         error_log("BODY: $body\n");
        http_response_code('200');
		header_remove("X-Powered-By");
		header_remove("Content-Type");
		header_remove("Content-Length");
		header("Allow: GET, POST, PATCH, DELETE, OPTIONS");
		header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Max-Age: 1000");
		header("Access-Control-Allow-Headers: origin, x-csrftoken, content-type, accept");
// 		header("");
        break;;
        
}








