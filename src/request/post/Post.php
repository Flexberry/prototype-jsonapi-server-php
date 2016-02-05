<?php 
namespace request\post;
use \fja\FJA;
use \storage\pdostore\Pdostore;
use \Neomerx\JsonApi\Encoder\Encoder;
use \Neomerx\JsonApi\Encoder\EncoderOptions;
use \Neomerx\JsonApi\Parameters\EncodingParameters;
use \Neomerx\JsonApi\Schema\Link;
/*
 *  Class support POST request
 */
class Post extends \request\Request {

    /*
     * Add the specified in the PATH and Bodyobject 
     * 
     * @return string or false
     */
     public static function addObject($path,$baseURL) {
//         echo "Create object $request_uri<br>\n";
        $json=false;
        $body=self::getBody();
        $object=self::dataToObject($body);
        $classType=get_class($object);
        $primaryKeyName=$object->primaryKeyName;
    //     echo "primaryKeyName=$primaryKeyName\n";
        $changed=false;
        $includePaths=[];
        if (!key_exists($primaryKeyName,$object->attributes) || !trim($object->attributes[$primaryKeyName])) {
            $changed=true;
            $primaryKey=\fja\FJA::uuid_gen();   //Primary key generation
            $object->attributes[$primaryKeyName]=$primaryKey;
        } else {
            $primaryKey=$object->attributes[$primaryKeyName];
        }
//         Pdostore::addObjectToDb($object);
        $included=[];
        if (key_exists('included',$body)) {
            $changed=true;
            list($included,$includePaths)=static::setRelationsInIncludedObjects($body['included'],$object);
//             $included=self::includedToObjectsArray($body);
            
//             echo "AddObject::INCLUDED=";print_r($included);
//             foreach ($included as $id=>$includeObject) {
//                 $includeObjectType=get_class($includeObject);
//                 $LinkRelName=$includeObject->getRelationNameByType($classType);
//                 if ($LinkRelName) {
//                     $includeObject->setRelationship($LinkRelName,$classType,$primaryKey);
//                     echo "setRelationship($includeObjectType -> $LinkRelName,$classType,$primaryKey)\n";
//                 }
//                 $includePaths=array_merge($includePaths,$object->ListLinksOfType($includeObjectType));
// 
//                 echo "includeObjectType=$includeObjectType includePaths=";print_r($includePaths);
//                 $primaryKeyName=$includeObject->primaryKeyName;
//                 $includedPrimaryKey=\fja\FJA::uuid_gen();   //Primary key generation
//                 $oldId=$includeObject->getId();
//                 $includeObject->setId($includedPrimaryKey);
// //                 Pdostore::addObjectToDb($includeObject);
//             }
        }
        if ($changed) {
            echo "AddObject::INCLUDED=";print_r($included);
            echo "POST Object Before:";print_r($object);
            $object=FJA::replaceRelationshipsObject($object,$included);        
            echo "POST Object Changed:";print_r($object);
            $schemas=FJA::formSchema($object);
//             echo "POST::schemas=";print_r($schemas);
            $encoder = Encoder::instance($schemas, new EncoderOptions(0, $baseURL));
            $includePaths=array_unique($includePaths);
            $encodingParameters = new EncodingParameters($includePaths,null);
            echo "encodingParameters=";print_r($encodingParameters);
            $json=$encoder->encodeData($object,$encodingParameters);
//             $objectTree=json_decode($json,true);
//             $location=$objectTree['data']['links']['self'];
        }
        return $json;
     }
     
     

}