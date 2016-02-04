<?php 
namespace request\post;
/*
 *  Class support POST request
 */
class Post extends \request\Request {

    /*
     * Add the specified in the PATH and Bodyobject 
     * 
     * @return string or false
     */
     public static function addObject($path) {
//         echo "Create object $request_uri<br>\n";
        $object=self::dataToObject(self::getBody());
        $primaryKeyName=$object->primaryKeyName;
    //     echo "primaryKeyName=$primaryKeyName\n";
        if (!key_exists($primaryKeyName,$object->attributes) || !trim($object->attributes[$primaryKeyName])) {
            $changed=true;
            $primaryKey=\fja\FJA::uuid_gen();
            $object->attributes[$primaryKeyName]=$primaryKey;
        } else {
            $changed=false;
        }
        Pdostore::addObjectToDb($object); 
        $json=false;
        if ($changed) {
            $object=FJA::replaceRelationshipsObject($object);        
            $schemas=FJA::formSchema($object);
            $encoder = Encoder::instance($schemas, new EncoderOptions(0, $baseURL));
            $json=$encoder->encodeData($object);
            $objectTree=json_decode($json,true);
            $location=$objectTree['data']['links']['self'];
        }
        return $json;
     }
     
     

}