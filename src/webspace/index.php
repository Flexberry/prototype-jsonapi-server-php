<?php
use \fja\FJA;
use \Neomerx\JsonApi\Encoder\Encoder;
use \Neomerx\JsonApi\Encoder\EncoderOptions;
use \Neomerx\JsonApi\Parameters\EncodingParameters;
use \Neomerx\JsonApi\Schema\Link;

use \request\post\Post;
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
        Pdostore::addObjectToDb($object); 
//         echo "object=".print_r($object,true);        
        $schemas=FJA::formSchemas([$object]);
//         echo "schemas=".print_r($schemas,true);
        $encoder = Encoder::instance($schemas, new EncoderOptions(JSON_PRETTY_PRINT, $baseURL));
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
//         echo "Fetch object $request_uri<br>\n";
        $parsedRequest=Get::urlParse($request_uri);
        $path=$parsedRequest['path'];
        $query=$parsedRequest['query'];
//         echo "parsedRequest=";print_r($parsedRequest);
        $links=[
            Link::SELF => new Link($parsedRequest['location'], null, true)
        ];
        $type=ListTypes::getTypeBySubUrl($path['collection']);
        $path['type']=$type;
        if (key_exists('id',$path)) {   // get one object
            $id=$path['id'];
            $objects=Pdostore::getObjects($type,$id,$query);
            $object=(key_exists(0,$objects)?$objects[0]:null);
            if ($object && key_exists('related',$path)) {   //Get related object: /articles/1/tags,  /articles/1/tags/...
//                 echo "<pre>object=";print_r($object);echo "</pre>";
                $related=$path['related'];
//                 echo "<pre>PATH=";print_r($path);echo "</pre>";
//                 echo "<pre>RELATED=";print_r($related);echo "</pre>";
                while (count($related)>0) {
                    $relName=$related[0];
//                     echo "<pre>relName=$relName Related=";print_r($related);echo "</pre>";
                    $subType=$type::getTypeByRelationName($relName);  
                    if (!$subType) {
                        $detail="The Relationship $relName does not exist";
                        \responce\Responce::sendErrorReply(['status'=>'404','title'=>'The Relationship  does not exist','detail'=>$detail]);            
                    }
                     if (!class_exists($subType)) {
                        $detail="The type  $subType does not exist";
                        \responce\Responce::sendErrorReply(['status'=>'404','title'=>'The type  does not exist','detail'=>$detail]);            
                    }
            
                    $PrimaryKeyName=$subType::$PrimaryKeyName;
//                     echo "<pre>subobject=";print_r($object->relationships[$relName]);echo "</pre>";
                    $data=$object->relationships[$relName]['data'];
                    if (is_array($data)) {
                        $id=$related[1];
                        $related=array_slice($related,2);
                    } else {
                        $id=$object->relationships[$relName]['data']->attributes[$PrimaryKeyName];
                        $related=array_slice($related,1);
                    }
                    $type=$subType;
//                     echo "<pre>id=$id\nrelated=";print_r($related);echo "</pre>";
                    $objects=Pdostore::getObjects($subType,$id,$query);
                    $object=(key_exists(0,$objects)?$objects[0]:null);
                }
            }
//             echo "<pre>OBJECT=";print_r($object);echo "</pre>\n";
            $listObjects=$objects;  //List Objects for schema generations
            $encodedObject=$objects;    // encoded Object
//             $schemas=FJA::formSchemas($objects);
//             $encoder = Encoder::instance($schemas, new EncoderOptions(JSON_PRETTY_PRINT, $baseURL));
//             $json=$encoder->withLinks($links)->encodeData($object);
// //             echo "<pre>JSON=$json</pre>\n";
// //             echo "<pre>PHPJSON=";print_r(json_decode($json,true));echo "</pre>\n";
        } else {    //get collection of objects
            $objects=Pdostore::getObjects($type,null,$query);
            $listObjects=$objects;  //List Objects for schema generations
            $encodedObject=$objects;    // encoded Object
        }
//         echo "LISTOBJECTS=";print_r($listObjects);
        $schemas=FJA::formSchemas($listObjects);
//         echo "schemas=";print_r($schemas);
        $includePaths=(key_exists('include',$query)?$query['include']:[]);
        $fieldSets=[];
        $encodingParameters = new EncodingParameters($includePaths,$fieldSets);

        $encoder = Encoder::instance($schemas, new EncoderOptions(JSON_PRETTY_PRINT, null));
//             echo "encoder=".print_r($encoder,true);
        $json=$encoder->withLinks($links)->encodeData($encodedObject,$encodingParameters);
//             echo "<pre>PHPJSON=";print_r(json_decode($json,true));echo "</pre>\n";
        
        $objectTree=json_decode($json,true);
        $location=$objectTree['links']['self'];
        Responce::sendObjects($json);

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









